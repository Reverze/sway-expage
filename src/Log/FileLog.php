<?php

namespace Rev\ExPage\Log;

class FileLog
{
    /**
     * The file log path
     * @var string
     */
    private $filePath = null;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Appends log content to file
     * @param string $content
     * @return int
     */
    public function append(string $content)
    {
        return file_put_contents($this->filePath, $content . PHP_EOL, FILE_APPEND | LOCK_EX );
    }
}

?>