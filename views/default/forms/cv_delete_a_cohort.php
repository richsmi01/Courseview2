<?php

/*
 * Form used to delete a cohort
 */

$userguid = elgg_get_logged_in_user_guid();

echo "<div class='cvminiview'>";
echo '<p><em>'.elgg_echo ('cv:forms:cv_delete_a_cohort:delete_a').'</em></p>';
echo elgg_echo ('cv:forms:cv_delete_a_cohort:please_choose');

$base_path = elgg_get_plugins_path() . 'courseview/views/default/courseview';
echo elgg_view ("courseview/cv_list_cohorts");
echo elgg_view('input/submit');
echo "</div>";


