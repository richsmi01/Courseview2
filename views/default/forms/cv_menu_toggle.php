<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$status = ElggSession::offsetGet('courseview');
    $show_menu = elgg_get_plugin_setting('show_courseview_sidebar_activation', 'courseview');
    //echo "show_menu".$show_menu;
    if($show_menu==0)
    {
        echo "<div id = 'cv_title'>CourseView</div>";
        return;
    }
    if ($status)
    {
        $menutext = "Exit CourseView";
    } else
    {
        $menutext = "View with CourseView";
    }
echo elgg_view('input/submit', array('name'=>'menutogge', 'value' => $menutext,'id'=>'menutogglebutton'));