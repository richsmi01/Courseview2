<?php

echo 'GUID of the Profs Group:<br/>';
echo elgg_view('input/text', array(
    'name' => 'params[profsgroup]',
    'value' => $vars['entity']->profsgroup,
    'disabled' => false));

echo 'Plugins to be recoginized by Courseview';

$regentitytypes = get_registered_entity_types();
$plugins =$regentitytypes['object'];

$shortname = array();
$pluginaddurl = array();
$studentapprovedlist = array();
$profapprovedlist = array();
/*Loop through each plugin and to see which plugins are approved for students and which for profs*/
foreach ($plugins as $plugin)
{
    $studentitem = 'check' . $plugin;
    $profitem ="prof".$plugin;
    echo '<div class=cvsettingsplugins> Student';
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
       
    echo '<br>Professor';
    echo elgg_view ("input/checkbox", $profoptions);
    
    echo $plugin;
    $pluginname = "createstring" . $plugin;
    $friendly = "friendly" . $plugin;
    $object_subtype ="object".$plugin;
    
    
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
        $approved_subtype [$plugin]= $plugin;
    }
    if ($vars['entity']->$profitem==1)  //if the profitem is checked
    {
        $pluginaddurl[$plugin] = $vars['entity']->$pluginname;
        $profapprovedlist [$plugin] = $vars['entity']->$friendly;
        $approved_subtype [$plugin]= $plugin;
    }
}
//var_dump ($studentapprovedlist);
//var_dump($profapprovedlist);
//var_dump($approved_subtype);

elgg_set_plugin_setting('availableplugins', serialize($studentapprovedlist), 'courseview');  //need to serialize arrays before putting in settings
elgg_set_plugin_setting('profavailableplugins', serialize($profapprovedlist), 'courseview');  //need to serialize arrays before putting in settings
elgg_set_plugin_setting('plugincreatestring', serialize($pluginaddurl), 'courseview');  //need to serialize arrays before putting in settings
elgg_set_plugin_setting('approved_subtype', serialize($approved_subtype), 'courseview');  //need to serialize arrays before putting in settings

