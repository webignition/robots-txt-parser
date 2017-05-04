<?php

namespace webignition\Tests\RobotsTxt;

use PHPUnit_Framework_TestCase;
use webignition\RobotsTxt\Directive\Directive;
use webignition\RobotsTxt\Directive\Factory as DirectiveFactory;
use webignition\RobotsTxt\UserAgentDirective\UserAgentDirective;

class DirectiveFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DirectiveFactory
     */
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new DirectiveFactory();
    }

    public function testFactory()
    {
        $this->assertTrue($this->factory->getDirective('user-agent:googlebot')->is('user-agent'));
        $this->assertInstanceOf(
            UserAgentDirective::class,
            $this->factory->getDirective('user-agent:googlebot')
        );

        $this->assertTrue($this->factory->getDirective('allow:/allowed-path')->is('allow'));
        $this->assertInstanceOf(
            Directive::class,
            $this->factory->getDirective('allow:/allowed-path')
        );
    }
}
