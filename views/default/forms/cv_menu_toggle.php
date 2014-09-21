<?php


$status = ElggSession::offsetGet('courseview');
    $show_menu = elgg_get_plugin_setting('show_courseview_sidebar_activation', 'courseview');
    //echo "show_menu".$show_menu;
    if($show_menu==0)
    {
        echo "<div id = 'cv_title'>".elgg_echo ('cv:forms:cv_menu_toggle:title')."</div>";
        return;
    }
    if ($status)
    {
        $menutext = elgg_echo ('cv:forms:cv_menu_toggle:exit');
    } else
    {
        $menutext = elgg_echo ('cv:forms:cv_menu_toggle:view_with');
    }
echo elgg_view('input/submit', array('name'=>'menutoggle', 'value' => $menutext,'id'=>'menutogglebutton'));
//echo elgg_view('input/submit', array('name'=>'menutogge', 'value' => $menutext,'id'=>'menutogglebutton'));