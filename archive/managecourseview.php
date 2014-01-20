<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$user = elgg_get_logged_in_user_entity();



//echo elgg_view('output/url', array("text" => "Add a Professor", "href" => "courseview/addProfessor", 'class' => 'elgg-button elgg-button-action'));

//echo elgg_echo (courseview_listplugins());
//echo elgg_view(courseview_listplugins()); 
  //include the form that allows the admin to add or remove plugins from CourseView's pervue
//echo elgg_view_form('adddeleteelggplugin');

$content =  elgg_echo('Manage CourseView');
$content .= elgg_echo('<br>This page is only accessable to administrators and will do the following:<br>');

$content.= elgg_echo('<br>Still to be done:');
$content .=  elgg_echo('Remove a cohort from courseview<br>');
$content .=  elgg_echo('edit courses, and cohorts');


$content.= elgg_view_form('cv_create_course');
$content.=elgg_view_form('cveditacourse');
$content.= elgg_view_form ('deletecourse');
$content.= elgg_view_form('cv_add_a_cohort');
$content.=elgg_view_form('deleteacohort');




$content.=elgg_view('courseview/cvcoursetree');


$vars = array('content' => $content,);
$body = elgg_view_layout('one_sidebar', $vars);
echo elgg_view_page($title, $body);





//    foreach ($cvcohorts as $cvcohort)
//{
//    echo '###'.$cvcohort->title.'<br/>';
//}
//
//  $userguid = elgg_get_logged_in_user_guid();
//
//    $cvcourses = elgg_get_entities_from_relationship(array
//        ('type' => 'object',
//        'metadata_names' => array('cvcourse'), 
//        'metadata_values' => array(true),  
//        'limit' => false,
//        'owner' => $userguid,
//            )
//    );
//     foreach ($cvcourses as $cvcourse)
//{
//    echo '!!!'.$cvcourse->title.'<br/>';
//}
    
    //var_dump ($cvcourses);
 
    
  
  ?>
