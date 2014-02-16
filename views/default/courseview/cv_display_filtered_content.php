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
$content_items = cv_get_content_by_menu_item($filter, $cvmenuguid, $relationship);
foreach ($content_items as $content_item)
{
    echo "<div id='contentitem'>";
        echo elgg_view_entity($content_item, array(full_view => false));
    echo "</div>";
}

