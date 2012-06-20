<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

class TestTest extends PHPUnit_Framework_TestCase {

    public function testThingy() {
        
        $dataPath = __DIR__ . '/data/stackoverflow.com.txt';
                
        $parser = new \webignition\RobotsTxt\Parser();
        $parser->setSource(file_get_contents($dataPath));
        $parser->setSourceUrl('http://stackoverflow.com/');
        
        

        //$data = file_get_contents();
        
        $this->assertTrue(true);        
        
    }
    
}