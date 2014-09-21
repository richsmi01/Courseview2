<?php
/*This is called if the delete cohort menu is selected in CourseView*/

//$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cv_cohort_guid = get_input('cvcohort');
$cv_cohort = get_entity($cv_cohort_guid);

if (!$cv_cohort->canEdit() || !elgg_instanceof($cv_cohort,'group'))
{
    register_error (elgg_echo('cv:actions:cv_delete_a_cohort:sorry'));
    forward (REFERER);
}

$cv_cohort->cvcohort = false; //this object is now just a simple group


system_message("$cv_cohort->name  ".elgg_echo('cv:actions:cv_delete_a_cohort:deleted'));
$cv_cohort->delete();  //now we delete the group

