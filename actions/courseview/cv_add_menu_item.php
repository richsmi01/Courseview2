<?php
/**
 * Add a menu item to the CourseView course 
 * We need to determine the level of indent to set the menu item,
 * the order of the menu item, create a new Elgg object with a subtype of
 * cvmenu and set the menuorder correctly for this object and increment the
 * menuorder of any cvmenu items that follow it.
 */

$current_cvmenu_guid = ElggSession::offsetGet('cvmenuguid');
$current_cvmenu = get_entity($current_cvmenu_guid);

//switch on what button the prof clicked on
switch (get_input('buttonchoice'))
{
    case 'Indent':
        $indent = $current_cvmenu->indent + 1;
        break;
    case "Outdent":
        if ($current_cvmenu->indent > 1)
        {
            $indent = $current_cvmenu->indent - 1;
        }
        else
        {
            $indent = $current_cvmenu->indent;
        }
        break;
    case "Same Level":
        $indent = $current_cvmenu->indent;
        break;
}

$user = elgg_get_logged_in_user_entity();
$modulename = get_input('newmodulename');
$moduletype = get_input('newmoduletype');
$moduleindent = get_input('newmoduleindent');
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cv_course_guid = get_entity($cv_cohort_guid)->container_guid;
$cv_course = get_entity($cv_course_guid);

/*need to set the menu order of the cvmenu item to one more than
 * the currently cvmenu item */
$moduleorder = $current_cvmenu->menuorder + 1;  
//Get all cvmenu items for this course
$menu = elgg_get_entities_from_relationship(array
    ('relationship_guid' => $cv_course_guid,
    'relationship' => 'menu',
    'type' => 'object',
    'subtype' => 'cvmenu',
    'order_by_metadata' => array('name' => 'menuorder', 'direction' => 'ASC', 'as' => 'integer'),
    'limit' => 1000,
        )
);
/*loop through the menu items from the newly inserted cvmenu item to the end of cvmenu items,
 * incrementing the menuorder of each one...This will make the newly created cvmenu item
 * fit nicely into the menuorder of cvmenu items.
 */
for ($a = $moduleorder; $a < sizeof($menu); $a++)
{
    $currentsort = $menu[$a]->menuorder;
    $newsort = $currentsort + 1;
    $menu[$a]->menuorder = $newsort;
    $menu[$a]->save();
}
//Construct the cvmenu item, making the course the container 
$cvmenu = new ElggObject();
$cvmenu->subtype = 'cvmenu';
$cvmenu->name = $modulename;
$cvmenu->owner_guid = $user->guid;
$cvmenu->container_guid = $cv_course_guid;
$cvmenu->access_id = ACCESS_PUBLIC;
$cvmenu->save();
$cvmenu->menutype = $moduletype;
$cvmenu->menuorder = $moduleorder;
$cvmenu->indent = $indent;
//add a relationship between the course and the cvmenu with the relationship of menu.
//this is how we are able to query all cvmenu items that belong to a particular course.
add_entity_relationship($cv_course_guid, 'menu', $cvmenu->guid);
system_message("Added the menu item: $cvmenu->name to $cv_course->title");
forward($cvmenu->getURL());
