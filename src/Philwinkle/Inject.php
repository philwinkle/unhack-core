<?php

namespace Philwinkle;

/**
 * Class Inject
 * @package Philwinkle
 */
class Inject
{
    protected $detectedHack;

    /**
     * @param Detect        $detectedHack [description]
     */
    public function __construct(Detect $detectedHack)
    {
        $this->detectedHack = $detectedHack;
	$this->_injectFix();
    }

    protected function _injectFix()
    {
	return false;
        if(!$this->className){
            //get defined classes in the file
            $classes = get_declared_classes();
            include($this->filePath);
            $diff = array_diff(get_declared_classes(), $classes);
            $this->className = reset($diff);
        }
        return $this->className;
    }
}
