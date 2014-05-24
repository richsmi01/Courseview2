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

    $jsurl = elgg_get_simplecache_url('js', 'courseview/js');
    elgg_register_js('cv_sidebar_js', $jsurl);
    elgg_register_ajax_view('courseview/cv_make_group_a_cohort');
    elgg_register_ajax_view('courseview/remove_group_from_cohort');
    //elgg_extend_view('js/elgg', 'courseview/group_js');
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
    set_input('cv_menu_guid', $page[2]);
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

