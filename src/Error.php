<?php

namespace Rev\ExPage\Error;

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
}

?>