
<!--move our link to just above the save button-->
<script>
    $(document).ready (function () {
        $(".elgg-form input[type='submit']").before($("#add_entity_to_cohort_menus"));
    });
</script>

<!--Begin our html-->
<div class ="cvtreeaddtocohort">
<br>

<?php
$current_content_entity= ($vars['entity']);
$cvcohortguid = ElggSession::offsetGet('cvcohortguid');
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$userguid = elgg_get_logged_in_user_guid();

//we'll need some of the library methods here
elgg_load_library('elgg:courseview');

$count = 0;
$prof_menu_item_already_used = array();
//get  a list of the cohorts that the logged in user belongs to
$cohorts = cv_get_users_cohorts();

//loop through each cohort and build the tree menu
foreach ($cohorts as $cohort)
{
    $cohortguid = $cohort->guid;
    
    // building the html of the treeview control and adding the correct css classes so that the css
    //can turn it into a tree that can be manipulated by the user 
    echo ('<div class ="css-treeview">');
    //we start our tree with indentlevel at 0.  The only menu items that will be at indent level 0 will be the course container folder
    $indentlevel = 0;
    //now, loop through each menu item (by menusort order)
    $menuitems = cv_get_menu_items_for_cohort($cohortguid);
    foreach ($menuitems as $menuitem)
    {
        /* We will check each $menuitem to see whether or not we should add a check in the checkbox associated with this
         * tree item.  If a relationship exists between the $menuitem and the current_content_entity, then the tree item needs to 
         * have a check in the checkbox.
         * 
         * Note that:
         * 
         * A $menu_item of type professor will have a relationship in the following format:  
         *              $menu_item->guid, 'content', $current_content_entity -> guid.
         *                      This is because any content inside of a $menu_item of type professor will belong to the entire course
         * 
         * A $menu_item of any other type (ie student) will have a relationship in the following format:
         *              $menu_item->guid, 'content'.<cohortguid>, $current_content_entity->guid
         *                      This is becase content inside of a $menu_item of type student will belong only to the current cohort
         * 
         * By separting relationships in this fashion, we can access student content in any particular cohort where as the
         * professor content will remain constant in all cohorts (ie, belongs to the entire course)
         */
           
        //figure out the correct $relationship string  (professor content is "content", studen content is "content<GUID> where guid is the cohort guid.
        $relationship =buildRelationship ($menuitem, $cohortguid);
        
        //decide whether or not the current $menuitem should have the checkbox checked when it is rendered.
        $checkoptions = setCheckStatus ($menuitem, $relationship, $current_content_entity,$cvmenuguid, $cvcohortguid, $cohortguid);

        //figure out whether this item should be indented, outdented or stay the same.
        echo buildindentstring($menuitem, $indentlevel);
        //now we set indent level to the current menu item indent level so that we can check against it on the next iteration
        $indentlevel = $menuitem->indent;

        //set up attributes to insert into the html tags
        $name = $menuitem->name;
        $count++;
        $indent = $menuitem->indent;

        //build html depending on menu type: student, professor, or folder
        if ($menuitem->menutype == "folder")
        {
            echo "<li>";
            echo "<input type ='checkbox'  name='$indent' class ='cvmenuitem'   />";
            echo "<label>";
            echo "<a href='" . elgg_get_site_url() . "courseview/contentpane/" . $cohortguid . "/" . $menuitem->guid . "'> " . $name . "</a>";
            echo "</label>";     
        }
        //otherwise, let's just create a link to the contentpane and pass the guid of the menu object...the css class indent is also added here
        elseif (($menuitem->menutype == 'professor' && !cv_isprof(get_entity($userguid))) || in_array($menuitem->guid, $prof_menu_item_already_used))
        {
            echo "<span class ='indent  disabled'>$name.</span>";            
        } 
        else
        {
            /* Note that the value that we are passing to the checkboxes is a String -- each String  contains three pieces 
             * of information in the format Xmenuitemguid|cohortguid where X is a + if a new relationship should be created.
             */
            $value = $menuitem->guid . "|" . $cohortguid;
        
            echo ("<li>");
            echo elgg_view('input/checkbox', array('name' => 'menuitems[]', 'value' => '+' . $value, 'class' => 'cvinsert', 'checked' => $checkoptions, 'default' => '-' . $value));
            //maybe make this a label instead of an anchor
            echo "<label  for ='$value'>$name.$value.$checkoptions</label>";
      
            if ($menuitem->menutype == 'professor' )
                {
                    $prof_menu_item_already_used [] = $menuitem->guid;
                }       
            }
    }
echo '</div>';
}
echo '<br>';
echo '</div>';


function buildRelationship ($menuitem, $cohortguid)
{
    $rel = 'content';
        //however, if the $menuitem is not of type 'professor' (ie, of type 'student'), then we need to append the particulart  cohort to 'content'
        if ($menuitem->menutype !='professor')
        {
            $rel .=  $cohortguid;
        }
        return $rel;
}

function setCheckStatus ($menuitem, $rel, $current_content_entity,$cvmenuguid, $cvcohortguid, $cohortguid)
{
   //if a relationship exists, them set we need the menu item to have a check in the checkbox
    if (check_entity_relationship($menuitem->guid, $rel, $current_content_entity->guid)->guid_one > 0)
        {
            $checkoptions = true;
        }
        else
        {
            $checkoptions = false;
        }
       //also, if the menu item and cohort is same menu item and cohort that the courseview interface is currently on, it must be checked
        if ($cvmenuguid == $menuitem->guid && $cohortguid ==$cvcohortguid)   //and correct cohort???
        {
            $checkoptions = true;
        }
        return $checkoptions;
}

function buildindentstring ($menuitem, $indentlevel)
{
    $returnhtml ="";
    if ($menuitem->indent > $indentlevel)
        {
            $returnhtml.='<ul>';
        }
        //if this menu item should be outdented, close off our list item and unorderedlist item
        else if ($menuitem->indent < $indentlevel)
        {
            $returnhtml.= '</ul></li>';
        }
        return $returnhtml;
}