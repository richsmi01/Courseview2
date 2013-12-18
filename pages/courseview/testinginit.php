<?php

/*
 * A setup page...won't be part of the main program
 */

//::TODO: Instead of having cvcourse = true and cvcohort = true...change it to cvObject = 'course' or 'cohort' or 'content' etc
 elgg_load_library('elgg:courseview');

echo elgg_echo("Rich's Testing Page<br>");

//get the currently logged in user entity
$user = elgg_get_logged_in_user_entity();
$aiko = get_user_by_username("Aiko");
$lori = get_user_by_username("Lori");
$rich = get_user_by_username("Rich");

//echo "<br>Courses<br>";
$options = array
        ('type' => 'object',
        'metadata_names' => array('cvcourse'),
        'metadata_values' => array(1),
        'limit' => false,
//        'owner_guid' => $user->guid
            );
 $courses = elgg_get_entities_from_metadata($options);
 foreach ($courses as $course)
{
    echo "Course:  $course->title --  $course->guid <br>";
    $cohorts = cv_get_cohorts_by_courseguid ($course->guid);
    foreach ($cohorts as $cohort)
    {
        echo "-------Cohort:  $cohort->title --  $cohort->guid <br>";
         $menuitems = cv_get_menu_items_for_cohort($cohort->guid);
          foreach ($menuitems as $menuitem)
          {
            echo "--------------Menu Item:  $menuitem->title --  $menuitem->guid --$menuitem->menutype -- indentlevel = $menuitem->indent<br>";
            $content = cv_get_content_by_menu_item('all', $menuitem->guid, 'content'.$cohort->guid);
            foreach ($content as $contentitem)
            {
                echo '------------------Content:'. $contentitem->getSubtype().' --'.$contentitem->title.'--'.$contentitem->guid.'<br>';
            }
            
          }
    }
}


//echo "<br>Cohorts<br>";
//$options = array
//        ('type' => 'group',
//        'metadata_names' => array('cvcohort'),
//        'metadata_values' => array(1),
//        'limit' => false,
////        'owner_guid' => $user->guid
//            );
//$cohorts = elgg_get_entities_from_metadata($options);
//
//foreach ($cohorts as $cohort)
//{
//    echo "Cohort:  $cohort->title --  $cohort->guid <br>";
//    $menuitems = cv_get_menu_items_for_cohort($cohort->guid);
//    foreach ($menuitems as $menuitem)
//    {
//        echo "Menu Item:  $menuitem->title --  $menuitem->guid --$menuitem->menutype<br>";
//    }
//}
//
//echo "<br>Menu Items<br>";
//$options = array
//        ('type' => 'object',
//        'subtype' => 'cvmenu',
//        'limit' => false,
////        'owner_guid'=> $user->guid
//            );
//$entities = elgg_get_entities_from_metadata($options);
//
//foreach ($entities as $entity)
//{
//    echo "Menu Items:  $entity->title --  $entity->guid <br>";
//}
//
//
//$menuitems = elgg_get_entities(
//     );
//

//
//$menuitems = elgg_get_entities(array
//        ('type' => 'object',
//        'subtype' => 'cvmenu',
//        'limit' => false,
//        'owner_guid'=> $user->guid
//            )
//     );
//    //echo elgg_echo (var_dump($menuitems));
//    echo elgg_echo ("<br/>Deleting all cvmenu items<br/>");
//    foreach ($menuitems as $temp)
//    {
//        echo elgg_echo('Deleting  cvmenu item: ' . $temp->name .'SUBTYPE: '.$temp->subtype. ' GUID: '.$temp->guid . '<br/>');
//      $temp->delete();
//    }
//
////first, remove all cvcohort objects so that we can start again
//$cvcohort = elgg_get_entities_from_metadata(array
//    ('type' => 'group',
//    'metadata_names' => array('testAttribute'),
//    'metadata_values' => array('123'),
//    'limit' => false,
//    'owner_guid' => $user->guid
//        )
//);
//
//foreach ($cvcohort as $temp)
//{
//    echo elgg_echo('Deleting  CourseName: ' . $temp->name . 'Title: ' . $temp->title . 'GUID: ' . $temp->guid . '<br/>');
//    $temp->delete();
//}
    
