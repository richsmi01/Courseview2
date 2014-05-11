<?php

define ("CV_GUID",  true);
define ("CV_ENTITY", false);



/**
 * Determines if the user passed is an Elgg admin
 *
 * @param ElggUser $user The user object being inspected
 * @return boolean returns true if the user is an admin and false if not
 */
function cv_is_admin ($user)
{
    return $user->isAdmin();
}

function cv_get_current_user ($return_type = CV_GUID)
{
    if ($return_type)
    {
        return elgg_get_logged_in_user_guid();
    }
    else
    {
        return elgg_get_logged_in_user_entity();
    }
}

function cv_user_is_member_of_cohort ($cohort)
{
    if (!isset($cohort->cvcohort)) //if the entity passed was not a cohort
    {
        return false;
    }
    $user = cv_get_current_user(CV_ENTITY);
    
    if (cv_is_cvcohort($cohort))
    {
    return $cohort->isMember ($user);
    }
    else 
    {
        return false;
    }
}



function cv_is_cvcohort ($group)
{
    return $group->cvcohort;
//    
}

//this method grabs the courseview object and returns the profsgroup attribute which contains the guid of the profsgroup
//function cv_get_profsgroup()
//{
//    $cvcourseview = elgg_get_entities(array('type' => 'object', 'subtype' => 'courseview'));
//    return $cvcourseview[0]->profsgroup;
//}
function cv_is_valid_plugin_by_keys ($user, $object)
{
     $validplugins = cv_get_valid_plugins($user);//fetch the list of approved plugins for courseview
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

function cv_register_hooks_events_actions ($base_path)
{
    //loop through availbable plugins and register a plugin hook for each to check if content is new since last login
    $availableplugins = unserialize(elgg_get_plugin_setting('approved_subtype', 'courseview'));
    foreach ($availableplugins as $plugin)
    {
        elgg_register_plugin_hook_handler('view', "object/$plugin", 'cv_new_content_intercept');
    }
    
    // allows us to hijack the sidebar.  Each time the sidebar is about to be rendered, this hook fires so 
    // that we can add our tree menu
    elgg_register_plugin_hook_handler('view', 'page/elements/sidebar', 'cv_sidebar_intercept');
    
    
    elgg_register_plugin_hook_handler('register', 'menu:entity', 'cv_group_buttons', 1000);
    

    /* allows us to intercept each time elgg calls a forward.  We will use this to be able to return to the coursview 
     * tool after adding a relationship to added content */
    elgg_register_plugin_hook_handler('forward', 'all', 'cvforwardintercept');
    
    //Need to occasionally intercep permissions check to ensure that people in different cohorts can see ecah others content
    elgg_register_plugin_hook_handler('container_permissions_check', 'all', 'cv_can_write_to_container');

    /* The cv_add_content_to_cohort view gets added to the bottom of each page.  This view has code in it to simply return
     * without doing anything unless the user belongs to at least one cohort and the current view is creating or updating
     * an approved object such as a blog, bookmark etc as chosen in the settings page. */
    elgg_extend_view('input/form', 'courseview/cv_add_content_to_cohort', 600);


    elgg_register_entity_url_handler('object', 'cvmenu', 'cv_menu_url_handler');


    //register page event handler
    elgg_register_page_handler('courseview', 'courseviewPageHandler');

    //  creating, updating or deleting content results in us calling the cv_intercept_update to make or remove any
    // relationships between the content and any menuitems deemed neccesary.
    elgg_register_event_handler('create', 'object', 'cv_intercept_update');
    elgg_register_event_handler('update', 'object', 'cv_intercept_update');
    elgg_register_event_handler('delete', 'object', 'cv_intercept_update');

    //intercept new user creation  
    elgg_register_event_handler('create', 'user', 'cv_intercept_newuser');  //use this to intercept users when they are created.
    // elgg_register_event_handler('register', 'user', 'cv_intercept_update');  //use this to intercept users when they are created.---or this....
    //or check grouptools plugin...
    //when a user joins a cohort, we need to add them to a acl list attached to the container course
    //when they leave a cohort, we need to remove them.
    elgg_register_event_handler('join', 'group', 'cv_join_group');
    elgg_register_event_handler('leave', 'group', 'cv_leave_group');

    //Need to intercept ACL writes to allow us to add the course ACL when needed
    elgg_register_plugin_hook_handler('access:collections:write', 'all', 'cv_intercept_ACL_write', 999);

    //set up our paths and various actions 
     //gives a relative path to the directory where this file exists
    elgg_register_action("cv_create_course", $base_path . '/actions/courseview/cv_create_course.php');
    elgg_register_action("cv_content_tree", $base_path . '/actions/courseview/cv_content_tree.php');
    elgg_register_action("cv_edit_a_course", $base_path . '/actions/courseview/cv_edit_a_course.php');
    elgg_register_action("cv_edit_menuitem", $base_path . '/actions/courseview/cv_edit_menuitem.php');
    elgg_register_action("cv_delete_a_cohort", $base_path . '/actions/courseview/cv_delete_a_cohort.php');
    elgg_register_action("cv_add_a_cohort", $base_path . '/actions/courseview/cv_add_a_cohort.php');
    elgg_register_action("cv_delete_course", $base_path . '/actions/courseview/cv_delete_course.php');
    elgg_register_action('toggle', $base_path . '/actions/courseview/cv_toggle_courseview.php');
    elgg_register_action('cv_add_menu_item', $base_path . '/actions/courseview/cv_add_menu_item.php'); 
    elgg_register_action('cv_edit_a_cohort', $base_path . '/actions/courseview/cv_edit_a_cohort.php');
    elgg_register_action('cv_move_prof_content', $base_path . '/actions/courseview/cv_move_prof_content.php');
    elgg_register_action('cv_menu_toggle', $base_path . '/actions/courseview/cv_menu_toggle.php');  
    elgg_register_action('cv_remove_cohort', $base_path . '/actions/courseview/cv_remove_cohort.php');
}

function cv_get_valid_plugins($user)
{
    if (cv_isprof($user))
    {
    $validplugins = unserialize(elgg_get_plugin_setting('profavailableplugins', 'courseview'));
    }
    else
    {
        $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));
    }
    return$validplugins;
}

