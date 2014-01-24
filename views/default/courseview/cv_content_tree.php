
<!--move our link to just above the save button-->
<script>
    $(document).ready(function() {
        $(".elgg-form input[type='submit']input[value='Save']").before($("#add_entity_to_cohort_menus"));
        $(".elgg-form input[type='submit']input[value='Post']").before($("#add_entity_to_cohort_menus"));
        $(".elgg-form input[type='submit']input[value='Upload']").before($("#add_entity_to_cohort_menus"));
    });
</script>



<!--A bit of javascript to collapse the cohorttree unless the user clicks on the topline-->
<script>
    function showCVAdd(selected_menu) {
        // alert (selected_menu);
        myDiv = document.querySelector("#" + selected_menu);

        if (myDiv.style.visibility == "visible") {
            myDiv.style.visibility = 'hidden';
            myDiv.style.height = '0px';
            myDiv.style.padding = '0px';

        }
        else
        {
            myDiv.style.visibility = 'visible';
            myDiv.style.height = 'auto';
            myDiv.style.padding = '10px';
        }
    }
</script>

<?php

elgg_load_library('elgg:cv_content_tree_helper_functions');
$cv_cohortguid = ElggSession::offsetGet('cvcohortguid');
$cv_cohort = get_entity(ElggSession::offsetGet('cvcohortguid'));
$entity = ($vars['entity']);
$current_content_entity = $entity;
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$userguid = elgg_get_logged_in_user_guid();
$cvuser = get_entity($userguid);

elgg_load_library('elgg:courseview');

$prof_menu_item_already_used = array();
$cohorts = cv_get_users_cohorts();  //get  a list of the cohorts that the logged in user belongs to

echo "<div id='add_entity_to_cohort_menus'>";
if (cv_isprof($cvuser))
{
    echo "<input onclick = 'showCVAdd(" . '"cvaddtocourse"' . ")' id ='cohort_menu_items' type ='checkbox' style='display:inline' />";
    echo "<label  for ='course_menu_items' style='display:inline'>Add this content to the professor content areas of Courseview Courses </label>";
    echo "<div id ='cvaddtocourse'>";

    $cvcourses = cv_get_prof_owned_courses($cvuser);
    $indentlevel = 0;
    foreach ($cvcourses as $cvcourse)
    {
        $menuitems = cv_get_menu_items_for_course($cvcourse->guid);
        echo '<div class ="css-treeview">';
        foreach ($menuitems as $menuitem)
        {
            $name = $menuitem->name;
            $relationship = cv_build_relationship($menuitem, $cohortguid);
            $checkoptions = setCheckStatus($menuitem, $relationship, $current_content_entity, $cvmenuguid, $cv_cohortguid, $cohortguid);
            echo buildindentstring($menuitem, $indentlevel);
            $indentlevel = $menuitem->indent;
            $value = $menuitem->guid . "|" . $cohortguid;
            if ($menuitem->menutype == "folder")
            {
                echo "<li>";
                echo "<input type ='checkbox'  name='$indent' class ='cvmenuitem'   />";
                echo "<label>";
                echo $name;
                echo "</label>";
            } 
            elseif ($menuitem->menutype == 'student')     // && !cv_isprof(get_entity($userguid))) || in_array($menuitem->guid, $prof_menu_item_already_used))
            {
                echo "<p class ='indent'>$name.</p>";
            } 
            else
            {
                echo "<li>";
                echo elgg_view('input/checkbox', array('name' => 'menuitems[]', 'id' => $value, 'value' => '+' . $value, 'class' => 'cvinsert', 'checked' => $checkoptions, 'default' => '-' . $value));
                echo "<a class ='indent'>$name.</a>";
            }
        }
        echo "</div>";
    }
    echo "</div>";
}

echo "<input onclick = 'showCVAdd(" . '"cv_add_to_cohort"' . ")' id ='cohort_menu_items' type ='checkbox' style='display:inline' />";
echo "<label  for ='cohort_menu_items' style='display:inline'>Add this content to Courseview Cohorts </label>";
echo "<div id ='cvaddtocohort'>";

