<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cohort = get_entity($cv_cohort_guid);
//echo get_input('cvcohortname');
$cohort->title = get_input('cvcohortname');
$cohort->save();


?>
