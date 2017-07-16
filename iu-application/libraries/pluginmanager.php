<?php
use CubeScripts\Filters\FilterBase;
use CubeScripts\Plugins\PluginBase;
use League\Event\Emitter;
use League\Event\EmitterInterface;

class PluginManager
{
    /**
     * @var Emitter
     */
    protected $emitter;

    /**
     * @var array
     */
    protected $bootstrapped = array();

    /**
     * @var array
     */
    protected $pages = array();

    /**
     * PluginManager constructor.
     */
    public function __construct()
    {
        $this->emitter = new Emitter;
    }

    /**
     * @param string   $hookName
     * @param Callable $callable
     * @param int      $priority
     */
    public function addHook($hookName, $callable, $priority = EmitterInterface::P_NORMAL)
    {
        $this->emitter->addListener($hookName, $callable, $priority);
    }

    /**
     * @param string $hookName
     * @param null   $paramValue
     */
    public function triggerHook($hookName, $paramValue = null)
    {
        $this->emitter->emit($hookName, $paramValue);
    }

    /**
     * @param string   $hookName
     * @param Callable $callable
     */
    public function removeHook($hookName, $callable)
    {
        $this->emitter->removeListener($hookName, $callable);
    }

    /**
     * @param string   $filterName
     * @param Callable $callable
     * @param int      $priority
     *
     * @throws Exception
     */
    public function addFilter($filterName, $callable, $priority = EmitterInterface::P_NORMAL)
    {
        $className = str_replace('.', ' ', strtolower($filterName));
        $className = str_replace(' ', '', ucwords($className));
        $fqdn      = "CubeScripts\\Filters\\".$className;

        if (!class_exists($fqdn) && !class_exists($className)) {
            throw new Exception("Filter $filterName does not exist!");
        }

        $this->emitter->addListener(class_exists($fqdn) ? $fqdn : $className, function (FilterBase $factory, $originalValue) use ($callable) {
            $factory->createFilter($callable, $originalValue);
        }, $priority);
    }

    /**
     * @param string $filterName
     * @param null   $paramValue
     *
     * @return \League\Event\EventInterface|string
     */
    public function triggerFilter($filterName, $paramValue = null)
    {
        $className = str_replace('.', ' ', strtolower($filterName));
        $className = str_replace(' ', '', ucwords($className));
        $fqdn      = "CubeScripts\\Filters\\".$className;
        $class     = class_exists($fqdn) ? $fqdn : $className;
        $_ci       = &get_instance();
        return $this->emitter->emit(new $class($_ci), $paramValue);
    }

    /**
     * @param string   $filterName
     * @param Callable $callable
     */
    public function removeFilter($filterName, $callable)
    {
        $className = str_replace('.', ' ', strtolower($filterName));
        $className = str_replace(' ', '', ucwords($className));
        $fqdn      = "CubeScripts\\Filters\\".$className;
        $this->removeHook(class_exists($fqdn) ? $fqdn : $className, $callable);
    }

    /**
     * @return DataMapperE
     */
    public function getActivePlugins()
    {
        return Plugin::factory()->where('active', true)->get();
    }

    /**
     * @return DataMapperE
     */
    public function getInActivePlugins()
    {
        return Plugin::factory()->where('active', false)->get();
    }

    /**
     * @return DataMapperE
     */
    public function getRegisteredPlugins()
    {
        return Plugin::factory()->get();
    }

    /**
     * @param string $pluginSlug
     *
     * @return bool
     */
    public function isRegisteredPlugin($pluginSlug)
    {
        return Plugin::factory()->where('slug', $pluginSlug)->limit(1)->get()->exists();
    }

    /**
     * @param string $pluginSlug
     *
     * @return bool|Plugin
     */
    public function registerPlugin($pluginSlug)
    {
        if ($this->isRegisteredPlugin($pluginSlug))
            return false;

        /** @var PluginBase $pluginInstance */
        $pluginInstance = $this->initPlugin($pluginSlug);
        $infoData       = $pluginInstance->info();

        $plugin              = new Plugin();
        $plugin->name        = $infoData['name'];
        $plugin->version     = $infoData['version'];
        $plugin->slug        = $infoData['slug'];
        $plugin->author      = $infoData['author'];
        $plugin->description = $infoData['description'];
        $plugin->author_url  = $infoData['author_url'];
        $plugin->plugin_url  = $infoData['plugin_url'];
        $plugin->save();

        $pluginInstance->installed($infoData['version']);

        return $plugin;
    }

    /**
     * @param string     $pluginSlug
     * @param bool|false $delete
     *
     * @return bool|Plugin
     * @throws Exception
     */
    public function unRegisterPlugin($pluginSlug, $delete = false)
    {
        if (!$this->isRegisteredPlugin($pluginSlug))
            return false;

        /** @var PluginBase $pluginInstance */
        $pluginInstance = $this->initPlugin($pluginSlug);
        if ($pluginInstance) {
            $infoData = $pluginInstance->info();
            $pluginInstance->unInstalled($infoData['version']);
        }

        $plugin = $this->getPluginDBO($pluginSlug);
        $plugin->delete();

        if ($delete) {
            @rmdir_recursive(APPPATH.'plugins/'.$pluginSlug);
        }

        return $plugin;
    }

