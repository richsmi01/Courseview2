<?php

/* This view determines whether or not to display the cv_content_tree at the bottom of a page.  We want the cv_content_tree
 * to appear when we are creating a new content artifact that is one of the approved plugins for CourseView.  However, there 
 * are many cases where we don't want the cv_content_tree to show up.  
 * 
 * We can easily filter out plugins that aren't approved.
 * However, certain pages are beyond our ability to automatically filter out.  For instance, we do want the cv_content_tree to
 * pop up when desiging a poll.  However, we don't want it to pop up when taking the poll.  The only way to do this is to look for
 * a particular word in the $action string that is created by the plugin.  For the poll plugin, this $action String looks something
 * like this: http://localhost/elgg/action/polls/vote  -- In this case we are able to pull out the word vote as being unique to this page and check 
 * for it.  If we find it, we don't want to add cv_content_tree to the page.  This vote word gets entered in the settings page by the admin and we 
 * check the action against this list
 * 
 * Also, if the posting is a reply, we don't want to show cv_content_tree
 */
elgg_load_library('elgg:courseview');
$action = $vars['action'];

$exceptions_to_the_rule = elgg_get_plugin_setting('dont_show_add_content_to_cohort_menu', 'courseview');
$exceptions_array = explode(' ', $exceptions_to_the_rule);
foreach ($exceptions_array as $exception_item)
{
    if (!cv_is_courseview_user() || strpos($action, $exception_item) !== false)
    {
        return;
    }
}

//If the content being generated is a reply, then don't show the cv_add_content_to_cohort menu
if (strpos($action, 'reply'))
{
    return;
}

/* Determine if the current view is editing a plugin object that is valid in courseview.  The list of the 
 * valid plugins for courseview is set in the settings view under the administration section of Elgg. */

$user = elgg_get_logged_in_user_entity();
$validplugins = cv_get_valid_plugins($user);
$validkeys = array_keys($validplugins);
$validkeys[] = "discussion";
$donotdisplay = true;
//Check to see if the action string contains any of our approved plugins...If it does, and the user is in a cohort, display the page.
foreach ($validkeys as $plugin)
{
    if (strpos($action, $plugin) !== false)
    {
        $donotdisplay = false;
        break;
    }
}

if ($donotdisplay)
{
    return true;
}

//finally, if this is a page that needs the cv_content_tree, then go ahead and load it.
echo elgg_view('courseview/cv_content_tree', $vars);



