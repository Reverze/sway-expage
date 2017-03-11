<?php

namespace Rev\ExPage\View;

class BrowserView extends View
{
    /**
     * Directory where built-in views are stored
     * @var null
     */
    private $viewsDirectoryPath = null;

    /**
     * Template file name
     * @var string
     */
    private $templateFile = 'default.phtml';

    /**
     * Variables passed to templates
     * @var array
     */
    protected $templateVars = array();

    public function __construct()
    {

    }

    /**
     * Sets views directory path
     * @param string $viewsDirectoryPath
     * @throws \Exception
     */
    public function setViewsDirectoryPath(string $viewsDirectoryPath)
    {
        if (!is_dir($viewsDirectoryPath)){
            throw new \Exception(sprintf("Directory '%s' was not found on path: '%s'", $viewsDirectoryPath));
        }

        $this->viewsDirectoryPath = $viewsDirectoryPath;
    }

    /**
     * Sets template file path
     * @param string $templateFile
     * @throws \Exception
     */
    public function setTemplateFile(string $templateFile)
    {
        if ($templateFile === 'default'){
            $this->templateFile = $this->viewsDirectoryPath . '/default.phtml';
        }
        else{
            if (!file_exists($templateFile)){
                throw new \Exception("Template file was not found on path: '%s'", $templateFile);
            }

            $this->templateFile = $templateFile;
        }
    }

    /**
     * Assigns variable to template
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $this->templateVars[$name] = $value;
    }

    /**
     * Gets template's variable
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if (isset($this->templateVars[$name])){
            return $this->templateVars[$name];
        }
        else{
            return null;
        }
    }


    public function render()
    {
        $this->errors = $this->occurredErrors;
        $this->exceptions = $this->uncaughtedExceptions;

        include_once($this->templateFile);
    }
}

?>