<?php

/*
 * Used in various forms to list courses that the current user (a professor)  is an owner of ...each of these 
 * courses has an associated radio button 
 */

$userguid = elgg_get_logged_in_user_guid();

$cvcourses = elgg_get_entities_from_relationship(array
    ('type' => 'object',
    'metadata_names' => array('cvcourse'),
    'metadata_values' => array(true),
    'limit' => false,
    'owner' => $userguid,
        )
);

foreach ($cvcourses as $cvcourse)
{
    $radioname = $cvcourse->title . '<br> - ' . $cvcourse->description.'<br>';
    echo elgg_view('input/radio', array('internalid' => $cvcourse->guid, 'name' => 'cvcourse', 'options' => array($radioname => $cvcourse->guid)));
}
?>
