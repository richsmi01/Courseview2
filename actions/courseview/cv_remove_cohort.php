<?php


/**
 * Description of cv_remove_cohort
 *
 * @author ITSC
 */

if (get_input ('remove_cohort')==='remove')
{
    $cv_cohort = get_entity(get_input ('group_guid'));
  
    $cv_cohort->cvcohort = false;
    system_message ("$cv_cohort->name is no longer a CourseView cohort");
    if (get_input  ('delete_group'))
    {
        $cv_cohort->delete();
        system_message ("$cv_cohort->name has been deleted");
    }
}
 