<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$user= elgg_get_logged_in_user_entity();
$cvcohortname = get_input('cvcohortname');
$cvcourseguid = get_input('cvcourse');  
$cvcourse = get_entity($cvcourseguid);
//echo $cvcohortname;
//echo '<br>'.$cvcourse->title;

 $cvcohort = new ElggGroup ();
    $cvcohort->title = $cvcohortname;  //use titles only for elgg groups
    $cvcohort->name = $cvcohortname;
    $cvcohort->access_id = ACCESS_PUBLIC;
    $cvcohort->owner_guid = elgg_get_logged_in_user_guid();
    $cvcohort->container_guid = $cvcourseguid;
    $cvcohort->save();
    $cvcohort->cvcohort = true;
      //echo elgg_echo ("Cohort Created:  ".$cvcohort->guid);
      //$id = create_access_collection ("cv_id",$cvcohort->guid);
      $id = create_access_collection ($cvcohort->title,$cvcohort->guid);
    $cvcohort->cv_acl = $id;
//$cvcourse->save();

//echo elgg_echo("Course Created! ");
    add_user_to_access_collection(elgg_get_logged_in_user_guid(), $id);
      
      //make the professor a member of the group (cohort)
      $cvcohort->join($user);
?>
