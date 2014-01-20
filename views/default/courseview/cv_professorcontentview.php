<?php

        /*
         * Displays all content that has been created within a 'professor' type menu item
         */

$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$cohortguid = ElggSession::offsetGet('cvcohortguid');

if (cv_isprof(elgg_get_logged_in_user_entity()))
{
    echo elgg_view('courseview/cv_filtercontent');
}

$params = get_input('params');

//pull all content with a relationship with the current menu item and sort it by the meta tag: sort_order
$content_items = elgg_get_entities_from_relationship(array(
    'order_by_metadata' => array('name' => 'sort_order', 'direction' => 'ASC', 'as' => 'integer'),
    'relationship_guid' => $cvmenuguid,
    'relationship' => 'content'));

foreach ($content_items as $content_item)
{
    if (cv_isprof($user))
    {
//                /* If the content doesn't have a sort_order metadata, then add one and set it to the time_created value of the object.  We
//                 * can now use this sort_order metadata to move content up or down in the professor content area
//                 */
//        if (!$content_item->sort_order)
//        {
//            $content_item->sort_order = $content_item->time_created;
//            $content_item->save();
//        }
        //*Guessing that we don't really need a form here...
//        echo '<form method="get" action=""' . current_page_url() . '">';
        //echo'xxx';
        echo elgg_view('output/url', array(
            'text' => 'move up ',
            'href' => elgg_get_site_url() . "/action/updown/?guidtomove=$content_item->guid&updown=up",
            'class' => 'grey',
            'is_action' => true));
        echo ' - ';
        echo elgg_view('output/url', array(
            'text' => 'move down ', // . $content_item->sort_order,
            'href' => elgg_get_site_url() . "/action/updown/?guidtomove=$content_item->guid&updown=down",
            'class' => 'grey',
            'is_action' => true));
   //     echo '</form>';
    }

    echo elgg_echo(elgg_view_entity($content_item, array(full_view => false)));
    echo '<br>';
}

