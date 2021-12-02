## Project Overview

The main features of the project are:
1. parse a file
2. create objects of a given structure from each row of the file
3. print objects in console
4. create a file with unique data combinations along with the count of their duplicates if output file name is provided

Key elements of the project:
- **parser.php** - entry point to the project
- **DataSource** - a directory with the source files, which can be used for parsing
- **Classes** - a directory that contains parser classes and parser interface
- **DTO** - a directory that contains a class which converts the data into the desired format
- **tests** - a directory that contains unit tests.

### Supported file extensions to parse:
- csv
- tsv
- xml
- json

### Supported output file extensions:
- csv
- tsv

***

### Starting the script
To run the script, run the following command:  
``php parser.php --file ./DataSource/products_comma_separated.csv --unique-combinations=combinations_count.csv``.  
In the command above:
- --file is a required parameter, which instruct a script which file to parse
- --unique-combinations is an optional parameter. If it is passed a script will generate a **csv** or **tsv** file with the count of duplicates for each unique data combination
***

### Starting tests
To run the tests you need to install the dependencies `composer install` and run this command:
``vendor/bin/phpunit --verbose tests/BaseParserTest.php``.
***

To import data from files with other extensions, you need to create a new class extending the **BaseParser** class and override the **"parse()"** method.