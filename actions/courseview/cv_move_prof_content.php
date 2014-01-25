<?php

elgg_load_library('elgg:cv_debug');

$guid_to_move = get_input('guidtomove'); //$params[4];
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$updown = get_input('updown'); //$params[3];

$content_items = elgg_get_entities_from_relationship(array(
    'order_by_metadata' => array('name' => 'sort_order', 'direction' => 'ASC', 'as' => 'integer'),
    'relationship_guid' => $cvmenuguid,
    'relationship' => 'content'));
$previous_content_item = $content_items[0];
$movedown = false;
foreach ($content_items as $content_item)
{
     
    if ($updown == 'up' && $guid_to_move == $content_item->guid)
    {
        $temp = $content_item->sort_order;
        $content_item->sort_order = $previous_content_item->sort_order;
        $previous_content_item->sort_order = $temp;
        $previous_content_item->save();
        $content_item->save();
    }

    if ($movedown)
    {
        $temp = $content_item->sort_order;
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
//exit;

