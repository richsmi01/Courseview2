<?php

/**
 * Rich Smith's Master's Project:  CourseView - A Distance Learning Engine
 */
elgg_register_event_handler('init', 'system', 'courseviewInit'); //call courseviewinit when the plugin initializes
    // <editor-fold defaultstate="collapsed" desc="*************  Load Libraries  ****************">
    require_once 'lib/cv_hooks.php';
    require_once 'lib/cv_events.php';
    // </editor-fold>
function courseviewInit()
{
    // <editor-fold defaultstate="collapsed" desc="*************  Register libraries *************">
    elgg_register_library('elgg:courseview', elgg_get_plugins_path() . 'courseview/lib/courseview.php');
    elgg_register_library('elgg:cv_rarely_used_functions', elgg_get_plugins_path() . 'courseview/lib/cv_rarely_used_functions.php');
    elgg_register_library('elgg:cv_content_tree_helper_functions', elgg_get_plugins_path() . 'courseview/lib/cv_content_tree_helper_functions.php');
    // </editor-fold> 

    // <editor-fold defaultstate="collapsed" desc="********* Lightbox ajax and js code **********">
    elgg_load_js('lightbox');
    elgg_load_css('lightbox');
    elgg_register_simplecache_view('js/courseview/js');


    /* Register ajax stuff for lightbox that allows prof to turn group into a cohort */
    elgg_register_ajax_view('courseview/cv_make_group_a_cohort');
    elgg_register_ajax_view('courseview/remove_group_from_cohort');
    // </editor-fold>
   
    //::TODO:Rich - Need to look at not loading courseview here 
    elgg_load_library('elgg:courseview');
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
    //register page event handler
    elgg_register_page_handler('courseview', 'courseviewPageHandler');

    // <editor-fold defaultstate="collapsed" desc="********** Extending Views *************">
    /*  Allows us to add ability to tag content into a particular module 
     *  The cv_add_content_to_cohort view gets added to the bottom of each page.  This view has code in it to simply return
     *  without doing anything unless the user belongs to at least one cohort and the current view is creating or updating
     *  an approved object such as a blog, bookmark etc as chosen in the settings page. */
    elgg_extend_view('input/form', 'courseview/cv_add_content_to_cohort', 600);

    /* The cv_make_group_a_cohort_from_group_page adds the ability to make a group a cohort when in the group edit/new group page */
    elgg_extend_view('groups/edit', 'courseview/cv_make_group_a_cohort_from_group_page', 600);

    elgg_extend_view('css/elgg', 'customize_css/courseview_css', 1000);
    //if animated heading is set in settings page, load the animation css
    if (elgg_get_plugin_setting('cv_animated_header', 'courseview'))
    {
        elgg_extend_view('css/elgg', 'customize_css/cv_header_animation', 1001);
    }
    //if animated menu items are set in settings page, load the appropriate css
    if (elgg_get_plugin_setting('cv_animated_menuitem', 'courseview'))
    {
        elgg_extend_view('css/elgg', 'customize_css/cv_menuitems_animation', 1001);
    }
    //elgg_extend_view('groups/add', 'courseview/test', 600);
 
    //if hp_mode is set to true in settings, have courseview completey take over
    if (elgg_get_plugin_setting('hp_mode', 'courseview'))
    {
        elgg_extend_view('css/elgg', 'customize_css/hp_css', 1001);
        //get rid of the menu items
        elgg_register_plugin_hook_handler('register', 'menu:site', 'myplugin_sitemenu', 1000);
        //turn on courseview
        ElggSession::offsetSet('courseview', true);
    }
        // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="********  Set Up Menu Items ********">

    /* register menu item to switch to CourseView */
    cv_register_courseview_menu();
    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="********  Set Up Plugin Hooks *********">
    /* loop through availbable plugins and register a plugin hook for each to check if content is new since last login */
    $availableplugins = unserialize(elgg_get_plugin_setting('approved_subtype', 'courseview'));
    foreach ($availableplugins as $plugin)
    {
        elgg_register_plugin_hook_handler('view', "object/$plugin", 'cv_new_content_intercept');
    }

    /* allows us to hijack the sidebar.  Each time the sidebar is about to be rendered, this hook fires so 
      that we can add our tree menu */
    elgg_register_plugin_hook_handler('view', 'page/elements/sidebar', 'cv_sidebar_intercept');

    /* Need to intercept content creation/update so that ACL writes to allow us to add the course ACL when needed */
    elgg_register_plugin_hook_handler('access:collections:write', 'all', 'cv_intercept_ACL_write', 999);

    /* intercepts every menu item that is displayed -- cv_group_buttons uses this to add CourseView info
      in the groups listing page */
    elgg_register_plugin_hook_handler('register', 'menu:entity', 'cv_group_buttons', 1000);

    /* intercepts each time elgg calls a forward.  We will use this to be able to return to the coursview 
     * tool after adding a relationship to added content */
    elgg_register_plugin_hook_handler('forward', 'all', 'cv_forward_intercept');

    /* Allow profs to write to courses they don't own --This allows profs to create cohorts for courses they don't own */
    elgg_register_plugin_hook_handler('container_permissions_check', 'all', 'cv_can_write_to_container');

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="*******  Register event handlers  *******">

    /*  this is left for future expansion - will call cv_shutdown_event at the end of the page rendering after everything
      else has completed */
    elgg_register_event_handler('shutdown', 'system', 'cv_shutdown_event');

    /* creating, updating or deleting content results in us calling the cv_intercept_content_update to make or remove any
      relationships between the content and any menuitems deemed neccesary. */
    elgg_register_event_handler('create', 'object', 'cv_intercept_content_update');
    elgg_register_event_handler('update', 'object', 'cv_intercept_content_update');
    elgg_register_event_handler('delete', 'object', 'cv_intercept_content_update');

    // intercept new user creation  - This has been left in for possible future expansion
    //elgg_register_event_handler('create', 'user', 'cv_intercept_newuser');  //use this to intercept users when they are created.
    //when a user joins a cohort, we need to add them to a acl list attached to the container course
    //when they leave a cohort, we need to remove them.
    elgg_register_event_handler('join', 'group', 'cv_join_group', 0);
    elgg_register_event_handler('leave', 'group', 'cv_leave_group', 0);

    //future expansion - fires when groups are created or updated
    elgg_register_event_handler('create', 'group', 'cv_update_group', 9999);
    elgg_register_event_handler('update', 'group', 'cv_update_group', 9999);
    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="************ Register Actions**** ********">
    $base_path = dirname(__FILE__);
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
    elgg_register_action('cv_remove_cohort', $base_path . '/actions/courseview/cv_remove_cohort.php');
    elgg_register_action('cv_admin_toggle', $base_path . '/actions/courseview/cv_admin_toggle.php');
    
    //intercepts call to entity_url_handler
    elgg_register_entity_url_handler('object', 'cvmenu', 'cv_menu_url_handler');
// </editor-fold>   
}

