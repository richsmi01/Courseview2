<?php

$cvuser = elgg_get_logged_in_user_entity();
$cvadmin = $cvuser->isAdmin();
$cvcohortowner = cv_is_cohort_owner($cvuser, $vars['entity']);
if (!$cvadmin && !$cvcohortowner)
{
    return;
}
echo elgg_view_form('cv_remove_cohort', $vars, array('group_guid' => $vars['entity']->guid));