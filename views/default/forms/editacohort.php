<?php

/*
 *A form used to edit a cohort name
 */
$userguid = elgg_get_logged_in_user_guid();
$cohortguid = ElggSession::offsetGet('cvcohortguid');
$cvcohort = get_entity($cohortguid);
echo "<div class='cvminiview'>";
echo  "<em>Edit  A COHORT Name:</em><br/><br/>";
echo "Please type in the name of the cohort that you wish to edit:";
echo elgg_view('input/text', array('name' => 'cvcohortname', 'value'=>$cvcohort->title));


//is this the best way to do this???
 //$base_path = elgg_get_plugins_path() . 'courseview/views/default/courseview';
 
 //echo elgg_view ("courseview/listcourses");
//require ($base_path.'/listcourses.php');
echo elgg_view('input/submit');
echo "</div>";

?>
