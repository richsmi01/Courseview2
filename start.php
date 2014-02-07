<?php

/**
 * Write something profound here...
 */
elgg_register_event_handler('init', 'system', 'courseviewInit'); //call courseviewinit when the plugin initializes

function courseviewInit()
{
    elgg_register_library('elgg:courseview', elgg_get_plugins_path() . 'courseview/lib/courseview.php');
    elgg_register_library('elgg:cv_content_tree_helper_functions', elgg_get_plugins_path() . 'courseview/lib/cv_content_tree_helper_functions.php');
    elgg_register_library('elgg:cv_debug', elgg_get_plugins_path() . 'courseview/lib/cv_debug.php');
    elgg_register_simplecache_view('js/courseview/js');
    $jsurl = elgg_get_simplecache_url('js', 'courseview/js');
    elgg_register_js('cv_sidebar_js', $jsurl);
    elgg_load_library('elgg:courseview');
    elgg_load_library('elgg:cv_debug');

    //if the user is not a member of any cohorts, not a prof, and not an admin then don't bother running anything.
    if (!cv_is_courseview_user() && !cv_isprof(elgg_get_logged_in_user_entity()) && !elgg_is_admin_logged_in())
    {
        return;
    }

    //set up our link to css rulesets
    elgg_extend_view('css/elgg', 'customize_css/css', 1000);

    //register menu item to switch to CourseView
    cv_register_courseview_menu();

    // allows us to hijack the sidebar.  Each time the sidebar is about to be rendered, this hook fires so 
    // that we can add our tree menu
    elgg_register_plugin_hook_handler('view', 'page/elements/sidebar', 'cv_sidebar_intercept');

    /* allows us to intercept each time elgg calls a forward.  We will use this to be able to return to the coursview 
     * tool after adding a relationship to added content */
    elgg_register_plugin_hook_handler('forward', 'all', 'cvforwardintercept');

    /* The cv_add_content_to_cohort view gets added to the bottom of each page.  This view has code in it to simply return
     * without doing anything unless the user belongs to at least one cohort and the current view is creating or updating
     * an approved object such as a blog, bookmark etc as chosen in the settings page. */
    elgg_extend_view('input/form', 'courseview/cv_add_content_to_cohort', 600);

    //register page event handler
    elgg_register_page_handler('courseview', 'courseviewPageHandler');

    //  creating, updating or deleting content results in us calling the cv_intercept_update to make or remove any
    // relationships between the content and any menuitems deemed neccesary.
    elgg_register_event_handler('create', 'object', 'cv_intercept_update');
    elgg_register_event_handler('update', 'object', 'cv_intercept_update');
    elgg_register_event_handler('delete', 'object', 'cv_intercept_update');

    //when a user joins a cohort, we need to add them to a acl list attached to the container course
    //when they leave a cohort, we need to remove them.
    elgg_register_event_handler('join', 'group', 'cv_join_group');
    elgg_register_event_handler('leave', 'group', 'cv_leave_group');

    //Need to intercept ACL writes to allow us to add the course ACL when needed
    elgg_register_plugin_hook_handler('access:collections:write', 'all', 'cv_intercept_ACL_write', 999);

    //set up our paths and various actions 
    $base_path = dirname(__FILE__); //gives a relative path to the directory where this file exists
    elgg_register_action("cv_create_course", $base_path . '/actions/courseview/cv_create_course.php');
    elgg_register_action("cv_content_tree", $base_path . '/actions/courseview/cv_content_tree.php');
    elgg_register_action("cv_edit_a_course", $base_path . '/actions/courseview/cv_edit_a_course.php');
    elgg_register_action("cv_edit_menuitem", $base_path . '/actions/courseview/cv_edit_menuitem.php');
    elgg_register_action("cv_delete_a_cohort", $base_path . '/actions/courseview/cv_delete_a_cohort.php');
    elgg_register_action("cv_add_a_cohort", $base_path . '/actions/courseview/cv_add_a_cohort.php');
    elgg_register_action("cv_delete_course", $base_path . '/actions/courseview/cv_delete_course.php');
    elgg_register_action('toggle', $base_path . '/actions/courseview/cv_toggle_courseview.php');
    elgg_register_action('cv_add_menu_item', $base_path . '/actions/courseview/cv_add_menu_item.php'); //::TODO: what is this again???
    elgg_register_action('cv_edit_a_cohort', $base_path . '/actions/courseview/cv_edit_a_cohort.php');
    elgg_register_action('cv_move_prof_content', $base_path . '/actions/courseview/cv_move_prof_content.php');

    //push breadcrumb to allow user to return to the current courseview cv_contentpane 
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');
    $cvmenuguid = ElggSession::offsetGet('cvmenuguid');
    // elgg_push_breadcrumb('Return to CourseView', '/courseview/cv_contentpane/' . $cvcohortguid . '/' . $cvmenuguid);
}

