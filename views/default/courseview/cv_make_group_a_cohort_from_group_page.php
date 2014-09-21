

<script>
    window.onload = function()
    {
        $target_div = document.querySelector("#testID");
        $target_button = document.querySelector("input[value='Save']");
        $target_button.outerHTML = $target_div.outerHTML + $target_button.outerHTML;
        $target_div.outerHTML = "";
        //alert ('####'+$target_button.valueOf());
    }
</script>

<?php

$cv_group_guid = $vars['entity']->guid;
$options = array(
    'name' => 'make_a_cohort',
    'id' => 'make_a_cohort',
    'class' => 'cvtesting',
    'options' => array('make_a_cohort' => 1),
    'value' => 1,
);

echo "<div id = 'testID'>";
//echo "To make this group a Course View Cohort, go to the Group List page and click on 'Link to CourseView'.<br><br>";
echo elgg_view('input/checkbox', $options);

echo "<label for = 'make_a_cohort'>" . elgg_echo('cv:views:cv_make_group_a_cohort_from_group_page:addremove') . "</label>";
echo "<div id ='cv_hidden_course_list'>";
if (get_entity($cv_group_guid)->cvcohort)
{
    echo elgg_view('input/radio', array(
        'name' => 'remove_from_courseview',
        'id' => 'remove_from_courseview',
        'options' => array(elgg_echo('cv:views:cv_make_group_a_cohort_from_group_page:keep') => 'keep',
            elgg_echo('cv:views:cv_make_group_a_cohort_from_group_page:remove') => 'remove'),
        'value' => 'keep',
        'class' => 'elgg-requires-confirmation'
    ));
} else
{
    echo elgg_echo('cv:views:cv_make_group_a_cohort_from_group_page:to_add');
    echo elgg_view("courseview/cv_list_courses", array('all' => true));
}
echo "</div>";
echo "</div>";

