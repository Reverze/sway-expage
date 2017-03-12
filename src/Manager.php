<?php

namespace Rev\ExPage;

use Rev\ExPage\Log\Entry;
use Rev\ExPage\Log\FileLog;
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

    /**
     * @var string
     */
    private $mode = 'dev';

    /**
     * Callback to authorize show error output
     * @var callable|null
     */
    private $authorizeOutputCallback = null;

    /**
     * If set to True, and callback returns True, shows error output in production mode
     * @var bool
     */
    private $authorizeInProduction = false;

    /**
     * Parameters in raw format given from user
     * @var array
     */
    private $rawParameters = array();

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
            if (!is_string($parameters['template']) && !is_null($parameters['template']) && !is_array($parameters['template'])){
                throw new \Exception("Parameter 'template' can be only null or 'default' or HTML template!");
            }

            $this->pageTemplate = $parameters['template'];

            if (is_array($this->pageTemplate)){
                if (!array_key_exists('dev', $this->pageTemplate)){
                    $this->pageTemplate['dev'] = null;
                }

                if (!array_key_exists('prod', $this->pageTemplate)){
                    $this->pageTemplate['prod'] = null;
                }
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

        $this->cliViewParameters = $parameters['cli_view'] ?? array();

        if (array_key_exists('mode', $parameters)){
            if ($parameters['mode'] === 'dev'){
                $this->mode = 'dev';
            }
            else if ($parameters['mode'] === 'prod'){
                $this->mode = 'prod';
            }
            else{
                throw new \Exception(spritnf("Invalid value for parameter 'mode': '%s'. Only 'prod' or 'dev'.", $parameters['mode']));
            }
        }

        $this->rawParameters = $parameters;
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

        $this->logEntries($occurredErrors, $uncaughtedExceptions);

        $sapiName = php_sapi_name();

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

            if (is_array($this->pageTemplate)){
                if ($this->mode === 'dev'){
                    $browserView->setTemplateFile($this->pageTemplate['dev']);
                }
                else{
                    $browserView->setTemplateFile($this->pageTemplate['prod']);
                }
            }
            else if (is_string($this->pageTemplate)){
                $browserView->setTemplateFile($this->pageTemplate);
            }

            /**
             * If mode 'dev' is set and authorize callback is not defined,
             * shows errors output
             */
            if ($this->mode === 'dev' && is_null($this->authorizeOutputCallback)){
                $browserView->showOutput();
            }

            /**
             * If authorize callback is defined, shows errors output.
             */
            if (is_callable($this->authorizeOutputCallback)){
                if ($this->mode === 'prod' && !$this->authorizeInProduction){
                    //do nothing
                }
                else{
                    $callback = $this->authorizeOutputCallback;

                    $authorizeResult = $callback();

                    if ($authorizeResult === true){
                        $browserView->showOutput();
                    }
                }
            }

            $browserView->mode = $this->mode;
            $browserView->parameters = $this->rawParameters;

            $browserView->render();
        }
    }

    /**
     * @param \Rev\ExPage\Error[] $occurredErrors
     * @param \Exception[] $uncaughtedExceptions
     */
    private function logEntries(array $occurredErrors, array $uncaughtedExceptions)
    {
        $defaultFileLog = new FileLog($this->workingDirectoryPath . '/' . $this->logFileName);

        if ($this->separate['errors'] !== null){
            if (sizeof($occurredErrors)){
                $errorFileLog = new FileLog($this->workingDirectoryPath . '/' . $this->separate['errors']);
                $logEntry = new Entry();
                $logEntry->addErrors($occurredErrors);

                $errorFileLog->append($logEntry->render());
            }
        }

        if ($this->separate['exceptions'] !== null){
            if (sizeof($uncaughtedExceptions)){
                $exceptionFileLog = new FileLog($this->workingDirectoryPath . '/'. $this->separate['exceptions']);
                $logEntry = new Entry();
                $logEntry->addExceptions($uncaughtedExceptions);

                $exceptionFileLog->append($logEntry->render());
            }
        }

        $logEntry = new Entry();
        $emptyDefaultLog = true;

        if (empty($this->separate['errors'])){
            if (sizeof($occurredErrors)){
                $logEntry->addErrors($occurredErrors);
                $emptyDefaultLog = false;
            }
        }

        if (empty($this->separate['exceptions'])){
            if (sizeof($uncaughtedExceptions)){
                $logEntry->addExceptions($uncaughtedExceptions);
                $emptyDefaultLog = false;
            }
        }

        if (!$emptyDefaultLog){
            $defaultFileLog->append($logEntry->render());
        }

    }


    /**
     * You can provide callback to authorize error output.
     * Your callback should return TRUE if user was authorized,
     * otherwise return FALSE to not show error output.
     * @param callable $callback
     * @param bool $authEvenInProd If true, and callback returns true, errors output will be showed in production mode
     */
    public function authorizeOutput(callable $callback, bool $authEvenInProd = true)
    {
        $this->authorizeOutputCallback = $callback;
        $this->authorizeInProduction = $authEvenInProd;
    }

}

?>