    /**
     * @return array
     */
    public function scanForPlugins()
    {
        $all   = scandir(APPPATH.'plugins');
        $valid = [];
        foreach ($all as $dir) {
            if ($dir == '.' || $dir == '..')
                continue;

            if (!is_dir(APPPATH.'plugins/'.$dir))
                continue;

            $valid[] = $dir;
        }

        return $valid;
    }

    /**
     * @param string $pluginSlug
     *
     * @return mixed
     */
    public function initPlugin($pluginSlug)
    {
        if (!is_file(APPPATH.'plugins/'.$pluginSlug.'/'.$pluginSlug.'.php')) {
            $this->unRegisterPlugin($pluginSlug);
            return false;
        }
        require_once APPPATH.'plugins/'.$pluginSlug.'/'.$pluginSlug.'.php';
        $name = str_replace(['-', '_'], ' ', strtolower($pluginSlug));
        $name = str_replace(' ', '', ucwords($name));
        return new $name();
    }

    /**
     * @param string $pluginSlug
     */
    public function bootstrapPlugin($pluginSlug)
    {
        if (!in_array($pluginSlug, $this->bootstrapped)) {
            $plugin = $this->initPlugin($pluginSlug);
            if ($plugin) {
                $plugin->bootstrap();
                $this->bootstrapped[] = $pluginSlug;
            }
        }
    }

    /**
     * Bootstraps the activated plugins so they can be used.
     */
    public function bootstrapActivePlugins()
    {
        $plugins = $this->getActivePlugins();
        foreach ($plugins as $plugin) {
            $this->bootstrapPlugin($plugin->slug);
        }
    }

    /**
     * @param string $pluginSlug
     *
     * @return Plugin
     * @throws Exception
     */
    protected function getPluginDBO($pluginSlug)
    {
        $plugin = Plugin::factory($pluginSlug);
        if (!$plugin->exists()) {
            throw new Exception("Plugin {$pluginSlug} is not registered!");
        }

        return $plugin;
    }

    /**
     * @param string $pluginSlug
     *
     * @return bool
     * @throws Exception
     */
    public function activatePlugin($pluginSlug)
    {
        $plugin         = $this->getPluginDBO($pluginSlug);
        $plugin->active = true;
        $plugin->save();

        /** @var PluginBase $pluginInstance */
        $pluginInstance = $this->initPlugin($pluginSlug);
        if ($pluginInstance) {
            $pluginInstance->activated();
        }

        return true;
    }

    /**
     * @param string $pluginSlug
     *
     * @return bool
     * @throws Exception
     */
    public function deActivatePlugin($pluginSlug)
    {
        $plugin = $this->getPluginDBO($pluginSlug);

        /** @var PluginBase $pluginInstance */
        $pluginInstance = $this->initPlugin($pluginSlug);
        if ($pluginInstance) {
            $pluginInstance->deActivated();
        }

        $plugin->active = false;
        $plugin->save();
        return true;
    }

    /**
     * @param string $pluginSlug
     *
     * @return bool
     * @throws Exception
     */
    public function togglePluginActivation($pluginSlug)
    {
        $plugin = $this->getPluginDBO($pluginSlug);

        if ($plugin->active) {
            $this->deActivatePlugin($pluginSlug);
        } else {
            $this->activatePlugin($pluginSlug);
        }

        return true;
    }

    /**
     * @param string $pluginSlug
     *
     * @return bool
     * @throws Exception
     */
    public function isPluginActive($pluginSlug)
    {
        $plugin = $this->getPluginDBO($pluginSlug);

        return (bool)$plugin->active;
    }

    /**
     * @param string         $pageSlug
     * @param Callable|array $callables
     */
    public function registerPage($pageSlug, $callables)
    {
        $postFunc = $getFunc = $callables;

        if (is_array($callables)) {
            $postFunc = $callables['post'];
            $getFunc  = $callables['get'];
        }

        $this->pages[$pageSlug] = [
            'post' => $postFunc,
            'get'  => $getFunc
        ];
    }

    /**
     * @param string   $pageSlug
     * @param Callable $callables
     */
    public function registerAdminPage($pageSlug, $callables)
    {
        $postFunc = $getFunc = $callables;

        if (is_array($callables)) {
            $postFunc = $callables['post'];
            $getFunc  = $callables['get'];
        }

        $this->pages[CS_ADMIN_CONTROLLER_FOLDER.'/'.$pageSlug] = [
            'post' => $postFunc,
            'get'  => $getFunc
        ];
    }

    /**
     * @param string $pageSlug
     *
     * @return bool
     */
    public function isPageRegistered($pageSlug)
    {
        return isset($this->pages[$pageSlug]);
    }

    /**
     * @param string $pageSlug
     * @param array  $args
     */
    public function processPage($pageSlug, $args)
    {
        if (empty($_POST)) {
            call_user_func_array($this->pages[$pageSlug]['get'], $args);
        } else {
            call_user_func_array($this->pages[$pageSlug]['post'], $args);
        }
    }

}