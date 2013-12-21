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


    // allows us to hijack the sidebar.  Each time the sidebar is about to be rendered, this hook fires so that we can add our menu
    elgg_register_plugin_hook_handler('view', 'page/elements/sidebar', 'cvsidebarintercept');

    /* allows us to intercept each time elgg calls a forward.  We will use this to be able to return to the coursview 
     * tool after adding a relationship to added content
     */
    elgg_register_plugin_hook_handler('forward', 'all', 'cvforwardintercept');

    //::TODO:  Matt - can you go over this again?  I don't think I'm using this now that I've chosen not to use ajax
    // allows us to add a menu choice to add an entity to a cohort
//    elgg_register_plugin_hook_handler('register', 'menu:entity', 'cventitymenu');
    //registering my ajax-based tree control for adding content from the wild to a cohort
//    elgg_register_ajax_view('ajaxaddtocohort');

    /* this view gets added to the bottom of each page.  The addcontenttocohort view has code in it to simply return
     * without doing anything unless the user belongs to at least one cohort and the current view is creating or updating
     * an approved object such as a blog, bookmark etc as chosen in the settings page.
     */
    elgg_extend_view('input/form', 'courseview/addcontenttocohort', 600);

    //register page event handler
    elgg_register_page_handler('courseview', 'courseviewPageHandler');

    /* both creating and updating content results in us calling the cvinterceptupdate to make or remove any
     * relationships between the content and any menuitems deemed neccesary.
     */
    elgg_register_event_handler('create', 'object', 'cvinterceptupdate');
    elgg_register_event_handler('update', 'object', 'cvinterceptupdate');

    //this is an experiment to intercept the database call and make changes to the ACLs as needed
    elgg_register_plugin_hook_handler('access:collections:read', 'all', 'richtest', 999);
    elgg_register_plugin_hook_handler('access:collections:write', 'all', 'richtest2', 999);


