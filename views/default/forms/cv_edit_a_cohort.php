<?php

/*
 *A form used to edit a cohort name
 */
$userguid = elgg_get_logged_in_user_guid();
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvcohort = get_entity($cv_cohort_guid);
echo "<div class='cvminiview'>";
echo  "<p><em>".elgg_echo ('cv:forms:cv_edit_a_cohort:title',array ("<span class='blue'>$cvcohort->name</span> "))."</em></p>";
echo elgg_echo ('cv:forms:cv_edit_a_cohort:please_type');  
echo elgg_view('input/text', array('name' => 'cvcohortname', 'value'=>$cvcohort->name));
echo elgg_view('input/submit');
echo "</div>";
?>
