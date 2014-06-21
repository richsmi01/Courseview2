<?php


/**
 * Description of cv_remove_cohort
 *
 * @author ITSC
 */
$group= get_entity($vars['group_guid']);

echo "<h3>Are you sure that you want to remove $group->title from CourseView?<br></h3><br>";

echo elgg_view('input/radio', array(
    'name' => 'remove_cohort',
    'id' => 'remove_cohort',
    'options' => array('Yes, remove this group from CourseView (Note, all CourseView indexing will be permanently deleted' => 'remove', 'No, Leave this group as is' => 'no'),
     'value' => 'no',
));
echo '<br>';
$options=array(
    'name' => 'delete_group',
    'id' => 'delete_group',
    'options' => array('delete_group' => 1),
     'value' =>1,
);
echo elgg_view('input/checkbox', $options);
echo "Check this box to delete the underlying group...THIS ACTION CANNOT BE UNDONE!";
echo'<br><br>';
 
echo elgg_view('input/hidden', array('name' => 'group_guid', 'value' => $vars['group_guid']));

echo elgg_view('input/submit');
