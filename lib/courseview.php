<?php

define("CV_GUID", true);
define("CV_ENTITY", false);
define("CV_COUNT", true);

/**
 * Determines if the user passed is an Elgg admin               .         
 * @param ElggUser $user The user object being inspected
 * @return boolean returns true if the user is an admin and false if not
 * */
//::TODO:Rich - get rid of this
//function cv_is_admin($user)
//{
//    return $user->isAdmin();
//}

/**
 * Is the user a member of the cohort?             .
 *
 * @param $cohort - the cohort to check that the currently logged in user belongs to
 * @return boolean
 */
function cv_user_is_member_of_cohort($cohort)
{
    if (cv_is_cvcohort($cohort))  //ensure that entity passed is a cohort
    {
        $user = elgg_get_logged_in_user_entity();
        return $cohort->isMember($user);
    } else
    {
        return false;
    }
}

/**
 * Check to see if the group passed in is a cvcohort                 .
 *
 * @param $group - group to check if it is a cohort
 * @return boolean
 */
function cv_is_cvcohort($group)
{
    return $group->cvcohort;
}

/**
 * Checks to see if the plugin is valid              !
 *
 * @param $user - user
 * @param $object - object passed
 * @return boolean 
 */
function cv_is_valid_plugin_by_keys($user, $object)
{
    $validplugins = cv_get_valid_plugins($user); //fetch the list of approved plugins for courseview
    $validkeys = array_keys($validplugins);  //this returns the keys of the array
    $valid_plugin = false;
    //looping through all approved plugins to ensure that this content can be added to courseview
    //if the content subtype hasn't been approved in the settings page, then just return without doing anything
    foreach ($validkeys as $plugin)
    {
        if ($object->getSubtype() == $plugin)
        {
            $valid_plugin = true;
        }
    }
    return $valid_plugin;
}

/**
 * This function had to be written due to the problems encountered when trying to change the container_guid in a group.
 * When the professor goes to the group edit page and clicks on adding the group to CourseView, they must select a 
 * CourseView course to attach the group to.  Unfortunately, this is not easy as we have to intercept the group update event,
 * calling cv_update_group (above) to make the change.  The difficulty occurs when the actual change to the group fires the 
 * group update event recursively, causing all sorts of problems.  The way that we solved this was to not make any changes to the
 * group in the cv_update_group function but to create an associative array in this format:  group_guid=>container_guid
 * then pushing this array into the session.  Next we listen for the shutdown event (fired when the page has completely
 * loaded and is about to stop processing.  At this point, cv_shutdown_event is called.  Now we can pull that array out of 
 * session and update the group as needed.  A little bit wonky but such is Elgg...
 */
function cv_shutdown_event($event, $type, $params)
{
    $cv_group_container_guid = elgg_get_config('cv_group_container_guid');
    if (is_array($cv_group_container_guid))
    {
        foreach ($cv_group_container_guid as $group_guid => $container_guid)
        {
            $cv_group = get_entity($group_guid);
            $cv_group->container_guid = $container_guid;
            $cv_group->save();
            $cv_group->cvcohort = true;
        }
    }
}

/**
 * Returns the plugins that are allowed for the current user             .
 * Professors have a different set of available plugins
 * than users.  This way, a professor may be allowed certain plugins while limiting plugins to the students
 *
 * @param array $user  - the user that is being checked
 * @return array of valid plugins
 */
function cv_get_valid_plugins($user)
{
    if (cv_isprof($user))
    {
        $validplugins = unserialize(elgg_get_plugin_setting('profavailableplugins', 'courseview'));  //pulled from settings page
    } else
    {
        $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));
    }
    return$validplugins;
}

//TODO:Rich - remove this hp stuff
//function cv_hp()
//{
//    return ElggSession::offsetGet('cv_hp');
//}

/**
 * Checks to see if the passed object is authorized for viewing withing Courseview           .
 * @param type $object
 * @return boolean
 */
//function cv_is_valid_content_to_display($object)
//{
//    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview')); //a list of approved plugins for courseview
//    $validkeys = array_keys($validplugins);
//    $valid_plugin = false;
//    //looping through all approved plugins to ensure that this content is to be displayed
//    foreach ($validkeys as $plugin)
//    {
//        if ($object->getSubtype() == $plugin)
//        {
//            $valid_plugin = true;
//        }
//    }
//    return $valid_plugin;
//}

/**
 * Returns an array of cvmenu items for a given course
 * @param type $courseguid
 * @return type
 */
