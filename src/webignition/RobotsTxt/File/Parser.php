<?php
namespace webignition\RobotsTxt\File;

class Parser {    
    
    const STATE_UNKNOWN = 0;
    const STATE_ADDING_TO_RECORD = 1;
    const STATE_ADDING_TO_FILE = 2;
    const STATE_STARTING_RECORD = 3;
    const STARTING_STATE = self::STATE_UNKNOWN;
    
    const USER_AGENT_FIELD_NAME = 'user-agent';
    const SITEMAP_DIRECTIVE_FIELD_MAME = 'sitemap';
    const COMMENT_START_CHARACTER = '#';    
    
    const UTF8_BOM = "\xef\xbb\xbf";
    
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
     * Previous state of the parser
     * 
     * @var int
     */
    private $previousState = self::STARTING_STATE;
    
    
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
     * @var array
     */
    private $recordlessDirectiveFieldNames = array(
        self::SITEMAP_DIRECTIVE_FIELD_MAME => true
    );
    
    
    /**
     *
     * @param string $source 
     */
    public function setSource($source) {
        $this->source = $this->prepareSource($source);
    }

    
    /**
     * 
     * @param string $source
     * @return string
     */
    private function prepareSource($source) {
        $source = trim($source);
        
        if (substr($source, 0, strlen(self::UTF8_BOM)) == self::UTF8_BOM) {
            $source = substr($source, strlen(self::UTF8_BOM));
        }
        
        return $source;
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
        
        if ($this->hasCurrentRecord()) {
            $this->file->addRecord($this->currentRecord);
        }
    }
    
    private function parseCurrentLine() {                
        switch ($this->currentState) {
            case self::STATE_UNKNOWN:
                $this->deriveStateFromCurrentLine();
                $this->previousState = self::STATE_UNKNOWN;
                break;
            
            case self::STATE_STARTING_RECORD:
                $this->currentRecord = new \webignition\RobotsTxt\Record\Record();
                $this->currentState = self::STATE_ADDING_TO_RECORD;                
                $this->previousState = self::STATE_STARTING_RECORD;
                break;

            case self::STATE_ADDING_TO_RECORD:
                if ($this->isCurrentLineARecordlessDirective()) {
                    $this->currentState = self::STATE_ADDING_TO_FILE;
                    $this->previousState = self::STATE_ADDING_TO_RECORD;
                    return;
                }
                
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
                $this->currentState = ($this->previousState == self::STATE_ADDING_TO_RECORD) ? self::STATE_ADDING_TO_RECORD : self::STATE_UNKNOWN;
                $this->previousState = self::STATE_ADDING_TO_FILE;
                $this->sourceLineIndex++;               
                
                break;

            default:
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
    private function isCurrentLineLastLine() {
        return $this->sourceLineIndex == $this->sourceLineCount - 1;
    }
    
    
    /**
     *
     * @return boolean
     */
    private function hasCurrentRecord() {
        return !is_null($this->currentRecord);
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
    
    
    /**
     *
     * @return boolean
     */
    private function isCurrentLineARecordlessDirective() {
        if (!$this->isCurrentLineADirective()) {
            return false;
        }
        
        $directive = $this->directiveFactory()->getDirective($this->getCurrentLine());
        return array_key_exists($directive->getField(), $this->recordlessDirectiveFieldNames);
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