<?php

/*
 * Used in various forms to list cohorts that the current user (a professor)  is an owner of ...each of these 
 * cohorts has an associated radio button 
 */
$cv_userguid =  elgg_get_logged_in_user_guid();
$cv_user = get_entity ($cv_userguid);

 if ($vars['all']==true)
 {
 $cvcohorts = cv_get_all_cohorts();
 }
 else
 {
     $cvcohorts = cv_get_owned_cohorts($cv_user);
 }
 
// $cvcohorts = elgg_get_entities_from_metadata(array
//        ('type' => 'group',
//        'metadata_names' => array('cvcohort'),
//        'metadata_values' => array(true),
//        'limit' => false,
//        'owner_guids' => $userguid,
//            )
//    );

    foreach ($cvcohorts as $cvcohort)
    {
        $radioname =$cvcohort->title.' - '.$cvcohort->description;
        echo "<div id='contentitem'>";
            echo elgg_view ('input/radio',array('internalid' => $cvcohort->guid,'name' => 'cvcohort', 'options'=>array($radioname=> $cvcohort->guid)));
       echo"</div>";
    }