function cv_get_menu_items_for_course($courseguid)
{
    $menu = elgg_get_entities_from_relationship(array
        ('relationship_guid' => $courseguid,
        'relationship' => 'menu',
        'type' => 'object',
        'subtype' => 'cvmenu',
        'order_by_metadata' => array('name' => 'menuorder', 'direction' => 'ASC', 'as' => 'integer'),
        'limit' => 1000,
            )
    );
    return $menu;
}

/**
 * Returns an array of cvmenu items for a given cohort
 * @param type $cvcohortguid
 * @return type
 */
function cv_get_menu_items_for_cohort($cvcohortguid)
{
    $menu = cv_get_menu_items_for_course(get_entity($cvcohortguid)->container_guid);
    return $menu;
}

/**
 * Returns all cohorts that are associated with a given cvcourse          .
 * @param type $courseguid
 * @return type
 */
function cv_get_cohorts_by_courseguid($courseguid)
{
    $options = array
        ('type' => 'group',
        'metadata_names' => array('cvcohort'),
        'metadata_values' => array(1),
        'limit' => false,
        'container_guid' => $courseguid,
    );

    $value = elgg_get_entities_from_metadata($options);
    return $value;
}

/**
 * Determines whether or not the given plugin is valid to be viewed in CourseView
 * @param type $plugin
 * @return type
 */
function is_valid_plugin($cvplugin)
{
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));
    return (array_key_exists($cvplugin, $validplugins));
}

/**
 * Returns an array of all cvmenu items that have a menutype of student
 * @param type $cvcohortguid
 * @return type
 */
//function cv_get_student_menu_items_by_cohort($cvcohortguid)
//{
//    $menu = elgg_get_entities_from_relationship(array
//        ('relationship_guid' => get_entity($cvcohortguid)->container_guid,
//        'relationship' => 'menu',
//        'type' => 'object',
//        'subtype' => 'cvmenu',
//        'order_by_metadata' => array('name' => 'menuorder', 'direction' => 'ASC', 'as' => 'integer'),
//        'limit' => 1000,
//        'metadata_names' => array('menutype'),
//        'metadata_values' => array('student'),
//            )
//    );
//    return $menu;
//}

/**
 * Determines whether a user has any cohorts in CourseView        .
 *   If the user has no cohorts, they are not a CourseView user and a 0 is returned.
 * @return type
 */
function cv_is_courseview_user()
{
    $cohorts = cv_get_users_cohorts();
    return sizeof($cohorts);
}

/**
 * Returns an array of a user's cohorts        .
 * @param type $user
 * @return array of user's cohorts
 */
function cv_get_users_cohorts($user = null) //default of null 
{
    if (elgg_instanceof($user, "user"))
    {
        $userguid = $user->guid;
    } else
    {
        $userguid = elgg_get_logged_in_user_guid();
    }
    $searchcriteria = array
        ('type' => 'group',
        'metadata_names' => array('cvcohort'),
        'metadata_values' => array(1),
        'limit' => false,
        'relationship' => 'member',
        'relationship_guid' => $userguid
    );
    $usersgroups = elgg_get_entities_from_relationship($searchcriteria);
    return $usersgroups;
}

/**
 * Determines if the user is a prof (by checking to see if the user is in the prof group      .
 * @param type $user
 * @return boolean
 */
function cv_isprof($user)
{
    $profgroupguid = elgg_get_plugin_setting('profsgroup', 'courseview');//from the settings page
    $profsgroup = get_entity($profgroupguid); 
    if ($profsgroup == false)
    {
        return false;
    } else
    {
        return $profsgroup->isMember($user);
    }
}

/**
 * Returns true if the user is the owner of the course
 * @param type $user
 * @param type $cohort
 * @return boolean
 */
function cv_is_course_owner($user, $cohort)
{
    if (!$user || !$cohort)
    {
        return false;  //0; ::TODO:Rich delete this 0
    }
    $cv_course_owner = get_entity($cohort->container_guid)->getOwnerEntity();
    return ($user->guid == $cv_course_owner->guid);
}

/**
 * Returns true if the user is the owner of the cohort
 * @param type $user
 * @param type $cohort
 * @return boolean
 */
function cv_is_cohort_owner($user, $cohort)
{
    if (!$user || !$cohort)
    {
        return false;
    }
    $cv_cohort_owner = $cohort->getOwnerEntity();
    return ($user->guid == $cv_cohort_owner->guid);
}

/**
 * returns an array of all courses that a user is taking
 * @param type $user
 * @return array
 */
