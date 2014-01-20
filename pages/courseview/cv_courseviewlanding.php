<?php
elgg_load_library('elgg:cv_debug');

$user = elgg_get_logged_in_user_entity();
$params = array('name' => $user->name);
$content = elgg_view('courseview/cv_courseviewlanding', $params);

//display the content in a one_sidebar layout
$vars = array('content' => $content,);
$body = elgg_view_layout('two_column_left_sidebar', $vars, $vars);  
echo elgg_view_page($title, $body);