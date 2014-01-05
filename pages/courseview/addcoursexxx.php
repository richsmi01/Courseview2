<?php //

/*
 * 
 */

//echo 'adding course';

echo "In addcourse.php<br>";
exit;

$html = elgg_view_form('createcourse');
//echo elgg_echo("Testing");
//$group = elgg_get_entities_from_metadata(array(guid=>51));
//$group = elgg_get_entities_from_metadata(array(type=>'group', name=>'RichGroup1'));
//$group = elgg_get_entities_from_annotations(array(type=>'group', annotation_name_value_pairs=>array('name','RichGroup1')));
//echo elgg_echo ('group:  '.var_dump($group[1]->getGUID()));
//echo elgg_echo (elgg_list_entities(array('type' => 'group', 'title'=>'RichGroup1')));
//echo elgg_echo (var_dump(get_entity(51)));
//$group = get_entity(51);
//$user = elgg_get_logged_in_user_entity();
//
//echo elgg_echo('Is Member? ' . $group->isMember($user));

//$course_title = 'Course 1';
echo 'this isnt used???  Im in addcourse page';
exit;
$layout = elgg_view_layout('content', array(
    'title' => 'Add a course',
    'content' => $html,
    'filter' => false //this will get rid of the tabs all, user etc.
        )
);
echo elgg_view_page('Add a Course', $layout)

?>
