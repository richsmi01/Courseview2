<?php

echo get_input ('newcoursename');
exit;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
  elgg_load_library('elgg:courseview');
//echo 'edit a course action';
//$userguid = elgg_get_logged_in_user_guid();
//
//$groupsmember = elgg_get_entities_from_relationship(array
//    ('type' => 'group',
//    'metadata_names' => array('cvcohort'),
//    'metadata_values' => array(1),
//    'limit' => false,
//    'relationship' => 'member',
//    'relationship_guid' => $userguid,
//        )
//);
$cohorts = cv_get_users_cohorts ();


//if (($groupsmember))
//{
//   // echo 'belongs to at least one group';
//} else
//{
//    //echo 'no';
//}


$cohortguid=ElggSession::offsetGet('cvcohortguid');
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');

foreach ($cohorts as $cohort)
{
    $cohortguid = $cohort->guid;
//This pulls all menu entitities that have a relationship with this course...
$menu = elgg_get_entities_from_relationship(array
        ( 'relationship_guid' => get_entity($cohortguid)->container_guid,   
            'relationship' => 'menu',
            'type'=>'object',  
            'subtype' =>'cvmenu',
            'order_by_metadata' =>array ('name'=>'menuorder', 'direction'=>'ASC', 'as'=>'integer'),
            'limit'=>1000,
        )
     );


//Here we are building the html of the treeview control and adding the correct css classes so that my css
//can turn it into a tree that can be manipulated by the user 
//echo elgg_echo ('<div class ="css-treeview">');
  foreach ($menu as $menuitem)
{
    //If this menu item should be indented from the previous one, add a <ul> tag to start a new unordered list
    if ($menuitem->indent=='+')
    {
        echo elgg_echo('
            <ul>
            ');
    }
    
    //if this menu item should be outdented, close off our unordered list and list item
    if ($menuitem->indent=='-')
    {
        echo elgg_echo('
            </ul>
            </li>
            ');
    }
    //if the menu item is a folder type, add a checkbox which the css will massage into the collapsing tree
    $name = '';
    if ($menuitem->guid==$cvmenuguid)
    {
        $name="* ";  //currently I'm just adding a * to the active module but eventually I should use it to force the active module folder to default to open
    }
   $name = $name.$menuitem->name;
    $name .= '--'.$menuitem->menuorder.$menuitem->indent;
    if ($menuitem->menutype=="folder")
    {
         echo elgg_echo("<ul>
           <li><input type ='checkbox'/><label><a href='".elgg_get_site_url()."courseview/contentpane/".$cohortguid."/".$menuitem->guid."'> ".$name."</a></label>");
    }
    //otherwise, let's just create a link to the contentpane and pass the guid of the menu object...the css class indent is also added here
 else
    {
        echo elgg_echo("<li><a class = 'indent' href ='".  elgg_get_site_url()."courseview/contentpane/".$cohortguid."/".$menuitem->guid."' >".$name."</a></li>");
    }
     
echo elgg_echo ('</div>')  ;  
}

}




    
?>
