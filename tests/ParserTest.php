<?php

namespace webignition\Tests\RobotsTxt;

use PHPUnit_Framework_TestCase;
use webignition\RobotsTxt\File\Parser;

class ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var string
     */
    private $dataSourceBasePath;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new Parser();
        $this->dataSourceBasePath = __DIR__ . '/data';
    }

    public function testParsingStackoverflowDotCom()
    {
        $this->setParserSourceFromDataFile('stackoverflow.com.txt');
        $file = $this->parser->getFile();

        $this->assertNotEmpty((string)$file);

        $records = $file->getRecords();

        $this->assertCount(5, $records);

        /* @var $record1 \webignition\RobotsTxt\Record\Record */
        $record1 = $records[0];

        $this->assertEquals(array('*'), $record1->userAgentDirectiveList()->getValues());
        $this->assertCount(48, $record1->directiveList()->get());
        $this->assertTrue($record1->directiveList()->contains('Disallow: /users/login/global/request/'));

        $this->assertEquals(
            'disallow:/*/ivc/*'."\n".'disallow:/users/flair/',
            (string)$file->getDirectivesFor('googlebot-image')
        );
        $this->assertEquals(
            'sitemap:/sitemap.xml',
            (string)$file->directiveList()->filter(array('field' => 'sitemap'))
        );
    }

    public function testParsingWithSitemapAsLastLineInSingleRecord()
    {
        $this->setParserSourceFromDataFile('sitemapAsLastLineInSingleRecord.txt');

        $file = $this->parser->getFile();

        $this->assertCount(1, $file->getRecords());
        $this->assertCount(1, $file->directiveList()->getValues());

        $this->assertEquals(
            'sitemap:http://example.com/sitemap.xml',
            (string)$file->directiveList()->filter(array('field' => 'sitemap'))
        );
    }

    public function testParsingWithSitemapWithinSingleRecord()
    {
        $this->setParserSourceFromDataFile('sitemapWithinSingleRecord.txt');

        $file = $this->parser->getFile();

        $this->assertCount(1, $file->getRecords());
        $this->assertCount(2, $file->getDirectivesFor('*')->getValues());
        $this->assertCount(1, $file->directiveList()->getValues());

        $this->assertEquals(
            'sitemap:http://example.com/sitemap.xml',
            (string)$file->directiveList()->filter(array('field' => 'sitemap'))
        );
    }

    public function testParsingInvalidSitemap()
    {
        $this->setParserSourceFromDataFile('newscientist.com.txt');

        $file = $this->parser->getFile();

        $this->assertCount(2, $file->getRecords());
        $this->assertCount(1, $file->getDirectivesFor('*')->getValues());
        $this->assertCount(6, $file->getDirectivesFor('zibber')->getValues());
        $this->assertCount(4, $file->directiveList()->getValues());
        $this->assertCount(1, $file->directiveList()->filter(array('field' => 'sitemap'))->getValues());
    }

    public function testParsingWithStartingBOM()
    {
        $this->setParserSourceFromDataFile('withBom.txt');

        $file = $this->parser->getFile();

        $this->assertCount(1, $file->getRecords());
        $this->assertCount(2, $file->getDirectivesFor('*')->getValues());

        $this->assertEquals(array(
            'disallow:/',
            'allow:/humans.txt'
        ), $file->getDirectivesFor('*')->getValues());
    }

    /**
     * @param string $relativePath
     */
    private function setParserSourceFromDataFile($relativePath)
    {
        $this->parser->setSource(file_get_contents($this->dataSourceBasePath . '/' . $relativePath));
    }
}
