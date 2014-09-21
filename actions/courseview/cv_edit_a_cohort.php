<?php

/*
 * Edit a cohort name
 */

//need to add some security to make sure that object is a cvcourse and the user has permissions


$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cv_cohort = get_entity($cv_cohort_guid);

if (!$cv_cohort->canEdit() || !elgg_instanceof($cv_cohort,'group'))
{
    register_error (elgg_echo('cv:actions:cv_edit_a_cohort:sorry'));
    forward (REFERER);
}
$cv_cohort->title = get_input('cvcohortname');
$cv_cohort->name = get_input('cvcohortname');
$cv_cohort->access_id = ACCESS_PUBLIC;
$cv_cohort->save();



