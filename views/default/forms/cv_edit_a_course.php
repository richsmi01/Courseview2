<?php


$userguid = elgg_get_logged_in_user_guid();

$current_course = get_entity (get_entity(ElggSession::offsetGet('cvcohortguid'))->container_guid);
$current_course_name = $current_course->title;
echo "<div class='cvminiview'>";
echo "<p><em>".elgg_echo('cv:forms:cv_edit_a_course:title', array("<span class=blue>$current_course_name</span>"))."</em></p>";

echo elgg_echo ('cv:forms:cv_edit_a_course:make_changes');
echo elgg_view('input/text', array(
    'name' => 'newcoursename',
    'value' => $current_course_name));
echo elgg_view('input/submit');
echo "</div>";


