<?php
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$courseguid = get_entity($cv_cohort_guid)->container_guid;
$menuguid = ElggSession::offsetGet('cvmenuguid');
$filter = get_input('filter', 'all'); //the currently selected dropdown list  item  
//echo '$filter '.$filter;
//pull down the create strings for the various plugins from the settings page:
$createString = unserialize(elgg_get_plugin_setting('plugincreatestring', 'courseview'));
$createString = str_replace('{url}', elgg_get_site_url(), $createString);
$createString = str_replace('{user_guid}', elgg_get_logged_in_user_guid(), $createString);
$createString = str_replace('{cohort_guid}', $cv_cohort_guid, $createString);
$test2 = json_encode($createString);
//var_dump($test2)
?>



<script>
    
    function blinker() {
                $('#notHidden').fadeOut(500);
                $('#notHidden').fadeIn(500);
            }

    function onChange(id, value)
    {
        if (id == 'filterDropDown' || id =='cohortDropDown')
        {
            document.getElementById("hiddenmessage").id="notHidden";
            document.getElementById("notHidden").style.visibility = "visible"; 
            setInterval(blinker, 500);
            document.getElementById("myform").submit();
        }
        else
        {
            var books = <?php echo $test2 ?>;
            //alert (books[value]);
            window.location.href = books[value];
        }
    }

</script>


<?php
/*
 * This file builds the dropdown filter used to filter content by cohort and content type
 */
//pull in any needed values
//$cohortFilter =$vars['cohortFilter'];




$create = get_input('create');
//echo 'Testing: ' . $create;
//build the string used for the create content button...need to substitute real value for the placeholders in the setup page
//$createbutton = $createString[$filter];      //elgg_get_plugin_setting('blogadd', 'courseview');
//$createbutton = str_replace('{url}', elgg_get_site_url(), $createbutton);
//$createbutton = str_replace('{user_guid}', elgg_get_logged_in_user_guid(), $createbutton);
//$createbutton = str_replace('{cohort_guid}', $cv_cohort_guid, $createbutton);
//create and populate a pulldown menu using the list of authorized plugins from the setup screen   
$availableplugins = unserialize(elgg_get_plugin_setting('availableplugins', 'courseview'));  //pull down list of authorized plugin types
while (array_search("", $availableplugins))
{
    unset($availableplugins[array_search("", $availableplugins)]);
}

$createplugins = $availableplugins;
$createplugins ['choose'] = 'Choose a content type';

$availableplugins['all'] = 'All Content Types';  //add the ability for the student to select all content
//this will remove any 'empty' choices such as is caused by the Page vs PageTop of the wiki plugin



$availablecohorts = cv_get_cohorts_by_courseguid($courseguid);

$dropdownlist = array();
foreach ($availablecohorts as $availablecohort)
{
    $cohort = "Parent course: " . get_entity($availablecohort->guid)->container_guid;
    $dropdownlist [$availablecohort->guid] = $availablecohort->title;
}
//echo 'cohort filter was '.get_input('cohortfilter').'<br>';
$cfilter = get_input('cohortfilter', $cv_cohort_guid);
//echo 'cohort filter now is '.$cfilter.'<br>';
//echo "<select class='elgg-input-dropdown' id='elgg-river-selector'>
//<option selected='selected' value='type=all'>Show All</option>
//        <option value='type=user'>Show Users</option>
//        <option value='type=object&amp;subtype=blog'>Show Blogs</option>
//        <option value='type=object&amp;subtype=bookmarks'>Show Bookmarks</option>
//</select>";


echo '<form id = "myform" method="get" action="' . current_page_url() . '">';
if (get_entity($menuguid)->menutype == 'student')
{
    echo' List content in cohort: ';
    echo elgg_view('input/dropdown', array(
        'name' => 'cohortfilter', //need to finish this code so the cohort filter works
        'value' => $cfilter,
        'id'=>'cohortDropDown',
         'onchange' => 'onChange(id, value)',
        'options_values' => $dropdownlist));
    echo 'Filter by:  ';
    
    echo elgg_view('input/dropdown', array(
    'name' => 'filter',
    'id' => 'filterDropDown',
    'onchange' => "onChange(id, value)",
    'value' => $filter,
    'options_values' => $availableplugins));
} 

echo 'Create a ';
echo elgg_view('input/dropdown', array(
    'name' => 'create',
    'id' => 'createDropDown',
    'value' => 'choose',
    'onchange' => 'onChange(id, value)',
    'options_values' => $createplugins));


//echo elgg_view('input/submit', array(
//    'value' => elgg_echo('Go!')));
//if ($createbutton != '')  //if there is a currently filtered plugin type, give the user the option to create content
//{
//    echo elgg_view('output/url', array(
//        'text' => 'Create a ' . $availableplugins[$filter] . ' posting',
//        'href' => $createbutton));
//}
echo '</form><br/>';
echo "<div id='hiddenmessage' style ='visibility:hidden; text-align:center; height:0px;' >Filtering</div>";