/*
 * this method gets called when one of the courseview urls is called. 
 * 
 * @param $page - array of restful url segments
 * @param $identifier - set to 'courseview'
 */
function courseviewPageHandler($page, $identifier)
{
    elgg_set_page_owner_guid($page[1]);   //set the page owner to the cohort and then call gatekeeper
    gatekeeper();  //gatekeeper ensures that user is authorized to view page
    $base_path = dirname(__FILE__);
    /* Since it is possible to require the current cohort and menuitem while on a non-courseview page, we push
     * this information into the session */
    ElggSession::offsetSet('cvcohortguid', $page[1]);
    ElggSession::offsetSet('cvmenuguid', $page[2]);
    ElggSession::offsetSet('hmm', $page);
    set_input('params', $page);  //place the $page array into params
    set_input('cv_menu_guid', $page[2]);

    //little hack to make sure course acls are set correctly for student in a cohort so that they can see other cohorts
    $cv_group = get_entity($page[1]);
    $cv_user = elgg_get_logged_in_user_entity();
    if (elgg_instanceof($cv_group, "group") && $cv_group->cvcohort && $cv_group->isMember($cv_user))
    {
        $cv_course = $cv_group->getContainerEntity();
        $result = add_user_to_access_collection($cv_user->guid, $cv_course->cv_acl);
    }

    switch ($page[0])  //switching on the first parameter passed through the RESTful url
    {
        case 'cv_contentpane':    //this is the main course content page
            require "$base_path/pages/courseview/cv_contentpane.php";
            break;
        case 'courseview':   //courseview welcome page
        case '':
            require "$base_path/pages/courseview/cv_contentpane.php";
            break;
        case 'examine':    //used to examine CourseView objects during development
        case 'inspect':
            require "$base_path/pages/courseview/examine.php";
            break;
        //in case a problem with the url exists
        default:
            echo "courseview request for " . $page[0] . " could not be completed";
    }
    return true;
}
