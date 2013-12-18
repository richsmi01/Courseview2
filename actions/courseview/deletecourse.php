<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//echo 'cool!<br/>';

$cvcourseguid = get_input('cvcourse'); 
//echo '!!!'.$cvcourseguid;

$cvcourse = get_entity($cvcourseguid);
//echo $cvcourse->title;

//echo 'Course:  '.$cvcourse->title.' has been deleted';
$cvcourse->delete();
//exit;


?>
