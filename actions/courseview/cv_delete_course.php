<?php

/*
 *Deletes a course object
 */

//need to add some security to make sure that object is a cvcourse and the user has permissions

$cvcourseguid = get_input('cvcourse'); 
$cvcourse = get_entity($cvcourseguid);
if (!$cvcourse->canEdit() ||$cvcourse->cvcourse !=true )
{
    register_error (elgg_echo('cv:actions:cv_delete_course:sorry'));
    forward (REFERER);
}

system_message("$cvcourse->title ".elgg_echo('cv:actions:cv_delete_course:sorry'));
delete_access_collection ($cvcourse->cv_acl);
$cvcourse->delete();

