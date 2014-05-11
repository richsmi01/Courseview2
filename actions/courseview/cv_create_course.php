<?php

/**
 * This action will create a course using information provided by the cv_create_course form.  
 * In addition, the root menu item (Elgg object with cvmenu subtype) will be created and a relationship
 * created between it and the course
 */

$cvcoursename = get_input('cvcoursename');
$cvcoursedescription = get_input('cvcoursedescription');

$cvcourse = new ElggObject();
$cvcourse->title = $cvcoursename;
$cvcourse->name = $cvcoursename;
$cvcourse->access_id = ACCESS_PUBLIC;
$cvcourse->owner_guid = elgg_get_logged_in_user_guid();
$cvcourse->container_guid = elgg_get_logged_in_user_guid();
$cvcourse->description = $cvcoursedescription;
$cvcourse->subtype = "cvcourse";
$cvcourse->save();
$cvcourse->cvcourse = true;
$id = create_access_collection($cvcourse->title, $cvcourse->guid);
$cvcourse->cv_acl = $id;

add_user_to_access_collection(elgg_get_logged_in_user_guid(), $id);

$cvmenu = new ElggObject();
$cvmenu->subtype = 'cvmenu';
$cvmenu->name = $cvcoursename;
$cvmenu->owner_guid = $user->guid;
$cvmenu->container_guid = $cvcourse->guid;
$cvmenu->access_id = ACCESS_PUBLIC;
$cvmenu->save();
$cvmenu->menutype = "folder";
//$cvmenu->meta1 = "closed";
$cvmenu->menuorder = 0;
$cvmenu->indent = "0";
$cvmenu->save();

add_entity_relationship($cvcourse->guid, "menu", $cvmenu->guid);
system_message("$cvcourse->title course added");
?>
