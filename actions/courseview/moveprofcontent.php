<?php
elgg_load_library('elgg:cv_debug');


//this whole thing should be moved to an action file...Matt, help!!!
//$updown = $params[3];
$guid_to_move = get_input('guidtomove');//$params[4];
//echo $guid_to_move;

//if ($updown)
//{
    $updown = get_input('updown');//$params[3];
//    $guid_to_move = $params[4];
//    $previous_content_item = $content_items[0];
//    $movedown = false;

//echo $updown;
//exit;
$content_items = elgg_get_entities_from_relationship(array(
    //'order_by' => 'e.time_created DESC',
    'order_by_metadata' => array('name' => 'sort_order', 'direction' => 'ASC', 'as' => 'integer'),
    'relationship_guid' => $cvmenuguid,
    'relationship' => 'content'));
$movedown=false;
    foreach ($content_items as $content_item)
    {
        if ($updown == 'up' && $guid_to_move == $content_item->guid)
        {
            cv_debug("content sort order:  match found","",100);
            $temp = $content_item->sort_order;
            $content_item->sort_order = $previous_content_item->sort_order;
            $previous_content_item->sort_order = $temp;
            $previous_content_item->save();
        $content_item->save();
        }

        if ($movedown)
        {
            cv_debug("content sort order:  match found","",100);
            $temp = $content_item->sort_order;
            $content_item->sort_order = $previous_content_item->sort_order;
            $previous_content_item->sort_order = $temp;
            $previous_content_item->save();
        $content_item->save();
        }

        if ($updown == 'down' && $content_item->guid == $guid_to_move)
        {
            $movedown = true;
        }
        

        
        $previous_content_item = $content_item;
        
      
    }

    //need a way to call this page again.

