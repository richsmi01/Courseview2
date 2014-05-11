<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of course
 *
 * @author Rich Smith
 */
class ElggCourse extends ElggObject
{
    
    public $test;
    
    protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = "course";
	}
        
        
}

?>
