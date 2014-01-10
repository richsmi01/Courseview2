<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$cohortguid = ElggSession::offsetGet('cvcohortguid');
//echo "professorcontentview<br>";
if (cv_isprof(elgg_get_logged_in_user_entity()))
{
    echo elgg_view('courseview/cvfiltercontent');
}

$params = get_input('params');

$content_items = elgg_get_entities_from_relationship(array(
    //'order_by' => 'e.time_created DESC',
    'order_by_metadata' => array('name' => 'sort_order', 'direction' => 'ASC', 'as' => 'integer'),
    'relationship_guid' => $cvmenuguid,
    'relationship' => 'content'));


//this whole thing should be moved to an action file...Matt, help!!!
$updown = $params[3];
$guid_to_move = $params[4];
if ($updown)
{
    $updown = $params[3];
    $guid_to_move = $params[4];
    $previous_content_item = $content_items[0];
    $movedown = false;

    foreach ($content_items as $content_item)
    {
        if ($updown == 'up' && $guid_to_move == $content_item->guid)
        {
            $temp = $content_item->sort_order;
            $content_item->sort_order = $previous_content_item->sort_order;
            $previous_content_item->sort_order = $temp;
        }

        if ($movedown)
        {
            $temp = $content_item->sort_order;
            $content_item->sort_order = $previous_content_item->sort_order;
            $previous_content_item->sort_order = $temp;
        }

        if ($updown == 'down' && $content_item->guid == $guid_to_move)
        {
            $movedown = true;
        }

        $previous_content_item = $content_item;
    }

    //need a way to call this page again.
}



foreach ($content_items as $content_item)
{
    if (cv_isprof($user))
    {

        /* If the content doesn't have a sort_order metadata, then add one and set it to the time_created value of the object.  We
         * can now use this sort_order metadata to move content up or down in the professor content area
         */
        if (!$content_item->sort_order)
        {
            $content_item->sort_order = $content_item->time_created;
            $content_item->save();
        }
        //*Guessing that we don't really need a form here...
        echo '<form method="get" action=""' . current_page_url() . '">';
        echo elgg_view('output/url', array(
            'text' => 'move up ',
            'href' => current_page_url() . '/up/' . $content_item->guid,
            'class' => 'grey'));
        echo elgg_view('output/url', array(
            'text' => '-  move down',
            'href' => current_page_url() . '/down/' . $content_item->guid,
            'class' => 'grey'));

        echo elgg_echo("<div class='editcourse'><a class ='uparrowcontainer' id = '$content_item->guid href='http://sheridancollege.ca'></a>");
        echo elgg_echo("<a class ='downarrowcontainer' href='http://sheridancollege.ca/$content_item->guid'></a>");
        echo elgg_echo('</div>');
        //echo '@@@' . $content_item->guid;
        echo '</form>';
    }
    echo elgg_echo(elgg_view_entity($content_item, array(full_view => false)));
    echo '<br>';
    //var_dump($content_item);
}

