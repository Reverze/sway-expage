<?php

namespace Rev\ExPage\View;

abstract class View
{
    /**
     * The array which contains all occurred errors.
     * @var \Rev\ExPage\Error\[]
     */
    protected $occurredErrors = array();

    /**
     * The array which contains all uncaughted exceptions
     * @var \Exception[]
     */
    protected $uncaughtedExceptions = array();

    public function __construct()
    {

    }

    /**
     * Sets an array which contains all occurred errors.
     * @param array $occurredErrors
     */
    public function setOccurredErrors(array $occurredErrors)
    {
        $this->occurredErrors = $occurredErrors;
    }

    /**
     * Sets an array which contains all uncaughted exceptions.
     * @param array $uncaughtedExceptions
     */
    public function setUncaughtedException(array $uncaughtedExceptions)
    {
        $this->uncaughtedExceptions = $uncaughtedExceptions;
    }

    public abstract function render();

}

?>