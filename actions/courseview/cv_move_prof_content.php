<?php
/* *
 * Allows content placed inside of 'professor' type menu items (ie relationship
 * between content and cvmenu item set to professor) to be moved up or down by the owner prof
 */
$guid_to_move = get_input('guidtomove');
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$updown = get_input('updown'); 

//get all content with a relationship to the cvmenu item
$content_items = elgg_get_entities_from_relationship(array(
    'order_by_metadata' => array('name' => 'sort_order', 'direction' => 'ASC', 'as' => 'integer'),
    'relationship_guid' => $cvmenuguid,
    'relationship' => 'content'));
//loop through all content, find the matching content, and move it up or down
$previous_content_item = $content_items[0];
$movedown = false;
foreach ($content_items as $content_item)
{
    if ($updown == 'up' && $guid_to_move == $content_item->guid)
    {
        $temp = $content_item->sort_order; //swap sort_order with previous cvmenu
        $content_item->sort_order = $previous_content_item->sort_order;
        $previous_content_item->sort_order = $temp;
        $previous_content_item->save();
        $content_item->save();
    }

    if ($movedown)
    {
        $temp = $content_item->sort_order;//swap sort_order with next cvmenu 
        $content_item->sort_order = $previous_content_item->sort_order;
        $previous_content_item->sort_order = $temp;
        $previous_content_item->save();
        $content_item->save();
        break;
    }

    if ($updown == 'down' && $content_item->guid == $guid_to_move)
    {
        $movedown = true;
    }

    $previous_content_item = $content_item;
}



