<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once dirname(__FILE__)."/datamapper.php";

class DataMapperE extends DataMapper implements JsonSerializable
{

    protected $cachepath = 'application/cache/db/sql';

    /** @var CS_Controller */
    protected $_ci;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        $this->_ci = &get_instance();
    }

    public function get_cache_filepath($sql, $cache_filename = null)
    {
        //$sql = $this->get_sql();

        $md5sql          = empty($cache_filename) ? md5($sql) : $cache_filename;
        $local_cachepath = $this->cachepath.'/'.$this->table;

        if (!is_dir($local_cachepath) || !is_dir($this->cachepath)) {
            @mkdir($this->cachepath);
            @mkdir($local_cachepath);
        }

        $cachefname = $md5sql.'.cache';

        return $local_cachepath.'/'.$cachefname;
    }

    public function get_cache($cache_length = 3600, $cache_filename = null)
    {
        $sql          = $this->get_sql(null, null, true);
        $cachefile    = $this->get_cache_filepath($sql, $cache_filename);
        $cache_exists = is_file($cachefile);

        $filemtime = ($cache_exists ? filemtime($cachefile) : 0);
        $timediff  = ($cache_exists ? time() - $filemtime : $cache_length + 1);

        if (!$cache_exists || ($timediff > $cache_length)) {
            //no cache, execute query and cache results
            $this->query($sql);
            $cachedOBJ            = new stdClass();
            $cachedOBJ->query     = $sql;
            $cachedOBJ->timestamp = time();
            $cachedOBJ->cache     = serialize($this);
            @file_put_contents($cachefile, json_encode($cachedOBJ));
            return $this;
        } else {
            //serve cache
            $data                         = file_get_contents($cachefile);
            $obj                          = json_decode($data);
            $cachedOBJ                    = unserialize($obj->cache);
            $cachedOBJ->cached            = isset($cachedOBJ->cached) ? $cachedOBJ->cached : new stdClass;
            $cachedOBJ->cached->timestamp = $obj->timestamp;
            $cachedOBJ->cached->query     = $obj->query;
            $cachedOBJ->cached->filename  = $cachefile;
            return $cachedOBJ;
        }

    }

    /**
     * Clears the cached query the next time get_cached is called.
     *
     * @return    DataMapper The DataMapper $object for chaining.
     */
    function clear_cache()
    {
        $dir = $this->cachepath.'/'.$this->table;
        @rmdir_recursive($dir);
        @mkdir($dir);

        return $this;
    }

    public function first()
    {
        return $this->all[0];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        $protected = ['salt', 'password'];
        $obj       = json_decode(($this->result_count() < 1) ? $this->to_json() : $this->all_to_json());

        foreach ($protected as $protect) {
            if (isset($obj->{$protect})) {
                unset($obj->{$protect});
            }
        }

        return $obj;
    }

    public function save($object = '', $related_field = '')
    {
        $new = !$this->exists();

        if ($new) {
//            $this->_ci->events->emit(new \CubeScripts\Events\Models\ModelCreating($this));
        } else {
//            $this->_ci->events->emit(new \CubeScripts\Events\Models\ModelUpdating($this));
        }

        parent::save($object, $related_field);

        if ($new) {
//            $this->_ci->events->emit(new \CubeScripts\Events\Models\ModelCreated($this));
        } else {
//            $this->_ci->events->emit(new \CubeScripts\Events\Models\ModelUpdated($this));
        }
    }

    public function delete($object = '', $related_field = '')
    {
//        $this->_ci->events->emit(new \CubeScripts\Events\Models\ModelDeleting($this));

        parent::delete($object, $related_field);

//        $this->_ci->events->emit(new \CubeScripts\Events\Models\ModelDeleted($this));
    }
}