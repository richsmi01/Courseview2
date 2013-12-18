<?php

elgg_load_library('elgg:cv_debug');
cv_debug("Entering pages/contentpane.php","contentpane" ,0);

$content = elgg_view('courseview/contentpane');
//$content .='@@@';
//$content .= elgg_view_form('treeview');
 
$vars = array('content' => $content,);
$body = elgg_view_layout('one_sidebar', $vars);
echo elgg_view_page('CourseView', $body);



