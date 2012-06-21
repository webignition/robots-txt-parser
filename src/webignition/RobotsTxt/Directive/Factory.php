<?php
namespace webignition\RobotsTxt\Directive;

/**
 * Get an appropriate Directive (or descentant type) from a raw directive line
 *  
 */
class Factory {
    
    const USER_AGENT_FIELD_NAME = 'user-agent';    
    
    /**
     *
     * @param string $directiveString 
     * @return \webignition\RobotsTxt\Directive\Directive
     */
    public function getDirective($directiveString) {
        $directive = new \webignition\RobotsTxt\Directive\Directive();
        $directive->parse($directiveString);
        
        if ($directive->is(self::USER_AGENT_FIELD_NAME)) {
            $directive = new \webignition\RobotsTxt\UserAgentDirective\UserAgentDirective();
            $directive->parse($directiveString);
        }
        
        return $directive;
    }
    
}