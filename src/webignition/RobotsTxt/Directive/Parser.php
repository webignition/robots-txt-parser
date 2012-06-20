<?php
namespace webignition\RobotsTxt\Directive;

/**
 * Parser for a robots.txt key:value directive line
 *  
 */
class Parser {
    
    const KEY_VALUE_SEPARATOR = ':';
    
    /**
     * Unmodified source directive
     * 
     * @var string
     */
    private $source = null;
    
    
    /**
     *
     * @var \webignition\RobotsTxt\Directive\Directive
     */
    private $directive = null;
    
    public function setSource($source) {
        $this->reset();
        $this->source = $source;        
    }
    
    /**
     * Set object back to default state
     *  
     */
    public function reset() {
        $this->source = '';
        $this->directive = null;
    }
    
    
    /**
     *
     * @return \webignition\RobotsTxt\Directive\Directive 
     */
    public function getDirective() {
        if (is_null($this->directive)) {
            $this->parse();
        }
        
        return $this->directive;
    }
    
    
    private function parse() {
        $commentlessValue = new \webignition\DisallowedCharacterTerminatedString\DisallowedCharacterTerminatedString();
        $commentlessValue->addDisallowedCharacterCode(ord('#'));
        $commentlessValue->set($this->source);

        $keyValue = explode(self::KEY_VALUE_SEPARATOR,(string)$commentlessValue);
        
        $this->directive = new \webignition\RobotsTxt\Directive\Directive();
        $this->directive->setField($keyValue[0]);
        $this->directive->setValue($keyValue[1]);
    }
    
}