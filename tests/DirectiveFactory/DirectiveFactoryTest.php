<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../../lib/bootstrap.php');

class DirectiveFactoryTest extends PHPUnit_Framework_TestCase {

    public function testFactory() {
        $factory = new \webignition\RobotsTxt\Directive\Factory();
        
        $this->assertTrue($factory->getDirective('user-agent:googlebot')->is('user-agent'));
        $this->assertTrue($factory->getDirective('user-agent:googlebot') instanceof \webignition\RobotsTxt\UserAgentDirective\UserAgentDirective);
        
        $this->assertTrue($factory->getDirective('allow:/allowed-path')->is('allow'));
        $this->assertTrue($factory->getDirective('allow:/allowed-path') instanceof \webignition\RobotsTxt\Directive\Directive);        
        
    }
    
}