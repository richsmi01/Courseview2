<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo 'Manage Courses<br>';

 $rs_type = ElggSession::offsetSet('object_type', 'course');
  //require "$base_path/cv_contentpane.php";

  
 //$content = elgg_view ('courseview/cv_contentpane').'<br>';
 
  $content .= elgg_view('output/url', array("text" => "Set Initial Testing Conditions", "href" => "courseview/testinginit", 'class' => 'elgg-button elgg-button-action'));
// $content .= elgg_view('output/url', array("text" => "Add a course", "href" => "courseview/addcourse", 'class' => 'elgg-button elgg-button-action'));
 $vars = array('content' => $content,);
 $body = elgg_view_layout('one_sidebar', $vars);
 echo elgg_view_page($title, $body);
  
 

?>
