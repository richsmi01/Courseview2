<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo "<div class='cvminiview'>";
echo  '<em>ADD A COURSE:</em><br/><br/>';
echo elgg_echo ('Course name: ');
echo elgg_view('input/text', array('name' => 'cvcoursename'));
echo elgg_echo ('Course description: ');
echo elgg_view('input/text', array('name' => 'cvcoursedescription'));
echo elgg_view('input/submit');
echo'</div>'

?>
