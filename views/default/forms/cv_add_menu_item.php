<?php

echo "<div class='cvminiview'>";
$currentmenu = get_entity(ElggSession::offsetGet('cvmenuguid'));
echo '<p>'.elgg_echo('cv:forms:cv_add_menu_item:add_menu_below', array("<em>$currentmenu->name</em>")).'</p>';      
echo elgg_echo('cv:forms:cv_add_menu_item:type_the_name');
echo elgg_view('input/text', array(
    'name' => 'newmodulename',
    'value' => ""));
echo elgg_echo('cv:forms:cv_add_menu_item:choose_menu_type');
$folder = elgg_echo('cv:forms:cv_add_menu_item:folder');
$professor = elgg_echo('cv:forms:cv_add_menu_item:professor');
$student = elgg_echo('cv:forms:cv_add_menu_item:student');
$contenttypes = array("folder" => $folder, "professor" => $professor, "student" => $student);
echo elgg_view('input/dropdown', array(
    'name' => 'newmoduletype',
    'value' => $filter,
    'options_values' => $contenttypes));
echo"<br><br>";

echo '<p>'.elgg_echo('cv:forms:cv_add_menu_item:indent_level', array("<em>$currentmenu->name</em>")).'</p>';
if ($currentmenu->indent > 1)  //can't outdent unless the menu is at least at indent level 2
{
    $outdent = elgg_echo('cv:forms:cv_add_menu_item:outdent');
    echo elgg_view('input/submit', array(
        'value' => elgg_echo($outdent),
        'name' => 'buttonchoice'));
}
$same_level = elgg_echo('cv:forms:cv_add_menu_item:same_level');
echo elgg_view('input/submit', array(
    'value' => elgg_echo($same_level),
    'name' => 'buttonchoice'));
echo elgg_view('input/hidden', array('name' => 'preview', 'value' => 1));  //this to prevent forward intercept from triggering 
$indent = elgg_echo('cv:forms:cv_add_menu_item:indent');
echo elgg_view('input/submit', array(
    'value' => elgg_echo($indent),
    'name' => 'buttonchoice'));
echo '</div>';
