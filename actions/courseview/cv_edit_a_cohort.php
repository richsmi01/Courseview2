<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$cohortguid = ElggSession::offsetGet('cvcohortguid');
$cohort = get_entity($cohortguid);
//echo get_input('cvcohortname');
$cohort->title = get_input('cvcohortname');
$cohort->save();


?>
