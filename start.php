<?php
/**
 * Write something profound here...
 */
elgg_register_event_handler('init', 'system', 'courseviewInit'); //call courseviewinit when the plugin initializes

function courseviewInit()
{
    ElggSession::offsetSet('cv_hp', false);
    elgg_register_library('elgg:courseview', elgg_get_plugins_path() . 'courseview/lib/courseview.php');
    elgg_register_library('elgg:cv_content_tree_helper_functions', elgg_get_plugins_path() . 'courseview/lib/cv_content_tree_helper_functions.php');
   // elgg_register_library('elgg:cv_debug', elgg_get_plugins_path() . 'courseview/lib/cv_debug.php');
    elgg_load_js('lightbox');
    elgg_load_css('lightbox');
    elgg_register_simplecache_view('js/courseview/js');

    //::TODO:Matt This is no longer important, correct?
    $jsurl = elgg_get_simplecache_url('js', 'courseview/js');
    elgg_register_js('cv_sidebar_js', $jsurl);
    elgg_register_ajax_view('courseview/make_group_a_cohort');
    elgg_register_ajax_view('courseview/remove_group_from_cohort');
    elgg_extend_view('js/elgg', 'courseview/group_js');

    elgg_load_library('elgg:courseview');
    //elgg_load_library('elgg:cv_debug');

    // Ensure that there is a logged in user before allowing access to page
    if (!elgg_get_logged_in_user_entity())
    {
        return;
    }

    //if the user is not a member of any cohorts, not a prof, and not an admin then don't bother running anything.
    if (!cv_is_courseview_user() && !cv_isprof(elgg_get_logged_in_user_entity()) && !elgg_is_admin_logged_in())
    {
        return;
    }

    //set up our link to css rulesets
    elgg_extend_view('css/elgg', 'customize_css/courseview_css', 1000);
    
    
    if (elgg_get_plugin_setting('cv_animated_header', 'courseview'))
    {
        elgg_extend_view('css/elgg', 'customize_css/cv_header_animation', 1001);
    }
    
    
    if (elgg_get_plugin_setting('cv_animated_menuitem', 'courseview'))
    {
        elgg_extend_view('css/elgg', 'customize_css/cv_menuitems_animation', 1001);
    }
   // 
    //just a little sneaky thing that I'll remove later on -- allows me to test hp functionality
    if (cv_hp())
    {
        elgg_extend_view('css/elgg', 'customize_css/hp_css', 1001);
        //get rid of the menu items
        elgg_register_plugin_hook_handler('register', 'menu:site', 'myplugin_sitemenu', 1000);
        //turn on courseview
        ElggSession::offsetSet('courseview', true);
    }

    //register menu item to switch to CourseView
    cv_register_courseview_menu();


    //$regentitytypes = get_registered_entity_types();
    // $plugins = $regentitytypes['object'];

    cv_register_hooks_events_actions(dirname(__FILE__));  //register all hooks and stuff, passing the current directory of this file
    // push the  cohort guid and menu guid into the session
    $cvcohortguid = ElggSession::offsetGet('cvcohortguid');
    $cvmenuguid = ElggSession::offsetGet('cvmenuguid');
}

//this method gets called when one of the courseview urls is called.  
function courseviewPageHandler($page, $identifier)
{
    // define("CV_GUID",   true);
    // echo CV_GUID;
    elgg_set_page_owner_guid($page[1]);   //set the page owner to the cohort and then call gatekeeper
    gatekeeper();  //gatekeeper ensures that user is authorized to view page
    $base_path = dirname(__FILE__);

    /* Since it is possible to require the current cohort and menuitem while on a non-courseview page, we push
     * this information into the session */
    ElggSession::offsetSet('cvcohortguid', $page[1]);
    ElggSession::offsetSet('cvmenuguid', $page[2]);
    set_input('params', $page);  //place the $page array into params

    switch ($page[0])  //switching on the first parameter passed through the RESTful url
    {
        case 'cv_contentpane':    //this is the main course content page
            require "$base_path/pages/courseview/cv_contentpane.php";
            break;
        case 'courseview':   //this is the landing page when a user first clicks on coursview
            set_input("object_type", 'all');
            require "$base_path/pages/courseview/cv_contentpane.php";
            break;
//        case 'cv_testing':
//            require "$base_path/pages/courseview/cv_testing.php";
//            break;
        case 'examine':
        case 'inspect':
            require "$base_path/pages/courseview/examine.php";
            break;
        default:
            echo "courseview request for " . $page[0];
    }
    return true;
}

function cv_group_buttons($hook, $type, $return, $params)
{
    if (!elgg_instanceof($params['entity'], 'group'))
    {
        return $return;
    }

    if (cv_is_cvcohort($params['entity']))
    {
        if (cv_is_admin(cv_get_current_user(CV_ENTITY)))
        {
            $link = new ElggMenuItem('cv_group_button', 'remove link to CourseView', "ajax/view/courseview/remove_group_from_cohort?guid={$params['entity']->guid}");
            $link->addLinkClass("cv_remove_group_from_cohort");
            $link->addLinkClass('elgg-lightbox');
            $return[] = $link;
        } 
        else
        {
            $link = new ElggMenuItem('cv_button', 'CV Enabled!', "");
            $link->addLinkClass("cv_enabled");
            $return[] = $link;
        }
    } else if (cv_is_admin(cv_get_current_user(CV_ENTITY)))
    {
        $link = new ElggMenuItem('cv_group_button', 'link to CourseView', "ajax/view/courseview/make_group_a_cohort?guid={$params['entity']->guid}");
        $link->addLinkClass("cv_add_to_cohort");
        $link->addLinkClass('elgg-lightbox');
        $return[] = $link;
    }
    return $return;
}

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
    $show_elgg_stuff = elgg_get_plugin_setting('show_elgg_stuff', 'courseview');
   
    if ($show_elgg_stuff == 0 && cv_is_cvcohort(page_owner_entity()))  //if don't show elgg stuff is selected in settings
    {
        $returnvalue = "";
    }
    $menu_visibility = elgg_get_plugin_setting('menu_visibility', 'courseview');
    $user_is_member_of_cohort = cv_user_is_member_of_cohort (page_owner_entity());
    //here we check to see if we are currently in courseview mode.  If we are, we hijack the sidebar for our course menu
    //if ((ElggSession::offsetGet('courseview') && $menu_visibility == 'always') || cv_is_cvcohort(page_owner_entity()))
        if ((ElggSession::offsetGet('courseview') && $menu_visibility == 'always') || $user_is_member_of_cohort)
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

function cv_create_group ($hook, $type, $return, $params)
{
    var_dump($params)            ;
    exit;
            $return .="###########";
    return $return;
}

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
    echo $menu_entity->name;
    echo $menu_entity->guid;

    $cvmenuguid = $menu_entity->guid;
    echo $cvmenuguid;
    //exit;

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
