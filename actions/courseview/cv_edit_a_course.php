<?php
/*
 * Allows the prof to change the name of a course
 */

$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvcourse = get_entity($cv_cohort_guid)->getContainerEntity();

//need to add some security to make sure that object is a cvcourse and the user has permissions
if (!$cvcourse->canEdit() ||$cvcourse->cvcourse !=true )
{
    register_error (elgg_echo('cv:actions:cv_edit_a_course:sorry'));
    forward (REFERER);
}
$cvcourse->title = get_input('newcoursename');
$cvcourse->save();
system_message("$cvcourse->title ".elgg_echo('cv:actions:cv_edit_a_course:edited'));
 

