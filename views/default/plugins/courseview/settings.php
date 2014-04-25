<?php

echo 'GUID of the Profs Group:<br/>';
echo elgg_view('input/text', array(
    'name' => 'params[profsgroup]',
    'value' => $vars['entity']->profsgroup,
    'disabled' => false));

echo 'Plugins to be recoginized by Courseview';

$regentitytypes = get_registered_entity_types();
$plugins = $regentitytypes['object'];

$shortname = array();
$pluginaddurl = array();
$studentapprovedlist = array();
$profapprovedlist = array();
/* Loop through each plugin and to see which plugins are approved for students and which for profs */
foreach ($plugins as $plugin)
{
    $studentitem = 'check' . $plugin;
    $profitem = "prof" . $plugin;
    echo "<div class=cvsettingsplugins><br><h3>$plugin </h3>";
    $studentoptions = array('name' => "params[$studentitem]", 'value' => 1);  //sends a 0 if the checkbox isn't checked
    if ($vars['entity']->$studentitem == 1)
    {
        $studentoptions['checked'] = true;
    }
    $profoptions = array('name' => "params[$profitem]", 'value' => 1);  //sends a 0 if the checkbox isn't checked
    if ($vars['entity']->$profitem == 1)
    {
        $profoptions['checked'] = true;
    }

    echo elgg_view('input/checkbox', $studentoptions);
    echo ' Student<br>';
    
    echo elgg_view("input/checkbox", $profoptions);
    echo ' Professor<br>';
   
    $pluginname = "createstring" . $plugin;
    $friendly = "friendly" . $plugin;
    $object_subtype = "object" . $plugin;

    echo elgg_view('input/text', array(
        'name' => 'params[' . $friendly . ']',
        'value' => $vars['entity']->$friendly));
    echo elgg_view('input/text', array(
        'name' => 'params[' . $pluginname . ']',
        'value' => $vars['entity']->$pluginname));
    echo'</div>';
    if ($vars['entity']->$studentitem == 1)  //if the studentitem is checked
    {
        $pluginaddurl[$plugin] = $vars['entity']->$pluginname;
        $studentapprovedlist [$plugin] = $vars['entity']->$friendly;
        $approved_subtype [$plugin] = $plugin;
    }
    if ($vars['entity']->$profitem == 1)  //if the profitem is checked
    {
        $pluginaddurl[$plugin] = $vars['entity']->$pluginname;
        $profapprovedlist [$plugin] = $vars['entity']->$friendly;
        $approved_subtype [$plugin] = $plugin;
    }
}


elgg_set_plugin_setting('availableplugins', serialize($studentapprovedlist), 'courseview');  //need to serialize arrays before putting in settings
elgg_set_plugin_setting('profavailableplugins', serialize($profapprovedlist), 'courseview');  //need to serialize arrays before putting in settings
elgg_set_plugin_setting('plugincreatestring', serialize($pluginaddurl), 'courseview');  //need to serialize arrays before putting in settings
elgg_set_plugin_setting('approved_subtype', serialize($approved_subtype), 'courseview');  //need to serialize arrays before putting in settings

echo "<br><h3>Sidebar Options</h3><br>";
echo "Displaying Cohorts within CourseView";
echo elgg_view('input/radio', array(
    'name' => 'params[display_cohorts_mode]',
    'id' => 'display_cohorts_mode',
    'options' => array('Display all cohorts in CourseView cohort menu' => 'all', 'Display only current Cohort in CourseView cohort menu' => 'current'),
     'value' => $vars['entity']->display_cohorts_mode,
));
elgg_set_plugin_setting('display_cohorts_mode', $vars['entity']->display_cohorts_mode, 'courseview'); 
echo '<br>';
echo elgg_view('input/radio', array(
    'name' => 'params[menu_visibility]',
    'id' => 'menu_visibility',
    'options' => array('Display CourseView cohort menu always' => 'all', 'Display CourseView cohort menu only when in CourseView cohorts' => 'current'),
     'value' => $vars['entity']->menu_visibility,
));
elgg_set_plugin_setting('display_cohorts_mode', $vars['entity']->menu_visibility, 'courseview'); 
echo '<br>';
$options=array(
    'name' => 'params[show_elgg_stuff]',
    'id' => 'show_elgg_stuff',
    'options' => array('Show Elgg stuff in sidebar' => 1),
     'value' => 1,
);
 if ($vars['entity']->show_elgg_stuff == 1)
    {
        $options['checked'] = true;
    }
echo elgg_view('input/checkbox', $options);
echo "Show Elgg content in sidebar while CourseView is active";
elgg_set_plugin_setting('show_elgg_stuff', $vars['entity']->show_elgg_stuff, 'courseview'); 

echo "<br>CourseView menu handling<br>";
$options=array(
    'name' => 'params[show_courseview_site_menu]',
    'id' => 'show_courseview_site_menu',
    'options' => array('Show CourseView menu item on site menu' => 1),
     'value' => 1,
);
 if ($vars['entity']->show_courseview_site_menu == 1)
    {
        $options['checked'] = true;
    }
echo elgg_view('input/checkbox', $options);
echo "Show CourseView menu item on site menu";
elgg_set_plugin_setting('show_elgg_stuff', $vars['entity']->show_courseview_site_menu, 'courseview'); 