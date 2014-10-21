<?php

//This is not currently used but left in for future expansion in case I want to trigger some activities
//when new users are created
function cv_intercept_newuser($event, $type, $params)
{
    
}

/**
 * Intercepts any new content creation, content updates, or content deletions.  If the content type is one of our valid 
 * plugins (as specified in the setup page), we will loop through the treeview generated by cv_content_tree
 * view that is appended to appropriate pages and use  the checkboxes in this to determine any 
 * relationships between the current menu item and the content to be created or removed. 
 *
 * @param string $event  not actually needed or used
 * @param string $type  not actually needed or used
 * @param string $object  --
 *
 * @return void
 */
function cv_intercept_content_update($event, $type, $object)
{
    $cvmenuguid = ElggSession::offsetGet('cvmenuguid');
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');
    $user = elgg_get_logged_in_user_entity();

    $valid_plugin = cv_is_valid_plugin_by_keys($user, $object);

    // pull array of checkboxes (associated with menu items) from the cv_content_tree 
    // so that we can loop through them in order to determine what realtionships need to be added or deleted
    // Note that $menu_items is an array of Strings passed from cv_add_to_cohort_tree where each 
    // element contains three pieces of information in the format Xmenuitemguid|cohortguid where
    //  X is a + if a new relationship should be created and a - if it should be removed.
    $menu_items = get_input('menuitems');
    if (is_array($menu_items))
    {
        foreach ($menu_items as $menu_item)
        {
            // menu_item_guid is stripped out and cohortguid is stripped out into $cohort_guid
            $cohort_guid = substr(strstr($menu_item, '|'), 1);
            $stop = stripos($menu_item, '|') - 1;

            $menu_item_guid = substr($menu_item, 1, $stop);
            $guid_one = $menu_item_guid;
            $guid_two = $object->guid;

            //need to check if this is a non-professor type content and change relationship accordingly...
            //If it is of type professor, the relationship String should be simply 'content'.  However, if 
            //the relationship is of type student, then the relationship String should have the current
            //$cohort_guid appended to it in the form contentXXX where XXX is the $cohort_guid

            $relationship = 'content';
            //however, if the $menuitem is not of type 'professor' (ie, of type 'student'), then we need to append the particulart  cohort to 'content'
            if (get_entity($menu_item_guid)->menutype != 'professor')
            {
                $relationship.= $cohort_guid;
            } else if (!$object->sort_order)
            {
                //if it is an object that will belong to professor content, we want to create a meta tag that we can use to move
                //the content up and down within the sort order
                $object->sort_order = $object->time_created;
                $object->save();
            }

            if (strrchr('+', $menu_item) && $valid_plugin)  //if the module was checked in the cv_content_tree view, then add relationship
            {
                // cv_debug("Adding Relationship: $guid_one, $relationship, $guid_two", "cv_intercept_content_update", 5);
                $z = add_entity_relationship($guid_one, $relationship, $guid_two);
            } else
            {
                $rel_to_delete = check_entity_relationship($guid_one, $relationship, $guid_two);
                if ($rel_to_delete)  //if the module was unchecked and there was a relationship, we need to remove the relationship
                {
                    //  cv_debug('deleting relationship', '', 5);
                    delete_relationship($rel_to_delete->id);
                }
            }
        }
    }
    $rootdomain = elgg_get_site_url();
    if (get_input('preview') || get_input('cancel'))
    {
        return true;
    }
    $cvredirect = $rootdomain . 'courseview/cv_contentpane/' . $cvcohortguid . '/' . $cvmenuguid;

    ElggSession::offsetSet('cvredirect', $cvredirect);
}

/**
 * Runs whenever a user joins a group.  If that group is actually a cvcohort then we need to add the users guid to the 
 * acl of the cvcourse which is the container of that particluar cvcohort
 *
 * @param array $event  - not used
 * @param array  $type - not used
 * @param array $params - the params array contains the group entity that the user is being added to and the user entity
 *                                            that is being added to the group. 
 *
 * @return void
 */
function cv_join_group($event, $type, $params)
{
    //system_message("Joining...");
    $cv_group = $params['group'];
    if (!$cv_group->cvcohort)
    {
        return;
    }
    $cv_course = $cv_group->getContainerEntity();
    $cv_user = $params['user'];
    //here we add the user to the course acl
    $result = add_user_to_access_collection($cv_user->guid, $cv_course->cv_acl);
    //system_message ("$result");
    system_message("$cv_user->name has just joined the cohort: $cv_group->name");
}

/**
 * Runs whenever a user leaves a group.  If that group is actually a cvcohort then we need to remove the users guid from the 
 * acl of the cvcourse which is the container of that particluar cvcohort
 *
 * @param array $event  - not used
 * @param array  $type - not used
 * @param array $params - the params array contains the group entity that the user is being removed from and the user entity
 *                                            that is being removed from the group. 
 *
 * @return void
 */
function cv_leave_group($event, $type, $params)
{
    $cv_group = $params['group'];
    if (!$cv_group->cvcohort)
    {
        return;
    }
    $cv_course = $cv_group->getContainerEntity();
    $cv_user = $params['user'];
    remove_user_from_access_collection($cv_user->guid, $cv_course->cv_acl);
    system_message($cv_user->name . " is no longer in the cohort: " . $cv_group->name);
}

function cv_update_group($event, $type, $params)
{
    if (get_input('remove_from_courseview') == 'remove')
    {
        $user = elgg_get_logged_in_user_entity();
        $params->cvcohort = false;
        //$params->container_guid = $user->guid;
        // $params->save();
        system_message($params->name . " is no longer a part of CourseView");
    } else
    {
        $cv_group_container_guid = elgg_get_config('cv_group_container_guid');
        if (!is_array($cv_group_container_guid))
        {
            $cv_group_container_guid = array();
        }
        if (get_input('cvcourse') > 0)
        {
            $cv_group_container_guid [$params->guid] = get_input('cvcourse');
        }
        elgg_set_config('cv_group_container_guid', $cv_group_container_guid);
    }
}

