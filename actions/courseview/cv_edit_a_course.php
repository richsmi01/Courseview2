<?php
/*
 * Allows the prof to change the name of a course
 */

$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvcourse = get_entity($cv_cohort_guid)->getContainerEntity();
$cvcourse->title = get_input('newcoursename');
$cvcourse->save();
system_message("$cvcourse->title course was successfully edited");
 

