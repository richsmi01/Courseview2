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
delete_access_collection ($cvcourse->cv_acl);
//echo 'Course:  '.$cvcourse->title.' has been deleted';
$cvcourse->delete();
//exit;


?>
