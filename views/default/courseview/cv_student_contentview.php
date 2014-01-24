<?php

/*
 *  calles the cv_filter_content view which provides the dropdown lists and the cv_display_filtered_content which lists the content
 * that has filtered through the dropdown lists
 */
//$cvcohortguid = ElggSession::offsetGet('cvcohortguid');
//$args=array();
//$args['cohortFilter'] = $cvcohortguid;
//echo 'checking...'.$args['cohortFilter'];
echo elgg_view ('courseview/cv_filter_content');
echo elgg_view ('courseview/cv_display_filtered_content');


