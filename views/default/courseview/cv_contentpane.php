<?php

elgg_load_library('elgg:courseview');
$user = elgg_get_logged_in_user_entity();
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');  
$menuitem = get_entity($cvmenuguid);  
$menutype = $menuitem->menutype;  //there are three types of menu items:  folder, professor, and student

//if the user is a prof, include the ability to edit the course
if ((cv_isprof($user)))
{
    echo elgg_view('courseview/cv_profeditcontentview');  
}
echo '<h1>'.$menuitem->name.'</h1><br>';

switch ($menutype)
{
    case "folder":    
        if ($menuitem->menuorder==0)  //if this is the first menu item in a course, display welcome
        {
            echo "<br><p id = 'cvfolderdescription'>Welcome to " . $menuitem->name."</p>";
            $cvcohort = get_entity (ElggSession::offsetGet('cvcohortguid'));
            $cvcourse = get_entity ($cvcohort->getContainerGUID());
            echo "<br> $cvcourse->description";
        }
        else
        {
            echo "<br><p id = 'cvfolderdescription'>" . $menuitem->name."</p>";
        }
        break;
        
    case "professor":
        echo elgg_view('courseview/cv_professorcontentview'); 
        break;
    
    case "student": 
        echo elgg_view('courseview/cv_studentcontentview');
        break;
    
    //if menutype isn't folder, student or professor then we must have just logged in
    default:
        echo elgg_echo("<BR><BR><BR><div id ='cvwelcome' >WELCOME TO COURSEVIEW!</div>");
        break;
}
 

 
 
 
