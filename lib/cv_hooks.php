<?php

function cv_group_buttons($hook, $type, $return, $params)
{
    if (!elgg_instanceof($params['entity'], 'group'))
    {
        return $return;
    }
    $is_cv_owner = cv_is_cohort_owner(elgg_get_logged_in_user_entity(), $params['entity']);
    $is_cv_admin = elgg_get_logged_in_user_entity()->isAdmin();
    if ($is_cv_admin || $is_cv_owner)
    {
        if (cv_is_cvcohort($params['entity']))
        {
            $link = new ElggMenuItem('cv_group_button', 'remove link to CourseView', "ajax/view/courseview/remove_group_from_cohort?guid={$params['entity']->guid}");
            $link->addLinkClass("cv_remove_group_from_cohort");
            $link->addLinkClass('elgg-lightbox');
            $return[] = $link;
        } else
        {
            $link = new ElggMenuItem('cv_group_button', 'link to CourseView', "ajax/view/courseview/cv_make_group_a_cohort?guid={$params['entity']->guid}");
            $link->addLinkClass("cv_add_to_cohort");
            $link->addLinkClass('elgg-lightbox');
            $return[] = $link;
        }
    } else if (cv_is_cvcohort($params['entity']))
    {
        $link = new ElggMenuItem('cv_button', 'CV Enabled!', "");
        $link->addLinkClass("cv_enabled");
        $return[] = $link;
    }
    return $return;
}


/**
 * When the sidebar plugin hook fires, cv_sidebar_intercept takes over and adds dropdowns and 
 * the course menu tree as needed
 *
 * @param string $hook  not actually needed or used
 * @param string $entity_type  not actually needed or used
 * @param string $returnvalue  If CourseView is enabled, the tree menu is added to the $returnvalue and returned to elgg
 * @param string $entity_type  not actually needed or used
 *
 * @return Returns the value of of $returnvalue that was passed by the hook.  This value may now have our tree view menu in it
 */
function cv_sidebar_intercept($hook, $entity_type, $returnvalue, $params)
{
    //echo elgg_view_form('cv_admin_toggle');
     if (cv_isprof($user))
{
            echo elgg_view('output/url', array('text'=>'CourseView Admin', 'href' => 'courseview/cv_contentpane/0/0',   'id'=>'menutogglebutton', 'class'=>'elgg-button elgg-button-submit'));
}
    $show_elgg_stuff = elgg_get_plugin_setting('show_elgg_stuff', 'courseview');

    if ($show_elgg_stuff == 0 && cv_is_cvcohort(page_owner_entity()))  //if don't show elgg stuff is selected in settings
    {
        $returnvalue = "";
    }
    $menu_visibility = elgg_get_plugin_setting('menu_visibility', 'courseview');
    $user_is_member_of_cohort = cv_user_is_member_of_cohort(page_owner_entity());
    //here we check to see if we are currently in courseview mode.  If we are, we hijack the sidebar for our course menu
    //if ((ElggSession::offsetGet('courseview') && $menu_visibility == 'always') || cv_is_cvcohort(page_owner_entity()))
    if ((ElggSession::offsetGet('courseview') && $menu_visibility == 'always') || $user_is_member_of_cohort)
    {
        $returnvalue = elgg_view('courseview/cv_hijack_sidebar') . $returnvalue;
    }
    return $returnvalue;
}

/**
 * When adding content through courseview context, elgg wants to redirect somewhere else but 
 * we want to force it to go back and display the courseview cohort/menuitem
 * @param type 
 * @param type 
 * @parcvforwardinterceptam type 
 *
 * @return void
 */
function cv_forward_intercept($hook, $type, $return, $params)
{
    $cvredirect = ElggSession::offsetGet('cvredirect');
    if (!empty($cvredirect))
    {
        $return = ElggSession::offsetGet('cvredirect');
        ElggSession::offsetUnset('cvredirect');
    }
    return $return;
}

/**
 * When we are creating new content, we want to present the user with a more granular set of permission choices
 * This adds the ability to limit access to any cohort associated with the current course or only this cohort.
 * @param hook - not used 
 * @param type - not used
 * @param return - the page being built
 * @param params - not used
 * @return $return the $return that was passed which includes the standard permissions such as public, logged in users etc
 *                along with, when appropriate, the Course: coursetitle   and Cohort: cohortname options
 */
function cv_intercept_ACL_write($hook, $type, $return, $params)
{
    $user = get_user($params["user_id"]);
    $cv_active = ElggSession::offsetGet('courseview');
    if (!$cv_active)  //If courseview is not active, we just return
    {
        return $return;
    }
    //we need to get this from session because we are not currently on a courseview page
    $cv_cohort = get_entity(ElggSession::offsetGet('cvcohortguid'));

    if ($cv_cohort) //make sure that the cv_cohort returned is, in fact, a valid courseview cohort
    {
        $course = get_entity($cv_cohort->getContainerGUID());

        //We want to add the Course: <coursetitle>   and Cohort: <cohortname> to the dropdown
        if ($course->cv_acl)
        {
            $return [$course->cv_acl] = 'Course: ' . $course->title;
            $return [$cv_cohort->group_acl] = 'Cohort: ' . $cv_cohort->name;
        }
    }
    return $return;
}

/**
 * Adds a green NEW icon to content that has been created since the user last logged in
 *
 * @param $hook
 * @param $type
 * @param $return - the page redering thus far
 * @param $params - allows us to pull the entity being displayed
 * 
 * @return the rendered page with any modifications that we have made
 */
function cv_new_content_intercept($hook, $type, $return, $params)
{
    $vars = $params['vars'];
    $user = elgg_get_logged_in_user_entity();
    $attributes = $vars->get_attributes;
    $entity = $vars['entity'];
    $show_new_content = elgg_get_plugin_setting('flag_new_content', 'courseview');  //check to see if the NEW content feature is turned on in settings
   //echo $entity->last_action.'---'.$user->prev_last_login.'   ';
    if ($entity->last_action > $user->prev_last_login && $vars['full_view'] == false && $show_new_content)
    {
        $return = "<div class='newContent'>New!</div>" . $return;  //insert a small green div with the word New! in it and apply .newContent css  ruleset to it.
    }
    return $return;
}

/**
 * This plugin hook intercepts when elgg checks to see if it can write to a container.
 * In the case when we are trying to allow professorA to create a cohort from a course
 * created and owned by professorB, we need to allow the course to be the container
 * for the cohort.  We first check to make sure that the container has a cvcourse subtype
 * and then we check to make sure that the user is a professor.  If both of these things
 * are true, we can go ahead and override.
 *
 * @param $hook 
 * @param $type 
 * @param $params - contains our container and user info
 *
 * @return  returns true if conditions are met and the default $return if they are not
 */
function cv_can_write_to_container($hook, $type, $return, $params)
{
    if (elgg_instanceof($params['container'], 'object', 'cvcourse'))
    {
        if (cv_isprof($params['user']))
        {
            return true;
        }
    }
    return $return;
}