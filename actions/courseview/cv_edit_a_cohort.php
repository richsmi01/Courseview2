<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cohort = get_entity($cv_cohort_guid);
//ech$cohort->title = get_input('cvcohortname');o get_input('cvcohortname');
$cohort->title = get_input('cvcohortname');
$cohort->name = get_input('cvcohortname');
$cohort->save();



