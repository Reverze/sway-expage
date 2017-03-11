<?php

namespace Rev\ExPage\View;

class CliView extends View
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Writes header
     */
    private function writeHeader()
    {
        echo "\e[1;41m Application run into problem \e[0m\n";
    }

    public function render()
    {
        $this->writeHeader();


    }
}

?>