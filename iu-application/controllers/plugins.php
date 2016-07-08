<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Plugins extends CS_Controller
{

    public function page($args = null)
    {
        $args     = empty($args) ? func_get_args() : $args;
        $pageSlug = array_shift($args);

        if ($this->in_admin()) {
            $pageSlug = CS_ADMIN_CONTROLLER_FOLDER.'/'.$pageSlug;
        }

        if (!$this->pluginmanager->isPageRegistered($pageSlug)) {
            error_404($pageSlug);
        }

        $this->pluginmanager->processPage($pageSlug, $args);
    }

}