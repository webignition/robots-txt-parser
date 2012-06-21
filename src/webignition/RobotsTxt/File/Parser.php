<?php
namespace webignition\RobotsTxt\File;

class Parser {
    
    /**
     * States:
     * 
     * adding to record
     * unknown
     * adding to file
     *  
     */
    
    const STATE_UNKNOWN = 0;
    const STATE_ADDING_TO_RECORD = 1;
    const STATE_ADDING_TO_FILE = 2;
    const STATE_STARTING_RECORD = 3;
    const STARTING_STATE = self::STATE_UNKNOWN;
    
    const USER_AGENT_FIELD_NAME = 'user-agent';
    const COMMENT_START_CHARACTER = '#';
    
    /**
     * Unmodified source of given robots.txt file
     * 
     * @var string
     */
    private $source = null;
    
    /**
     * Lines from source
     *  
     * @var array
     */
    private $sourceLines = array();
    
    /**
     * Number of lines in source file
     * 
     * @var int
     */
    private $sourceLineCount = 0;
    
    
    /**
     * Index in $sourceLines of current line being parsed
     * 
     * @var int
     */
    private $sourceLineIndex = 0;
    
    
    /**
     * URL of source robots.txt file. Required for correctly parsing relative
     * URLs.
     * 
     * @var string
     */
    private $sourceUrl = null;
    
    /**
     *
     * @var \webignition\RobotsTxt\File\File
     */
    private $file = null;
    
    
    /**
     * Current state of the parser
     * 
     * @var int
     */
    private $currentState = self::STARTING_STATE;
    
    
    /**
     *
     * @var \webignition\RobotTxt\Record\Record
     */
    private $currentRecord = null;
    
    
    /**
     *
     * @var \webignition\RobotsTxt\Directive\Factory
     */
    private $directiveFactory = null;
    
    
    /**
     *
     * @param string $source 
     */
    public function setSource($source) {
        $this->source = $source;
    }
    
    
    /**
     *
     * @param string $sourceUrl 
     */
    public function setSourceUrl($sourceUrl) {
        $this->sourceUrl = $sourceUrl;
    }
    
    
    /**
     *
     * @return \webignition\RobotsTxt\File 
     */
    public function getFile() {
        if (is_null($this->file)) {
            $this->file = new \webignition\RobotsTxt\File\File();
            $this->parse();
        }
        
        return $this->file;
    }
    
    /**
     *
     * @return \webignition\RobotsTxt\Directive\Factory
     */
    private function directiveFactory() {
        if (is_null($this->directiveFactory)) {
            $this->directiveFactory = new \webignition\RobotsTxt\Directive\Factory();
        }
        
        return $this->directiveFactory;
    }
    
    private function parse() {
        $this->currentState = self::STARTING_STATE;
        $this->sourceLines = explode("\n", trim($this->source));
        $this->sourceLineCount = count($this->sourceLines);
        
        while ($this->sourceLineIndex < $this->sourceLineCount) {
            $this->parseCurrentLine();
        }
    }
    
    private function parseCurrentLine() {
        switch ($this->currentState) {
            case self::STATE_UNKNOWN:
                $this->deriveStateFromCurrentLine();
                break;
            
            case self::STATE_STARTING_RECORD:
                $this->currentRecord = new \webignition\RobotsTxt\Record\Record();
                $this->currentState = self::STATE_ADDING_TO_RECORD;
                break;

            case self::STATE_ADDING_TO_RECORD:
                if ($this->isCurrentLineADirective()) {
                    $directive = $this->directiveFactory()->getDirective($this->getCurrentLine());
                    if ($directive->is(self::USER_AGENT_FIELD_NAME)) {
                        $this->currentRecord->userAgentDirectiveList()->add($directive->getValue());
                    } else {
                        $this->currentRecord->directiveList()->add($this->getCurrentLine());
                    }                                        
                } else {
                    if ($this->isCurrentLineBlank()) {
                        $this->file->addRecord($this->currentRecord);
                        $this->currentRecord = null; 
                    }
                    
                    $this->currentState = self::STATE_UNKNOWN;
                }
                
                $this->sourceLineIndex++; 

                break;

            case self::STATE_ADDING_TO_FILE:                
                $this->file->directiveList()->add($this->getCurrentLine());
                $this->currentState = self::STATE_UNKNOWN;
                $this->sourceLineIndex++;               
                
                break;

            default:
                //var_dump('uknown state '.$this->getCurrentLine());
                //exit();
        }
    }
    
    /**
     *
     * @return string
     */
    private function getCurrentLine() {
        return isset($this->sourceLines[$this->sourceLineIndex]) ? trim($this->sourceLines[$this->sourceLineIndex]) : '';
    }
    
    /**
     *
     * @return boolean
     */
    private function isCurrentLineBlank() {
        return $this->getCurrentLine() == '';
    }
    
    /**
     *
     * @return boolean
     */
    private function isCurrentLineAComment() {
        return substr($this->getCurrentLine(), 0, 1) == self::COMMENT_START_CHARACTER;
    }    
    
    /**
     *
     * @return boolean 
     */
    private function isCurrentLineADirective() {
        if ($this->isCurrentLineBlank()) {
            return false;
        }
        
        if ($this->isCurrentLineAComment()) {
            return false;
        }
        
        return true;
    }
    
    private function deriveStateFromCurrentLine() {
        if (!$this->isCurrentLineADirective()) {
            $this->sourceLineIndex++;
            return $this->currentState = self::STATE_UNKNOWN;
        }
        
        $directive = new \webignition\RobotsTxt\Directive\Directive();
        $directive->parse($this->getCurrentLine());
        
        if ($directive->is(self::USER_AGENT_FIELD_NAME)) {
            return $this->currentState = self::STATE_STARTING_RECORD;
        }
        
        return $this->currentState = self::STATE_ADDING_TO_FILE;
    }
    
}