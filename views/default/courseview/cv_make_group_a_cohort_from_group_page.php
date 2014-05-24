

<script>
    window.onload = function ()
    {
        $target_div = document.querySelector ("#testID");
        $target_button =  document.querySelector ("input[value='Save']");
        $target_button.outerHTML=$target_div.outerHTML + $target_button.outerHTML;
        $target_div.outerHTML="";
        //alert ('####'+$target_button.valueOf());
    }
</script>

<?php
$cv_group_guid =$vars['entity']->guid;

$options=array(
    'name' => 'make_a_cohort',
    'id' => 'make_a_cohort',
    'class' => 'cvtesting',
    'options' => array('make_a_cohort' => 1),
     'value' => 1,
);

echo "<div id = 'testID'>";
echo elgg_view('input/checkbox', $options);

echo "<label for = 'make_a_cohort'>Add or remove this group from CourseView</label>";
//elgg_set_plugin_setting('show_elgg_stuff', $vars['entity']->make_a_cohort, 'courseview'); 
//var_dump ($vars['entity']);
echo "<div id ='cv_hidden_course_list'>";
if(get_entity($cv_group_guid)->cvcohort)
{
    echo elgg_view('input/radio', array(
    'name' => 'remove_from_courseview',
    'id' => 'remove_from_courseview',
    'options' => array('Keep this group in CourseView' => 'keep', 'Remove this group from CourseView' => 'remove'),
     'value' => 'keep',
     'class'=>'elgg-requires-confirmation'
));
}
 else
{
 //echo 'Please choose a course to base this cohort on:';
 //echo elgg_view_form('cv_add_a_cohort',$vars,array ('group_guid'=>$cv_group_guid, 'inGroupPage'=>true));
 echo "To add a group to CourseView, click on the 'link to CourseView' link in the group list page";
//echo elgg_view("courseview/cv_list_courses", array('all' => true));
}
//echo elgg_view_form('cv_add_a_cohort',$vars,array ('group_guid'=>$cv_group_guid, 'inGroupPage'=>true)); 
echo "</div>";
echo "</div>";
//var_dump( get_info('cv_menu_guid'));
//var_dump( get_info('params'));

