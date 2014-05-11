<?php
/**
 * Add a menu item to the CourseView course 
 * We need to determine the level of indent to set the menu item,
 * the order of the menu item, create a new Elgg object with a subtype of
 * cvmenu and set the menuorder correctly for this object and increment the
 * menuorder of any cvmenu items that follow it.
 */

$currentcvmenuguid = ElggSession::offsetGet('cvmenuguid');
$currentcvmenu = get_entity($currentcvmenuguid);
$indent = 0;
switch (get_input('buttonchoice'))
{
    case 'Indent':
        $indent = $currentcvmenu->indent + 1;
        break;
    case "Outdent":
        if ($currentcvmenu->indent > 1)
        {
            $indent = $currentcvmenu->indent - 1;
        }
        else
        {
            $indent = $currentcvmenu->indent;
        }
        break;
    case "Same Level":
        $indent = $currentcvmenu->indent;
        break;
}

$user = elgg_get_logged_in_user_entity();
$modulename = get_input('newmodulename');
$moduletype = get_input('newmoduletype');
$moduleindent = get_input('newmoduleindent');
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvcourseguid = get_entity($cv_cohort_guid)->container_guid;
$cvcourse = get_entity($cvcourseguid);
$moduleorder = $currentcvmenu->menuorder + 1;

$menu = elgg_get_entities_from_relationship(array
    ('relationship_guid' => $cvcourseguid,
    'relationship' => 'menu',
    'type' => 'object',
    'subtype' => 'cvmenu',
    'order_by_metadata' => array('name' => 'menuorder', 'direction' => 'ASC', 'as' => 'integer'),
    'limit' => 1000,
        )
);

for ($a = $moduleorder; $a < sizeof($menu); $a++)
{
    $currentsort = $menu[$a]->menuorder;
    $newsort = $currentsort + 1;
    $menu[$a]->menuorder = $newsort;
    $menu[$a]->save();
}
$cvmenu = new ElggObject();
$cvmenu->subtype = 'cvmenu';
$cvmenu->name = $modulename;
$cvmenu->owner_guid = $user->guid;
$cvmenu->container_guid = $cvcourseguid;
$cvmenu->access_id = ACCESS_PUBLIC;
$cvmenu->save();
$cvmenu->menutype = $moduletype;
//$cvmenu->meta1 = "closed";
$cvmenu->menuorder = $moduleorder;
$cvmenu->indent = $indent;

add_entity_relationship($cvcourseguid, 'menu', $cvmenu->guid);

system_message("Added the menu item: $cvmenu->name to $cvcourse->title");
forward($cvmenu->getURL());
