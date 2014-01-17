<?php

echo 'GUID of the Profs Group:<br/>';
echo elgg_view('input/text', array(
    'name' => 'params[profsgroup]',
    'value' => $vars['entity']->profsgroup,
    'disabled' => false));

echo 'Plugins to be recoginized by Courseview';

//this doesn't work on the linux elgg:  $plugins = get_registered_entity_types()['object'];  -- Why???
$regentitytypes = get_registered_entity_types();
$plugins =$regentitytypes['object'];

$shortname = array();
$pluginaddurl = array();
$approvedlist = array();


foreach ($plugins as $plugin)
{
    $menuitem = 'check' . $plugin;
    echo '<div class=cvsettingsplugins>';
    $checkoptions = array('name' => "params[$menuitem]", 'value' => 1);  //sends a 0 if the checkbox isn't checked
    if ($vars['entity']->$menuitem == 1)
    {
        $checkoptions['checked'] = true;
    }
    echo elgg_view('input/checkbox', $checkoptions);
    echo $plugin;
    $pluginname = "createstring" . $plugin;
    $friendly = "friendly" . $plugin;

    echo elgg_view('input/text', array(
        'name' => 'params[' . $friendly . ']',
        'value' => $vars['entity']->$friendly));
    

    echo elgg_view('input/text', array(
        'name' => 'params[' . $pluginname . ']',
        'value' => $vars['entity']->$pluginname));
    echo'</div>';
    if ($vars['entity']->$menuitem == 1)
    {
        $pluginaddurl[$plugin] = $vars['entity']->$pluginname;
        $approvedlist [$plugin] = $vars['entity']->$friendly;
    }
}

//var_dump($pluginaddurl);
elgg_set_plugin_setting('availableplugins', serialize($approvedlist), 'courseview');  //need to serialize arrays before putting in settings
elgg_set_plugin_setting('plugincreatestring', serialize($pluginaddurl), 'courseview');  //need to serialize arrays before putting in settings
?>
