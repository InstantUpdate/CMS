<?php namespace CubeScripts\Exceptions;

class DatamapperSQLException extends \Exception
{

    protected $query;

    public function __construct($query, $message = '')
    {
        $this->query = strip_tags($query);
        parent::__construct($message. ': '. $this->query);
    }

    public function getQuery()
    {
        return $this->query;
    }
}