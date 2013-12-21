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

$currentcourse = get_entity (get_entity(ElggSession::offsetGet('cvcohortguid'))->container_guid)->title;
echo '<br>Make any changes to the course title and click on submit : ';
echo elgg_view('input/text', array(
    'name' => 'newcoursename',
    'value' => "$currentcourse"));
echo ('Please choose course name to edit: ');

//$base_path = elgg_get_plugins_path() . 'courseview/views/default/courseview';
//require ($base_path . '/listcourses.php');

//echo elgg_view ("courseview/listcourses");
echo elgg_view('input/submit');
echo "</div>";
// var_dump ($cvcourses);
//  exit;

