<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


//forward('courseview/cv_contentpane');
           $plugin_path = elgg_get_plugins_path().'courseview/pages/courseview';
           $elgg_path =elgg_get_site_url();	
            $status = ElggSession::offsetGet('courseview');
//if the courseview session variable was false, toggle it to true and viceversa
            if ($status)
            {
                //put these back in order to exit courseview
                ElggSession::offsetSet ('courseview', false);
                ElggSession::offsetSet('cvmenuguid', null);
                 ElggSession::offsetSet('cvcohortguid', null);
                forward ($elgg_path.'activity');
                //forward('http://localhost/elgg/activity');
            }
            else
            {
   
                ElggSession::offsetSet('courseview', true);
                 
                 // //set session variable telling elgg that we are in 'masters' mode
                //$base_path=dirname(__FILE__); //gives a relative path to the directory where this file exists
              
                
//               stuff to change the menu to say Exit Courseview when in courseview
//                elgg_unregister_menu_item('site', 'courseview');
//                $item = new ElggMenuItem('courseview', 'Exit CourseView', elgg_add_action_tokens_to_url('action/toggle'));
//                elgg_register_menu_item('site', $item);
                
                forward("courseview/courseview"); //load the default courseview welcome page
           }

?>
