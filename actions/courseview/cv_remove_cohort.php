<?php

/**
 * Action to remove a cohort from the lightbox projected from the groups list page
 *
 * @author Rich Smith
 */

$cv_cohort_guid = get_input('group_guid');
$cv_cohort = get_entity($cv_cohort_guid);
if (!$cv_cohort->canEdit() || !elgg_instanceof($cv_cohort,'group'))
{
    register_error (elgg_echo('cv:actions:cv_remove_cohort:sorry'));
    forward (REFERER);
}

if (get_input ('remove_cohort')==='remove')
{
    $cv_cohort = get_entity(get_input ('group_guid'));
    $cv_cohort->cvcohort = false;
    system_message ("$cv_cohort->name ". elgg_echo ('cv:actions:cv_remove_cohort:removed'));
    if (get_input  ('delete_group'))
    {
        $cv_cohort->delete();
        system_message ("$cv_cohort->name". elgg_echo ('cv:actions:cv_remove_cohort:removed') );
    }
}
forward (REFERER);
 