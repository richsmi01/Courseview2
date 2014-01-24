<?php

  //elgg_load_library('elgg:courseview');
  
 
$cohortguid = ElggSession::offsetGet('cvcohortguid');
echo 'Here I am in edit_a_course file!!!!';
$cvcourse = get_entity($cohortguid)->getContainerEntity();
echo 'coursename'.$cvcourse->title;
echo 'new course name'.get_input('newcoursename');
$cvcourse->title = get_input('newcoursename');
$cvcourse->save();

echo 'Here I am in edit_a_course file!!!!'.$cvcourse->title;  

