<?php

namespace Rev\ExPage\Log;

use Rev\ExPage\Error;

class Entry
{
    /**
     * Log entry content
     * @var string
     */
    private $entryContent = "";

    /**
     * Log entry dateformat
     * @var string
     */
    private $dateFormat = 'd.m.Y H:i:s';

    public function __construct(string $dateFormat = "d.m.Y H:i:s")
    {
        $this->dateFormat = $dateFormat;

        /**
         * Adds log entry header
         */
        $this->addHeader();
    }

    /**
     * Adds log entry header
     */
    private function addHeader()
    {
        $this->entryContent .= sprintf("\n################# Log entry DATE: %s  TIMESTAMP: %d #####################",
            date_format($this->dateFormat, time()), time());
    }

    /**
     * Adds log entry footer
     */
    private function addFooter()
    {
        $this->entryContent .= sprintf("\n#######################################################################################");
    }

    /**
     * Adds error log
     * @param Error $error
     */
    public function addError(Error $error)
    {
        $errorLog = '--------------------------------------------------------';
        $errorLog .= sprintf("Level: %s Message: %s \n", $error->getLevelString(), $error->getMessage());
        $errorLog .= sprintf("\t at file: %s  on line: %d", $error->getFileName(), $error->getLineNumber());
        $errorLog .= '--------------------------------------------------------';
        $this->entryContent .= sprintf("\n %s", $errorLog);
    }

    /**
     * Adds exception log
     * @param \Exception $exception
     */
    public function addException(\Exception $exception)
    {
        $exceptionLog = '-------------------------------------------------------';
        $exceptionLog .= sprintf("Uncaughted exception [%s] Message: %s", get_class($exception), $exception->getMessage());
        $exceptionLog .= sprintf("\t with code: '%d' at file: %s  on line: %d", $exception->getCode(), $exception->getFile(), $exception->getLine());
        $exceptionLog .= sprintf("\t Trace: %s", $exception->getTraceAsString());
        $exceptionLog .= '-------------------------------------------------------';

        $this->entryContent .= sprintf("\n\n %s", $exceptionLog);
    }

    /**
     * Adds errors to log entry
     * @param \Rev\ExPage\Error[] $errors
     */
    public function addErrors(array $errors)
    {
        foreach ($errors as $error){
            $this->addError($error);
        }
    }

    /**
     * Adds exceptions to log entry
     * @param \Exception[] $exceptions
     */
    public function addExceptions(array $exceptions)
    {
        foreach ($exceptions as $exception){
            $this->addException($exception);
        }
    }


    /**
     * Renders entry log content
     * @return string
     */
    public function render() : string
    {
        $this->addFooter();
        return $this->entryContent;
    }




}

?>