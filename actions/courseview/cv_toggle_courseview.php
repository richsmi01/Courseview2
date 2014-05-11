<?php

/**
 * ::TODO:Matt - Should probable amalgamate this code with cv_menu_toggle
 * @author Rich Smith
 */

           $plugin_path = elgg_get_plugins_path().'courseview/pages/courseview';
           $elgg_path =elgg_get_site_url();	
            $status = ElggSession::offsetGet('courseview');
//if the courseview session variable was false, toggle it to true and viceversa
            if ($status)
            {
        
                ElggSession::offsetSet ('courseview', false);
                ElggSession::offsetSet('cvmenuguid', null);
                 ElggSession::offsetSet('cvcohortguid', null);
                forward ($elgg_path.'activity');
                //forward('http://localhost/elgg/activity');
            }
            else
            {
   
                ElggSession::offsetSet('courseview', true);
                 
                
                forward("courseview/courseview"); //load the default courseview welcome page
           }

?>
