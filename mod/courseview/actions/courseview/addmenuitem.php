<?php
//echo 'entering addmenuitem.php';
$currentcvmenuguid = ElggSession::offsetGet('cvmenuguid');
$currentcvmenu= get_entity($currentcvmenuguid);
$indent=0;
switch (get_input('buttonchoice'))
{
    case 'Indent':
        $indent = $currentcvmenu->indent +1;
        break;
    case "Outdent":
        $indent = $currentcvmenu->indent - 1;
        break;
    case "Same Level":
        $indent =$currentcvmenu->indent ;
        break;
}
//echo 'indent:  '.$indent;
$user = elgg_get_logged_in_user_entity();
$modulename = get_input('newmodulename');
$moduletype = get_input('newmoduletype');
$moduleindent = get_input('newmoduleindent');

//echo '<br>$$$:  '.get_input('newmoduleindent').'<br>';

$cohortguid = ElggSession::offsetGet('cvcohortguid');
$cvcourseguid = get_entity($cohortguid)->container_guid;
//echo 'courseguid:  '.$cvcourseguid;
//echo'name of module: '.$modulename;
//echo 'module type: '.$moduletype;
//echo 'module indent: '.$moduleindent;
//echo '<br>got to here';
$moduleorder = $currentcvmenu->menuorder + 1;
//echo "order num:  " . $moduleorder . '<br>';

$menu = elgg_get_entities_from_relationship(array
    ('relationship_guid' => $cvcourseguid,
    'relationship' => 'menu',
    'type' => 'object',
    'subtype' => 'cvmenu',
    'order_by_metadata' => array('name' => 'menuorder', 'direction' => 'ASC', 'as' => 'integer'),
    'limit' => 1000,
        )
);
//echo '<br>got to here';
//var_dump($menu);
//echo 'Number to change' . sizeof($menu) . '###';
for ($a = $moduleorder; $a < sizeof($menu); $a++)
{
    //echo'!!!!<br>';
    $currentsort = $menu[$a]->menuorder;
    $newsort = $currentsort + 1;
    //echo'<br/>changing ' . $menu[$a]->name . ' from ' . $currentsort . ' to ' . $newsort;
    $menu[$a]->menuorder = $newsort;
    $menu[$a]->save();
}
///echo '<br>got to here3';
$cvmenu = new ElggObject();
$cvmenu->subtype = 'cvmenu';
$cvmenu->name = $modulename;
$cvmenu->owner_guid = $user->guid;
$cvmenu->container_guid = $cvcourseguid;
$cvmenu->access_id = ACCESS_PUBLIC;
$cvmenu->save();
$cvmenu->menutype = $moduletype;
$cvmenu->meta1 = "closed";
$cvmenu->menuorder = $moduleorder;
$cvmenu->indent = $indent;

//echo 'new menu item guid: '.$cvmenu->guid;
//echo '<br>got to here4';
//now, connect it to the course
//echo 'got to here';
add_entity_relationship($cvcourseguid, 'menu', $cvmenu->guid);

//error_log ("CV# -  Added a menuitem");

forward(REFERER);

//echo 'cvcourse = ' . get_entity($cvcourseguid)->title;
//echo 'cvmenu = ' . $cvmenu->name;
//echo 'indent: '.$indent;
//exit;