<?php
//Maybe I just need to check to see if the user owns this group...this might be better than making the admin do it.
if (!cv_is_admin(cv_get_current_user(CV_ENTITY)))
{
    return;
}

echo elgg_view_form('cv_remove_cohort',$vars,array ('group_guid'=>$vars['entity']->guid)); 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

