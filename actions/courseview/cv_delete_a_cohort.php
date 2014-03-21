<?php


$cv_cohort_guid = get_input('cvcohort'); 
$cvcohort = get_entity($cv_cohort_guid);

$cvcohort->delete();



?>
