<?php

namespace Rev\ExPage;

use Rev\ExPage\Error;

/**
 * Class Listener
 * Helps to handle errors and exceptions.
 *
 * @package Rev\ExPage
 */
class Listener
{
    /**
     * Stores all occurred errors
     * @var \Rev\ExPage\Error[]
     */
    private $errors = array();

    /**
     * Stores all uncaughted exceptions
     * @var \Exception[]
     */
    private $uncaughtExceptions = array();

    public function __construct()
    {
        $this->registerErrorHandler();

        $this->registerUncaughtExceptionHandler();
    }

    /**
     * Registers error handler
     */
    protected function registerErrorHandler()
    {
        set_error_handler(function(int $level, string $message, string $filename, int $linenumber, array $context){
            $error = new Error();
            $error->setLevel($level);
            $error->setMessage($message);
            $error->setFileName($filename);
            $error->setLineNumber($linenumber);
            $error->setScopeAround($context);

            array_push($this->errors, $error);
        });
    }

    /**
     * Registers handler on uncaughted exceptions
     */
    protected function registerUncaughtExceptionHandler()
    {
        set_exception_handler(function(\Exception $uncaughtedException) {
            /**
             * Stores uncaughted exception
             */
            array_push($this->uncaughtExceptions, $uncaughtedException);
        });
    }

    /**
     * Gets array with occurred errors
     * @return \Rev\ExPage\Error[]
     */
    public function getOccurredErrors() : array
    {
        return $this->errors;
    }

    /**
     * Gets array with uncaughted exceptions
     * @return array
     */
    public function getUncaughtedExceptions() : array
    {
        return $this->uncaughtExceptions;
    }

}

?>