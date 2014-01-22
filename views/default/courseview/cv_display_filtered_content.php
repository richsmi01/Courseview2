<?php


$filter = get_input('filter', 'all'); //the currently selected dropdown list  item  
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$defaultcohortguid = ElggSession::offsetGet('cvcohortguid');
$cohortguid=get_input ('cohortfilter',$defaultcohortguid);

$cohortname = get_entity($cohortguid)->title;
//echo "dropdown info: ".$cohortname;


//$relationship = 'content' . $cvcohortguid;
$relationship = 'content' . $cohortguid;

//echo elgg_echo("Relationship name:  " . $relationship);
//echo elgg_echo("Relationship GUID:  " . $cvmenuguid);
//
$content_items = cv_get_content_by_menu_item($filter, $cvmenuguid, $relationship);
foreach ($content_items as $content_item)
{
    echo elgg_view_entity($content_item, array(full_view => false));
}