//function cv_debug_edit ($guid, $subtype)
//{
//    $cv_entity = get_entity($guid);
//    $cv_entity->subtype=$subtype;
//}

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
    //echo'<br>menu items returned '.  sizeof($menu);
    //var_dump($menu);
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
    //echo "Entering cv_isprof<br>";
    // echo "<br>User: ".($user->name)."--".$user->guid;
    $profgroupguid = elgg_get_plugin_setting('profsgroup', 'courseview');
    //echo "<br>Prof group guid: ". $profgroupguid;
    $profsgroup = get_entity(elgg_get_plugin_setting('profsgroup', 'courseview'));

    // echo "Profsgroup GUID: ". $profsgroup->guid;
    //echo "Is memeber? "+$profsgroup->isMember($user);
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
    $cv_course_owner = get_entity ($cohort->container_guid)->getOwnerEntity();
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
            //'relationship' => 'owner',
            // 'relationship_guid' => $user->guid,
    );

    $ownedcourses = elgg_get_entities_from_relationship($searchcriteria);
    return $ownedcourses;
}

function cv_is_valid_plugin($arg1)
{
    $validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));

    return (array_key_exists($arg1, $validplugins));
}

//start changing long args functions to this!!!
function abc (array $args)
{
    $defaults = array ('arg1'=>'hello', 'arg2'=>'world');
    $options = array_merge ($defaults, $args);
}

function learning ()
{
    
    $db_prefix = elgg_get_config ('db_prefix');
    $relationship = 'cvmenu';
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
            'wheres' => array ("NOT EXISTS ( SELECT 1 FROM {$db_prefix}entity_relationships WHERE guid_one = e.guid AND relationship = '$relationship')"),
           // 'owner_guid' => elgg_get_logged_in_user_guid ()
  
        );
}

function cv_get_content_by_menu_item1 (array $args)
{
    $defaults = array ('filter'=>'hello', 'cvmenuguid'=>' ', 'relationship'=>'', 
        'list'=>false, 'sort'=>'chrono', 'page_size'=>10,
        'only_current_user'=>true);
    echo 'here';
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
    } 
    else if ($args['sort' ]== 'likes')
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
    
    if($args['only_current_user'])
    {
    $options['owner_guid'] = elgg_get_logged_in_user_guid ();
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
        }
 else {
     return false;
    }
}

function courseview_create_course()
{
//  echo elgg_echo('in courseview_create_course method!');
//    echo  count (elgg_get_entities(array('type' => 'object', 'subtype' => 'blog' )));
//    $abc = elgg_get_entities(array('type' => 'object', 'subtype' => 'blog' ));
//    $postings = elgg_get_entities(array('type' => 'object', 'subtype' => 'blog' ));
//    foreach ($postings as $temp) {
//                echo $temp->title;
//    }

    $abc = count(elgg_get_entities(array('type' => 'object', 'subtype' => 'course')));

    if (abc == 0)
    {
        $course = new ElggCourse ();
        $course->subtype = "course";
        $course->title = "COMP 697";
        $course->access_id = 2;
        $course->save();
        $course->test = "It works!";
        $course->save();
        echo 'inside if';
    }
// echo  count (elgg_get_entities(array('type' => 'object', 'subtype' => 'course' )));
    $postings = elgg_get_entities(array('type' => 'object', 'subtype' => 'course'));
    foreach ($postings as $temp)
    {
//          echo $temp->title;
//          echo $temp -> id;
//          echo $temp -> test;
// $temp->delete ();
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


function cv_get_owned_courses ($user)
{
    $userguid=$user->guid;
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

function cv_get_all_courses ()
{
 
    $cvcourses = elgg_get_entities(array
    ('type' => 'object',
    'subtype' => array('cvcourse'),
    'limit' => false,
        )
);
    return $cvcourses;
}

function cv_prof_num_courses_owned ($user)
{
    $userguid=$user->guid;
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

function cv_get_owned_cohorts ($user)
{
    $userguid=$user->guid;
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

function cv_get_all_cohorts ()
{
    $userguid=$user->guid;
    $cvcohorts = elgg_get_entities_from_metadata(array
        ('type' => 'group',
        'metadata_names' => array('cvcohort'),
        'metadata_values' => array(true),
        'limit' => false,
            )
    );
    return $cvcohorts;
}