//First, we create a cvcourse:
//course 1 - PROG100
//    $cvcourse = new ElggObject();
//    $cvcourse->title = 'Prog 1000';
//    $cvcourse->access_id = ACCESS_PUBLIC;
//    $cvcourse->owner_guid = elgg_get_logged_in_user_guid();
//    $cvcourse->container_guid = elgg_get_logged_in_user_guid();
//    $cvcourse->save();
//    $cvcourse->cvcourse = true;
//    $cvcourse->description ="This course is about computer stufff";
//    echo elgg_echo ("Course Created:  ".$cvcourse->guid);
    
   //Next, we'll create a cvcohort that is owned by the professor but has a container of the cvcourse (in this case COMP 697)
    
//    $cvcohort = new ElggGroup ();
//    $cvcohort->title = 'Prog 100 - Cohort 001';  //use titles only for elgg groups
//    $cvcohort->access_id = ACCESS_PUBLIC;
//    $cvcohort->owner_guid = elgg_get_logged_in_user_guid();
//    $cvcohort->container_guid = $cvcourse->guid;
//    $cvcohort->save();
//    $cvcohort->cvcohort = true;
//      echo elgg_echo ("Cohort Created:  ".$cvcohort->guid);

    
//now we're adding some members to the cvcohort
//    $cvcohort->join($user);
//    $cvcohort->join($aiko);
//    $cvcohort->join($lori);
    
    
//    ElggSession::offsetSet('currentcourse', $comp697->guid);
//     echo elgg_echo('<br/>currently selected course is '.ElggSession::offsetGet('currentcourse').'<br/>');
 
   
//create the cvmenu objects that will make up the menu for this course
//    $cvmenu = new ElggObject();
//    $cvmenu->subtype = 'cvmenu';
//    $cvmenu->name = 'Prog 1000';
//    $cvmenu->owner_guid = $user->guid;
//    $cvmenu->container_guid = $cvcourse->guid;
//    $cvmenu->access_id = ACCESS_PUBLIC;
//    $cvmenu->save();
//    $cvmenu->menutype = "folder";
//    $cvmenu->meta1 = "closed";
//    $cvmenu->menuorder = 0;
//    $cvmenu->indent="";
//now, connect it to the course
//    add_entity_relationship($cvcourse->guid, "menu", $cvmenu->guid);
    
//    $cvmenu = new ElggObject();
//    $cvmenu->subtype = 'cvmenu';
//    $cvmenu->name = 'Module 1';
//    $cvmenu->owner_guid = $user->guid;
//    $cvmenu->container_guid = $cvcourse->guid;
//    $cvmenu->access_id = ACCESS_PUBLIC;
//    $cvmenu->save();
//    $cvmenu->menutype = "folder";
//    $cvmenu->meta1 = "closed";
//    $cvmenu->menuorder = 2;
//    $cvmenu->indent="";
//now, connect it to the course
//    add_entity_relationship($cvcourse->guid, "menu", $cvmenu->guid);


