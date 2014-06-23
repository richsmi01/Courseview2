<!--used to create the administration views that are presented to a professor-->


<?php
$cvcohort = get_entity(ElggSession::offsetGet('cvcohortguid'));
$cv_check = '';
if (!$cvcohort)
{
     $cv_check = "checked";
}
echo "<ul>
                <li>
                    <input type='checkbox' class='cv_collapsible' id ='cv_check1'  $cv_check/>
                    <label for='cv_check1' >CourseView Administration</label>
                    <div>
                    <ul>";

elgg_load_library('elgg:courseview');
$user = elgg_get_logged_in_user_entity();

$cvmenu = get_entity(ElggSession::offsetGet('cvmenuguid'));
if (cv_is_course_owner($user, $cvcohort))
{
    if ($cvmenu->indent > 0)
    {
        $cv_edit_menuitem = elgg_view_form('cv_edit_menuitem');
        echo "<li class ='sub'>
                        <input type='checkbox' id='cv_check2' class='cv_collapsible  $cv_check'/>
                        <label for='cv_check2' >Edit the current menu item</label>
                        <div>
                            $cv_edit_menuitem
                        </div>
                    </li>";
    }
    
    $cv_add_menu_item = elgg_view_form ('cv_add_menu_item');
    echo "<li class ='sub'>
                    <input type='checkbox' id='cv_check3' class = 'cv_collapsible $cv_check'/>
                    <label for='cv_check3' >Add new menu item below the current menu item</label>
                    <div > 
                        $cv_add_menu_item
                    </div>
                </li>";
}

$cv_create_course = elgg_view_form('cv_create_course'); 
echo "<li class='sub'>
                <input type='checkbox' id='cv_check4' class = 'cv_collapsible' $cv_check/>
                <label for='cv_check4' >Manage Courses and Cohorts</label>
                <div class='sub'>
                    <input type='checkbox' id='cv_check5' class = 'cv_collapsible' />
                    <label for='cv_check5' >Add a Course</label>
                    <div class ='sub2' >
                        $cv_create_course
                    </div>";

        $cv_edit_a_course = elgg_view_form('cv_edit_a_course'); 
        if (cv_is_course_owner($user, $cvcohort))
        {
            echo"   <input type='checkbox' id='cv_check6' class = 'cv_collapsible' />
                          <label for='cv_check6'>Edit this Course</label>
                          <div class ='sub2'>
                                $cv_edit_a_course
                         </div>";
        }
        elgg_load_library('elgg:cv_rarely_used_functions');
        if (cv_prof_num_courses_owned($user) > 0 || (cv_is_admin(elgg_get_logged_in_user_entity()) && cv_get_all_courses($CV_COUNT) > 0))
        {
            $cv_delete_course = elgg_view_form('cv_delete_course'); 
            echo "<input type='checkbox' id='cv_check7' class = 'cv_collapsible' />
                       <label for='cv_check7' >Delete a Course</label>
                       <div class ='sub2'>
                            $cv_delete_course
                       </div>";
        }
        
        if (cv_prof_num_courses_owned($user) > 0 || cv_isprof($user))
        {
            $cv_add_a_cohort = elgg_view_form('cv_add_a_cohort'); 
            echo "<input type='checkbox' id='cv_check8' class = 'cv_collapsible' />
                        <label for='cv_check8' >Add a Cohort</label>
                        <div class ='sub2'>
                                $cv_add_a_cohort
                        </div>";
        }
        if (cv_is_cohort_owner($user, $cvcohort))
        {
              $cv_edit_a_cohort = elgg_view_form('cv_edit_a_cohort'); 
            echo "<input type='checkbox' id='cv_check9' class = 'cv_collapsible' />
                        <label for='cv_check9' >Edit a Cohort</label>
                        <div class ='sub2'>
                                $cv_edit_a_cohort
                        </div class ='sub2'>";
        }
        $owned_cohorts = cv_get_owned_cohorts($user);
        //echo "~~~".sizeof($owned_cohorts);
        if (sizeof($owned_cohorts) > 0)
        {
            $cv_delete_a_cohort = elgg_view_form('cv_delete_a_cohort'); 
            echo "<input type='checkbox' id='cv_check10' class = 'cv_collapsible' />
                        <label for='cv_check10' >Delete a Cohort</label>
                        <div class ='sub2'>
                            $cv_delete_a_cohort
                        </div>";
        }
      echo"         </div>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        </br>";