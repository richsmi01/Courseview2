<?php

/**
 * ::TODO:Rich - Should probable amalgamate this code with cv_menu_toggle
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
                forward (REFERER);  //goes to current page
                //forward('http://localhost/elgg/activity');
            }
            else
            {
                ElggSession::offsetSet('courseview', true);            
                forward (REFERER);
                //forward("courseview/courseview"); //load the default courseview welcome page
           }

?>
