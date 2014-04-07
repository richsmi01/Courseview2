<?php

/*
 * Used in various forms to list courses that the current user (a professor)  is an owner of ...each of these 
 * courses has an associated radio button 
 */

$cv_userguid = elgg_get_logged_in_user_guid();
$cv_user = get_entity($cv_userguid);
//echo "!!!".get_entity($userguid)->name;
if ($vars['all']==true)
{
    $cvcourses = cv_get_all_courses();
}
else
{
    $cvcourses = cv_get_owned_courses($cv_user);
}

//$cvcourses = elgg_get_entities_from_relationship(array
//    ('type' => 'object',
//    'metadata_names' => array('cvcourse'),
//    'metadata_values' => array(true),
//    'limit' => false,
//    'owner_guids' => $userguid,    
//        )
//);

//if (sizeof($cvcourses)==0)
//{
//    echo "<br> You have no courses to delete <br>";
//    return;
//}

foreach ($cvcourses as $cvcourse)
{
    //echo "###".$cvcourse->getOwnerEntity()->guid."@@@".$userguid;
        $radioname = "Course: $cvcourse->title  <p>Owner: ".$cvcourse->getOwnerEntity()->name." <p>Description: $cvcourse->description</p>";
    echo "<div id='contentitem'>";
    echo elgg_view('input/radio', array('internalid' => $cvcourse->guid, 'name' => 'cvcourse', 'options' => array($radioname => $cvcourse->guid)));
    echo"</div>";
}

