<?php namespace CubeScripts\Exceptions;

use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

class Handler
{
    public function __construct()
    {
        throw new \Exception("This is a static class.");
    }

    public static function render(\Exception $exception)
    {
        $_ci = &get_instance();

        /**
         * Example of custom error handling.
         */

        //handler for ValidationFailedException
//        if ($exception instanceof ValidationFailedException) {
//            if ($_ci->request()->previous_url()) {
//                $_ci->session->set_flashdata('errors', $exception->errors());
//                $_ci->session->set_flashdata('old', $_ci->request()->all());
//                redirect($_ci->request()->previous_url());
//                die;
//            }
//
//            \show_error($exception->getErrors());
//        }

        /**
         * Do not edit below! This will display stack trace if app is in the debug mode,
         * or just show an error message otherwise.
         */
        if (defined('IU_APP_DEBUG') && (IU_APP_DEBUG == true)) {
            $run     = new Run;
            $handler = new PrettyPageHandler;
            $config  = get_config();

            unset($config['datamapper']);

            if (class_exists(\DataMapper::class)) {
                $dm_config               = \DataMapper::$config;
                $dm_config['last_query'] = $_ci->db->last_query();
                $handler->addDataTable('Database', $dm_config);
            }

            $handler->setPageTitle("Whoops! There was a problem.");

            $run->pushHandler($handler);

            if (Misc::isAjaxRequest()) {
                $run->pushHandler(new JsonResponseHandler);
            }

            $run->register();
            $run->handleException($exception);
        } else {
            \show_error($exception->getMessage());
        }

    }
}