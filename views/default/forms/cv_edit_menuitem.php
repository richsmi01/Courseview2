<?php

$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$cvmenu = get_entity($cvmenuguid);

echo "<div class='cvminiview'>";
echo "<p><em>".elgg_echo ( 'cv:forms:cv_edit_menuitem:title', array("<span class = blue> $cvmenu->name</span>"))."</em></p>";
echo elgg_echo ('cv:forms:cv_edit_menuitem:enter_name');
echo elgg_view('input/text', array(
    'name' => 'cvmodulename',
    'value' => $cvmenu->name));

echo elgg_view('input/text', array(
    'name' => 'cvmenutype',
    'disabled'=>true,
    'value' => $cvmenu->menutype));

$change_name = elgg_echo ('cv:forms:cv_edit_menuitem:change_name');
echo elgg_view('input/submit', array(
    'value' =>$change_name,
     'name' =>'buttonchoice'));

 $indent = elgg_echo ('cv:forms:cv_edit_menuitem:indent');
echo elgg_view('input/submit', array(
    'value' => $indent,
     'name' =>'buttonchoice'));
//can't outdent unless the menu is at least at indent level 2
if ($cvmenu->indent >1)
{
    $outdent = elgg_echo ('cv:forms:cv_edit_menuitem:outdent');
      echo elgg_view('input/submit', array(
      'value' => $outdent,
      'name' =>'buttonchoice',));
}
$moveup = elgg_echo ('cv:forms:cv_edit_menuitem:move_up');
echo elgg_view('input/submit', array(
    'value' => $moveup,
     'name' =>'buttonchoice'));
$movedown = elgg_echo ('cv:forms:cv_edit_menuitem:move_down');
echo elgg_view('input/submit', array(
    'value' => $movedown,
    'name' =>'buttonchoice'));
echo '<br/>';
$delete = elgg_echo ('cv:forms:cv_edit_menuitem:delete');
echo elgg_view('input/submit', array(
    'value' => $delete,
    'name' =>'buttonchoice',
    'class' => 'elgg-requires-confirmation'));
//echo '<input type ="checkbox" id = "editcoursecheckbox"/>Confirm Delete --(need to Elggify this)';
echo '</div>';
?>
