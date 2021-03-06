<?php
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$courseguid = get_entity($cv_cohort_guid)->container_guid;
$menuguid = ElggSession::offsetGet('cvmenuguid');
$filter = get_input('filter', 'all'); //the currently selected dropdown list  item  

//build create string
$createString = unserialize(elgg_get_plugin_setting('plugincreatestring', 'courseview'));
$createString = str_replace('{url}', elgg_get_site_url(), $createString);
$createString = str_replace('{user_guid}', elgg_get_logged_in_user_guid(), $createString);
$createString = str_replace('{cohort_guid}', $cv_cohort_guid, $createString);
$user = elgg_get_logged_in_user_entity();
$create = get_input('create');
$cvcohort = get_entity(ElggSession::offsetGet('cvcohortguid'));
//use json to allow the create string to be passed into the javascript below
$json_create_string = json_encode($createString);
?>

<script>
    //flashing Working sign in the header
    function blinker() {
        $('#notHidden').fadeOut(500);
        $('#notHidden').fadeIn(500);
    }
    //handle any changes to dropdown listboxes
    function onChange(id, value)
    {
        if (id == 'filterDropDown' || id == 'cohortDropDown' || id == 'numItemsDropdown' || id == 'sortDropDown')
        {
            document.getElementById("myform").submit();
        }
        else
        {
            var create_string = <?php echo $json_create_string ?>;
            window.location.href = create_string [value];
        }
    }
</script>


<?php
$availableplugins = cv_get_valid_plugins($user);
//if an available plugin is nothing more than an empty String then remove it from the array
//this will remove any 'empty' choices such as is caused by the Page vs PageTop of the wiki plugin
while (array_search("", $availableplugins))
{
    unset($availableplugins[array_search("", $availableplugins)]);
}
$createplugins = $availableplugins;
$createplugins ['choose'] = elgg_echo('cv:views:cv_filter_content:choose');  //Add the choose menu item to the array...we will default to that
$availableplugins['all'] = elgg_echo('cv:views:cv_filter_content:all');  //add the ability for the student to select all content
$availableplugins['myPostings'] = elgg_echo('cv:views:cv_filter_content:only');  //add the ablility for the students to select only their own postings
$availablecohorts = cv_get_cohorts_by_courseguid($courseguid);

$dropdownlist = array();
foreach ($availablecohorts as $availablecohort)
{
    $cohort = "Parent course: " . get_entity($availablecohort->guid)->container_guid;
    $dropdownlist [$availablecohort->guid] = $availablecohort->name;   
}

$cfilter = get_input('cohortfilter', $cv_cohort_guid);
$numItems = $user->num_items;
$sort_by = $user->sort_by;

echo '<form id = "myform" method="get" action="' . current_page_url() . '">';
if (get_entity($menuguid)->menutype == 'student')
{
    echo "<div id = courseview_sidebar_filter>";
    $numItemsDrop = array();
    $numItemsDrop ['4'] = '4';
    $numItemsDrop ['10'] = '10';
    $numItemsDrop ['25'] = '25';
    echo elgg_echo ('cv:views:cv_filter_content:items').'<br>';
    echo elgg_view('input/dropdown', array(
        'name' => 'numItems',
        'value' => $numItems,
        'id' => 'numItemsDropdown',
        'onchange' => 'onChange(id, value)',
        'options_values' => $numItemsDrop));

    echo'<br>'.elgg_echo('cv:views:cv_filter_content:filter_cohort').'<br> ';
    echo elgg_view('input/dropdown', array(
        'name' => 'cohortfilter',
        'value' => $cfilter,
        'id' => 'cohortDropDown',
        'onchange' => 'onChange(id, value)',
        'options_values' => $dropdownlist));

    echo "<br>".elgg_echo('cv:views:cv_filter_content:filter_type')."<br>";
    echo elgg_view('input/dropdown', array(
        'name' => 'filter',
        'id' => 'filterDropDown',
        'onchange' => "onChange(id, value)",
        'value' => $filter,
        'options_values' => $availableplugins));

    $sort_dropdown = array();
    $sort_dropdown ['likes'] = elgg_echo ('cv:views:cv_filter_content:likes');
    $sort_dropdown ['chrono'] = elgg_echo ('cv:views:cv_filter_content:date');
    echo'<br>'.elgg_echo ('cv:views:cv_filter_content:sort').'<br> ';
    echo elgg_view('input/dropdown', array(
        'name' => 'sortby',
        'value' => $sort_by,
        'id' => 'sortDropDown',
        'onchange' => 'onChange(id, value)',
        'options_values' => $sort_dropdown));
    echo "</div>";
}

if (cv_isprof($user) && cv_is_course_owner($user, $cvcohort) && get_entity($menuguid)->menutype == 'professor'|| get_entity($menuguid)->menutype == 'student')
{
    echo "<div id = courseview_sidebar_create>";
    echo elgg_echo ('cv:views:cv_filter_content:create').'<br> ';
    echo elgg_view('input/dropdown', array(
        'name' => 'create',
        'id' => 'createDropDown',
        'value' => 'choose',
        'onchange' => 'onChange(id, value)',
        'options_values' => $createplugins));
    echo "</div>";
}

echo '</form>';

