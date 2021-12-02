<?php

namespace Classes;

class ParserFactory
{
    /**
     * Create and returns an instance of appropriate Parser based on the file extension
     *
     * @param  string  $filename
     * @param  string|null  $outputFileName
     * @return Parser
     * @throws \Exception
     */
    public static function makeParser(string $filename, ?string $outputFileName): Parser
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'csv':
            case 'tsv':
                return new BaseParser($filename, $outputFileName);
            case 'json':
                return new JSONParser($filename, $outputFileName);
            case 'xml':
                return new XMLParser($filename, $outputFileName);
            default:
                throw new \Exception('Sorry, but the system doesn`t support this extension yet.');
        }
    }
}