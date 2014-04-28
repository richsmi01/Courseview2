<?php

//echo "Hey, I'm a view<br>";
//echo print_r($vars['entity'],true);
//echo $vars[guid];
//echo elgg_view('courseview/cv_list_courses');
//$vars['test']='Yay!';
//var_dump($vars);
echo elgg_view_form('cv_add_a_cohort',$vars,array ('group_guid'=>$vars['entity']->guid)); 

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

