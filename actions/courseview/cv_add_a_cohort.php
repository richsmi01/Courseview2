<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$user= elgg_get_logged_in_user_entity();
$cvcohortname = get_input('cvcohortname');
$cvcourseguid = get_input('cvcourse');  
//echo "cvcohortname".$cvcohortname;
//exit;
$cvcourse = get_entity($cvcourseguid);


 $cvcohort = new ElggGroup ();
    $cvcohort->title = $cvcohortname;  
    $cvcohort->name = $cvcohortname;
    $cvcohort->access_id = ACCESS_PUBLIC; 
    $cvcohort->owner_guid = elgg_get_logged_in_user_guid();
    $cvcohort->container_guid = $cvcourseguid;
   
    $cvcohort->save();
  
    $cvcohort->cvcohort = true;
    
    
    $cvcohort->membership=ACCESS_PUBLIC;
    
    
    
    add_user_to_access_collection($user, $cvcohort->group_acl);
      
      //make the professor a member of the group (cohort)
      $cvcohort->join($user);

