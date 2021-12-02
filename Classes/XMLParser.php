<?php

declare(strict_types=1);

namespace Classes;

use DTO\ProductDTO;

class XMLParser extends BaseParser
{
    private const XML = 'xml';
    public const ALLOWED_FILE_EXTENSIONS = [self::XML];

    /**
     * 1. parse a file passed as a parameter
     * 2. create objects of a given structure for each row of the file
     * 3. print objects in console
     * 4. generate a file with unique data combinations along with the count of their duplicates
     *    if output file name is provided
     *
     * @return object[]
     * @throws \Exception
     */
    public function parse(): array
    {
        $xml = simplexml_load_string(file_get_contents($this->fileName));

        foreach ($xml as $XMLElement) {
            $product = ProductDTO::transform((array)$XMLElement);
            $this->checkForDuplicate($product->toArray());
            $productObject = $product->toObject();
            if (getenv("ENV") !== "TEST") {
                var_dump($productObject);
            }

            $this->data[] = $productObject;
        }

        if ($this->outputFileName) {
            $this->createUniqueCombinationsFile();
        }

        return $this->data;
    }
}