<?php

/*
 * Edit a cohort name
 */

$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cv_cohort = get_entity($cv_cohort_guid);
$cv_cohort->title = get_input('cvcohortname');
$cv_cohort->name = get_input('cvcohortname');
$cv_cohort->access_id = ACCESS_PUBLIC;
$cv_cohort->save();