function cv_get_user_courses($user)
{
    $cohorts = cv_get_users_cohorts($user);
    if (!$cohorts) //If no cohorts then return empty array
    {
        return array();
    }
    $courses = array();
    foreach ($cohorts as $cohort)
    {
        $cvcourse = get_entity($cohort->getContainerGUID());
        if (!$cvcourse->cv_acl)
        {
            $id = create_access_collection("cv_id", $cvcourse->guid);
            $cvcourse->cv_acl = $id;
            $cvcourse->save();
        }
        $courses[$cohort->getContainerGUID()] = $cvcourse;  //placeholder value
    }
    return $courses;
}

/**
 * Returns an array of all courses owned by a professor
 * @param type $user
 * @return array
 */
function cv_get_prof_owned_courses($user)
{
    $searchcriteria = array
        ('type' => 'object',
        'owner_guid' => $user->guid,
        'metadata_names' => array('cvcourse'),
        'metadata_values' => array(1),
        //'subtype' => 'cvcourse',
        'limit' => false,
    );
    $ownedcourses = elgg_get_entities_from_relationship($searchcriteria);
    return $ownedcourses;
}

/**
 * Returns true if the the plugin passed is authorized for CourseView
 * @param type $arg1
 * @return type
 */
function cv_is_valid_plugin($plugin)
{
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));

    return (array_key_exists($plugin, $validplugins));
}

/**
 * Gets all content created in a group that does not have a relationship with a cvmenu object of CourseView
 * @return a list of Elgg objects that don't have a menu relationship
 */
function cv_get_content_not_assigned()
{
    $cohort_guid = ElggSession::offsetGet('cvcohortguid');
    $db_prefix = elgg_get_config('db_prefix');
    $relationship = 'menu';
    $options = array
        (
        'type' => 'object',
        'container_guid' => $cohort_guid,
        'limit' => get_input('limit', $page_size),
        'count' => get_input('count', 0),
        'offset' => get_input('offset', 0),
        'list_class' => 'studentcontentitem',
        'full_view' => false,
        'wheres' => array("NOT EXISTS ( SELECT 1 FROM {$db_prefix}elgg_entity_relationships WHERE guid_one = e.guid AND relationship = '$relationship')"),
    );
    $content_objects = elgg_list_entities($options);
    return $content_objects;
}

/**
 * Returns an array of all content that has been assigne a relationship with the current cvmenu item
 * @param array $args
 * @return type
 */
function cv_get_content_by_menu_item1(array $args)
{
    $defaults = array('filter' => 'hello', 'cvmenuguid' => ' ', 'relationship' => '',
        'list' => false, 'sort' => 'chrono', 'page_size' => 10,
        'only_current_user' => true);

    if ($args['sort'] == 'chrono')
    {
        $options = array
            ('relationship_guid' => $args['cvmenuguid'],
            'relationship' => $args['relationship'],
            'type' => 'object',
            'subtype' => $args['filter'],
            'limit' => get_input('limit', $args['page_size']),
            'count' => get_input('count', 0),
            'offset' => get_input('offset', 0),
            'list_class' => 'studentcontentitem',
            'full_view' => false,
        );
    } else if ($args['sort'] == 'likes')
    {
        $dbprefix = elgg_get_config('dbprefix');
        $likes_metastring = get_metastring_id('likes');
        $options = array(
            'relationship_guid' => $args['cvmenuguid'],
            'relationship' => $args['relationship'],
            'type' => 'object',
            'list_class' => 'studentcontentitem',
            'subtype' => $args['filter'],
            // 'container_guid' => $entity->guid,
            'annotation_names' => array('likes'),
            'selects' => array("(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid) AS likes"),
            'order_by' => 'likes DESC',
            'full_view' => false,
            'limit' => get_input('limit', $args['page_size']),
            'count' => get_input('count', 0),
            'offset' => get_input('offset', 0),
        );
    }
    if ($args[filter] == 'all')
    {
        unset($options['subtype']);
    }
    if ($args['only_current_user'])
    {
        $options['owner_guid'] = elgg_get_logged_in_user_guid();
    }
    if ($args['list'])
    {
        $content = elgg_list_entities_from_relationship($options);
    } else
    {
        $content = elgg_get_entities_from_relationship($options);
    }
    return $content;
}


