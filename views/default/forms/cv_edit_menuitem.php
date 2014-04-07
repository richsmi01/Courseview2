<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$cvmenu = get_entity($cvmenuguid);

echo "<div class='cvminiview'>";
echo '<em>Edit the current module</em>';
echo '<br/>Enter name of new module';
echo elgg_view('input/text', array(
    'name' => 'cvmodulename',
    'value' => $cvmenu->name));


echo elgg_view('input/text', array(
    'name' => 'cvmenutype',
    'value' => $cvmenu->menutype));




echo elgg_view('input/submit', array(
    'value' => 'Change Name',
     'name' =>'buttonchoice'));
echo elgg_view('input/submit', array(
    'value' => 'Indent',
     'name' =>'buttonchoice'));
echo elgg_view('input/submit', array(
    'value' => 'Outdent',
     'name' =>'buttonchoice',));
echo elgg_view('input/submit', array(
    'value' => 'Move Up',
     'name' =>'buttonchoice'));
echo elgg_view('input/submit', array(
    'value' => 'Move Down',
    'name' =>'buttonchoice'));
echo '<br/>';
echo elgg_view('input/submit', array(
    'value' => 'Delete Menu Item',
    'name' =>'buttonchoice',
    'class' => 'elgg-requires-confirmation'));
//echo '<input type ="checkbox" id = "editcoursecheckbox"/>Confirm Delete --(need to Elggify this)';
echo '</div>';
?>
