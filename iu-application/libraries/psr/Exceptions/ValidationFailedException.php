<?php namespace CubeScripts\Exceptions;

class ValidationFailedException extends \Exception
{
    /** @var array */
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors  = $errors;
        $this->message = $this->getErrors(false);
    }

    public function getErrors($html = true)
    {
        $output = '';
        foreach ($this->errors as $field) {
            foreach ($field as $error) {
                $output .= $error."\n";
            }
        }

        if ($html) {
            return nl2br($output);
        }

        return $output;
    }

    public function errors()
    {
        return $this->errors;
    }
}