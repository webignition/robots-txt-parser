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
        
        $this->assertFalse((string)$file == '');
        
        $records = $file->getRecords();        
        $this->assertEquals(5, count($records));
        
        /* @var $record1 \webignition\RobotsTxt\Record\Record */
        $record1 = $records[0];
        
        $this->assertEquals(array('*'), $record1->userAgentDirectiveList()->getValues());
        $this->assertEquals(48, count($record1->directiveList()->get()));
        $this->assertTrue($record1->directiveList()->contains('Disallow: /users/login/global/request/'));
        
        $this->assertEquals('disallow:/*/ivc/*'."\n".'disallow:/users/flair/', (string)$file->getDirectivesFor('googlebot-image'));
        
        $this->assertEquals('sitemap:/sitemap.xml', (string)$file->directiveList()->filter(array('field' => 'sitemap')));
    }
    
}