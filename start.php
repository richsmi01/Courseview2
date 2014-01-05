<?php

/**
 * Write something profound here...
 */
elgg_register_event_handler('init', 'system', 'courseviewInit'); //call courseviewinit when the plugin initializes

function courseviewInit()
{
    //register libraries:
    elgg_register_library('elgg:courseview', elgg_get_plugins_path() . 'courseview/lib/courseview.php');
    elgg_register_library('elgg:cv_debug', elgg_get_plugins_path() . 'courseview/lib/cv_debug.php');
    elgg_load_library('elgg:courseview');
    elgg_load_library('elgg:cv_debug');

    //if the user is not a member of any cohorts, not a prof, and not an admin then don't bother running anything.
    if (!cv_is_courseview_user() && !cv_isprof(elgg_get_logged_in_user_entity()) && !elgg_is_admin_logged_in())
    {
        return;
    }

    //set up our link to css rulesets
    elgg_extend_view('css/elgg', 'courseview/css', 1000);

    //register menu item to switch to CourseView
    register_courseview_menu();


    // allows us to hijack the sidebar.  Each time the sidebar is about to be rendered, this hook fires so that we can add our menu
    elgg_register_plugin_hook_handler('view', 'page/elements/sidebar', 'cvsidebarintercept');

    /* allows us to intercept each time elgg calls a forward.  We will use this to be able to return to the coursview 
     * tool after adding a relationship to added content
     */
    elgg_register_plugin_hook_handler('forward', 'all', 'cvforwardintercept');

    /* The addcontenttocohort view gets added to the bottom of each page.  This view has code in it to simply return
     * without doing anything unless the user belongs to at least one cohort and the current view is creating or updating
     * an approved object such as a blog, bookmark etc as chosen in the settings page.
     */
    elgg_extend_view('input/form', 'courseview/addcontenttocohort', 600);

    //register page event handler
    elgg_register_page_handler('courseview', 'courseviewPageHandler');

    /* both creating and updating content results in us calling the cv_intercept_update to make or remove any
     * relationships between the content and any menuitems deemed neccesary.
     */
    elgg_register_event_handler('create', 'object', 'cv_intercept_update');
    elgg_register_event_handler('update', 'object', 'cv_intercept_update');

    //this is an experiment to intercept the database call and make changes to the ACLs as needed
    elgg_register_plugin_hook_handler('access:collections:read', 'all', 'intercept_ACL_read', 999);
    elgg_register_plugin_hook_handler('access:collections:write', 'all', 'intercept_ACL_write', 999);


//set up our paths and various actions 
    $base_path = dirname(__FILE__); //gives a relative path to the directory where this file exists

    elgg_register_action("createcourse", $base_path . '/actions/courseview/createcourse.php');
    elgg_register_action("cv_content_tree", $base_path . '/actions/courseview/cv_content_tree.php');
    elgg_register_action("cveditacourse", $base_path . '/actions/courseview/cveditacourse.php');
    elgg_register_action("editmenuitem", $base_path . '/actions/courseview/editmenuitem.php');
    elgg_register_action("deleteacohort", $base_path . '/actions/courseview/deleteacohort.php');
    elgg_register_action("addacohort", $base_path . '/actions/courseview/addacohort.php');
    elgg_register_action("deletecourse", $base_path . '/actions/courseview/deletecourse.php');
    elgg_register_action('toggle', $base_path . '/actions/courseview/togglecourseview.php');
    elgg_register_action('addmenuitem', $base_path . '/actions/courseview/addmenuitem.php'); //::TODO: what is this again???
    elgg_register_action('editacohort', $base_path . '/actions/courseview/editacohort.php');
}

//the method that gets called when one of the courseview urls is called.  
function courseviewPageHandler($page, $identifier)
{
    //TODO:: Matt, do I need to be more worried about gatekeeper functions etc?
    elgg_set_page_owner_guid($page[1]);   //set the page owner to the cohort and then call gatekeeper
    group_gatekeeper();
    $base_path = dirname(__FILE__);

    /* Since it is possible to require the current cohort and menuitem while on a non-courseview page, we push
     * this information into the session
     */
    ElggSession::offsetSet('cvcohortguid', $page[1]);
    ElggSession::offsetSet('cvmenuguid', $page[2]);
    set_input ('params',$page);

    //$base_path = elgg_get_plugins_path() . 'courseview/pages/courseview';

    switch ($page[0])  //switching on the first parameter passed through the RESTful url
    {
        case 'contentpane':    //this is the main course content page
            // cv_debug("contentpane...","", 100);

            require "$base_path/pages/courseview/contentpane.php";
            break;
        case 'courseview':   //this is the landing page when a user first clicks on coursview
            set_input("object_type", 'all');
            require "$base_path/pages/courseview/contentpane.php";
            break;
        default:
            echo "request for " . $page[0];
    }
    return true;
}

