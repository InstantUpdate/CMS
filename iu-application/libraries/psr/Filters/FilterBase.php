<?php namespace CubeScripts\Filters;

use League\Event\AbstractEvent;

abstract class FilterBase extends AbstractEvent
{
    protected $filters = array();
    protected $originalValue = null;
    protected $controller = null;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function app()
    {
        return $this->controller;
    }

    /**
     * Internal function to create a filter.
     *
     * @param Callable $callable
     * @param mixed    $originalValue
     */
    public function createFilter($callable, $originalValue = null)
    {
        $this->filters[] = $callable;
        if ($originalValue !== null) {
            $this->originalValue = $originalValue;
        }
    }

    /**
     * Call all filters for particular event modifying value as they are executed.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function callFilters($value)
    {
        foreach ($this->filters as $filter) {
            $value = call_user_func($filter, $value, $this->originalValue, $this);
        }

        return $value;
    }

    /**
     * Call all filters for particular event and return each result as an array.
     *
     * @param $value
     *
     * @return array
     */
    public function stackFilters($value)
    {
        $result = [];
        foreach ($this->filters as $filter) {
            $result[] = call_user_func($filter, $value, $this->originalValue, $this);
        }

        return $result;
    }

    /**
     * Call all filters for particular event and return each result as an array element.
     *
     * @param $value
     *
     * @return array
     */
    public function flattenStackFilters($value)
    {
        $result  = [];
        $filters = $this->stackFilters($value);
        foreach ($filters as $filter) {
            if (is_array($filter)) {
                $result = array_merge($result, $filter);
            } else {
                $result[] = $filter;
            }
        }

        return $result;
    }

    /**
     * Get all registered filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

}