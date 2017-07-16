<?php namespace CubeScripts\Plugins;

use League\Event\EmitterInterface;
use PluginManager;

abstract class PluginBase
{
    private $_ci;

    public function __construct()
    {
        $this->_ci = &get_instance();
    }

    /**
     * Get application instance (current controller instance).
     *
     * @return \CI_Controller
     */
    public function app()
    {
        return $this->_ci;
    }

    /**
     * Return PluginManager instance.
     *
     * @return PluginManager
     */
    public function plugins()
    {
        return $this->_ci->pluginmanager;
    }

    /**
     * Provide plugin info as an associative array.
     * Required fields are: name, slug, version, description, author
     *
     * @return array
     */
    public abstract function info();

    /**
     * Executed when plugin is initialized (each page load when plugin is active).
     * Here you can add hooks and filters, register pages etc...
     */
    public abstract function bootstrap();

    /**
     * Get one tag from plugin info.
     *
     * @param string $tag
     *
     * @return string
     */
    public function getInfo($tag)
    {
        return $this->info()[$tag];
    }

    /**
     * Executed when plugin is first installed.
     *
     * @param string $version
     */
    public function installed($version = null)
    {
    }

    /**
     * Executed when plugin is uninstalled.
     *
     * @param string $version
     */
    public function unInstalled($version = null)
    {
    }

    /**
     * Executed when plugin is activated.
     */
    public function activated()
    {
    }

    /**
     * Executed when plugin is deactivated.
     */
    public function deActivated()
    {
    }

    /**
     * @param string   $hookName
     * @param Callable $callable
     * @param int      $priority
     */
    public function addHook($hookName, $callable, $priority = EmitterInterface::P_NORMAL)
    {
        $this->app()->pluginmanager->addHook($hookName, $callable, $priority);
    }

    /**
     * @param string   $filterName
     * @param Callable $callable
     * @param int      $priority
     */
    public function addFilter($filterName, $callable, $priority = EmitterInterface::P_NORMAL)
    {
        $this->app()->pluginmanager->addFilter($filterName, $callable, $priority);
    }

    /**
     * @param string         $pageSlug
     * @param Callable|array $callables
     */
    public function registerPage($pageSlug, $callables)
    {
        $this->app()->pluginmanager->registerPage($pageSlug, $callables);
    }

    /**
     * @param string         $pageSlug
     * @param Callable|array $callables
     */
    public function registerAdminPage($pageSlug, $callables)
    {
        $this->app()->pluginmanager->registerAdminPage($pageSlug, $callables);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        $plugin = \Plugin::factory($this->getInfo('slug'));

        if (!$plugin->exists())
            return $default;

        if (empty($plugin->data))
            return $default;

        $data = json_decode($plugin->data, true);
        return (isset($data[$key])) ? $data[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function setData($key, $value)
    {
        $plugin = \Plugin::factory($this->getInfo('slug'));

        if (!$plugin->exists())
            return false;

        if (empty($plugin->data))
            $data = [];
        else
            $data = json_decode($plugin->data, true);

        $data[$key]   = $value;
        $plugin->data = json_encode($data);
        $plugin->save();
        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function removeData($key)
    {
        $plugin = \Plugin::factory($this->getInfo('slug'));

        if (!$plugin->exists())
            return false;

        if (empty($plugin->data))
            $data = [];
        else
            $data = json_decode($plugin->data, true);

        unset($data[$key]);
        $plugin->data = json_encode($data);
        $plugin->save();
        return true;
    }

    /**
     * Activate plugin.
     */
    public function activate()
    {
        $this->app()->pluginmanager->activatePlugin($this->getInfo('slug'));
    }

    /**
     * Deactivate plugin.
     */
    public function deActivate()
    {
        $this->app()->pluginmanager->deActivatePlugin($this->getInfo('slug'));
    }

    /**
     * Return path to plugin folder. You can provide optional relative
     * file path to append to the plugin folder path.
     *
     * @param null|string $path
     *
     * @return string
     */
    public function plugin_path($path = null)
    {
        return FCPATH.'plugins'.DIRECTORY_SEPARATOR.$this->getInfo('slug').DIRECTORY_SEPARATOR.(!empty($path) ? $path : '');
    }

    /**
     * Return URL to plugin folder. You can provide optional relative
     * file path to append to the plugin folder URL.
     *
     * @param null|string $path
     *
     * @return string
     */
    public function plugin_url($path = null)
    {
        return base_url().'plugins/'.$this->getInfo('slug').'/'.(!empty($path) ? $path : '');
    }

}