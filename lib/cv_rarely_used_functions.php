<?php

/**
 * Returns all cohorts
 * @return array
 */
function cv_get_all_cohorts()
{
    $userguid = $user->guid;
    $cvcohorts = elgg_get_entities_from_metadata(array
        ('type' => 'group',
        'metadata_names' => array('cvcohort'),
        'metadata_values' => array(true),
        'limit' => false,
            )
    );
    return $cvcohorts;
}

/**
 * Returns a list of all courses if argment is false and the number of courses if it is true
 * @param type $cv_count
 * @return type
 */
function cv_get_all_courses($cv_count = false)
{
    $cvcourses = elgg_get_entities(array
        ('type' => 'object',
        'subtype' => array('cvcourse'),
        'limit' => false,
            )
    );
    if ($cv_count)
    {
        return sizeof($cvcourses);
    } else
    {
        return $cvcourses;
    }
}
