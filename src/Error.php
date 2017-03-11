<?php

namespace Rev\ExPage;

/**
 * Class Error
 * Represents the Error
 * @package Rev\ExPage\Error
 */
class Error
{
    /**
     * Contains the level of error raised
     * @var int
     */
    private $level = 0;

    /**
     * Contains the error message
     * @var string
     */
    private $message = "";

    /**
     * Contains the filename that the error was raised in
     * @var string
     */
    private $fileName = "";

    /**
     * Contains the line number the error was raised at
     * @var int
     */
    private $raisedInLine = 0;

    /**
     * Contains an array of every variable that existed in the scope when
     * the error was triggered in.
     * @var array
     */
    private $scopeAround = array();

    public function __construct()
    {

    }

    /**
     * Sets the error level.
     * @param int $errorLevel
     */
    public function setLevel(int $errorLevel)
    {
        $this->level = $errorLevel;
    }

    /**
     * Sets the error message.
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * Sets the filename that the error was raised in
     * @param string $fileName
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Sets the line number that the error was raised at
     * @param int $lineNumber
     */
    public function setLineNumber(int $lineNumber)
    {
        $this->raisedInLine = $lineNumber;
    }

    /**
     * Sets an array of every variable that existed in the scope when
     * the error was triggered in.
     * @param array $scope
     */
    public function setScopeAround(array $scope = array())
    {
        $this->scopeAround = $scope;
    }

    /**
     * Gets the error level.
     * @return int
     */
    public function getLevel() : int
    {
        return $this->level;
    }

    /**
     * Gets the error message.
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * Gets the filename that the error was raised in.
     * @return string
     */
    public function getFileName() : string
    {
        return $this->fileName;
    }

    /**
     * Gets the line number that the error was raised at.
     * @return string
     */
    public function getLineNumber() : string
    {
        return $this->raisedInLine;
    }

    /**
     * Gets the array of every variable that existed in the scope when
     * the error was triggered in.
     * @return array
     */
    public function getScopeAround() : array
    {
        return $this->scopeAround;
    }

    /**
     * Gets the error level as string
     * @return string
     */
    public function getLevelString() : string
    {
        switch($this->level){
            case 1:
                return 'E_ERROR';
            case 2:
                return 'E_WARNING';
            case 4:
                return 'E_PARSE';
            case 8:
                return 'E_NOTICE';
            case 16:
                return 'E_CORE_ERROR';
            case 32:
                return 'E_CORE_WARNING';
            case 64:
                return 'E_COMPILE_ERROR';
            case 128:
                return 'E_COMPILE_WARNING';
            case 256:
                return 'E_USER_ERROR';
            case 512:
                return 'E_USER_WARNING';
            case 1024:
                return 'E_USER_NOTICE';
            case 2048:
                return 'E_STRICT';
            case 4096:
                return 'E_RECOVERABLE_ERROR';
            case 8192:
                return 'E_DEPRECATED';
            case 16384:
                return 'E_USER_DEPRECATED';
            case 32767:
                return 'E_ALL';
            default:
                return 'E_ALL';
        }
    }

}

?>