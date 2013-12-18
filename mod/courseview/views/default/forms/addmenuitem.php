<?php

//::TODO: will need to figure out about whether to do folder descriptions
echo "<div class='cvminiview'>";
echo '<em>Add Menu item below current Menu item</em>';
echo '<br>Module Name: ';
echo elgg_view('input/text', array(
    'name' => 'newmodulename',
    'value' => ""));
echo '<br/>Module Type';
echo elgg_view('input/text', array(
    'name' => 'newmoduletype',
    'value' => ""));
//echo '<br/>Enter indent of new module';
//echo elgg_view('input/text', array(
//    'name' => 'newmoduleindent',
//    'value' => ""));
//echo '<br/>Enter order of new module';
//echo elgg_view('input/text', array(
//    'name' => 'newmoduleorder',
//    'value' => ""));
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
?>
