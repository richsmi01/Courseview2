<?php

  elgg_load_library('elgg:courseview');
$user = elgg_get_logged_in_user_entity();
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');  //current menu item guid (stored in session)
$menuitem = get_entity($cvmenuguid);  //get the menuitem object
$menutype = $menuitem->menutype;  //there are three types of menu items:  folder, professor, and student
//$base_path=dirname(__FILE__); //gives a relative path to the directory where this file exists
 
//if the user is a prof, include the ability to edit the course
if ((cv_isprof($user)))
{
    echo elgg_view('courseview/profeditcontentview');  
}
echo '<h1>'.$menuitem->name.'</h1><br>';
//depending on what type of module is selected, load the correct view for folder, professor or student
switch ($menutype)
{
    case "folder":
        echo elgg_echo("<br><p id = 'cvfolderdescription'>" . $menuitem->name."</p>");
        break;
    case "professor":
    case "bundle":    //delete this down the road
        echo elgg_view('courseview/professorcontentview'); 
        break;
    case "student": 
         echo elgg_view('courseview/studentcontentview');
        break;
    default:
        echo elgg_echo("<BR><BR><BR><div id ='cvwelcome' >WELCOME TO COURSEVIEW!</div>");
        break;
}
 

 
 
 
