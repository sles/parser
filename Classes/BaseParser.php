<?php

declare(strict_types=1);

namespace Classes;

use DTO\ProductDTO;
use Exception;

/**
 * The class purpose is to:
 * 1. parse a file
 * 2. create objects of a given structure from each row of the file
 * 3. print objects in console
 * 4. create a file with unique data combinations along with the count of their duplicates
 *    if output file name is provided
 */
class BaseParser implements Parser
{
    /**
     * The name of the file to parse.
     *
     * @var string
     */
    protected string $fileName;

    /**
     * The name of the file in which the counts for each unique combination will be written.
     *
     * @var string|null
     */
    protected ?string $outputFileName;

    /**
     * Contains the parsed data.
     *
     * @var object[]
     */
    protected array $data = [];

    /**
     * Contains duplicates of data.
     *
     * @var array
     */
    protected array $duplicates = [];

    public const CSV = 'csv';
    public const TSV = 'tsv';
    public const ALLOWED_FILE_EXTENSIONS = [self::CSV, self::TSV];
    public const ALLOWED_OUTPUT_FILE_EXTENSIONS = [self::CSV, self::TSV];
    public const FILE_SEPARATORS = [
        self::CSV => ',',
        self::TSV => "\t"
    ];

    /**
     * @param  string  $fileName
     * @param  string|null  $outputFileName
     * @throws Exception
     */
    public function __construct(string $fileName, ?string $outputFileName = null)
    {
        $this->setFileName($fileName);
        $this->setOutputFileName($outputFileName);
    }

    /**
     * 1. parse a file
     * 2. create objects of a given structure from each row of the file
     * 3. print objects in console
     * 4. create a file with unique data combinations along with the count of their duplicates
     *    if output file name is provided
     *
     * @return object[]
     *
     * @throws Exception
     */
    public function parse(): array
    {
        $file = fopen($this->fileName, 'rb');

        if (!$file) {
            throw new Exception("File '$this->fileName' does not exists!");
        }

        $fileSeparator = $this->getFileSeparator($this->fileName);
        $headings = fgetcsv($file, 0, $fileSeparator);

        while (($data = fgetcsv($file, 0, $fileSeparator)) !== false) {
            $product = ProductDTO::transform(array_combine($headings, $data));
            $this->checkForDuplicate($product->toArray());
            $productObject = $product->toObject();
            if (getenv("ENV") !== "TEST") {
                var_dump($productObject);
            }

            $this->data[] = $productObject;
        }

        fclose($file);

        if ($this->outputFileName) {
            $this->createUniqueCombinationsFile();
        }

        return $this->data;
    }

    /**
     * Creates a file with unique data combinations along with the count of their duplicates
     *
     * @throws Exception
     */
    public function createUniqueCombinationsFile(): void
    {
        $file = fopen($this->outputFileName, 'wb');

        if (!$file) {
            throw new Exception("Can't create a '$this->outputFileName' file");
        }

        $fileSeparator = $this->getFileSeparator($this->outputFileName);

        /**
         * Get the first array element and take its keys to use them as column names,
         * which will be written in the first line of the file.
         */
        $headings = array_keys(reset($this->duplicates));
        $rows = array_map(static fn($item) => array_values($item), $this->duplicates);

        fputcsv($file, $headings, $fileSeparator);

        foreach ($rows as $row) {
            fputcsv($file, $row, $fileSeparator);
        }

        fclose($file);
    }

    /**
     * Returns parsed data from a file.
     *
     * @return object[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns an array of data duplicates
     *
     * @return array
     */
    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    /**
     * Validates and stores provide name of the file to parse
     *
     * @param  string  $fileName
     * @throws Exception
     */
    public function setFileName(string $fileName): void
    {
        if (!file_exists($fileName)) {
            throw new Exception("File '$fileName' does not exists!");
        }

        $inputFileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!in_array($inputFileExtension, $this::ALLOWED_FILE_EXTENSIONS, true)) {
            throw new Exception("The '$inputFileExtension' extension is not allowed.");
        }

        $this->fileName = $fileName;
    }

    /**
     * Validates and stores provide name of the output file
     *
     * @param  string|null  $outputFileName
     * @throws Exception
     */
    public function setOutputFileName(?string $outputFileName): void
    {
        if ($outputFileName) {
            $outputFileExtension = pathinfo($outputFileName, PATHINFO_EXTENSION);

            if (!in_array($outputFileExtension, $this::ALLOWED_OUTPUT_FILE_EXTENSIONS, true)) {
                throw new Exception("The '$outputFileExtension' extension for output file not allowed.");
            }
        }

        $this->outputFileName = $outputFileName;
    }

    /**
     * Returns the file separator based on the file extension.
     *
     * @param  string  $fileName
     * @return string
     *
     * @throws Exception
     */
    protected function getFileSeparator(string $fileName): string
    {
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileSeparator = $this::FILE_SEPARATORS[$fileExtension] ?? null;

        if (!$fileSeparator) {
            throw new Exception("The '$fileExtension' extension is not allowed.");
        }

        return $fileSeparator;
    }

    /**
     * Checks if the data is a duplicate.
     * In order to make the comparison algorithm fast a sha256 hashing is applied to the data to create a unique key.
     * If the hashed key already exists in the duplicates array, it increments the count field.
     *
     * @param  array  $data
     * @return void
     */
    protected function checkForDuplicate(array $data): void
    {
        $hash = hash('sha256', json_encode($data));

        if (array_key_exists($hash, $this->duplicates)) {
            $this->duplicates[$hash]['count'] += 1;
        } else {
            $this->duplicates[$hash] = array_merge($data, ['count' => 1]);
        }
    }
}