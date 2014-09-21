<?php

/*
 * Used in various forms to list courses that the current user (a professor)  is an owner of ...each of these 
 * courses has an associated radio button 
 */

$cv_userguid = elgg_get_logged_in_user_guid();
$cv_user = get_entity($cv_userguid);
if ($vars['all']==true)
{
    elgg_load_library('elgg:cv_rarely_used_functions');
    $cvcourses = cv_get_all_courses();
}
else
{
    $cvcourses = cv_get_owned_courses($cv_user);
}
foreach ($cvcourses as $cvcourse)
{
    $radioname = elgg_echo('cv:views:cv_list_courses:owner', array ($cvcourse->title.'<br>' , $cvcourse->getOwnerEntity()->name.'<br>   ',$cvcourse->description.'<br>'));
    echo "<div id='contentitem'>";
    echo elgg_view('input/radio', array('internalid' => $cvcourse->guid, 'name' => 'cvcourse', 'options' => array($radioname => $cvcourse->guid)));
    echo"</div>";
}

