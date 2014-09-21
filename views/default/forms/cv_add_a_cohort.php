<?php

$userguid = elgg_get_logged_in_user_guid();

echo elgg_view('input/hidden', array('name' => 'group_guid', 'value' => $vars['group_guid']));
echo "<div class='cvminiview'>";
echo "<em>".elgg_echo ('cv:forms:add_a_cohort:make_cohort_title')." :</em><br/><br/>";
$make_open = elgg_echo ('cv:forms:add_a_cohort:cohort_open');
$make_closed = elgg_echo ('cv:forms:add_a_cohort:cohort_closed');
if (!isset($vars['group_guid']))
{
    echo elgg_view('input/radio', array(
        'name' => 'cohort_permissions',
        'id' => 'cohort_permissions',
        'options' => array($make_open => 'open', $make_closed => 'closed'),
        'value' => 'open',
    ));
    echo elgg_echo ('cv:forms:add_a_cohort:type_in_name'); //"Please type in the name of the cohort that you wish to create:";
    echo elgg_view('input/text', array('name' => 'cvcohortname'));
}
echo elgg_echo ('cv:forms:add_a_cohort:please_choose');  //"Please choose course that this cohort will be based on: "
echo elgg_view("courseview/cv_list_courses", array('all' => true));  
echo elgg_view('input/submit');
echo "</div>";  //cvminiview

