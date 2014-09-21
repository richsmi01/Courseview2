<?php


    if (cv_isprof($user))
{
            
            $menutext = elgg_echo ( 'cv:forms:cv_admin_toggle:menu_text');
            echo elgg_view('input/submit', array('name'=>'menutogge', 'value' => $menutext,'id'=>'menutogglebutton'));
}
    