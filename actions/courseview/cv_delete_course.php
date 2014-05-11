<?php

/*
 *Deletes a course object
 */

$cvcourseguid = get_input('cvcourse'); 
system_message("$cvcourse->title cohort deleted");
$cvcourse = get_entity($cvcourseguid);
delete_access_collection ($cvcourse->cv_acl);
$cvcourse->delete();

?>
