<?php

/*
 * Edit a cohort name
 */

$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cohort = get_entity($cv_cohort_guid);
$cohort->title = get_input('cvcohortname');
$cohort->name = get_input('cvcohortname');
$cohort->save();



