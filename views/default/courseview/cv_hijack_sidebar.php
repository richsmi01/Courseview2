<?php
//Builds the CourseView sidebar
elgg_load_js ('cv_sidebar_js');
elgg_load_library('elgg:courseview');

$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
//$userguid = elgg_get_logged_in_user_guid();
$cv_home_url = elgg_get_site_url ().'courseview/courseview';

//get  a list of the cohorts that the logged in user belongs to
$cohorts = cv_get_users_cohorts();

$cv_mode = elgg_get_plugin_setting('display_cohorts_mode', 'courseview');

if ($cv_mode=='current' )
{
    if (cv_is_cvcohort(elgg_get_page_owner_entity()))
    {
        $cohorts = array (elgg_get_page_owner_entity());
    }
    else
    {
        $cohorts=null;
    }
    
}

//echo "<h3><a href = '$cv_home_url'>CourseView</a></h3>";

echo elgg_view_form('cv_menu_toggle');

$status = ElggSession::offsetGet('courseview');
if ($status==false)
{
    return;
}
echo "<div id = courseview_sidebar>";
//Begin building view

if ($cohorts==null)
{
    echo "<p id = 'cv_center'>No cohort selected</p>";
    echo "</div>";
    return;
}

if (cv_is_list_page())
{ 
    echo elgg_view ('courseview/cv_filter_content');
}


$count = 0;
echo "<div id = courseview_sidebar_menu>";
//loop through each cohort and build the tree menu
foreach ($cohorts as $cohort)
{
    $cv_current_examined_cohort_guid = $cohort->guid;
    
    $menuitems = cv_get_menu_items_for_cohort($cv_current_examined_cohort_guid);

    //Here we are building the html of the treeview control and adding the correct css classes so that my css
    //can turn it into a tree that can be manipulated by the user 
    echo elgg_echo('<div class ="css-treeview">');
    $indentlevel = 0;

    //now, loop through each menu item (by menusort order)
    foreach ($menuitems as $menuitem)
    {
        //If this menu item should be indented from the previous one, add a <ul> tag to start a new unordered list
        if ($menuitem->indent > $indentlevel)
        {
            echo elgg_echo('<ul>');
        }
        //if this menu item should be outdented, close off our list item and unorderedlist item
        else if ($menuitem->indent < $indentlevel)
        {
            echo elgg_echo('</li> </ul>');
        }
        //now we set indent level to the current menu item indent level so that we can check against it on the next iteration
        $indentlevel = $menuitem->indent;

        //setting up attributes to insert into the html tags
        $name = $menuitem->name;
        if ($indentlevel == 0)  //if this is a topline course menuitem, use the cohort name instead
        {
            $name = $cohort->title ;
        }
        $id1 = $count; //$menuitem->menuorder;
        $count++;
        $class2 = "";
        $indent = $menuitem->indent;
        if ($menuitem->guid == $cvmenuguid && $cv_current_examined_cohort_guid == $cv_cohort_guid)
        {
            $class2 = " cvcurrent";  //setting the current menu item
        }
        
        if ($menuitem->menutype == "professor")
        {
            $class3 = "professor_item";
        }
        else 
        {
            $class3 ="student_item";
        }
        if ($menuitem->menutype == "folder")
        {
            echo "<li>";
            echo "<input type ='checkbox' abc ='m' name='$indent' class ='cvmenuitem $class2' id ='$id1' />";
            echo "<label>";
            echo "<a title = '$name' href='" . elgg_get_site_url() . "courseview/cv_contentpane/" . $cv_current_examined_cohort_guid . "/" . $menuitem->guid . "'> " . $name . "</a>";
            echo "</label>";
        }
        //otherwise, let's just create a link to the cv_contentpane and pass the guid of the menu object...the css class indent is also added here
        else
        {
    
            echo elgg_echo("<li><a title='$name' abc ='m' name='$indent' class = 'cvmenuitem $class2 $class3 indent' id ='$id1' "
                . "href ='" . elgg_get_site_url() . "courseview/cv_contentpane/" . $cv_current_examined_cohort_guid . "/" . $menuitem->guid . "' >" . $name . "</a></li>");
        }
    }
    echo '</div>';
}
echo'</div>';
//echo '<br>';
echo "</div>";
