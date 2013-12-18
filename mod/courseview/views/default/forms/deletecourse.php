<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$userguid = elgg_get_logged_in_user_guid();


echo "<div class='cvminiview'>";
echo '<em>DELETE A COURSE:</em><br/><br/>';
echo ('Please choose course name to edit: ');

//$base_path = elgg_get_plugins_path() . 'courseview/views/default/courseview';
//require ($base_path . '/listcourses.php');

echo elgg_view ("courseview/listcourses");
echo elgg_view('input/submit');
echo "</div>";
// var_dump ($cvcourses);
//  exit;
?>
