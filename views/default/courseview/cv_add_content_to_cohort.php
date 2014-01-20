<?php

//Check to see if the action string contains any of our approved plugins...If it does, and the user is in a cohort, display the page.


elgg_load_library('elgg:courseview');
elgg_load_library('elgg:cv_debug');
//var_dump($vars);
$attributes = $vars['attibutes'];
//var_dump ($attributes);

$action = $vars['action'];

//$actionsequence= $action;//.'discussion/edit';  //had to add this to make group forums work...need to think about how to fix this
//echo 'action:'.$action;
//cv_debug("actionsequence: $actionsequence", "", 5);

/* first, we should check to see if the user has any cohorts...if they don't, return without doing anything else.
 * Also, certain pages are beyond our ability to automatically filter out.  For instance, we do want the cv_content_tree to
 * pop up when desiging a poll.  However, we don't want it to pop up when taking the poll.  The only way to do this is to look for
 * a particular word in the $action string that is created by the plugin.  Again, for the poll plugin, this $action String looks something
 * like this: http://localhost/elgg/action/polls/vote  -- In this case we are able to pull out the word vote as being unique to this page and check 
 * for it.  If we find it, we don't want to add cv_content_tree to the page.
 * 
 * Note - For now, I've just hard-coded the 'vote' but what I really should do is eventually add a text input to the settings form that will allow
 * the user to type in a set of keywords, separated by spaces, and use them instead of a hardcoded 'vote'.  
 * 
 * Further Note - I should add the ability for my cv_debug to switch to certain modes from the settings form.  For instance, a series of checkboxes  of 
 * all possible logging activities.  One could be 'Show Actions' which the administrator could select and have all actions added to the log file. 
 *  That way the administrator could look for filter words.
 */
if (!cv_is_courseview_user() || strpos($action, 'vote') !== false)
{
    return;
}


if (strpos($action, 'reply') )
{
    return;
}

/* Determine if the current view is editing a plugin object that is valid in courseview.  The list of the 
 * valid plugins for courseview is set in the settings view under the administration section of Elgg.
 */
$validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));
$validkeys = array_keys($validplugins);
        $validkeys[ ] = "discussion";
$donotdisplay = true;

cv_debug( "addcontent to cohort action :".$action."<br>","",9);

foreach ($validkeys as $plugin)
{
    cv_debug( "Plugin :".$plugin.'<br>',"",9);
    if (strpos($action, $plugin) !== false)
    {
        $donotdisplay = false;
        cv_debug( "Match! :".$plugin.'<br>',"",9);
        break;
    }
}

if ($donotdisplay)
{
    return true;
}
?>
<!--<script>
    /**
     * A bit of javascript to collapse the cohorttree unless the user clicks on the topline
     */
    function showCVAdd() {

        myDiv = document.querySelector("#addToCohort");
        //alert (myDiv.style.visibility);
        if (myDiv.style.visibility == "visible") {
            myDiv.style.visibility = 'hidden';
            myDiv.style.height = '0px';

        }
        else
        {
            myDiv.style.visibility = 'visible';
            myDiv.style.height = 'auto';

        }
    }
</script>-->


<!--<div id='add_entity_to_cohort_menus'>
    <input onclick = "showCVAdd()" id ="showtree" type ='checkbox' style='display:inline' />

    <label  for ="showtree" style="display:inline">Add this content to a CourseView cohort </label><br><br>
    <div id ='addToCohort'>-->
        <?php
        
         
//        $cv_cohort = get_entity(ElggSession::offsetGet('cvcohortguid'));
//        $entity = $current_content_entity->guid;
        //echo elgg_view('courseview/debug');
        $cv_menuitem = get_entity(ElggSession::offsetGet('cvmenuguid'));
       
 //       $vars = array('cv_menutype' => $cvmenuitem->menutype, 'entity'=>$entity);
        echo elgg_view('courseview/cv_content_tree', $vars);



        