//    $cvmenu2 = new ElggObject();
//    $cvmenu2->subtype = "cvmenu";
//    $cvmenu2->name = "Professor Rant";  //should use title instead of name
//    $cvmenu2->owner_guid = $user->guid;
//    $cvmenu2->container_guid = $cvcourse->guid;
//    $cvmenu2->access_id = ACCESS_PUBLIC;
//    $cvmenu2->save();
//    $cvmenu2->menutype = "professor";
//    $cvmenu2->meta1 = "";
//    $cvmenu2->menuorder = 3;
//    $cvmenu2->indent="+";
//    add_entity_relationship($cvcourse->guid, "menu", $cvmenu2->guid);
// 
//     //add a couple of blog postings through a relationship to the above cvmenu
//    add_entity_relationship($cvmenu2->guid, "content", 164);  //added a blog post to this menu item
//    add_entity_relationship($cvmenu2->guid, "content", 64);  //added a blog post to this menu item
//    
//
//    $cvmenu3 = new ElggObject();
//    $cvmenu3->subtype = "cvmenu";
//    $cvmenu3->name = "Student Content";
//    $cvmenu3->owner_guid = $user->guid;
//    $cvmenu3->container_guid = $cvcourse->guid;
//    $cvmenu3->access_id = ACCESS_PUBLIC;
//    $cvmenu3->save();
//    $cvmenu3->menutype = "student";
//    $cvmenu3->meta1 = "";
//    $cvmenu3->menuorder = 4;
//    $cvmenu3->indent=".";
//    add_entity_relationship($cvcourse->guid, 'menu', $cvmenu3->guid);
//    
//     $cvmenu4 = new ElggObject();
//    $cvmenu4->subtype = "cvmenu";
//    $cvmenu4->name = "Student Content";
//    $cvmenu4->owner_guid = $user->guid;
//    $cvmenu4->container_guid = $cvcourse->guid;
//    $cvmenu4->access_id = ACCESS_PUBLIC;
//    $cvmenu4->save();
//    $cvmenu4->menutype = "folder";
//    $cvmenu4->meta1 = "";
//    $cvmenu4->menuorder = 4;
//    $cvmenu4->indent="-";
//    add_entity_relationship($cvcourse->guid, 'menu', $cvmenu4->guid);
//    
//     $cvmenu5 = new ElggObject();
//    $cvmenu5->subtype = "cvmenu";
//    $cvmenu5->name = "Student Content";
//    $cvmenu5->owner_guid = $user->guid;
//    $cvmenu5->container_guid = $cvcourse->guid;
//    $cvmenu5->access_id = ACCESS_PUBLIC;
//    $cvmenu5->save();
//    $cvmenu5->menutype = "student";
//    $cvmenu5->meta1 = "";
//    $cvmenu5->menuorder = 5;
//    $cvmenu5->indent="+";
//    add_entity_relationship($cvcourse->guid, 'menu', $cvmenu5->guid);
//
//
//
//
//
////Here we are listing all groups that are owned by the currently logged in user that have the testAttribute=123 set
//
//    $groupsowned = elgg_get_entities_from_metadata(array
//        ('type' => 'group',
//        'metadata_names' => array('testAttribute'),
//        'metadata_values' => array('123'),
//        'limit' => false,
//        'owner_guid' => $user->guid
//            )
//    );
//    echo elgg_echo('<br/>Groups owned by Professor: <br/>');
//    foreach ($groupsowned as $group)
//    {
//        echo elgg_echo("<br/>GROUP: " . $group->guid . ', ' . $group->title . ', ' . $group->testAttribute);
//    }
//    
////And here we are listing all groups that the currently logged in user belongs to.
//    $groupsmember = elgg_get_entities_from_relationship(array
//        ('type' => 'group',
//        'metadata_names' => array('cvcohort'),
//        'metadata_values' => array(1),
//        'limit' => false,
//        'relationship' => 'member',
//        'relationship_guid' => $user->guid
//            )
//    );
//    
//    echo elgg_echo('<br/><br/>Groups owned that professor belongs to: <br/>');
//    $somegroup = new ElggGroup;
//    foreach ($groupsmember as $group)
//    {
//        echo elgg_echo("<br/>GROUP: " . $group->guid . ', ' . $group->title . ', ' . $group->testAttribute);
//        $somegroup = $group;
//    }



//list all cvtreeitems that belong to somegroup and meet the testAttribute criteria
//    $treeitems = elgg_get_entities_from_metadata(array
//        ('type' => 'object',
//        'subtype' => 'cvtreeitem',
//        'contaier_guid' => $somegroup,
//        'limit' => false,
//            //some sort of ordering here
//            ));
//
//    echo elgg_echo("<br/>Testing pulling out all of the tree entities...<br/>");
//    foreach ($treeitems as $treeitem)
//    {
//        echo elgg_echo("<br/>cvtreeitem:  " . $treeitem->guid . $treeitem->name . $treeitem->title . $treeitem->testAttribute);
//        $somegroup = $group;
//    }
//
////add_entity_relationship(268, "testrelationshiptype", 267);
//
//    $relationshipTest = elgg_get_entities_from_relationship(array('relationship' => 'testrelationshiptype', 'relationship_guid' => '268'));
//
//    foreach ($relationshipTest as $relationship)
//    {
//        echo elgg_echo('<br/>A relationship exists between guid 268 and ' . $relationship->guid);
//    }
?>