foreach ($cohorts as $cohort)
{
    $cohortguid = $cohort->guid;

    // building the html of the treeview control and adding the correct css classes so that the css
    //can turn it into a tree that can be manipulated by the user 
    echo '<div class ="css-treeview">';
    //we start our tree with indentlevel at 0.  The only menu items that will be at indent level 0 will be the course container folder
    $indentlevel = 0;
    //now, loop through each menu item (by menusort order)
    $menuitems = cv_get_menu_items_for_cohort($cohortguid);
     
    /* We will check each $menuitem to see whether or not we should add a check in the checkbox associated with this
         * tree item.  If a relationship exists between the $menuitem and the current_content_entity, then the tree item needs to 
         * have a check in the checkbox.
         * 
         * Note that:
         * 
         * A $menuitem of type professor will have a relationship in the following format:  
         *              $menuitem->guid, 'content', $current_content_entity -> guid.
         * This is because any content inside of a $menuitem of type professor will belong to the entire course
         * 
         * A $menuitem of any other type (ie student) will have a relationship in the following format:
         *              $menuitem->guid, 'content'.<cohortguid>, $current_content_entity->guid
         * This is becase content inside of a $menuitem of type student will belong only to the current cohort
         * 
         * By separting relationships in this fashion, we can access student content in any particular cohort where as the
         * professor content will remain constant in all cohorts (ie, belongs to the entire course)
         */

    //figure out the correct $relationship string  (professor content is "content", studen content is "content<GUID> where guid is the cohort guid.
    foreach ($menuitems as $menuitem)
    {
        $relationship = cv_build_relationship($menuitem, $cohortguid);

        //decide whether or not the current $menuitem should have the checkbox checked when it is rendered.
        $checkoptions = setCheckStatus($menuitem, $relationship, $current_content_entity, $cvmenuguid, $cv_cohortguid, $cohortguid);

        //figure out whether this item should be indented, outdented or stay the same.
        echo buildindentstring($menuitem, $indentlevel);
        //now we set indent level to the current menu item indent level so that we can check against it on the next iteration
        $indentlevel = $menuitem->indent;

        //set up attributes to insert into the html tags
        $name = $menuitem->name;
        $indent = $menuitem->indent;
        $value = $menuitem->guid . "|" . $cohortguid;
        //build html depending on menu type: student, professor, or folder
        if ($menuitem->menutype == "folder")
        {

            echo "<li>";
            echo "<input type ='checkbox' id ='$value'  name='$indent' class ='cvmenuitem'   />";
            echo "<label for ='$value' class ='cvfolder'>";
            echo $name;
            echo "</label>";
        }
        //otherwise, let's just create a link to the cv_contentpane and pass the guid of the menu object...the css class indent is also added here
        elseif ($menuitem->menutype == 'professor')// && !cv_isprof(get_entity($userguid))) || in_array($menuitem->guid, $prof_menu_item_already_used))
        {
            echo "<p class ='indent  disabled'>$name.</p>";
        } else
        {
            /* Note that the value that we are passing to the checkboxes is a String -- each String  contains three pieces 
             * of information in the format Xmenuitemguid|cohortguid where X is a + if a new relationship should be created.
             */


            echo "<li>";
            echo elgg_view('input/checkbox', array('name' => 'menuitems[]', 'id' => $value, 'value' => '+' . $value, 'class' => 'cvinsert', 'checked' => $checkoptions, 'default' => '-' . $value));
            echo "<a class ='indent  disabled'>$name.</a>";
            /* we need to flag any menuitems of type professor so that they are only assigned a checkbox the first time they 
             * are display.  Theoretically, a professor running two cohorts of the same course at the same time could end up
             * with two of the cohorts showing up in the tree.  If that is the case, we only want the prof to be able to change
             * the actual course scaffolding content in the first cohort displayed for that course
             */
        }
    }
    echo '</div>';
}
echo '<br>';
echo '</div>';

