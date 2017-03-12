<?php

namespace Rev\ExPage\View;

class CliView extends View
{
    /**
     * Errors render parameters
     * @var array
     */
    private $errorsRenderParameters = [
        'show_file' => true,
        'show_line' => true,
        'show_scope' => true
    ];

    /**
     * Exception renderer parameters
     * @var array
     */
    private $exceptionRenderParameters = [
        'show_file' => true,
        'show_line' => true,
        'show_trace' => true
    ];

    public function __construct(array $parameters = array())
    {
        parent::__construct();

        $this->parseParameters($parameters);
    }

    /**
     * Parses renderer parameters given from user
     * @param array $parameters
     */
    private function parseParameters(array $parameters)
    {
        if (array_key_exists('error', $parameters)){
            $this->errorsRenderParameters['show_file'] = $parameters['error']['show_file'] ?? true;
            $this->errorsRenderParameters['show_line'] = $parameters['error']['show_line'] ?? true;
            $this->errorsRenderParameters['show_scope'] = $parameters['error']['show_scope'] ?? true;
        }

        if (array_key_exists('exception', $parameters)){
            $this->exceptionRenderParameters['show_file'] = $parameters['exception']['show_file'] ?? true;
            $this->exceptionRenderParameters['show_line'] = $parameters['exception']['show_line'] ?? true;
            $this->exceptionRenderParameters['show_trace'] = $parameters['exception']['show_trace'] ?? true;
        }
    }

    /**
     * Writes header
     */
    private function writeHeader()
    {
        echo "\e[1;41m Your application run into problem \e[0m\n";
    }

    public function render()
    {
        $this->writeHeader();

        $this->renderErrors();

        $this->renderExceptions();
    }

    /**
     * Renders errors
     */
    private function renderErrors()
    {
        if (!sizeof($this->occurredErrors)){
            return;
        }

        foreach($this->occurredErrors as $occurredError){
            echo sprintf("\e[1;31m Level [%s]\e[0m", $occurredError->getLevelString());
            echo sprintf(" %s ", $occurredError->getMessage());

            $filePath = $occurredError->getFileName();

            if (strlen($filePath) > 20 && strlen($filePath) * 2 > 40){
                $filePath = "..." . substr($filePath, 30, strlen($filePath) - 1);
            }

            if ($this->errorsRenderParameters['show_file']){
                echo sprintf(" in file: %s ", $filePath);
            }

            if ($this->errorsRenderParameters['show_line']){
                echo sprintf(" at line: %d ", $occurredError->getLineNumber());
            }

            echo sprintf(" \n");

            if ($this->errorsRenderParameters['show_scope']){
                $scopeAround = $occurredError->getScopeAround();

                foreach ($scopeAround as $variableName => $variableValue){
                    if (is_array($variableValue)){
                        if (sizeof($variableValue) > 7){
                            echo sprintf("\t \e[1;30m %s \e[0m: array(...) \n", $variableName);
                            continue;
                        }
                    }

                    if (is_object($variableValue)){
                        $varExported = var_export($variableValue, true);
                        $varExported = str_replace(" ", "", $varExported);

                        echo sprintf("\t \e[1;30m %s \e[0m: %s) \n", $variableName, str_replace("\n", "", $varExported));
                        continue;
                    }

                    $varExported = var_export($variableValue, true);

                    echo sprintf("\t \e[1;30m %s \e[0m: %s\n", $variableName, str_replace("\n", "", $varExported));
                }
            }

            echo sprintf("\n");

        }
    }

    /**
     * Renders exceptions
     */
    private function renderExceptions()
    {
        if (!sizeof($this->uncaughtedExceptions)){
            return;
        }

        foreach ($this->uncaughtedExceptions as $exception){
            echo sprintf("\e[1;45m %s: \e[0m '%s' ", get_class($exception), $exception->getMessage());
            echo sprintf(" with code: %d ", $exception->getCode());

            $filePath = $exception->getFile();

            if (strlen($filePath) > 20 && strlen($filePath) * 2 > 40){
                $filePath = "..." . substr($filePath, 30, strlen($filePath) - 1);
            }

            if ($this->exceptionRenderParameters['show_file']){
                echo sprintf(" in file: '%s' ", $filePath);
            }

            if ($this->exceptionRenderParameters['show_line']){
                echo sprintf("at line: '%d' \n", $exception->getLine());
            }


            if ($this->exceptionRenderParameters['show_trace']){
                echo sprintf("Trace: \n");

                $traceCounter = 0;

                foreach($exception->getTrace() as $traceEntry){
                    $filePath = $traceEntry['file'] ?? "";

                    if (strlen($filePath) > 20 && strlen($filePath) * 2 > 40){
                        $filePath = "..." . substr($filePath, 30, strlen($filePath) - 1);
                    }

                    echo sprintf("\t#%d in file: %s at line: %d ", $traceCounter, $filePath, $traceEntry['line'] ?? 0);
                    echo sprintf("\n\t   at class: '%s' in function: '%s'", $traceEntry['class'], $traceEntry['function']);

                    $exportedArgs = var_export($traceEntry['args'], true);
                    $exportedArgs = str_replace("\n", "", $exportedArgs);
                    $exportedArgs = str_replace(" ", "", $exportedArgs);

                    echo sprintf("\n\t   args: %s", $exportedArgs );

                    echo sprintf("\n");
                    $traceCounter++;

                }
            }

        }
    }
}

?>