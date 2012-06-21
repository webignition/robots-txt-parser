<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../../lib/bootstrap.php');

class ParserTest extends PHPUnit_Framework_TestCase {

    public function testParsing() {
        
        $dataPath = __DIR__ . '/../data/stackoverflow.com.txt';
                
        $parser = new \webignition\RobotsTxt\File\Parser();
        $parser->setSource(file_get_contents($dataPath));
        $parser->setSourceUrl('http://stackoverflow.com/');
        
        $file = $parser->getFile();
        
        // So far we're only really sure that the file should not cast to a blank
        // string
        $this->assertFalse((string)$file == '');
    }
    
}