<?php

define("CV_GUID", true);
define("CV_ENTITY", false);
define ("CV_COUNT", true);

/**
 * Determines if the user passed is an Elgg admin
 * @param ElggUser $user The user object being inspected
 * @return boolean returns true if the user is an admin and false if not
 * */
function cv_is_admin($user)
{
    return $user->isAdmin();
}

/**
 * Returns the current user entity or guid
 *
 * @param $return_type  if set to CV_GUID then functin returns GUID
 * Else, function returns entity
 * */
function cv_get_current_user($return_type = CV_GUID)
{
    if ($return_type)
    {
        return elgg_get_logged_in_user_guid();
    } else
    {
        return elgg_get_logged_in_user_entity();
    }
}

/**
 * Is the user a member of the cohort?
 *
 * @param $cohort - the cohort to check that the currently logged in user belongs to
 * @return boolean
 */
function cv_user_is_member_of_cohort($cohort)
{
    if (!isset($cohort->cvcohort)) //check to make sure that the entity passed is a cohort
    {
        return false;
    }
    $user = elgg_get_logged_in_user_entity();

    if (cv_is_cvcohort($cohort))
    {
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
//    
}

function cv_is_valid_plugin_by_keys($user, $object)
{
    $validplugins = cv_get_valid_plugins($user); //fetch the list of approved plugins for courseview
    $validkeys = array_keys($validplugins);
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

function cv_update_group($event, $type, $params)
{
    if (get_input('remove_from_courseview') == 'remove')
    {
        $user = elgg_get_logged_in_user_entity();
        $params->cvcohort = false;
        //$params->container_guid = $user->guid;
       // $params->save();
        system_message($params->name." is no longer a part of CourseView");
    } 
    else
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

/**
 * This function had to be written due to the problems encountered when trying to change the container_guid in a group.
 * When the professor goes to the group edit page and clicks on adding the group to CourseView, they must select a 
 * CourseView course to attach the group to.  Unfortunately, this is not easy as we have to intercept the group update event,
 * calling cv_update_group (above) to make the change.  The difficulty occurs when the actual change to the group fires the 
 * group update event recursively, causing all sorts of problems.  The way that we solved this was to not make any changes to the
 * group in the cv_update_group function but to create an associative array in this format:  group_guid=>container_guid
 * then pushing this array into the session.  Next we had to listen for the shutdown event (fired when the page has completely
 * loaded and is about to stop processing.  At this point, cv_shutdown_event is called.  It's job is to pull that array out of 
 * session and update the group.  A little bit wonky but such is Elgg.
 */
function cv_shutdown_event()
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
 * Register all plugin hooks and event hooks
 * 
 */
function cv_register_hooks_events_actions($base_path)
{
//loop through availbable plugins and register a plugin hook for each to check if content is new since last login
    $availableplugins = unserialize(elgg_get_plugin_setting('approved_subtype', 'courseview'));
    foreach ($availableplugins as $plugin)
    {
        elgg_register_plugin_hook_handler('view', "object/$plugin", 'cv_new_content_intercept');
    }
    
    //page is about finish processing...want to update any group with correct course if changed
    elgg_register_event_handler('shutdown', 'system', 'cv_shutdown_event');  
    
    // allows us to hijack the sidebar.  Each time the sidebar is about to be rendered, this hook fires so 
    // that we can add our tree menu
    elgg_register_plugin_hook_handler('view', 'page/elements/sidebar', 'cv_sidebar_intercept');
    
    //::TODO:Matt - intercepts every menu item that is displayed -- cv_group_buttons uses this to add CourseView info
    //in the groups listing page
    elgg_register_plugin_hook_handler('register', 'menu:entity', 'cv_group_buttons', 1000);

    /*::TODO:Matt -  Why did we do this again???  intercepts each time elgg calls a forward.  We will use this to be able to return to the coursview 
     * tool after adding a relationship to added content */
    elgg_register_plugin_hook_handler('forward', 'all', 'cvforwardintercept');

    //::TODO:Matt - Why??  Need to occasionally intercept permissions check to ensure that people in different cohorts can see each others content
    elgg_register_plugin_hook_handler('container_permissions_check', 'all', 'cv_can_write_to_container');

    /* The cv_add_content_to_cohort view gets added to the bottom of each page.  This view has code in it to simply return
     * without doing anything unless the user belongs to at least one cohort and the current view is creating or updating
     * an approved object such as a blog, bookmark etc as chosen in the settings page. */
    elgg_extend_view('input/form', 'courseview/cv_add_content_to_cohort', 600);

    /*The cv_make_group_a_cohort_from_group_page adds the ability to make a group a cohort when in the group edit/new group page*/
    elgg_extend_view('groups/edit', 'courseview/cv_make_group_a_cohort_from_group_page', 600);
    
    //::TODO:Matt - How do I word this?
    elgg_register_entity_url_handler('object', 'cvmenu', 'cv_menu_url_handler');

    //::TODO:Matt - register page event handler
    elgg_register_page_handler('courseview', 'courseviewPageHandler');

    // creating, updating or deleting content results in us calling the cv_intercept_update to make or remove any
    // relationships between the content and any menuitems deemed neccesary.
    elgg_register_event_handler('create', 'object', 'cv_intercept_update');
    elgg_register_event_handler('update', 'object', 'cv_intercept_update');
    elgg_register_event_handler('delete', 'object', 'cv_intercept_update');

    // intercept new user creation  
    //elgg_register_event_handler('create', 'user', 'cv_intercept_newuser');  //use this to intercept users when they are created.
 
    //when a user joins a cohort, we need to add them to a acl list attached to the container course
    //when they leave a cohort, we need to remove them.
    elgg_register_event_handler('join', 'group', 'cv_join_group',0);
    elgg_register_event_handler('leave', 'group', 'cv_leave_group',0);

    elgg_register_event_handler('create', 'group', 'cv_update_group', 9999);
    elgg_register_event_handler('update', 'group', 'cv_update_group', 9999);

    //Need to intercept ACL writes to allow us to add the course ACL when needed
    elgg_register_plugin_hook_handler('access:collections:write', 'all', 'cv_intercept_ACL_write', 999);

    elgg_extend_view('groups/add', 'courseview/test', 600);
   
    //set up our paths and various actions 
    elgg_register_action("cv_create_course", $base_path . '/actions/courseview/cv_create_course.php');
    elgg_register_action("cv_content_tree", $base_path . '/actions/courseview/cv_content_tree.php');
    elgg_register_action("cv_edit_a_course", $base_path . '/actions/courseview/cv_edit_a_course.php');
    elgg_register_action("cv_edit_menuitem", $base_path . '/actions/courseview/cv_edit_menuitem.php');
    elgg_register_action("cv_delete_a_cohort", $base_path . '/actions/courseview/cv_delete_a_cohort.php');
    elgg_register_action("cv_add_a_cohort", $base_path . '/actions/courseview/cv_add_a_cohort.php');
    elgg_register_action("cv_delete_course", $base_path . '/actions/courseview/cv_delete_course.php');
    elgg_register_action('toggle', $base_path . '/actions/courseview/cv_toggle_courseview.php');
    elgg_register_action('cv_menu_toggle', $base_path . '/actions/courseview/cv_toggle_courseview.php');
    elgg_register_action('cv_add_menu_item', $base_path . '/actions/courseview/cv_add_menu_item.php');
    elgg_register_action('cv_edit_a_cohort', $base_path . '/actions/courseview/cv_edit_a_cohort.php');
    elgg_register_action('cv_move_prof_content', $base_path . '/actions/courseview/cv_move_prof_content.php');
    //elgg_register_action('cv_menu_toggle', $base_path . '/actions/courseview/cv_menu_toggle.php');
    elgg_register_action('cv_remove_cohort', $base_path . '/actions/courseview/cv_remove_cohort.php');
    elgg_register_action('cv_admin_toggle', $base_path . '/actions/courseview/cv_admin_toggle.php');
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
    system_message ("Joining group, I hope");
    //exit;
    $cv_group = $params['group'];
    //if the group isn't a cvcohort, then just return without doing anything
    if (!$cv_group->cvcohort)
    {
        return;
    }
    $ignore_acess = elgg_set_ignore_access(true); // grants temporary permission overrides
    $cv_course = $cv_group->getContainerEntity();
    $cv_user = $params['user'];
    //here we add the user to the course acl
    $result = add_user_to_access_collection($cv_user->guid, $cv_course->cv_acl);
    elgg_set_ignore_access($ignore_acess); // restore permissions
    system_message ("$cv_user->name just joined $cv_group->name");
    exit;
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
     system_message ("Leaving the group, I hope");
    $cv_group = $params['group'];
    if (!$cv_group->cvcohort)
    {
        return;
    }
    $cv_course = $cv_group->getContainerEntity();
    $cv_user = $params['user'];
    remove_user_from_access_collection($cv_user->guid, $cv_course->cv_acl);
}
function cv_get_valid_plugins($user)
{
    if (cv_isprof($user))
    {
        $validplugins = unserialize(elgg_get_plugin_setting('profavailableplugins', 'courseview'));
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

function courseview_listplugins()
{
    // echo 'Got here!!!';
    $cvobject = ElggSession::offsetGet('courseviewobject');
    $returnvalue = 'Currently Registered Plugins:  ' . $cvobject->plugins[0];
//echo $returnvalue;
    return $returnvalue;
}

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

function cv_get_all_courses($cv_count=false)
{
    $cvcourses = elgg_get_entities(array
        ('type' => 'object',
        'subtype' => array('cvcourse'),
        'limit' => false,
            )
    );
    if($cv_count)
    {
        return sizeof($cvcourses);
    }
    else
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

function cv_group_buttons($hook, $type, $return, $params)
{
    if (!elgg_instanceof($params['entity'], 'group'))
    {
        return $return;
    }
    $is_cv_owner = cv_is_cohort_owner(elgg_get_logged_in_user_entity(), $params['entity']);
    $is_cv_admin = cv_is_admin(elgg_get_logged_in_user_entity());
    if ($is_cv_admin || $is_cv_owner)
    {
        if (cv_is_cvcohort($params['entity']))
        {
            $link = new ElggMenuItem('cv_group_button', 'remove link to CourseView', "ajax/view/courseview/remove_group_from_cohort?guid={$params['entity']->guid}");
            $link->addLinkClass("cv_remove_group_from_cohort");
            $link->addLinkClass('elgg-lightbox');
            $return[] = $link;
        } else
        {
            $link = new ElggMenuItem('cv_group_button', 'link to CourseView', "ajax/view/courseview/cv_make_group_a_cohort?guid={$params['entity']->guid}");
            $link->addLinkClass("cv_add_to_cohort");
            $link->addLinkClass('elgg-lightbox');
            $return[] = $link;
        }
    } else if (cv_is_cvcohort($params['entity']))
    {
        $link = new ElggMenuItem('cv_button', 'CV Enabled!', "");
        $link->addLinkClass("cv_enabled");
        $return[] = $link;
    }
    return $return;
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
 * When the sidebar plugin hook fires, cv_sidebar_intercept takes over and adds dropdowns and 
 * the course menu tree as needed
 *
 * @param string $hook  not actually needed or used
 * @param string $entity_type  not actually needed or used
 * @param string $returnvalue  If CourseView is enabled, the tree menu is added to the $returnvalue and returned to elgg
 * @param string $entity_type  not actually needed or used
 *
 * @return Returns the value of of $returnvalue that was passed by the hook.  This value may now have our tree view menu in it
 */
function cv_sidebar_intercept($hook, $entity_type, $returnvalue, $params)
{
    echo elgg_view_form('cv_admin_toggle');
    $show_elgg_stuff = elgg_get_plugin_setting('show_elgg_stuff', 'courseview');

    if ($show_elgg_stuff == 0 && cv_is_cvcohort(page_owner_entity()))  //if don't show elgg stuff is selected in settings
    {
        $returnvalue = "";
    }
    $menu_visibility = elgg_get_plugin_setting('menu_visibility', 'courseview');
    $user_is_member_of_cohort = cv_user_is_member_of_cohort(page_owner_entity());
    //here we check to see if we are currently in courseview mode.  If we are, we hijack the sidebar for our course menu
    //if ((ElggSession::offsetGet('courseview') && $menu_visibility == 'always') || cv_is_cvcohort(page_owner_entity()))
    if ((ElggSession::offsetGet('courseview') && $menu_visibility == 'always') || $user_is_member_of_cohort)
    {
        $returnvalue = elgg_view('courseview/cv_hijack_sidebar') . $returnvalue;
    }
    return $returnvalue;
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
function cv_intercept_update($event, $type, $object)
{
    //elgg_load_library('elgg:cv_debug');
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
                // cv_debug("Adding Relationship: $guid_one, $relationship, $guid_two", "cv_intercept_update", 5);
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
 * 
 * @param type 
 * @param type 
 * @param type 
 *
 * @return void
 */
function cvforwardintercept($hook, $type, $return, $params)
{
    $cvredirect = ElggSession::offsetGet('cvredirect');
    if (!empty($cvredirect))
    {
        $return = ElggSession::offsetGet('cvredirect');
        ElggSession::offsetUnset('cvredirect');
    }
    return $return;
}

function cv_intercept_ACL_write($hook, $type, $return, $params)
{
    $user = get_user($params["user_id"]);
    $cv_active = ElggSession::offsetGet('courseview');
    if (!$cv_active)
    {
        return $return;
    }

    $cv_cohort = get_entity(ElggSession::offsetGet('cvcohortguid'));
    if ($cv_cohort)
    {
        $course = get_entity($cv_cohort->getContainerGUID());
        //if the course has been assigned a custom acl (stored in cv_acl) then add it to the list
        if ($course->cv_acl)
        {
            $return [$course->cv_acl] = 'Course: ' . $course->title;
            $return [$cv_cohort->group_acl] = 'Cohort: ' . $cv_cohort->name;
        }
    }

    return $return;
}

/**
 * When the CourseView menu item is clicked, set the CourseView session variable to true or false and set the
 * menu title accordingly
 *
 * @param type 
 * @param type 
 * @param type 
 *
 * @return void
 */
function cv_register_courseview_menu()
{
    $status = ElggSession::offsetGet('courseview');
    $show_menu = elgg_get_plugin_setting('show_courseview_site_activation', 'courseview');
    //echo "show_menu".$show_menu;
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

function myplugin_sitemenu($hook, $type, $return, $params)
{
    $item = new ElggMenuItem('courseview', 'Upgrade to Premium Memebership!', elgg_add_action_tokens_to_url('action/upgrade'));
    $returnValue = array();
    $returnValue[0] = $item;
    return $returnValue;
}

//function cv_create_group($hook, $type, $return, $params)
//{
//    var_dump($params);
//    exit;
//    $return .="###########";
//    return $return;
//}

//adds green NEW icon to new content
function cv_new_content_intercept($hook, $type, $return, $params)
{
    $vars = $params['vars'];
    $user = elgg_get_logged_in_user_entity();
    $attributes = $vars->get_attributes;
    $entity = $vars['entity'];
    $show_new_content = elgg_get_plugin_setting('flag_new_content', 'courseview');
    if ($entity->last_action > $user->prev_last_login && $vars['full_view'] == false && $show_new_content)//   && !in_array($entity->guid, $visited))
    {
        $return = "<div class='newContent'>New!</div>" . $return;
    }
    return $return;
}

function cv_intercept_newuser($event, $type, $params)
{
    
}

/**
 * This elgg_register_entity_url_handler builds the url for when an action occurs on an object with a cvmenu subtype
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

/**
 * This plugin hook intercepts when elgg checks to see if it can write to a container.
 * In the case when we are trying to allow professorA to create a cohort from a course
 * created and owned by professorB, we need to allow the course to be the container
 * for the cohort.  We first check to make sure that the container has a cvcourse subtype
 * and then we check to make sure that the user is a professor.  If both of these things
 * are true, we can go ahead and override.
 *
 * @param $hook 
 * @param $type 
 * @param $params - contains our container and user info
 *
 * @return  returns true if conditions are met and the default $return if they are not
 */
function cv_can_write_to_container($hook, $type, $return, $params)
{
    if (elgg_instanceof($params['container'], 'object', 'cvcourse'))
    {
        if (cv_isprof($params['user']))
        {
            return true;
        }
    }
    return $return;
}
