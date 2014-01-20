<?php
echo'!!!!!!!!courseview landing';
$user = elgg_get_logged_in_user_entity();
//check to see if the user is an admin and provide appropriate admin button
if (elgg_is_admin_logged_in())
{
    echo "<div class='cvminiview'>";
    echo '<br>';
    echo elgg_view('output/url', array
        ("text" => "Manage CourseView", 
        "href" => "courseview/managecourseview", 
        'class' => 'elgg-button elgg-button-action'));
    echo '</div>';
}
//check to see if the user is a professor and add appropriate content based on this
echo elgg_view('courseview/courseview');
elgg_load_library('elgg:courseview');
if (cv_isprof($user))
{
    echo "<div class='cvminiview'>";
    echo '<br>' . $user->name . ' is in the profs group<br/>';
    echo elgg_view('output/url', array
        ("text" => "Debug Stuff",
        "href" => "courseview/managecourses",
        'class' => 'elgg-button elgg-button-action'));
    echo '</div>';
}

//add the actual content
echo elgg_view('courseview/cv_contentpane');
?>
