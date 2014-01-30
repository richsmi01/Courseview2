<?php

/*
 *A form used to edit a cohort name
 */
$userguid = elgg_get_logged_in_user_guid();
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvcohort = get_entity($cv_cohort_guid);
echo "<div class='cvminiview'>";
echo  "<em>Edit  A COHORT Name:</em><br/><br/>";
echo "Please type in the name of the cohort that you wish to edit:";
echo elgg_view('input/text', array('name' => 'cvcohortname', 'value'=>$cvcohort->title));


//is this the best way to do this???
 //$base_path = elgg_get_plugins_path() . 'courseview/views/default/courseview';
 
 //echo elgg_view ("courseview/cv_list_courses");
//require ($base_path.'/cv_list_courses.php');
echo elgg_view('input/submit');
echo "</div>";

?>
