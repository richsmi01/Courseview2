<?php

$userguid = elgg_get_logged_in_user_guid();
//$existing_group =$vars['existing_group'];
//echo 'existing_group?'.$existing_group;
//echo $vars['group_guid'];
//echo elgg_view('input/hidden', array('name' => 'existing_group', 'value' => $vars['existing_group']));
echo elgg_view('input/hidden', array('name' => 'group_guid', 'value' => $vars['group_guid']));
echo "<div class='cvminiview'>";
echo "<em>ADD A COHORT :</em><br/><br/>";
if (!isset($vars['group_guid']))
{
    echo elgg_view('input/radio', array(
        'name' => 'params[cohort_permissions]',
        'id' => 'cohort_permissions',
        'options' => array('Make Cohort open' => 'open', 'Make Cohort closed' => 'closed'),
        'value' => 'open',
    ));
    echo "Please type in the name of the cohort that you wish to create:";
    echo elgg_view('input/text', array('name' => 'cvcohortname'));
}
echo "Please choose course that this cohort will be based on: ";

echo elgg_view("courseview/cv_list_courses", array('all' => true));  //would prefer this true but add cohort action has bug
echo elgg_view('input/submit');
echo "</div>";

