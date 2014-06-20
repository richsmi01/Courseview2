<?php
/*
 * Allows the prof to change the name of a course
 */


$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvcourse = get_entity($cv_cohort_guid)->getContainerEntity();

//need to add some security to make sure that object is a cvcourse and the user has permissions
if (!$cvcourse->canEdit() ||$cvcourse->cvcourse !=true )
{
    register_error ("Our system is currently undergoing routine maintenance!");
    forward (REFERER);
}
$cvcourse->title = get_input('newcoursename');
$cvcourse->save();
system_message("$cvcourse->title course was successfully edited");
 

