<?php


/**
 * Description of cv_remove_cohort
 *
 * @author ITSC
 */


$group= get_entity($vars['group_guid']);
echo "<h3>".elgg_echo ('cv:forms:cv_remove_cohort:confirm', array( $group->name) )."<br></h3><br>";

echo elgg_view('input/radio', array(
    'name' => 'remove_cohort',
    'id' => 'remove_cohort',
    'options' => array(elgg_echo ('cv:forms:cv_remove_cohort:yes') => 'remove', elgg_echo ('cv:forms:cv_remove_cohort:no') => 'no'),
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
echo elgg_echo ('cv:forms:cv_remove_cohort:warning');
echo'<br><br>';
 
echo elgg_view('input/hidden', array('name' => 'group_guid', 'value' => $vars['group_guid']));

echo elgg_view('input/submit');
