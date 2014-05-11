<?php

/* *
 * Toggles the courseview session variable to true (CourseView activated)
 * or false (CourseView not active)
 */
$status = ElggSession::offsetGet('courseview');
if ($status)
{
    ElggSession::offsetSet('courseview', false);
    forward("activity"); 
}
else
{
    ElggSession::offsetSet('courseview', true);
}