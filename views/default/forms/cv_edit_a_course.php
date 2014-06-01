<?php


$userguid = elgg_get_logged_in_user_guid();

echo "<div class='cvminiview'>";
echo '<em>EDIT CURRENT COURSE NAME:</em><br/><br/>';

$currentcourse = get_entity (get_entity(ElggSession::offsetGet('cvcohortguid'))->container_guid);
$currentcoursename = $currentcourse->title;
echo '<br>Make any changes to the course title and click on submit : '.$currentcourse->guid;
echo elgg_view('input/text', array(
    'name' => 'newcoursename',
    'value' => "$currentcoursename"));
echo ('Please choose course name to edit: ');
echo elgg_view('input/submit');
echo "</div>";


