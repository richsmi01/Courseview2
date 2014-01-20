<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$userguid = elgg_get_logged_in_user_guid();

echo "<div class='cvminiview'>";
echo  "<em>ADD A COHORT :</em><br/><br/>";
echo "Please type in the name of the cohort that you wish to create:";
echo elgg_view('input/text', array('name' => 'cvcohortname'));
echo "Please choose course that this cohort will be based on: ";

//is this the best way to do this???
 //$base_path = elgg_get_plugins_path() . 'courseview/views/default/courseview';
 
 echo elgg_view ("courseview/cv_list_courses");
//require ($base_path.'/cv_list_courses.php');
echo elgg_view('input/submit');
echo "</div>";

?>
