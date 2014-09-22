<?php
/*
 * When we want to turn a group into a cohort, there is some stuff that we must do:
 * -  If the action is coming from the CourseView Admin menu, we must first make a group and set the open/closed appropriately
 * - Else the group has already been created.
 * - 
 * - We have to set the container object to be the course instead of the professor
 * - We have to set the meta data cvcohort to true
 * - Next we have to add the prof to the access collection
 * - And finally, add the prof to the group in case this hasn't already been done
 */

if (!cv_isprof(elgg_get_logged_in_user_entity))
{
    register_error (elgg_echo ('cv:actions:cv_add_a_cohort:sorry'));
    forward (REFERER);
}

$user = elgg_get_logged_in_user_entity();
$cvcohortname = get_input('cvcohortname');
$cvcourseguid = get_input('cvcourse');
$cvcourse = get_entity($cvcourseguid);

if (get_input('group_guid') > 0)
{
    $cvcohort = get_entity(get_input('group_guid'));
    $cv_course = $cvcohort->getContainerEntity();
    $cv_user = elgg_get_logged_in_user_entity ();
    //here we add the prof to the course acl
    $result = add_user_to_access_collection($cv_user->guid, $cv_course->cv_acl);
    system_message ("$cv_user->name".elgg_echo ('cv:actions:cv_add_a_cohort:has_just'). "$cvcohort->name");
} else  //If there is not yet a group, we need to make one  --this occurs if the user has elected to build a courseview cohort           
{           //from the CourseView Administrative menu.
    $cvcohort = new ElggGroup ();
    $cvcohort->name = $cvcohortname;
    $cvcohort->join($user);
}

$cvcohort->access_id = ACCESS_PUBLIC;
    if (get_input('cohort_permissions') == 'open')
    {
        $cvcohort->membership = ACCESS_PUBLIC;
    } else if(get_input('cohort_permissions') == 'closed')
    {
        $cvcohort->membership = ACCESS_PRIVATE;
    }
$cvcohort->owner_guid = elgg_get_logged_in_user_guid();
$cvcohort->container_guid = $cvcourseguid;
$cvcohort->save();
$cvcohort->cvcohort = true;
system_message("The cohort: $cvcohort->name ".elgg_echo ('cv:actions:cv_add_a_cohort:has_been'). " $cvcourse->title");
