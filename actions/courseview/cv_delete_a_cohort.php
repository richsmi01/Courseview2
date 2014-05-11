<?php

$cvcohort->cvcohort = false;

$cv_cohort_guid = get_input('cvcohort');
$cvcohort = get_entity($cv_cohort_guid);
system_message("$cvcohort->title cohort deleted");
$cvcohort->delete();
?>
