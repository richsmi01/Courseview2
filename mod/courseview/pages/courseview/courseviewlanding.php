<?php
elgg_load_library('elgg:cv_debug');
cv_debug("Entering pages/courseviewlanding.php", 'stuff',0);
exit;


//$courseview_guid = courseview_initialize();
//echo elgg_echo ("~~~".var_dump($courseview_guid));
//this puts the courseview object into the session
//ElggSession::offsetSet('courseviewobject', courseview_initialize());
//build and call greetings view
$user = elgg_get_logged_in_user_entity();
$params = array('name' => $user->name);
$content = elgg_view('courseview/courseviewlanding', $params);

//display the content in a one_sidebar layout
$vars = array('content' => $content,);
$body = elgg_view_layout('two_column_left_sidebar', $vars, $vars);  //::TODO:  Why doesn't this show us two column layout?
echo elgg_view_page($title, $body);
?>