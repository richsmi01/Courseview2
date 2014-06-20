<?php
/**
 * This action will create a course using information provided by the cv_create_course form.  
 * In addition, the root menu item (Elgg object with cvmenu subtype) will be created and a relationship
 * created between it and the course
 */

$cvcoursename = get_input('cvcoursename');
$cvcoursedescription = get_input('cvcoursedescription');
//build cvcourse object
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
/*We need to create an access collection for this cvcourse object.  Then
 * whenever a user joins a cohort, that user can be added to this collection 
 * so that they can access content in other cohorts when the content permissions
 * has been set to share with all cohorts in course
 */
$id = create_access_collection($cvcourse->title, $cvcourse->guid);
$cvcourse->cv_acl = $id;
//Don't forget to add the prof to the access collection
add_user_to_access_collection(elgg_get_logged_in_user_guid(), $id);

/*We need to build the very first cvmenu item for the course.  This cvmenu item
 * is a folder with the name of course in it and set to indent level 0 and menuorder 0
 */
$cvmenu = new ElggObject();
$cvmenu->subtype = 'cvmenu';
$cvmenu->name = $cvcoursename;
$cvmenu->title = $cvcoursename;
$cvmenu->owner_guid = $user->guid;
$cvmenu->container_guid = $cvcourse->guid;
$cvmenu->access_id = ACCESS_PUBLIC;
$cvmenu->save();
$cvmenu->menutype = "folder";
$cvmenu->menuorder = 0;
$cvmenu->indent = "0";
$cvmenu->save();
/*Can't forget to add a relationship between the cvcourse and the cvmenu so that it is loaded
 * when querying all cvmenu items in the course
 */

add_entity_relationship($cvcourse->guid, "menu", $cvmenu->guid);
system_message("$cvcourse->title course added");
?>
