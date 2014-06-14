<?php

define("CV_GUID", true);
define("CV_ENTITY", false);
define("CV_COUNT", true);

/**
 * Determines if the user passed is an Elgg admin
 * @param ElggUser $user The user object being inspected
 * @return boolean returns true if the user is an admin and false if not
 * */
function cv_is_admin($user)
{
    return $user->isAdmin();
}

//::TODO:Rich - Delete this code
/**
 * Returns the current user entity or guid
 *
 * @param $return_type  if set to CV_GUID then functin returns GUID
 * Else, function returns entity
 * */
//function cv_get_current_user($return_type = CV_GUID)
//{
//    if ($return_type)
//    {
//        return elgg_get_logged_in_user_guid();
//    } else
//    {
//        return elgg_get_logged_in_user_entity();
//    }
//}

/**
 * Is the user a member of the cohort?
 *
 * @param $cohort - the cohort to check that the currently logged in user belongs to
 * @return boolean
 */
function cv_user_is_member_of_cohort($cohort)
{
//    if (!isset($cohort->cvcohort)) //check to make sure that the entity passed is a cohort
//    {
//        return false;
//    }

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
 * Check to see if the group passed in is a cvcohort
 *
 * @param $group - group to check if it is a cohort
 * @return boolean
 */
function cv_is_cvcohort($group)
{
    return $group->cvcohort;
}

/**
 * Checks to see if the plugin is valid
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




//::TODO:Rich  - delete all this
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


//::TODO:Rich - Delete this
/**
 * Register all plugin hooks and event hooks
 * 
 */
//function cv_register_hooks_events_actions($base_path)
//{
//     //register page event handler
//    elgg_register_page_handler('courseview', 'courseviewPageHandler');
//    
//    /*  loop through availbable plugins and register a plugin hook for each to check if content is new since last login*/
//    $availableplugins = unserialize(elgg_get_plugin_setting('approved_subtype', 'courseview'));
//    foreach ($availableplugins as $plugin)
//    {
//        elgg_register_plugin_hook_handler('view', "object/$plugin", 'cv_new_content_intercept');
//    }
//    
//    /*  Need to intercept content creation/update so that ACL writes to allow us to add the course ACL when needed*/
//    elgg_register_plugin_hook_handler('access:collections:write', 'all', 'cv_intercept_ACL_write', 999);
//    /*  allows us to hijack the sidebar.  Each time the sidebar is about to be rendered, this hook fires so 
//        that we can add our tree menu */
//    elgg_register_plugin_hook_handler('view', 'page/elements/sidebar', 'cv_sidebar_intercept');
//    /*  intercepts every menu item that is displayed -- cv_group_buttons uses this to add CourseView info
//         in the groups listing page */
//    elgg_register_plugin_hook_handler('register', 'menu:entity', 'cv_group_buttons', 1000);
//    /*  intercepts each time elgg calls a forward.  We will use this to be able to return to the coursview 
//        tool after adding a relationship to added content */
//    elgg_register_plugin_hook_handler('forward', 'all', 'cv_forward_intercept');
//    /*  Allow profs to write to courses they don't own */
//    elgg_register_plugin_hook_handler('container_permissions_check', 'all', 'cv_can_write_to_container');
//    /* The cv_add_content_to_cohort view gets added to the bottom of each page.  This view has code in it to simply return
//     * without doing anything unless the user belongs to at least one cohort and the current view is creating or updating
//     * an approved object such as a blog, bookmark etc as chosen in the settings page. */
//    elgg_extend_view('input/form', 'courseview/cv_add_content_to_cohort', 600);
//    /* The cv_make_group_a_cohort_from_group_page adds the ability to make a group a cohort when in the group edit/new group page */
//    elgg_extend_view('groups/edit', 'courseview/cv_make_group_a_cohort_from_group_page', 600);
//
//    //intercepts calls to get url for any cvmenu objects
//    elgg_register_entity_url_handler('object', 'cvmenu', 'cv_menu_url_handler');
//
//    // creating, updating or deleting content results in us calling the cv_intercept_content_update to make or remove any
//    // relationships between the content and any menuitems deemed neccesary.
//    elgg_register_event_handler('create', 'object', 'cv_intercept_content_update');
//    elgg_register_event_handler('update', 'object', 'cv_intercept_content_update');
//    elgg_register_event_handler('delete', 'object', 'cv_intercept_content_update');
//
//    // intercept new user creation  - This has been left in for possible future expansion
//    //elgg_register_event_handler('create', 'user', 'cv_intercept_newuser');  //use this to intercept users when they are created.
//    //when a user joins a cohort, we need to add them to a acl list attached to the container course
//    //when they leave a cohort, we need to remove them.
//    elgg_register_event_handler('join', 'group', 'cv_join_group', 0);
//    elgg_register_event_handler('leave', 'group', 'cv_leave_group', 0);
//
//    //when groups are created or updated, fire this cv_update_group
//    //elgg_register_event_handler('create', 'group', 'cv_update_group', 9999);
//    //elgg_register_event_handler('update', 'group', 'cv_update_group', 9999);
//
//    elgg_extend_view('groups/add', 'courseview/test', 600);
//
//    //set up our paths and various actions 
//    elgg_register_action("cv_create_course", $base_path . '/actions/courseview/cv_create_course.php');
//    elgg_register_action("cv_content_tree", $base_path . '/actions/courseview/cv_content_tree.php');
//    elgg_register_action("cv_edit_a_course", $base_path . '/actions/courseview/cv_edit_a_course.php');
//    elgg_register_action("cv_edit_menuitem", $base_path . '/actions/courseview/cv_edit_menuitem.php');
//    elgg_register_action("cv_delete_a_cohort", $base_path . '/actions/courseview/cv_delete_a_cohort.php');
//    elgg_register_action("cv_add_a_cohort", $base_path . '/actions/courseview/cv_add_a_cohort.php');
//    elgg_register_action("cv_delete_course", $base_path . '/actions/courseview/cv_delete_course.php');
//    elgg_register_action('toggle', $base_path . '/actions/courseview/cv_toggle_courseview.php');
//    elgg_register_action('cv_menu_toggle', $base_path . '/actions/courseview/cv_toggle_courseview.php');
//    elgg_register_action('cv_add_menu_item', $base_path . '/actions/courseview/cv_add_menu_item.php');
//    elgg_register_action('cv_edit_a_cohort', $base_path . '/actions/courseview/cv_edit_a_cohort.php');
//    elgg_register_action('cv_move_prof_content', $base_path . '/actions/courseview/cv_move_prof_content.php');
//    //elgg_register_action('cv_menu_toggle', $base_path . '/actions/courseview/cv_menu_toggle.php');
//    elgg_register_action('cv_remove_cohort', $base_path . '/actions/courseview/cv_remove_cohort.php');
//    elgg_register_action('cv_admin_toggle', $base_path . '/actions/courseview/cv_admin_toggle.php');
//}



/**
 * Returns the plugins that are allowed for the current user.  Professors have a different set of available plugins
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

function cv_hp()
{

    return ElggSession::offsetGet('cv_hp');
}

function cv_is_valid_content_to_display($object)
{
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview')); //a list of approved plugins for courseview
    $validkeys = array_keys($validplugins);
    $valid_plugin = false;
    //looping through all approved plugins to ensure that this content is to be displayed

    foreach ($validkeys as $plugin)
    {
        if ($object->getSubtype() == $plugin)
        {
            $valid_plugin = true;
        }
    }
    return $valid_plugin;
}

function cv_get_menu_items_for_course($courseguid)
{
    // echo "<br>Getting menu items from relationship:". $courseguid;
    $menu = elgg_get_entities_from_relationship(array
        ('relationship_guid' => $courseguid,
        'relationship' => 'menu',
        'type' => 'object',
        'subtype' => 'cvmenu',
        'order_by_metadata' => array('name' => 'menuorder', 'direction' => 'ASC', 'as' => 'integer'),
        'limit' => 1000,
            )
    );
//    foreach($menu as $item)
//    {
//        echo ($item->name).'<br>';
//    }
    return $menu;
}

function cv_get_menu_items_for_cohort($cvcohortguid)
{
    // echo $cohortguid->get_entity(container_guid);
    $menu = cv_get_menu_items_for_course(get_entity($cvcohortguid)->container_guid);

    return $menu;
}

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

function is_valid_plugin($arg1)
{
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));
    return (array_key_exists($arg1, $validplugins));
}

function cv_get_student_menu_items_by_cohort($cvcohortguid)
{
    $menu = elgg_get_entities_from_relationship(array
        ('relationship_guid' => get_entity($cvcohortguid)->container_guid,
        'relationship' => 'menu',
        'type' => 'object',
        'subtype' => 'cvmenu',
        'order_by_metadata' => array('name' => 'menuorder', 'direction' => 'ASC', 'as' => 'integer'),
        'limit' => 1000,
        'metadata_names' => array('menutype'),
        'metadata_values' => array('student'),
            )
    );
    return $menu;
}

function cv_is_courseview_user()
{
    $cohorts = cv_get_users_cohorts();
    return sizeof($cohorts);
}

function cv_get_users_cohorts($user = null) //default of null 
{

    if (elgg_instanceof($user, "user"))
    {
        $userguid = $user->guid;
    } else
    {
        $userguid = elgg_get_logged_in_user_guid();
    }
    //echo get_entity($userguid)->name;
    //echo "searchcriteria<br>";
    $searchcriteria = array
        ('type' => 'group',
        'metadata_names' => array('cvcohort'),
        'metadata_values' => array(1),
        'limit' => false,
        'relationship' => 'member',
        'relationship_guid' => $userguid
    );

    $usersgroups = elgg_get_entities_from_relationship($searchcriteria);
//    
    //  echo 'num cohorts returned: '.sizeof($usersgroups);
//   
    return $usersgroups;
}

function cv_isprof($user)
{
    $profgroupguid = elgg_get_plugin_setting('profsgroup', 'courseview');
    $profsgroup = get_entity(elgg_get_plugin_setting('profsgroup', 'courseview'));
    if ($profsgroup == false)
    {
        return false;
    } else
    {
        return $profsgroup->isMember($user);
    }
}

function cv_is_course_owner($user, $cohort)
{
    if (!$user || !$cohort)
    {
        return 0;
    }
    $cv_course_owner = get_entity($cohort->container_guid)->getOwnerEntity();
    return ($user->guid == $cv_course_owner->guid);
}

function cv_is_cohort_owner($user, $cohort)
{
    if (!$user || !$cohort)
    {
        return 0;
    }
    $cv_cohort_owner = $cohort->getOwnerEntity();
    return ($user->guid == $cv_cohort_owner->guid);
}

function cv_get_user_courses($user)
{
    $cohorts = cv_get_users_cohorts($user);

    if (!$cohorts)
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

function cv_is_valid_plugin($arg1)
{
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));

    return (array_key_exists($arg1, $validplugins));
}

/**
 * Gets all content created in a group that does not have a relationship with a cvmenu object of CourseView
 *
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

function cv_get_content_by_menu_item($filter, $cvmenuguid, $relationship, $list = false, $sort = 'chrono', $page_size = 10)
{
    if ($sort == 'chrono')
    {
        $options = array
            ('relationship_guid' => $cvmenuguid,
            'relationship' => $relationship,
            'type' => 'object',
            'subtype' => $filter,
            'limit' => get_input('limit', $page_size),
            'count' => get_input('count', 0),
            'offset' => get_input('offset', 0),
            'list_class' => 'studentcontentitem',
            'full_view' => false,
                // 'owner_guid' => elgg_get_logged_in_user_guid ()
        );
    } else if ($sort == 'likes')
    {
        $dbprefix = elgg_get_config('dbprefix');
        $likes_metastring = get_metastring_id('likes');
        $options = array(
            'relationship_guid' => $cvmenuguid,
            'relationship' => $relationship,
            'type' => 'object',
            'list_class' => 'studentcontentitem',
            'subtype' => $filter,
            // 'container_guid' => $entity->guid,
            'annotation_names' => array('likes'),
            'selects' => array("(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid) AS likes"),
            'order_by' => 'likes DESC',
            'full_view' => false,
            'limit' => get_input('limit', $page_size),
            'count' => get_input('count', 0),
            'offset' => get_input('offset', 0),
        );
    }
    if ($filter == 'all')
    {
        unset($options['subtype']);
    }

    if ($list)
    {
        $content = elgg_list_entities_from_relationship($options);
    } else
    {
        $content = elgg_get_entities_from_relationship($options);
    }
    return $content;
}

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

//function courseview_listplugins()
//{
//    $cvobject = ElggSession::offsetGet('courseviewobject');
//    $returnvalue = 'Currently Registered Plugins:  ' . $cvobject->plugins[0];
//    return $returnvalue;
//}

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

function cv_get_all_courses($cv_count = false)
{
    $cvcourses = elgg_get_entities(array
        ('type' => 'object',
        'subtype' => array('cvcourse'),
        'limit' => false,
            )
    );
    if ($cv_count)
    {
        return sizeof($cvcourses);
    } else
    {
        return $cvcourses;
    }
}

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

function cv_get_all_cohorts()
{
    $userguid = $user->guid;
    $cvcohorts = elgg_get_entities_from_metadata(array
        ('type' => 'group',
        'metadata_names' => array('cvcohort'),
        'metadata_values' => array(true),
        'limit' => false,
            )
    );
    return $cvcohorts;
}



//function temp_cv_group_buttons($hook, $type, $return, $params)
//{
//    if (!elgg_instanceof($params['entity'], 'group'))
//    {
//        return $return;
//    }
//
//    $cv_owner = cv_is_cohort_owner(elgg_get_logged_in_user_entity(), $params['entity']);
//    if (cv_is_cvcohort($params['entity']) || $cv_owner)
//    {
//
//        if (cv_is_admin(elgg_get_logged_in_user_entity()) && cv_is_cvcohort($params['entity']))
//        {
//            $link = new ElggMenuItem('cv_group_button', 'remove link to CourseView', "ajax/view/courseview/remove_group_from_cohort?guid={$params['entity']->guid}");
//            $link->addLinkClass("cv_remove_group_from_cohort");
//            $link->addLinkClass('elgg-lightbox');
//            $return[] = $link;
//        } else
//        {
//            $link = new ElggMenuItem('cv_button', 'CV Enabled!', "");
//            $link->addLinkClass("cv_enabled");
//            $return[] = $link;
//        }
//    } else if (elgg_get_logged_in_user_entity())
//    {
//        $link = new ElggMenuItem('cv_group_button', 'link to CourseView', "ajax/view/courseview/cv_make_group_a_cohort?guid={$params['entity']->guid}");
//        $link->addLinkClass("cv_add_to_cohort");
//        $link->addLinkClass('elgg-lightbox');
//        $return[] = $link;
//    }
//    return $return;
//}




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
 * 
 * @return  returns the url for the action to load the next page
 */
function cv_menu_url_handler($menu_entity)
{
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');
    $cvmenuguid = $menu_entity->guid;
    return (elgg_get_site_url() . "courseview/cv_contentpane/$cvcohortguid/$cvmenuguid");
}


