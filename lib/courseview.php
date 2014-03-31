<?php

//this method grabs the courseview object and returns the profsgroup attribute which contains the guid of the profsgroup
//function cv_get_profsgroup()
//{
//    $cvcourseview = elgg_get_entities(array('type' => 'object', 'subtype' => 'courseview'));
//    return $cvcourseview[0]->profsgroup;
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
        //cv_debug( 'cohortname: '.$cohort->title.'<br>',"",100);
        //cv_debug('coursename:: '.$cvcourse->title.'<br>',"",100);

        if (!$cvcourse->cv_acl)
        {
            // echo "nope<br>";
            $id = create_access_collection("cv_id", $cvcourse->guid);
            $cvcourse->cv_acl = $id;
            $cvcourse->save();
            // echo'generating...'. $cvcourse->cv_acl;
        }
//        else
//        {
//            echo 'yep!<br>';
//        }
//       $id = create_access_collection ("cv_id",$cvcourse->guid);
//$cvcourse->cv_acl = $id;

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

//function cv_debug_to_console($data)
//{
//
//    if (is_array($data))
//        $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
//    else
//        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
//
//    echo $output;
//}
//function cv_calc_relationship_string($menuitem)
//{
//
//    //if the $menuitem is of type 'professor', the relationship string is simple 'content'
//    $relationship = 'content';
//    //however, if the $menuitem is not of type 'professor' (ie, of type 'student'), then we need to append the particulart  cohort to 'content'
//    if (get_entity($menu_item_guid)->menutype != 'professor')
//    {
//        $relationship.= $cohort_guid;
//    }
//    return $relationship;
//}
//    echo elgg_entity_exists(elgg_get_plugin_setting('profsgroup','courseview'));
//    //echo get_entity($profsgroup)->isMember($user);
//    if(get_entity($profsgroup)->isMember($user))
//    {
//        return true;
//    }
//    else 
//        {
//        return false;
//        }
//   $profs =cv_get_profsgroup();
//    
//   if   (get_entity(cv_get_profsgroup())->isMember ($user))
//    {
//        return true;
//    }

function cv_get_content_by_menu_item($filter, $cvmenuguid, $relationship, $list = false, $sort = 'chrono', $page_size = 10)
{

    // $options = array(
//		'container_guid' => $entity->guid,
//		'annotation_names' => array('likes'),
//		'selects' => array("(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid) AS likes"),
//		'order_by' => 'likes DESC',
//		'full_view' => false
//	  );



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
//             if ($filter == 'all')
//    {
//        unset($options['subtype']);
//    }
        //  $content = elgg_list_entities_from_annotations($options);  
        //    return $content;
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

//function courseview_initialize()
//{
//just some learning stuff
//  $courseview_object = elgg_get_entities(array('type' => 'object', 'subtype' => 'courseview'))[0];
//echo elgg_echo ('coursetreexxx'.var_dump($courseview_object->coursetree));
//   ElggSession::offsetSet('currentcourse', $courseview_object->coursetree);
//echo 'courseview_object guid:  ' . $courseview_object->guid;
//   $courseview_object->plugins = array('Hi Rich...It works!', 'blog', 'bookmark');
//   $courseview_object->save;
// echo '######'.$courseview_object->plugins[0];
//if a CourseView Object doesn't exist, this must be the first time the plugin has run.  In that case,
//we build a CourseView Object to track various things that our plugin needs.
//    if (!$courseview_object)
//    {
//Since this is the first time that CourseView has run, we need to create a professor group
//        $courseview_profsgroup = new ElggGroup();
//        $courseview_profsgroup->subtype = 'group';
//        $courseview_profsgroup->title = 'profsgroup';
//        $courseview_profsgroup->name = 'profsgroup'; //just added this...should it be name or title?
//        $courseview_profsgroup->save();
//
//        $courseview_object = new ElggObject();
//        $courseview_object->subtype = "courseview";
//        $courseview_object->access_id = 2;
//        $courseview_object->save();
//        $courseview_object->plugins = array('blog', 'bookmark');
//        $courseview_object->profsgroup = $courseview_profsgroup->guid; //add the profsgroup guid to our courseview object.
//        $courseview_object->save();
//    }
//    return $courseview_object->guid;
//}

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
 
    $cvcourses = elgg_get_entities_from_relationship(array
    ('type' => 'object',
    'metadata_names' => array('cvcourse'),
    'metadata_values' => array(true),
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