//the method that gets called when one of the courseview urls is called.  
function courseviewPageHandler($page, $identifier)
{
    //TODO:: Matt, do I need to be more worried about gatekeeper functions etc?
    elgg_set_page_owner_guid($page[1]);   //set the page owner to the cohort and then call gatekeeper
    gatekeeper();
    $base_path = dirname(__FILE__);

    /* Since it is possible to require the current cohort and menuitem while on a non-courseview page, we push
     * this information into the session */
    ElggSession::offsetSet('cvcohortguid', $page[1]);
    ElggSession::offsetSet('cvmenuguid', $page[2]);
    set_input('params', $page);  //place the $page into params
    //if the courseview page is being displayed, then we don't need the return to courseview breadcrumb--pop it off
    //elgg_pop_breadcrumb();

    switch ($page[0])  //switching on the first parameter passed through the RESTful url
    {
        case 'cv_contentpane':    //this is the main course content page
            require "$base_path/pages/courseview/cv_contentpane.php";
            break;
        case 'courseview':   //this is the landing page when a user first clicks on coursview
            set_input("object_type", 'all');
            require "$base_path/pages/courseview/cv_contentpane.php";
            break;
        case 'cv_testing':
            require "$base_path/pages/courseview/cv_testing.php";
            break;
        case 'examine':
             require "$base_path/pages/courseview/examine.php";
            break;
        default:
            echo "courseview request for " . $page[0];
    }
    return true;
}

/**
 * When the sidebar plugin hook fires, cv_sidebar_intercept takes over and adds the course menu tree as needed
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
    //here we check to see if we are currently in courseview mode.  If we are, we hijack the sidebar for our course menu
    if (ElggSession::offsetGet('courseview'))
    {
        $returnvalue = elgg_view('courseview/cv_hijack_sidebar') . $returnvalue;
    }
    return $returnvalue;
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
    $cv_group = $params['group'];
    if (!$cv_group->cvcohort)
    {
        return;
    }
    $ia = elgg_set_ignore_access(true); // grants temporary permission overrides
        $cv_course = $cv_group->getContainerEntity();
        $cv_user = $params['user'];
        $result = add_user_to_access_collection($cv_user->guid, $cv_course->cv_acl);
    elgg_set_ignore_access($ia); // restore permissions
    echo $result;
    //exit;
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
}

/**
 * Intercepts any new content creation, content updates, or content deletions.  If the content type is one of our valid 
 * plugins (as specified in the setup page, we will loop through the treeview generated by cv_content_tree
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
    elgg_load_library('elgg:cv_debug');
    $cvmenuguid = ElggSession::offsetGet('cvmenuguid');
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');

    //fetch the list of approved plugins for courseview
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));
    $validkeys = array_keys($validplugins);
    $valid_plugin = false;
    //looping through all approved plugins to ensure that this content can be added to courseview
    foreach ($validkeys as $plugin)
    {
        if ($object->getSubtype() == $plugin)
        {
            $valid_plugin = true;
        }
    }

    // pull array of checkboxes (associated with menu items) from the cv_content_tree 
    // so that we can loop through them in order to determine what realtionships need to be added or deleted
    // Note that $menu_items is an array of Strings passed from cv_add_to_cohort_tree where each 
    // element contains three pieces of information in the format Xmenuitemguid|cohortguid where
    //  X is a + if a new relationship should be created and a - if it should be removed.
    $menu_items = get_input('menuitems');
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
            cv_debug("Adding Relationship: $guid_one, $relationship, $guid_two", "cv_intercept_update", 5);
            $z = add_entity_relationship($guid_one, $relationship, $guid_two);
            cv_debug("Added relationship", "", 5);
            cv_debug($z, "cv_intercept_update", 5);
        } else
        {
            $rel_to_delete = check_entity_relationship($guid_one, $relationship, $guid_two);
            if ($rel_to_delete)  //if the module was unchecked and there was a relationship, we need to remove the relationship
            {
                cv_debug('deleting relationship', '', 5);
                delete_relationship($rel_to_delete->id);
            }
        }
    }

    $rootdomain = elgg_get_site_url();
    $cvredirect = $rootdomain . 'courseview/cv_contentpane/' . $cvcohortguid . '/' . $cvmenuguid;
    ElggSession::offsetSet('cvredirect', $cvredirect);
}

//::TODO:  Matt, I want to better understand why I had to do this

/**
 * 
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

/**
 * 
 *
 * @param type 
 * @param type 
 * @param type 
 *
 * @return void
 */
