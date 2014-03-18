<?php


$filter = get_input('filter', 'all'); //the currently selected dropdown list  item  
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$defaultcohortguid = ElggSession::offsetGet('cvcohortguid');
$cv_cohort_guid=get_input ('cohortfilter',$defaultcohortguid);

$cohortname = get_entity($cv_cohort_guid)->title;
//echo "dropdown info: ".$cohortname;


//$relationship = 'content' . $cvcohortguid;
$relationship = 'content' . $cv_cohort_guid;

//echo elgg_echo("Relationship name:  " . $relationship);
//echo elgg_echo("Relationship GUID:  " . $cvmenuguid);
//
$user = elgg_get_logged_in_user_entity();
if (!$user->num_items)  //If the user has never selected pagination or sort by options, assign a default
{
    $user->num_items = 10;
    $user->sort_by='chrono';
}


$num_items =get_input('numItems',$user->num_items);
$sort_by=get_input ('sortby',$user->sort_by);
$user->num_items = $num_items;
$user->sort_by = $sort_by;

//echo $num_items.'---'.$sort_by;
$content_items = cv_get_content_by_menu_item($filter, $cvmenuguid, $relationship,true,$sort_by,$num_items);  
echo $content_items;



//$content_display_items;
//foreach ($content_items as $content_item)
//{
////    $test = $content_item->getAnnotations(
////                                    'likes                             );
//
//    $content_display_items .= "<div id='contentitem'>";  //this needs to be a class
//    $content_display_items .=elgg_view_entity($content_item, array(full_view => false));
//    $content_display_items .= "</div>";
//}


//$options = array
//        ('relationship_guid' => $cvmenuguid,
//        'relationship' => $relationship,
//        'type' => 'object',
//      
//        'count'=> true,
////        'limit' => false,
//        'list_class' =>'contentitem',
//        'full_view'=>false,
//    'limit'=>get_input('limit',10),
//    'offset'=>get_input('offset',0),
//    );
//
//$count = elgg_get_entities_from_relationship ($options);
//echo'Hi Matt-- sorry Im so dumb!';
//echo $count;
//
//$options ['count'] = false;
//
//$content_items = elgg_get_entities_from_relationship ($options);
//
//foreach ($content_items as $test)
//{
//    echo elgg_view_entity ($test,array('view'=>false));
//    
//}
//echo elgg_view ('navigation/pagination', array ('count'=> $count, 'limit'=>get_input('limit',10), 'offset'=>get_input('offset',0)));
////echo $content_items;


//code to sort by likes---from Matt
// $options = array(
//		'container_guid' => $entity->guid,
//		'annotation_names' => array('likes'),
//		'selects' => array("(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid) AS likes"),
//		'order_by' => 'likes DESC',
//		'full_view' => false
//	  );

//https://github.com/AU-Landing-Project/liked_content
//[10:55:36 PM] Matt Beckett: https://github.com/AU-Landing-Project/liked_content/blob/master/start.php#L38