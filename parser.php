<?php

declare(strict_types=1);

require 'autoload.php';

/**
 * Array of the arguments provided to a script
 * - file: name of the file to parse. Has to be located in the DataSource directory
 * - unique-combinations: name of the file where a parser has to store all the unique data combinations
 *   along with the count of their duplicates
 *
 * @var array $arguments
 */
$arguments = getopt('', ['file:', 'unique-combinations::']);

if (empty($arguments)) {
    throw new Exception('Argument "file" is mandatory');
}

/**
 * An instance of a class which implements Parser interface. Appropriate Parser is created based on a file extension
 *
 * @var \Classes\Parser $parser
 */
$parser = \Classes\ParserFactory::makeParser(
    $arguments['file'],
    $arguments['unique-combinations'] ?? null
);

$data = $parser->parse();