//set up our paths and various actions 
    $base_path = dirname(__FILE__); //gives a relative path to the directory where this file exists

    elgg_register_action("createcourse", $base_path . '/actions/courseview/createcourse.php');
    elgg_register_action("cvaddtocohorttreeview", $base_path . '/actions/courseview/cvaddtocohorts.php');
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

    //$base_path = elgg_get_plugins_path() . 'courseview/pages/courseview';

    switch ($page[0])  //switching on the first parameter passed through the RESTful url
    {
        case 'contentpane':    //this is the main course content page
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
 * setup page, we will loop through the treeview generated by cvaddtocohorttreeview view that is appended to appropriate pages and use
 * the checkboxes in this to determine any relationships between the current menu item and the content to be created or removed. 
 */

function cvinterceptupdate($event, $type, $object)
{
    elgg_load_library('elgg:cv_debug');
    cv_debug("Entering cvinterceptupdate - Object:  " . $object->getSubtype(), "cvinterceptupdate");
    $cvmenuguid = ElggSession::offsetGet('cvmenuguid'); //need to get this from the session since we are no longer on a courseview page
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview')); //a list of approved plugins for courseview
    $validkeys = array_keys($validplugins);
    $valid_plugin = false;
    //looping through all approved plugins to ensure that this content can be added to courseview
    cv_debug("Is this valid: " . is_valid_plugin($object->getSubtype()), "cvinterceptupdate", 6);

    foreach ($validkeys as $plugin)
    {
        cv_debug("Checking:  " . $plugin . '--' . $object->getSubtype(), "cvinterceptupdate", 6);
        if ($object->getSubtype() == $plugin)
        {
            $valid_plugin = true;
            cv_debug("Match!!!", "cvinterceptupdate");
        }
    }

    cv_debug("valid debug? $valid_plugin", "cvinterceptupdate");

    /* pull array of checkboxes from the cvaddtocohorttreeview so that we can loop through them to determine what realtionships
     * need to be added or deleted
     */
    $menu_items = get_input('menuitems');

    cv_debug("Number of menuitems: " . sizeof($menu_items), "cvinterceptupdate");
    foreach ($menu_items as $menu_item)
    {
        cv_debug($menu_item, "cvinterceptupdate");
        /* Note that $menu_items is an array of Strings passed from cvaddtocohorttree where each element contains three pieces 
         * of information in the format Xmenuitemguid|cohortguid where X is a + if a new relationship should be created and a - if
         * it should be removed.
         * 
         * menuitemguid is stripped out into $menu_item and cohortguid is stripped out into $cohort_guid
         */
        $cohort_guid = substr(strstr($menu_item, '|'), 1);
        $stop = stripos($menu_item, '|') - 1;
        //cv_debug("stop: ".$stop,"cvinterceptupdate");  

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

        if (strrchr('+', $menu_item) && $valid_plugin)  //if the module was checked in the cvaddtocohorttreeview view, then add relationship
        {
            cv_debug("Adding Relationship: $guid_one, $relationship, $guid_two", "cvinterceptupdate", 5);
            $z = add_entity_relationship($guid_one, $relationship, $guid_two);
            cv_debug($z, "cvinterceptupdate");
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



function richtest($hook, $type, $return, $params)
{
    echo 'richtest2<br>';
    var_dump($hook);
    echo'<br>';
    var_dump($type);
    echo'<br>';
    var_dump($return);
    echo'<br>';
    var_dump($params);
    echo'<br>';
//    if (!$cv_active)
//    {
//        return $return;
//    }
    /* We need to unregister the plugin hook handler before getting the courses or we will crash the php interpreter through
     * recursion ;->
     */
    elgg_unregister_plugin_hook_handler('access:collections:read', 'all', 'richtest');
    $user = get_user($params["user_id"]);
    $courses = cv_get_user_courses($user);

    foreach ($courses as $course)
    {
        if ($course->cv_acl)
        {
            $return [] = (int) $course->cv_acl; //add the course acl to the acl list being returned
            //we also have to add the user to the acl collection.
            add_user_to_access_collection($user->guid, $course->cv_acl);
        }
    }
    echo 'modified $return:<br>';
    var_dump($return);
    echo'<br>';
    return $return;
}

function richtest2($hook, $type, $return, $params)
{
    
    /*
     * Think about just adding the current cohort's course to the access list...I think that's all I need.
     */
//    echo 'richtest2<br>';
//    var_dump($hook);
//    echo'<br>';
//    var_dump($type);
//    echo'<br>';
//    var_dump($return);
//    echo'<br>';
//    var_dump($params);
//    echo'<br>';

    $user = get_user($params["user_id"]);
    $cv_active = ElggSession::offsetGet('courseview');
//    if (!cv_isprof($user) || !$cv_active)
//    {
//        return $return;
//    }
    elgg_unregister_plugin_hook_handler('access:collections:write', 'all', 'richtest2');
    $courses = cv_get_user_courses($user);

    //var_dump ($courses);
    // echo 'Num courses: '.sizeof($courses).'<br>';
    foreach ($courses as $course)
    {
//       echo 'accessid: '.$course->cv_acl.'<br>';
//       echo 'courseguid: '.$course->guid.'<br>';
//       echo 'coursename: '.$course->title.'<br>';
// echo 'Looping through courses'.$course->cv_acl;
        // if ($course->cv_acl)
        {
            $return [$course->cv_acl] = $course->title . '-' . $course->cv_acl;  //cast this to int...
            //echo "ACL: ". $return [$course->cv_acl]."<br>";
        }
    }




    return $return;


    //use userid to make sure that the user is a professor (if not, just return $return)
    /*
     * get all of the professors courses, iterate through and get all of the acls that
     * belong to each course and add to the return 
     * 
     * return is associative array - key is accessid and the value is what gets displayed (course name)
     * 
     * maybe check to see if courseview is active first...write code to allow profs to archive courses or cohorts.
     */
}
