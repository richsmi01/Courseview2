<?php

/*
 * Builds and returns the String that will be used to determine if a relationship entity exists between the 
 *  menuitem currently being examined and the content object associated with the current view.
 * 
 * @param $menuitem the menuitem object that we are currently examining
 * @param $cohortguid the guid of the current cohort.
 * 
 * @return the string that represents potential the relationship between the menuitem and the content object
 */

//move functions into a library instead of in a view

function cv_build_relationship($menuitem, $cohortguid)
{
    $rel = 'content';
    //however, if the $menuitem is not of type 'professor' (ie, of type 'student'), then we need to append the particulart  cohort to 'content'
    if ($menuitem->menutype != 'professor')
    {
        $rel .= $cohortguid;
    }
    return $rel;
}

/* Determines whether the html checkbox that is being generated to represent the current menuitem should have a checkmark
 * in it or not.
 * 
 * @param $menuitem the menuitem object that we are currently examining
 */

//consider placing all of these arguments into an associative array (string keys)
function setCheckStatus($menuitem, $rel, $current_content_entity, $cvmenuguid, $cvcohortguid, $cohortguid)
{
    //if a relationship exists, them set we need the menu item to have a check in the checkbox
    // echo '<br>&&&'.$menuitem->guid. $rel. $current_content_entity->guid.'<br>';
    $checkoptions = false;

    if (check_entity_relationship($menuitem->guid, $rel, $current_content_entity->guid)->guid_one > 0)
    {
        $checkoptions = true;
        // echo'$$$';
    }
    //also, if the menu item and cohort is same menu item and cohort that the courseview interface is currently on, it must be checked
    if ($cvmenuguid == $menuitem->guid && $cohortguid == $cvcohortguid)   //and correct cohort???
    {
        $checkoptions = true;
    }
    //if the menuitem is of type professor, ignore all the cohort stuff and check to see if the menuitem we are examining
    //is the same one as CV is currently displaying.  If so, add a check to the checkbox.
    if ($menuitem->menutype == 'professor' && $cvmenuguid == $menuitem->guid)
    {
        $checkoptions = true;
    }

    return $checkoptions;
}

function cv_buildindent_string($menuitem, $indentlevel)
{
    $returnhtml = "";
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
