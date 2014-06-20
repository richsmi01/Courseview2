<?php

/*
 * The main CourseView page
 */

$content = elgg_view('courseview/cv_contentpane');

$vars = array('content' => $content,);
$body = elgg_view_layout('one_sidebar', $vars);
echo elgg_view_page('CourseView', $body);



