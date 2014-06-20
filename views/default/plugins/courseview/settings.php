<?php
echo "<div id='cv_settings'>";
echo '<h3>GUID of the Profs Group:</h3><br/>';
echo elgg_view('input/text', array(
    'name' => 'params[profsgroup]',
    'value' => $vars['entity']->profsgroup,
    'disabled' => false));

echo '<br><br><h3>Plugins to be recoginized by Courseview:</h3>';

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
    echo ' Allow students access to this plugin<br>';
    
    echo elgg_view("input/checkbox", $profoptions);
    echo ' Allow professors access to this plugin<br>';
   echo 'Friendly Name:';
    $pluginname = "createstring" . $plugin;
    $friendly = "friendly" . $plugin;
    $object_subtype = "object" . $plugin;
 
    echo elgg_view('input/text', array(
        'name' => 'params[' . $friendly . ']',
        'value' => $vars['entity']->$friendly));
       echo 'Create String for object:';
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

echo "<br><h3>Manual Overrides</h3><br>Some plugins are not well written and need manual overrides<br>Type in any keywords needed to prevent the
CourseView add to cohort menu from appearing at the bottom of the posting:";
echo elgg_view('input/text', array('name' => 'params[dont_show_add_content_to_cohort_menu]', 'value'=>$vars['entity']->dont_show_add_content_to_cohort_menu));
elgg_set_plugin_setting('dont_show_add_content_to_cohort_menu', $vars['entity']->dont_show_add_content_to_cohort_menu, 'courseview'); 
echo "<br><br><h3>CourseView Activation/Deactivation:</h3>";
$options=array(
    'name' => 'params[show_courseview_site_activation]',
    'id' => 'show_courseview_site_activation',
    'options' => array('Show CourseView activation menu item on main site menu' => 1),
     'value' => 1,
);
 $options['checked'] = ($vars['entity']->show_courseview_site_activation==1);
echo elgg_view('input/checkbox', $options);
echo "Show CourseView activation menu item on main site menu.";
elgg_set_plugin_setting('show_courseview_site_activation', $vars['entity']->show_courseview_site_activation, 'courseview'); 
echo'<br>';
$options=array(
    'name' => 'params[show_courseview_sidebar_activation]',
    'id' => 'show_courseview_sidebar_activation',
    'options' => array('Show CourseView activation menu item on sidebar. ' => 1),
     'value' => 1,
);

 $options['checked'] = ($vars['entity']->show_courseview_sidebar_activation==1);
echo elgg_view('input/checkbox', $options);
echo "Show CourseView activation menu item on sidebar.<br> ";
elgg_set_plugin_setting('show_courseview_sidebar_activation', $vars['entity']->show_courseview_sidebar_activation, 'courseview'); 


$options=array(
    'name' => 'params[hp_mode]',
    'id' => 'hp_mode',
    'options' => array('hp_mode ' => 1),
     'value' => 1,
);
$options['checked'] = ($vars['entity']->hp_mode==1);
echo elgg_view('input/checkbox', $options);
echo "Have CourseView completely take over - for testing only";
elgg_set_plugin_setting('hp_mode', $vars['entity']->hp_mode, 'courseview'); 




echo "<br><br><h3>Sidebar Options:</h3>";
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
    'options' => array('Display CourseView cohort menu always' => 'always', 'Display CourseView cohort menu only when in CourseView cohorts' => 'cohort'),
     'value' => $vars['entity']->menu_visibility,
));
elgg_set_plugin_setting('menu_visibility', $vars['entity']->menu_visibility, 'courseview'); 

echo '<br>';
$options=array(
    'name' => 'params[show_elgg_stuff]',
    'id' => 'show_elgg_stuff',
    'options' => array('Show Elgg stuff in sidebar' => 1),
     'value' => 1,
);
 $options['checked'] = ($vars['entity']->show_elgg_stuff==1);
echo elgg_view('input/checkbox', $options);
echo "Show Elgg content in sidebar underneath CourseView menu while CourseView is active";
elgg_set_plugin_setting('show_elgg_stuff', $vars['entity']->show_elgg_stuff, 'courseview'); 



//echo "<br><br><h3>Default Cohort (Group):</h3>";
//echo "CourseView can automatically sign new users up to a particular cohort...Please enter the GUID of this cohort<br>";
//echo "Cohort GUID:";
//echo elgg_view('input/text', array(
//    'name' => 'params[defaultCohort]',
//    'value' => $vars['entity']->defaultCohort,
//    'disabled' => false));

//echo "<br><br><h3> Page Layout</h3>";
//echo "Maximize screen area?<br>Sidebar to left?<br>";

echo "<br><br><h3>Asthetics</h3>";
$options=array(
    'name' => 'params[cv_animated_header]',
    'id' => 'cv_animated_header',
    'options' => array('Animate header?' => 1),
     'value' => 1,
);
 $options['checked'] = ($vars['entity']->cv_animated_header==1);
echo elgg_view('input/checkbox', $options);

elgg_set_plugin_setting('cv_animated_header', $vars['entity']->cv_animated_header, 'courseview'); 
echo "Animated header?<br>";

$options=array(
    'name' => 'params[cv_animated_menuitem]',
    'id' => 'cv_animated_menuitem',
    'options' => array('Animate header?' => 1),
     'value' => 1,
);
//::TODO:Rich - Couldn't this whole if be replaced with $options['checked'] = $vars['entity']->cv_animated_menuitem; ?
// if ($vars['entity']->cv_animated_menuitem == 1)
//    {
//        $options['checked'] = true;
//    }
$options['checked']= ($vars['entity']->cv_animated_menuitem==1);
echo elgg_view('input/checkbox', $options);

elgg_set_plugin_setting('cv_animated_menuitem', $vars['entity']->cv_animated_menuitem, 'courseview'); 
echo "Animated listview?<br>";


$options=array(
    'name' => 'params[cv_flashing_status]',
    'id' => 'cv_flashing_status',
    'options' => array('Flashing Updating status message?' => 1),
     'value' => 1,
);
//::TODO:Rich - Like this?  
$options['checked'] = ($vars['entity']->cv_flashing_status==1);
echo elgg_view('input/checkbox', $options);

elgg_set_plugin_setting('cv_flashing_status', $vars['entity']->cv_flashing_status, 'courseview'); 
echo "Flashing Updating status message? <br>";





echo "<br><h3>Content</h3><br>";
$options=array(
    'name' => 'params[flag_new_content]',
    'id' => 'flag_new_content',
    'options' => array('flag_new_content' => 1),
     'value' => 1,
);
 if ($vars['entity']->flag_new_content == 1)
    {
        $options['checked'] = true;
    }
echo elgg_view('input/checkbox', $options);

elgg_set_plugin_setting('flag_new_content', $vars['entity']->flag_new_content, 'courseview'); 
echo "Show new content tags on content created since last login";
echo "</div>";