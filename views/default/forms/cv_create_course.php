<?php

echo "<div class='cvminiview'>";
echo  '<p><em>'.elgg_echo ( 'cv:forms:cv_create_course:add_course').'</em></p>';
echo elgg_echo ('cv:forms:cv_create_course:course_name');
echo elgg_view('input/text', array('name' => 'cvcoursename'));
echo elgg_echo ('cv:forms:cv_create_course:course_description');
echo elgg_view('input/text', array('name' => 'cvcoursedescription'));
echo elgg_view('input/submit');
echo'</div>'

?>
