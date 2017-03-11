<?php

namespace Rev\ExPage;

class Manager
{
    /**
     * Working directory path (for ExPage)
     * @var string
     */
    private $workingDirectoryPath = null;

    /**
     * Default log filename (if errors are not separated)
     * @var string
     */
    private $logFileName = null;

    /**
     * Page template.
     * If null, page will be not showed, if 'default', built-in template will be used,
     * otherwise you must set raw HTML template
     * @var string|null
     */
    private $pageTemplate = null;

    /**
     * The name of files where log errors separated by type are stored.
     * @var array
     */
    private $separate = [
        'errors' => null,
        'exceptions' => null
    ];

    /**
     * If you want temporarily disable error recording.
     * @var bool
     */
    private $disabled = false;

    /**
     * The listener for errors and uncaughted exceptions
     * @var \Rev\ExPage\Listener
     */
    private $listener = null;

    public function __construct(bool $enabled = true, array $parameters)
    {
        $this->startOutputBuffering();

        $this->loadParameters($parameters);

        $this->startListen();

        $this->registerHandlerOnExit();
    }

    /**
     * Starts listening for errors and uncaughted exceptions.
     */
    private function startListen()
    {
        $this->listener = new Listener();
    }

    /**
     * Loads parameters from array given by user.
     * @param array $parameters
     * @throws Exception
     */
    private function loadParameters(array $parameters)
    {
        /**
         * Working directory path must be declared.
         */
        if (!array_key_exists('dirname', $parameters)){
            throw new \Exception("Working directory path is not specified. Missed parameter 'dirname'!");
        }

        if (!is_dir($parameters['dirname'])){
            throw new \Exception(sprintf("Directory was not found on path '%s'.", $parameters['dirname']));
        }

        if (!array_key_exists('filelog', $parameters)){
            throw new \Exception("You must determine name of default filelog!");
        }


        if (array_key_exists('template', $parameters)){
            if (!is_string($parameters['template']) && !is_null($parameters['template'])){
                throw new \Exception("Parameter 'template' can be only null or 'default' or HTML template!");
            }
        }

        if (array_key_exists('separate', $parameters)){
            /**
             * If name of file which stores user errors is not determined,
             * all user errors will be stored in default log file.
             */
            $this->separate['user_errors'] = $parameters['separate']['user_errors'] ?? null;

            /**
             * If name of file which stores php errors is not determined,
             * all php errors will be stored in default log file.
             */
            $this->separate['php_errors'] = $parameters['separate']['php_errors'] ?? null;

            /**
             * If name of file which stores uncaugthed exceptions is not determined,
             * all uncaughted exception will be stored in default log file.
             */
            $this->separate['exceptions'] = $parameters['separate']['exceptions'] ?? null;
        }
    }

    /**
     * Starts output buffering.
     */
    private function startOutputBuffering()
    {
        ob_start();
    }

    /**
     * All errors and exceptions will be serviced on script exit.
     */
    private function registerHandlerOnExit()
    {
        register_shutdown_function(function(){
            $this->handleOnExit();
        });
    }

    private function handleOnExit()
    {
        /**
         * If error manager is disabled, exit.
         */
        if ($this->disabled){
            return;
        }

        /**
         * Gets all occurred errors recorded by the listener
         */
        $occurredErrors = $this->listener->getOccurredErrors();

        /**
         * Gets all uncaughted exceptions recorded by the listener
         */
        $uncaughtedExceptions = $this->listener->getUncaughtedExceptions();

        var_dump($_SERVER);
    }

}

?>