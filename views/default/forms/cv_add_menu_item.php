<?php

echo "<div class='cvminiview'>";
$currentmenu = get_entity(ElggSession::offsetGet('cvmenuguid'))->name;
echo "<em>Add Menu item below  the $currentmenu menu item </em><br>";
echo '<br>Please type the name of the menu item: ';
echo elgg_view('input/text', array(
    'name' => 'newmodulename',
    'value' => ""));
echo '<br/>   Please choose the menu item type:';
$contenttypes= array ("folder"=>"Folder","professor"=>"Professor", "student"=>"Student");
echo elgg_view('input/dropdown', array(
    'name' => 'newmoduletype',
    'value' => $filter,
    'options_values' => $contenttypes));
echo"<br><br>";

echo "Please choose the indent level of the new menu item relative to the $currentmenu item:<br>";
echo elgg_view('input/submit', array(
    'value' => elgg_echo('Outdent'),
     'name' =>'buttonchoice'));
echo elgg_view('input/submit', array(
    'value' => elgg_echo('Same Level'),
     'name' =>'buttonchoice'));
echo elgg_view('input/submit', array(
    'value' => elgg_echo('Indent'),
     'name' =>'buttonchoice'));
echo '</div>';