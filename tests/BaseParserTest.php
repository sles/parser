<?php

declare(strict_types=1);

namespace Tests;

require 'autoload.php';

use Classes\BaseParser;
use PHPUnit\Framework\TestCase;

class BaseParserTest extends TestCase
{
    /**
     * Parser data provider
     *
     * @return array
     */
    public function parserDataProvider(): array
    {
        return [
            'parse csv file' => ['DataSource/products_comma_separated.csv', 'unique_combinations.csv'],
            'parse xml file' => ['DataSource/products.xml', 'unique_combinations.csv'],
            'parse json file' => ['DataSource/products.json', 'unique_combinations.csv']
        ];
    }

    /**
     * Tests successful parsing of the file and creation of the file with unique data combinations
     *
     * @covers       BaseParser::parse()
     * @dataProvider parserDataProvider
     *
     * @uses         BaseParser::ALLOWED_FILE_EXTENSIONS
     * @uses         BaseParser::$fileName
     * @uses         BaseParser::$outputFileName
     */
    public function testParse(string $fileName, ?string $outputFileName): void
    {
        putenv("ENV=TEST");

        /** Arrange */
        $outputObjectStructure = array_keys(get_class_vars(get_class(new \DTO\ProductDTO())));

        /** Act */
        $parser = \Classes\ParserFactory::makeParser($fileName, $outputFileName);
        $data = $parser->parse();

        /** Asserts */
        $duplicates = $parser->getDuplicates();

        $this->assertNotEmpty($data);
        $this->assertNotEmpty($duplicates);

        $this->assertInstanceOf('stdClass', reset($data));

        /**check if the first, middle and the last elements of the data array are of the correct structure
           and contain required fields **/
        $this->checkDataValues(reset($data), $outputObjectStructure);
        $this->checkDataValues($data[count($data) - 1], $outputObjectStructure);
        $this->checkDataValues($data[(count($data) / 2) - 1], $outputObjectStructure);

        $this->assertFileExists($outputFileName);
    }

    /**
     * Checks if data objects is of correct structure and contains required fields
     *
     * @param  \stdClass  $data
     * @param $outputObjectStructure
     */
    private function checkDataValues(\stdClass $data, $outputObjectStructure): void
    {
        $this->assertSame(array_keys((array)$data), $outputObjectStructure);
        $this->assertNotNull($data->make);
        $this->assertNotNull($data->model);
    }

    /**
     * Negative test caused by providing an invalid file extension
     *
     * @covers ::BaseParser
     */
    public function testParserWithInvalidParameters(): void
    {
        /** Arrange */
        $fileName = 'DataSource/products_comma_separated.csvv';

        /** Asserts */
        $this->expectException(\Exception::class);

        /** Act */
        new BaseParser($fileName);
    }
}

