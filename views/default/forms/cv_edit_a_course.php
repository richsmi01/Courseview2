<?php


$userguid = elgg_get_logged_in_user_guid();


echo "<div class='cvminiview'>";
echo '<em>EDIT CURRENT COURSE NAME:</em><br/><br/>';

//$cohortguid = ElggSession::offsetGet('cvcohortguid');
//$cvcohort = get_entity($cohortguid);
//echo "<div class='cvminiview'>";
//echo  "<em>Edit  A COHORT Name:</em><br/><br/>";
//echo "Please type in the name of the cohort that you wish to edit:";
//echo elgg_view('input/text', array('name' => 'cvcohortname', 'value'=>$cvcohort->title));

$currentcourse = get_entity (get_entity(ElggSession::offsetGet('cvcohortguid'))->container_guid);
$currentcoursename = $currentcourse->title;
echo '<br>Make any changes to the course title and click on submit : '.$currentcourse->guid;
echo elgg_view('input/text', array(
    'name' => 'newcoursename',
    'value' => "$currentcoursename"));
echo ('Please choose course name to edit: ');

//$base_path = elgg_get_plugins_path() . 'courseview/views/default/courseview';
//require ($base_path . '/cv_list_courses.php');

//echo elgg_view ("courseview/cv_list_courses");
echo elgg_view('input/submit');
echo "</div>";
// var_dump ($cvcourses);
//  exit;

