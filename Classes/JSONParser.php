<?php

declare(strict_types=1);

namespace Classes;

use DTO\ProductDTO;
use Exception;

class JSONParser extends BaseParser
{
    private const JSON = 'json';
    public const ALLOWED_FILE_EXTENSIONS = [self::JSON];

    /**
     * 1. parse a file passed as a parameter
     * 2. create objects of a given structure for each row of the file
     * 3. print objects in console
     * 4. generate a file with unique data combinations along with the count of their duplicates
     *    if output file name is provided
     *
     * @return object[]
     * @throws Exception
     */
    public function parse(): array
    {
        $json = file_get_contents($this->fileName);

        if (!$json) {
            throw new Exception("File '$this->fileName' does not exists!");
        }

        $data = json_decode($json, true);

        foreach ($data as $datum) {
            $product = ProductDTO::transform($datum);
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