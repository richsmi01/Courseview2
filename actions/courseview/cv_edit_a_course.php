<?php

  //elgg_load_library('elgg:courseview');
  
 
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
//echo 'Here I am in edit_a_course file!!!!';
$cvcourse = get_entity($cv_cohort_guid)->getContainerEntity();
//echo 'coursename'.$cvcourse->title;
//echo 'new course name'.get_input('newcoursename');
$cvcourse->title = get_input('newcoursename');
$cvcourse->save();

//echo 'Here I am in edit_a_course file!!!!'.$cvcourse->title;  

