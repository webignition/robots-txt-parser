<?php

class ParserTest extends PHPUnit_Framework_TestCase {    

    public function testParsingStackoverflowDotCom() {        
        $dataPath = __DIR__ . '/../data/stackoverflow.com.txt';
                
        $parser = new \webignition\RobotsTxt\File\Parser();
        $parser->setSource(file_get_contents($dataPath));
        
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
    
    public function testParsingWithSitemapAsLastLineInSingleRecord() {        
        $dataPath = __DIR__ . '/../data/sitemapAsLastLineInSingleRecord.txt';
                
        $parser = new \webignition\RobotsTxt\File\Parser();
        $parser->setSource(file_get_contents($dataPath));
        
        $file = $parser->getFile();
        
        $this->assertEquals(1, count($file->getRecords()));
        $this->assertEquals(1, count($file->directiveList()->getValues()));
        
        $this->assertEquals('sitemap:http://example.com/sitemap.xml', (string)$file->directiveList()->filter(array('field' => 'sitemap'))); 
    }    
    
    public function testParsingWithSitemapWithinSingleRecord() {
        $dataPath = __DIR__ . '/../data/sitemapWithinSingleRecord.txt';
                
        $parser = new \webignition\RobotsTxt\File\Parser();
        $parser->setSource(file_get_contents($dataPath));
        
        $file = $parser->getFile();
        
        $this->assertEquals(1, count($file->getRecords()));
        $this->assertEquals(2, count($file->getDirectivesFor('*')->getValues()));
        $this->assertEquals(1, count($file->directiveList()->getValues()));
        
        $this->assertEquals('sitemap:http://example.com/sitemap.xml', (string)$file->directiveList()->filter(array('field' => 'sitemap')));         
    }
    
    public function testParsingInvalidSitemap() {
        $dataPath = __DIR__ . '/../data/newscientist.com.txt';
                
        $parser = new \webignition\RobotsTxt\File\Parser();
        $parser->setSource(file_get_contents($dataPath));
        
        $file = $parser->getFile();
        
        $this->assertEquals(2, count($file->getRecords()));
        $this->assertEquals(1, count($file->getDirectivesFor('*')->getValues()));
        $this->assertEquals(6, count($file->getDirectivesFor('zibber')->getValues()));
        $this->assertEquals(4, count($file->directiveList()->getValues()));    
        
        $this->assertEquals(1, count($file->directiveList()->filter(array('field' => 'sitemap'))->getValues()));        
    }
    
    public function testParsingWithStartingBOM() {
        $dataPath = __DIR__ . '/../data/withBom.txt';
                
        $parser = new \webignition\RobotsTxt\File\Parser();
        $parser->setSource(file_get_contents($dataPath));
        
        $file = $parser->getFile();
   
        $this->assertEquals(1, count($file->getRecords()));
        $this->assertEquals(2, count($file->getDirectivesFor('*')->getValues()));
        $this->assertEquals(array(
            'disallow:/',
            'allow:/humans.txt'
        ), $file->getDirectivesFor('*')->getValues());       
    }    
    
}