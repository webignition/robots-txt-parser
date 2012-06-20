<?php
namespace webignition\RobotsTxt;

class Parser {
    
    /**
     * Unmodified source of given robots.txt file
     * 
     * @var string
     */
    private $source = null;
    
    /**
     * URL of source robots.txt file. Required for correctly parsing relative
     * URLs.
     * 
     * @var string
     */
    private $sourceUrl = null;
    
    /**
     *
     * @var \webignition\RobotsTxt\File
     */
    private $file = null;
    
    
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
        return new \webignition\RobotsTxt\File();
    }
    
}