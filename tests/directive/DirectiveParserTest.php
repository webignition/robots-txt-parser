<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../../lib/bootstrap.php');

class DirectiveParserTest extends PHPUnit_Framework_TestCase {

    public function testParse() {
        $parser = new \webignition\RobotsTxt\Directive\Parser();
        $parser->setSource('allow: /allowed-path');
        
        $this->assertEquals('allow', (string)$parser->getDirective()->getField());
        $this->assertEquals('/allowed-path', (string)$parser->getDirective()->getValue());
    }   
}