function cvsidebarintercept($hook, $entity_type, $returnvalue, $params)
{
    //here we check to see if we are currently in courseview mode.  If we are, we hijack the sidebar for our course menu
    if (ElggSession::offsetGet('courseview'))
    {
        $returnvalue = elgg_view('courseview/hijacksidebar') . $returnvalue;
    }
    return $returnvalue;
}

/*  This function intercepts any new content creation or content updates.  If the content type is one of our valid plugins (as specified in the
 * setup page, we will loop through the treeview generated by cv_content_tree view that is appended to appropriate pages and use
 * the checkboxes in this to determine any relationships between the current menu item and the content to be created or removed. 
 */

function cv_intercept_update($event, $type, $object)
{
    elgg_load_library('elgg:cv_debug');
    cv_debug("Entering cv_intercept_update - Object:  " . $object->getSubtype(), "cv_intercept_update");
    $cvmenuguid = ElggSession::offsetGet('cvmenuguid'); //need to get this from the session since we are no longer on a courseview page
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview')); //a list of approved plugins for courseview
    $validkeys = array_keys($validplugins);
    $valid_plugin = false;
    //looping through all approved plugins to ensure that this content can be added to courseview
    cv_debug("Is this valid: " . is_valid_plugin($object->getSubtype()), "cv_intercept_update", 6);

    foreach ($validkeys as $plugin)
    {
        cv_debug("Checking:  " . $plugin . '--' . $object->getSubtype(), "cv_intercept_update", 6);
        if ($object->getSubtype() == $plugin)
        {
            $valid_plugin = true;
            cv_debug("Match!!!", "cv_intercept_update");
        }
    }

    cv_debug("valid debug? $valid_plugin", "cv_intercept_update");

    /* pull array of checkboxes from the cv_content_tree so that we can loop through them to determine what realtionships
     * need to be added or deleted
     */
    $menu_items = get_input('menuitems');
    //var_dump ($menu_items);
    //exit;

    cv_debug("Number of menuitems: " . sizeof($menu_items), "cv_intercept_update");
    foreach ($menu_items as $menu_item)
    {
        cv_debug($menu_item, "cv_intercept_update");
        /* Note that $menu_items is an array of Strings passed from cvaddtocohorttree where each element contains three pieces 
         * of information in the format Xmenuitemguid|cohortguid where X is a + if a new relationship should be created and a - if
         * it should be removed.
         * 
         * menuitemguid is stripped out into $menu_item and cohortguid is stripped out into $cohort_guid
         */
        $cohort_guid = substr(strstr($menu_item, '|'), 1);
        $stop = stripos($menu_item, '|') - 1;
        //cv_debug("stop: ".$stop,"cv_intercept_update");  

        $menu_item_guid = substr($menu_item, 1, $stop); //substr(strstr($menu_item, '|', true), 1); //This doesn't work in older versions of php
        $guid_one = $menu_item_guid;
        $guid_two = $object->guid;

        /* need to check if this is a non-professor type content and change relationship accordingly...If it is of type professor, the relationship
         * String should be simply 'content'.  However, if the relationship is of type student, then the relationship String should have the current
         * $cohort_guid appended to it in the form contentXXX where XXX is the $cohort_guid
         */
        $relationship = 'content';
        //however, if the $menuitem is not of type 'professor' (ie, of type 'student'), then we need to append the particulart  cohort to 'content'
        if (get_entity($menu_item_guid)->menutype != 'professor')
        {
            $relationship.= $cohort_guid;
        }

        if (strrchr('+', $menu_item) && $valid_plugin)  //if the module was checked in the cv_content_tree view, then add relationship
        {
            cv_debug("Adding Relationship: $guid_one, $relationship, $guid_two", "cv_intercept_update", 5);
            $z = add_entity_relationship($guid_one, $relationship, $guid_two);
            cv_debug($z, "cv_intercept_update");
        } else
        {
            $rel_to_delete = check_entity_relationship($guid_one, $relationship, $guid_two);
            if ($rel_to_delete)  //if the module was unchecked and there was a relationship, we need to remove the relationship
            {
                delete_relationship($rel_to_delete->id);
            }
        }
    }
    //::TODO:  change this $rootdomain to use this instead 
    // $rootdomain= dirname(__FILE__); //gives a relative path to the directory where this file exists

    $rootdomain = elgg_get_site_url();
    $cvredirect = $rootdomain . 'courseview/contentpane/' . $cvcohortguid . '/' . $cvmenuguid;
    ElggSession::offsetSet('cvredirect', $cvredirect);
}

