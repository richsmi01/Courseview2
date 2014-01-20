<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$form_body = "<p>To add an Elgg plugin so that it is recognized by CourseView, type the plugin subtype and click add</p>";
$form_body .= "<p>To remove an Elgg plugin so that it is no longer recognized by CourseView, type the plugin subtype and click add</p>";
$form_body .= elgg_view('input/text', array('internalname' => 'subtype', 'value' => 'subtype'));
$form_body .= elgg_view('input/submit', array('internalname' => 'add', value =>'add'));
$form_body .= elgg_view('input/submit', array('internalname' => 'remove', value =>'remove'));
 
echo elgg_view('input/form', array('body' => $form_body, 'action' => "{$CONFIG->url}actions/my/action"))

?>
