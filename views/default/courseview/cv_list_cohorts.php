<?php
/*
 * Used in various forms to list cohorts that the current user (a professor)  is an owner of ...each of these 
 * cohorts has an associated radio button 
 * If $vars['all'] has been set to true, it will list all cohorts.  If not, it lists only the cohorts ownedby the 
 * currently logged in user (professor)
 */
 elgg_load_library('elgg:cv_rarely_used_functions');
$cv_userguid = elgg_get_logged_in_user_guid();
$cv_user = get_entity($cv_userguid);

if ($vars['all'] == true)
{
    $cvcohorts = cv_get_all_cohorts();
} else
{
    $cvcohorts = cv_get_owned_cohorts($cv_user);
}

foreach ($cvcohorts as $cvcohort)
{
    $radioname = $cvcohort->name . ' - owner: ' . $cvcohort->getOwnerEntity()->name. $cvcohort->description;
    echo "<div id='contentitem'>";
    echo elgg_view('input/radio', array('internalid' => $cvcohort->guid, 'name' => 'cvcohort', 'options' => array($radioname => $cvcohort->guid)));
    echo"</div>";
}

