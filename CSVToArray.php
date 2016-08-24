<?php
namespace Component;

class CSVImporter
{
    private $file;

    public function __construct($path = null)
    {
        $this->setPath($path);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function setPath($path)
    {
        $this->file = $this->open($path);
    }

    public function convert()
    {
        return $this->getArray();
    }

    private function getArray()
    {
        $array = array();

        if (!$this->isOpen()) {
            return $array;
        }

        $indexes = $this->getIndexes();

        while (($line = $this->fetch()) !== false) {
            $array[] = (object) array_combine($indexes, $line);
        }

        $this->close();

        return $array;
    }

    private function getIndexes()
    {
        if ($indexes = $this->fetch($this->file)) {
            $indexes = array_map(function ($value) {
                return mb_strtolower($value, 'UTF-8');
            }, $indexes);
        }

        return $indexes;
    }

    private function fetch()
    {
        return fgetcsv($this->file, 0, '|');
    }

    private function isOpen()
    {
        return !(is_null($this->file) || false == $this->file);
    }

    private function open($path)
    {
        if (gettype($path) != 'string') {
            return false;
        }

        if (!is_readable($path)) {
            return false;
        }

        $this->close();

        return fopen($path, 'r');
    }

    private function close()
    {
        if ($this->file) {
            fclose($this->file);
            $this->file = false;
        }
    }
}
