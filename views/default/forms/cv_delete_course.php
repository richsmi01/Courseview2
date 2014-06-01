<?php

/**
 *  List all courses with radio buttons to allow selection of single course and delete it
 **/
$userguid = elgg_get_logged_in_user_guid();
echo "<div class='cvminiview'>";
echo '<em>DELETE A COURSE:</em><br/><br/>';
echo ('Please choose course name to delete: ');

if (cv_is_admin(elgg_get_logged_in_user_entity())) //if the user is admin, show all courses
{
    echo elgg_view ("courseview/cv_list_courses", array ('all'=>true));
}
else  //otherwise, just show owned courses
{
    echo elgg_view ("courseview/cv_list_courses", array ('all'=>false));
}
echo elgg_view('input/submit');
echo "</div>";
?>
