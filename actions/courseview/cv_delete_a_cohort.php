<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//echo 'cool!<br/>';
$cv_cohort_guid = get_input('cvcohort'); 
//echo $cvcohortguid."!!!";
$cvcohort = get_entity($cv_cohort_guid);
//echo $cvcohort->title;






//echo 'Course:  '.$cvcohort->title.' has been deleted';
$cvcohort->delete();
//exit;


?>
