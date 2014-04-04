<?php



$userguid = elgg_get_logged_in_user_guid();

echo "<div class='cvminiview'>";
    echo  "<em>ADD A COHORT :</em><br/><br/>";
    echo "Please type in the name of the cohort that you wish to create:";
    echo elgg_view('input/text', array('name' => 'cvcohortname'));
    echo "Please choose course that this cohort will be based on: ";

    echo elgg_view ("courseview/cv_list_courses", array ('all'=>true));  //would prefer this true but add cohort action has bug
    echo elgg_view('input/submit');
echo "</div>";

