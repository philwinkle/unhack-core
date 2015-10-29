<?php

namespace Philwinkle;

/**
 * Class Detect
 * @package Philwinkle
 */
class Detect
{
    public $line;
    public $file;
    public $filePath;
    public $shortCode;
    public $methodSource;
    public $className;
    public $type;

    /**
     * @param \SplFileObject $hackedFile [description]
     * @param integer        $lineNumber [description]
     */
    public function __construct(\SplFileObject $hackedFile, $lineNumber = 0)
    {
        $this->line = $lineNumber;
        $this->file = $hackedFile;
        $this->filePath = $this->file->getRealPath();
        //TODO replace this, requires array for now
        $this->_find(file($this->filePath));
    }

    protected function _findDefinedClasses()
    {
        if(!$this->className){
            //interrogate class from file
            //get the current pointer to reset
            $pointer = $this->file->key();
            $max = $this->file->getSize();

            for($line = $pointer; $line<=$max; $line++){
                $this->file->seek($line);

                $matches = [];
                if(stristr($this->file->current(), 'class')){
                    preg_match_all('/class\s+(.*?)\s+/', $this->file->current(), $matches);
                    $className = $matches[1][0];
                    if(class_exists($className)){
                        return $className;
                    }
                }
            }

            //reset the pointer
            $this->file->seek($pointer);

            //get defined classes in the file
            $classes = get_declared_classes();
            include($this->filePath);
            $diff = array_diff(get_declared_classes(), $classes);
            $this->className = end($diff);
        }
        return $this->className;
    }

    protected function _getMethodSource($fileSource)
    {
        $className = $this->_findDefinedClasses();

        //find method name to copy
        $matches = [];
        preg_match_all('/function(.*?)\(/', $this->file->current(), $matches);
        $fnName = trim($matches[1][0]);

        $rc = new \ReflectionMethod($className, $fnName);

        //zero-indexed start line of function
        $startLine = $rc->getStartLine() - 1;
        $endLine = $rc->getEndLine();
        $length = $endLine - $startLine;

        return implode("", array_slice($fileSource, $startLine, $length));
    }

    protected function _getShortCode($className)
    {
        $types = [
            'model'=>'_Model_',
            'block'=>'_Block_',
            'helper'=>'_Helper_',
            'controller' => 'Controller'
        ];

        //find the type
        foreach($types as $type=>$typeString){
            if(stristr($className, $typeString)){
                $shortCodeParts = explode($typeString, ltrim($className, 'Mage_'));
                $shortCodeParts = array_map('strtolower', $shortCodeParts);
                $this->shortCode = $type!=='controller' ? implode('/', $shortCodeParts) : null;
                $this->type = $type;

                return $this->shortCode;
            }
        }

    }

    protected function _find($fileSource)
    {
        $className = $this->_findDefinedClasses();

        //move backward from changed line to find function definition
        for($fnStart = $this->line; $fnStart>=0; $fnStart--){

            $this->file->seek($fnStart);

            //find if there is a function defined and get its name
            if(stristr($this->file->current(), 'function')){

                $shortCode = $this->_getShortCode($className);
                $this->methodSource = $this->_getMethodSource($fileSource);
                return;
            }
        }

    }
}