<?php

/**
 * Add a cohort to CourseView - This is essentially adding an Elgg group and setting the meta tag
 * cvcohort to true and then adding the professor to this group
 */

$user = elgg_get_logged_in_user_entity();
$cvcohortname = get_input('cvcohortname');
$cvcourseguid = get_input('cvcourse');
$cvcourse = get_entity($cvcourseguid);
$cvcourse = get_entity($cvcourseguid);

if (get_input('group_guid') > 0)
{
    echo 'Existing group';
    $cvcohort = get_entity(get_input('group_guid'));
} 
else
{
    $cvcohort = new ElggGroup ();

    $cvcohort->name = $cvcohortname;
    if (get_input('cohort_permissions') === 'open')
    {
        $cvcohort->access_id = ACCESS_PUBLIC;
        system_message("open");
    } else
    {
        $cvcohort->access_id = ACCESS_PRIVATE;
        system_message("closed");
    }

    $cvcohort->owner_guid = elgg_get_logged_in_user_guid();
}
$cvcohort->container_guid = $cvcourseguid;
$cvcohort->title = $cvcohort->name;
$cvcohort->save();
$cvcohort->cvcohort = true;

add_user_to_access_collection($user, $cvcohort->group_acl);

//make the professor a member of the group (cohort)
$cvcohort->join($user);

system_message("The cohort: $cvcohort->name has been bound to the course: $cvcourse->title");
