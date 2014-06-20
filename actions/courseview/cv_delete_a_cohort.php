<?php
/*This is called if the delete cohort menu is selected in CourseView*/
$cvcohort->cvcohort = false; //this object is now just a simple group
$cv_cohort_guid = get_input('cvcohort');
$cvcohort = get_entity($cv_cohort_guid);
system_message("$cvcohort->title cohort deleted");
$cvcohort->delete();  //now we delete the group

