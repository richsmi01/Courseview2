
<script>
    //This script adds divs to allow for animated clouds if desired
    $(document).ready(function() {
        $header = document.querySelector('.elgg-page-header');
        $header.innerHTML = "<div id='cvcloud1'></div><div id='cvcloud2'></div><div id='cvcloud3'></div>" + $header.innerHTML;
    });
    //this code sets up the flashing "Working" message when fetching from the server
    window.onbeforeunload = function() {
        document.getElementById("hiddenmessage").id = "notHidden";
        document.getElementById("notHidden").style.visibility = "visible";
        setInterval(blinker, 500);
    }
</script>
<?php
/**
 * The main view used in CourseView
 * 
 * @author Rich Smith
 */
 

if (cv_isprof($user))
{
    echo elgg_view('courseview/cv_profedit_contentview');
}

//if we're not logged into courseview then return
$status = ElggSession::offsetGet('courseview');
if ( get_input ('cv_menu_guid')==0)
{
    $status=true;
}
if (!$status )
{
    return;
}

elgg_load_library('elgg:courseview');
$user = elgg_get_logged_in_user_entity();
$cv_menu_guid = get_input('cv_menu_guid'); 
$cv_cohort = elgg_get_page_owner_entity();  
$menu_item = get_entity($cv_menu_guid);
$menu_type = $menu_item->menutype;  //there are three types of menu items:  folder, professor, and student


//if the user is a prof and owns the course, include the ability to edit the course

//Add the message asking the user to wait for processing.
echo "<div id='hiddenmessage' style ='visibility:hidden; text-align:center; height:0px;' >".elgg_echo ( 'cv:views:cv_contentpane:wait_message')."</div>";
echo "<div id='cv_head'>";
echo '<h1 id="menuitem">' . $menu_item->name . '</h1><br>';

switch ($menu_type)  //what kind of cvmenu item is it?  folder, professor, or student
{
    case "folder":
        if ($menu_item->indent == 0)  //if this is the first menu_item in a course, display welcome
        {
            $cvcourse = get_entity($cv_cohort->getContainerGUID());
            $cv_course_owner = get_entity($cv_cohort->container_guid)->getOwnerEntity();
            $cv_cohort_owner = $cv_cohort->getOwnerEntity();
            echo "<br><p id = 'cvwelcome'>".elgg_echo('cv:views:cv_contentpane:welcome_to', array ($cv_cohort->name)) . "<p>";
            echo "<br><div id='contentitem'>".elgg_echo ('cv:views:cv_contentpane:course_name',array($cvcourse->title))."</div>";
            echo "<br><div id='contentitem'> ".elgg_echo('cv:views:cv_contentpane:course_description',array($cvcourse->description))."</div>";  
            echo "<br><div id='contentitem'> ".elgg_echo('cv:views:cv_contentpane:cohort_name',array($cv_cohort->name))."</div>";
            echo "<br><div id='contentitem'> ".elgg_echo('cv:views:cv_contentpane:course_owner',array($cv_course_owner->name))."</div>";
            echo "<br><div id='contentitem'> ".elgg_echo('cv:views:cv_contentpane:cohort_owner',array($cv_cohort_owner->name))."</div>";
            echo '<br><label>'.elgg_echo('cv:views:cv_contentpane:posting_message').'</label><br>';
            $unassigned_content = cv_get_content_not_assigned();
            echo $unassigned_content;
        } else
        {
            echo "<br><p id = 'cvwelcome'>" . $menu_item->name . "</p>";
        }
        break;

    case "professor":
        echo elgg_view('courseview/cv_professor_contentview');
        break;

    case "student":
        echo elgg_view('courseview/cv_student_contentview');
        break;

    //if menu_type isn't folder, student or professor then we must have just logged in
    default:
        echo elgg_echo("<BR><BR><BR><div id ='cvwelcome' >".elgg_echo ('cv:views:cv_contentpane:big_welcome')."</div>");
        //system_message("Welcome");
        break;
}

