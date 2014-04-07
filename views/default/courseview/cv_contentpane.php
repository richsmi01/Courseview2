<script>
    $(document).ready(function() {
       $header = document.querySelector('.elgg-page-header');
       $header.innerHTML ="<div id='cvcloud1'></div><div id='cvcloud2'></div><div id='cvcloud3'></div>"+$header.innerHTML;

//        document.getElementById('elgg-page-header').innerHtml += "<div id='cvcloud1'>div abc</div>";
    });
    
    window.onbeforeunload = function(){ 
    document.getElementById("hiddenmessage").id="notHidden";
            document.getElementById("notHidden").style.visibility = "visible"; 
             setInterval(blinker, 500);
            }
</script>
<?php

//if we're not logged into courseview then return
$status = ElggSession::offsetGet('courseview');
if (!$status)
{
    return;
}

elgg_load_library('elgg:courseview');
$user = elgg_get_logged_in_user_entity();
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');  
$cvcohort = get_entity (ElggSession::offsetGet('cvcohortguid'));
$menuitem = get_entity($cvmenuguid);  
$menutype = $menuitem->menutype;  //there are three types of menu items:  folder, professor, and student

//if the user is a prof and owns the course, include the ability to edit the course
//echo 'owner?'.cv_is_course_owner ($user, $cvcohort);
echo "<div id='hiddenmessage' style ='visibility:hidden; text-align:center; height:0px;' >Updating!</div>";

echo "<div id='cv_head'>";
if (cv_isprof($user))
{
    echo elgg_view('courseview/cv_profedit_contentview');  
}
echo '<h1 id="menuitem">'.$menuitem->name.'</h1><br>';
//echo "...".$menuitem->indent;
switch ($menutype)
{
    
    case "folder":    
        
        if ($menuitem->indent==0)  //if this is the first menu item in a course, display welcome
        {
            $cvcourse = get_entity ($cvcohort->getContainerGUID());
             $cv_course_owner = get_entity($cvcohort->container_guid)->getOwnerEntity();
            $cv_cohort_owner = $cvcohort->getOwnerEntity();
            echo "<br><p id = 'cvwelcome'>Welcome to " . $cvcohort->name."<p>";//$menuitem->name."</p>";
            
            
           
            echo "<br><div id='contentitem'> Course Name:  $cvcourse->title</div>";
            echo "<br><div id='contentitem'> Course Description: $cvcourse->description</div>";
             echo "<br><div id='contentitem'> Cohort Name:  $cvcohort->name</div>";
           echo "<br><div id='contentitem'> Course Owner:  $cv_course_owner->name</div>";
           echo "<br><div id='contentitem'> Cohort Professor:  $cv_cohort_owner->name</div>";
        }
        else
        {
            echo "<br><p id = 'cvwelcome'>" . $menuitem->name."</p>";
        }
        break;
        
    case "professor":
        echo elgg_view('courseview/cv_professor_contentview'); 
        break;
    
    case "student": 
        echo elgg_view('courseview/cv_student_contentview');
        break;
    
    //if menutype isn't folder, student or professor then we must have just logged in
    default:
        echo elgg_echo("<BR><BR><BR><div id ='cvwelcome' >WELCOME TO COURSEVIEW!</div>");
        break;
}

