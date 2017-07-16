<?php

class Plugin extends DataMapper
{
    public static function factory($idOrSlug = null)
    {
        $obj = new Plugin;
        if (is_numeric($idOrSlug)) {
            $obj->where('id', $idOrSlug)->limit(1)->get();
        } elseif (is_string($idOrSlug)) {
            $obj->where('slug', $idOrSlug)->limit(1)->get();
        }

        return $obj;
    }
}