<?php

/**
 * Runs when a prof clicks on the CourseView admin button...all this does is load
 *cv_content with both cohort and cvmenu item set to 0.  The cv_content_pane will
 * take it from there
 */
$cv_path = $elgg_path = elgg_get_site_url() . 'courseview/cv_contentpane/0/0';
forward($cv_path);