//function intercept_ACL_read($hook, $type, $return, $params)
//{
//    //If courseview is not active, we just want to continue on as usual
//    $cv_active = ElggSession::offsetGet('courseview');
//    if (!$cv_active)
//    {
//        return $return;
//    }
//    /* We need to unregister the plugin hook handler before getting the courses or we will crash the php interpreter through
//     * recursion ;->
//     */
//    elgg_unregister_plugin_hook_handler('access:collections:read', 'all', 'intercept_ACL_read');
//
//
//    cv_debug("Entering intercept_ACL_read:: ", "", 100);
//    cv_debug($return, "", 100);
//
//    /* I'm thinking that we don't need to do this for all user courses, just the current one??? */
//
//    $user = get_user($params["user_id"]);
//    $courses = cv_get_user_courses($user);
//
//    foreach ($courses as $course)
//    {
//        if ($course->cv_acl)
//        {
//            //cv_debug("***", "", 100);
//            $return [] = (int) $course->cv_acl; //add the course acl to the acl list being returned
//            //we also have to add the user to the acl collection. --hmmm..what happens when a user leaves a cohort?
//            // add_user_to_access_collection($user->guid, $course->cv_acl);
//        }
//    }
//    cv_debug("Exiting intercept_ACL_read: ", "", 100);
//    cv_debug($return, "", 100);
//    return $return;
//}

/**
 * When the professor creates content, we want to add an option to the access dropdown
 * to allow viewing to any users who are members of any of the cohorts attached to the current
 * course.
 * 
 * We do this by intercepting the access:collections:write and adding the ACL of the course
 * object to the ACL array before returning it back to elgg.
 * 
 * The ACL array is an associative array where the key is the accessid and the value is what gets displayed 
 * in the ACCESS dropdown.
 *
 * @param type $hook
 * @param type $type
 * @param type $return
 * @param type $params
 *
 * @return void
 */
function cv_intercept_ACL_write($hook, $type, $return, $params)
{

    $user = get_user($params["user_id"]);
    $cv_active = ElggSession::offsetGet('courseview');
    if ( !$cv_active)
    {
        return $return;
    }
    
    //var_dump($return);
    
    //elgg_unregister_plugin_hook_handler('access:collections:write', 'all', 'cv_intercept_ACL_write');
    //$menu_items = get_input('menuitems');

    $cv_cohort = get_entity(ElggSession::offsetGet('cvcohortguid'));
    if ($cv_cohort)  //::TODO: this is a test to fix an error that I got:Fatal error: Call to a member function getContainerGUID() on a non-object in /home/richsmit/public_html/mod/courseview/start.php on line 374
    {
        $course = get_entity($cv_cohort->getContainerGUID());
        //if the course has been assigned a custom acl (stored in cv_acl) then add it to the list
        if ($course->cv_acl)
        {
            $return [$course->cv_acl] = 'Course: ' . $course->title;
            //var_dump ($return);
            
            $return [$cv_cohort->group_acl] = 'Cohort: ' . $cv_cohort->title;
            //var_dump ($return);
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
