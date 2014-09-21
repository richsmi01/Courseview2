<?php
/**
 * Get input from all of the filter pulldowns and build the content page to display
 * 
 * @author Rich Smith
 */
$filter = get_input('filter', 'all'); //the currently selected dropdown list  item  
if ($filter =='myPostings') //if the user has selected to only display their postings
{
    $filter = 'all';
    $myPostings =true;
}
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$defaultcohortguid = ElggSession::offsetGet('cvcohortguid');
$cv_cohort_guid=get_input ('cohortfilter',$defaultcohortguid);
$cohortname = get_entity($cv_cohort_guid)->title;
$relationship = 'content' . $cv_cohort_guid;
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
$options = array ('filter'=>$filter, 'cvmenuguid'=>$cvmenuguid, 'relationship'=>$relationship, 
        'list'=>true, 'sort'=>$sort_by, 'page_size'=>$num_items,
        'only_current_user'=>$myPostings);
$content_items = cv_get_content_by_menu_item1($options);  
echo $content_items;


//code to sort by likes---
// $options = array(
//		'container_guid' => $entity->guid,
//		'annotation_names' => array('likes'),
//		'selects' => array("(SELECT count(distinct l.id) FROM {$dbprefix}annotations l WHERE l.name_id = $likes_metastring AND l.entity_guid = e.guid) AS likes"),
//		'order_by' => 'likes DESC',
//		'full_view' => false
//	  );
