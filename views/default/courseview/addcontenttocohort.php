<?php
//Check to see if the action string contains any of our approved plugins...If it does, and the user is in a cohort, display the page.
 

elgg_load_library('elgg:courseview');
elgg_load_library('elgg:cv_debug');
 

$action = $vars['action'];
cv_debug ("action: $action", "",5);

/*first, we should check to see if the user has any cohorts...if they don't, return without doing anything else.
 * Also, certain pages are beyond our ability to automatically filter out.  For instance, we do want the cvaddtocohorttreeview to
 * pop up when desiging a poll.  However, we don't want it to pop up when taking the poll.  The only way to do this is to look for
 * a particular word in the $action string that is created by the plugin.  Again, for the poll plugin, this $action String looks something
 * like this: http://localhost/elgg/action/polls/vote  -- In this case we are able to pull out the word vote as being unique to this page and check 
 * for it.  If we find it, we don't want to add cvaddtocohorttreeview to the page.
 * 
 * Note - For now, I've just hard-coded the 'vote' but what I really should do is eventually add a text input to the settings form that will allow
 * the user to type in a set of keywords, separated by spaces, and use them instead of a hardcoded 'vote'.  
 * 
 * Further Note - I should add the ability for my cv_debug to switch to certain modes from the settings form.  For instance, a series of checkboxes  of 
 * all possible logging activities.  One could be 'Show Actions' which the administrator could select and have all actions added to the log file. 
 *  That way the administrator could look for filter words.
 */
if ( !cv_is_courseview_user()|| strpos($action, 'vote')!==false)
{
    return;
}

/* Determine if the current view is editing a plugin object that is valid in courseview.  The list of the 
 * valid plugins for courseview is set in the settings view under the administration section of Elgg.
 */
$validplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));
$validkeys = array_keys($validplugins);
$donotdisplay = true;

foreach ($validkeys as $plugin)
{
  //  echo "Plugin :".$plugin.'<br>';
    if (strpos($action, $plugin)!==false)
    {
        $donotdisplay=false;
        break;
    }
}

    if ($donotdisplay)
    {
       return true;
    }
?>
<script>
    /**
     * A bit of javascript to collapse the cohorttree unless the user clicks on the topline
     */
    function showCVAdd() {
       
        myDiv = document.querySelector("#addToCohort");
        //alert (myDiv.style.visibility);
        if (myDiv.style.visibility=="visible") {
            myDiv.style.visibility='hidden';
            myDiv.style.height='0px';
                 
        }
        else
        {
            myDiv.style.visibility='visible';
            myDiv.style.height='auto';
                 
        } 
    }
</script>


<div id='add_entity_to_cohort_menus'>
    <input onclick = "showCVAdd ()" id ="showtree" type ='checkbox' style='display:inline' />
    <label  for ="showtree" style="display:inline">Add this content to a CourseView module </label><br><br>
    <div id ='addToCohort'>
    <?php
     //echo elgg_view('courseview/debug');
        echo elgg_view('courseview/cvaddtocohorttreeview',$vars);  //what $vars???