//::TODO:  Matt, I want to better understand why I had to do this
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

//function cventitymenu($hook, $type, $return, $params)
//{
//    if (is_valid_plugin($params['entity']->getSubtype()))
//    {
//        $item = new ElggMenuItem('cvpin', 'add to Cohort', '#');
//        $return [] = $item;
//    }
//    return $return;
//}


/*  I think there may be an easier/more effective way to do this?  Instead of looping through
 * all of the courses, couldn't we just look at the current cohort's container (which should) be
 * the course that the current cohort belongs to.  Then we just add that courses acl to the return?
 * Need to get Matt to walk through this one again...I think there is an easier way but I'm not sure
 * that I completely understand the process
 */

function intercept_ACL_read($hook, $type, $return, $params)
{
    //If courseview is not active, we just want to continue on as usual
    $cv_active = ElggSession::offsetGet('courseview');
    if (!$cv_active)
    {
        return $return;
    }
    /* We need to unregister the plugin hook handler before getting the courses or we will crash the php interpreter through
     * recursion ;->
     */
    elgg_unregister_plugin_hook_handler('access:collections:read', 'all', 'intercept_ACL_read');


    cv_debug("Entering intercept_ACL_read:: ", "", 100);
    cv_debug($return, "", 100);


    /* I'm thinking that we don't need to do this for all user courses, just the current one??? */

    $user = get_user($params["user_id"]);
    $courses = cv_get_user_courses($user);

    foreach ($courses as $course)
    {
        if ($course->cv_acl)
        {
            //cv_debug("***", "", 100);
            $return [] = (int) $course->cv_acl; //add the course acl to the acl list being returned
            //we also have to add the user to the acl collection. --hmmm..what happens when a user leaves a cohort?
            add_user_to_access_collection($user->guid, $course->cv_acl);
        }
    }
    cv_debug("Exiting intercept_ACL_read: ", "", 100);
    cv_debug($return, "", 100);
    return $return;
}

/*
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
 * I orginally wrote this to show all courses owned by the prof but I'm starting to think that it should just
 * be the current course that the prof is in within courseview.  I need to run this past the other guys to 
 * get their thoughts....
 */

function intercept_ACL_write($hook, $type, $return, $params)
{
    $user = get_user($params["user_id"]);
    $cv_active = ElggSession::offsetGet('courseview');
    if (!cv_isprof($user) || !$cv_active)
    {
        return $return;
    }
    elgg_unregister_plugin_hook_handler('access:collections:write', 'all', 'intercept_ACL_write');
    cv_debug("Running intercept_ACL_write:<br>", "", 100);
    cv_debug($params, "", 100);
    cv_debug($type, "", 100);
    cv_debug($hook, "", 100);
    cv_debug($return, "", 100);
//    $courses = cv_get_user_courses($user);
//    foreach ($courses as $course)
//    { 
//        if ($course->cv_acl)
//        {
//            $return [$course->cv_acl] = $course->title . '-' . $course->cv_acl;  
//            //echo "ACL: ". $return [$course->cv_acl]."<br>";
//        }
//    }

    $cv_cohort = get_entity(ElggSession::offsetGet('cvcohortguid'));

    $course = get_entity($cv_cohort->getContainerGUID());
    //if the course has been assigned a custom acl (stored in cv_acl) then add it to the list
    if ($course->cv_acl)
    {
        $return [$course->cv_acl] = 'Course: ' . $course->title;
    }


    cv_debug($return, "", 100);
    return $return;
}

function register_courseview_menu()
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
