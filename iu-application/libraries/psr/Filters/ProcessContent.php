<?php namespace CubeScripts\Filters;

class ProcessContent extends FilterBase
{
    public function page() {
        return $this->app()->page;
    }

    public function content() {
        return $this->app()->content;
    }
}