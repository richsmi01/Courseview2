<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//echo 'cool!<br/>';
$cohortguid = get_input('cvcohort'); 
//echo $cvcohortguid."!!!";
$cvcohort = get_entity($cohortguid);
//echo $cvcohort->title;






//echo 'Course:  '.$cvcohort->title.' has been deleted';
$cvcohort->delete();
//exit;


?>