//function cv_get_content_by_menu_item($filter, $cvmenuguid, $relationship, $list = false, $sort = 'chrono', $page_size = 10)
//{
//    if ($sort == 'chrono')
//    {
//        $options = array
//            ('relationship_guid' => $cvmenuguid,
//            'relationship' => $relationship,
//            'type' => 'object',
//            'subtype' => $filter,
//            'limit' => get_input('limit', $page_size),
//            'count' => get_input('count', 0),
//            'offset' => get_input('offset', 0),
//            'list_class' => 'studentcontentitem',
//            'full_view' => false,
//                // 'owner_guid' => elgg_get_logged_in_user_guid ()
//        );
//    } else if ($sort == 'likes')
//    {
//        $dbprefix = elgg_get_config('dbprefix');
//        $likes_metastring = get_metastring_id('likes');
//        $options = array(
//            'relationship_guid' => $cvmenuguid,
//            'relationship' => $relationship,
//            'type' => 'object',
//            'list_class' => 'studentcontentitem',
//            'subtype' => $filter,
//            // 'container_guid' => $entity->guid,
//            'annotation_names' => array('likes'),
//            'selects' => array("(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid) AS likes"),
//            'order_by' => 'likes DESC',
//            'full_view' => false,
//            'limit' => get_input('limit', $page_size),
//            'count' => get_input('count', 0),
//            'offset' => get_input('offset', 0),
//        );
//    }
//    if ($filter == 'all')
//    {
//        unset($options['subtype']);
//    }
//
//    if ($list)
//    {
//        $content = elgg_list_entities_from_relationship($options);
//    } else
//    {
//        $content = elgg_get_entities_from_relationship($options);
//    }
//    return $content;
//}

/**
 * Returns true if the current page is cv_contentpane
 * @return boolean
 */
function cv_is_list_page()
{
    $cv_url = current_page_url();
    if (strpos($cv_url, 'cv_contentpane') !== false)
    {
        return true;
    } else
    {
        return false;
    }
}

/**
 * Returns an array of all courses owned by the user passed in     .
 * @param type $user
 * @return type
 */
function cv_get_owned_courses($user)
{
    $userguid = $user->guid;
    $cvcourses = elgg_get_entities_from_relationship(array
        ('type' => 'object',
        'metadata_names' => array('cvcourse'),
        'metadata_values' => array(true),
        'limit' => false,
        'owner_guids' => $userguid,
            )
    );
    return $cvcourses;
}

/**
 * Returns the number of courses that are owned by the professor passed to it     .
 * @param type $user
 * @return int
 */
function cv_prof_num_courses_owned($user)
{
    $userguid = $user->guid;
    $cvcourses = elgg_get_entities_from_relationship(array
        ('type' => 'object',
        'metadata_names' => array('cvcourse'),
        'metadata_values' => array(true),
        'limit' => false,
        'owner_guids' => $userguid,
            )
    );
    return sizeof($cvcourses);
}

/**
 * Return all cohorts owned by the user passed in       .
 * @param type $user
 * @return type
 */
function cv_get_owned_cohorts($user)
{
    $userguid = $user->guid;
    $cvcohorts = elgg_get_entities_from_metadata(array
        ('type' => 'group',
        'metadata_names' => array('cvcohort'),
        'metadata_values' => array(true),
        'limit' => false,
        'owner_guids' => $userguid,
            )
    );
    return $cvcohorts;
}



/**
 * If the settings page is set to display the CourseView menu item on the main menu,
 * add the menu item with appropriate text
 * When the CourseView menu item is clicked, set the CourseView session variable to true or false and set the
 * menu title accordingly 
 *
 * @return void
 */
function cv_register_courseview_menu()
{
    $status = ElggSession::offsetGet('courseview');
    $show_menu = elgg_get_plugin_setting('show_courseview_site_activation', 'courseview');
    if ($show_menu == 0)
    {
        return;
    }
    if ($status)
    {
        $menutext = "Exit CourseView";
    } else
    {
        $menutext = "CourseView";
    }
    $item = new ElggMenuItem('courseview', $menutext, elgg_add_action_tokens_to_url('action/toggle'));
    elgg_register_menu_item('site', $item);
}

//::TODO:Rich - Need to take this out for Masters fork - This may be used by me in a later fork
function myplugin_sitemenu($hook, $type, $return, $params)
{
    $item = new ElggMenuItem('courseview', 'Upgrade to Premium Memebership!', elgg_add_action_tokens_to_url('action/upgrade'));
    $returnValue = array();
    $returnValue[0] = $item;
    return $returnValue;
}

/**
 * This elgg_register_entity_url_handler builds the url for when the method getURL is called on a menu_entity
 * Had to do this because we may wish to delete a menu item.  If we do this, we want the previous menu item to become
 * the 'active' item.
 *
 * @param $menu_entity - a menu object 
 * @return  returns the url for the action to load the next page
 */
function cv_menu_url_handler($menu_entity)
{
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');
    $cvmenuguid = $menu_entity->guid;
    return (elgg_get_site_url() . "courseview/cv_contentpane/$cvcohortguid/$cvmenuguid");
}


