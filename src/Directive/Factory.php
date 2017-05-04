<?php

namespace webignition\RobotsTxt\Directive;

use webignition\RobotsTxt\Directive\Directive as RobotsTxtDirective;
use webignition\RobotsTxt\UserAgentDirective\UserAgentDirective;

/**
 * Get an appropriate Directive (or descendant type) from a raw directive line
 */
class Factory
{
    const USER_AGENT_FIELD_NAME = 'user-agent';

    /**
     *
     * @param string $directiveString
     * @return Directive
     */
    public function getDirective($directiveString)
    {
        $directive = new RobotsTxtDirective();
        $directive->parse($directiveString);

        if ($directive->is(self::USER_AGENT_FIELD_NAME)) {
            $directive = new UserAgentDirective();
            $directive->parse($directiveString);
        }

        return $directive;
    }
}
