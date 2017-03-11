<?php

namespace Rev\ExPage;

use Rev\ExPage\View\BrowserView;
use Rev\ExPage\View\CliView;

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

    /**
     * Parameters for cli view
     * @var array
     */
    private $cliViewParameters = array();

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

        $this->workingDirectoryPath = $parameters['dirname'];

        if (!array_key_exists('filelog', $parameters)){
            throw new \Exception("You must determine name of default filelog!");
        }

        $this->logFileName = $parameters['filelog'];


        if (array_key_exists('template', $parameters)){
            if (!is_string($parameters['template']) && !is_null($parameters['template'])){
                throw new \Exception("Parameter 'template' can be only null or 'default' or HTML template!");
            }

            $this->pageTemplate = $parameters['template'];
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

        $this->cliViewParameters = $parameters['cli_view'] ?? array();
    }

    /**
     * Starts output buffering.
     */
    private function startOutputBuffering()
    {
        ob_start();
    }

    /**
     * Cleans output buffer
     */
    private function cleanOutputBuffer()
    {
        ob_clean();
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

        /**
         * No errors occurred and no caughted exceptions
         */
        if (!sizeof($occurredErrors) && !sizeof($uncaughtedExceptions)){
            return;
        }

        $sapiName = php_sapi_name();

        $sapiName = "dasdas";
        if ($sapiName === 'cli'){
            $cliView = new CliView($this->cliViewParameters);
            $cliView->setOccurredErrors($occurredErrors);
            $cliView->setUncaughtedException($uncaughtedExceptions);
            $cliView->render();
        }
        else{
            $browserView = new BrowserView();
            $browserView->setOccurredErrors($occurredErrors);
            $browserView->setUncaughtedException($uncaughtedExceptions);
            $browserView->setViewsDirectoryPath(dirname(__FILE__) . '/Resources/View');
            $browserView->setTemplateFile($this->pageTemplate);
            $browserView->render();
        }
    }

}

?>