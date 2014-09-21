<?php
/*
 * Displays all content that has been created within a 'professor' type menu item
 */

$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$user = elgg_get_logged_in_user_entity();
$cvcohort = get_entity($cv_cohort_guid);

$params = get_input('params');
//pull all content with a relationship with the current menu item and sort it by the meta tag: sort_order
$content_items = elgg_get_entities_from_relationship(array(
    'order_by_metadata' => array('name' => 'sort_order', 'direction' => 'ASC', 'as' => 'integer'),
    'relationship_guid' => $cvmenuguid,
    'relationship' => 'content',
    'limit' => false));

//display all content items for this cvmenu item
foreach ($content_items as $content_item)
{
    $sort_order = $content_item->sort_order;
    $my_guid = $content_item->guid;
    
    //if the user is a prof and the course owner, give him the ability to move content up or down
    if (cv_isprof($user) && cv_is_course_owner($user, $cvcohort))
    {
        echo elgg_view('output/url', array(
            'text' => elgg_echo ('cv:views:cv_professor_contentview:up'),
            'href' => elgg_get_site_url() . "/action/cv_move_prof_content/?guidtomove=$content_item->guid&updown=up",
            'class' => 'grey',
            'is_action' => true));
        echo ' - ';
        echo elgg_view('output/url', array(
            'text' =>elgg_echo ('cv:views:cv_professor_contentview:down'),
            'href' => elgg_get_site_url() . "/action/cv_move_prof_content/?guidtomove=$content_item->guid&updown=down",
            'class' => 'grey',
            'is_action' => true));
    }
    echo "<div class = 'profcontentitem'>";
    echo elgg_echo(elgg_view_entity($content_item, array(full_view => false)));
    echo "</div>";
}

