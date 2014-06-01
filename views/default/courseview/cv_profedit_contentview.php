<!--used to create the administration views that are presented to a professor-->

<ul>
    <li>
        <input type='checkbox' class='cv_collapsible' id ='cv_check1'/>
        <label for='cv_check1' >CourseView Administration</label>
        <div>
            <ul>
                <?php
                elgg_load_library('elgg:courseview');
                $user = elgg_get_logged_in_user_entity();
                $cvcohort = get_entity(ElggSession::offsetGet('cvcohortguid'));
                $cvmenu = get_entity(ElggSession::offsetGet('cvmenuguid'));
                if (cv_is_course_owner($user, $cvcohort))
                {
                    if ($cvmenu->indent > 0)
                    {
                        echo "<li class ='sub'>
                    <input type='checkbox' id='cv_check2' class='cv_collapsible'/>
                    <label for='cv_check2' >Edit the current menu item</label>
                    <div>";
                        echo elgg_view_form('cv_edit_menuitem');
                        echo"</div>";
                        echo "</li>";
                    }
                    echo "<li class ='sub'>
                    <input type='checkbox' id='cv_check3' class = 'cv_collapsible'/>
                    <label for='cv_check3' >Add new menu item below the current menu item</label>
                    <div > ";
                    echo elgg_view_form('cv_add_menu_item');
                    echo"</div>
                </li>";
                }
                ?>

                <li class='sub'>
                    <input type='checkbox' id='cv_check4' class = 'cv_collapsible'/>
                    <label for='cv_check4' >Manage Courses and Cohorts</label>
                    <div class='sub'>
                        <input type='checkbox' id='cv_check5' class = 'cv_collapsible'/>
                        <label for='cv_check5' >Add a Course</label>

                        <div class ='sub2' >
                            <?php echo elgg_view_form('cv_create_course'); ?>
                        </div>
                        <?php
                        if (cv_is_course_owner($user, $cvcohort))
                        {
                            echo"   <input type='checkbox' id='cv_check6' class = 'cv_collapsible'/>";
                            echo "<label for='cv_check6'>Edit this Course</label>";
                            echo "<div class ='sub2'>";
                            echo elgg_view_form('cv_edit_a_course');
                            echo "</div>";
                        }

                        if (cv_prof_num_courses_owned($user) > 0 || (cv_is_admin(elgg_get_logged_in_user_entity())&&  cv_get_all_courses($CV_COUNT)>0))
                        {
                            echo "<input type='checkbox' id='cv_check7' class = 'cv_collapsible'/>";
                            echo "<label for='cv_check7' >Delete a Course</label>";
                            echo "<div class ='sub2'>";
                            echo elgg_view_form('cv_delete_course');
                            echo "</div>";
                        }
                        if (cv_prof_num_courses_owned($user) > 0 || cv_isprof($user))
                        {
                            echo "<input type='checkbox' id='cv_check8' class = 'cv_collapsible'/>";
                            echo "<label for='cv_check8' >Add a Cohort</label>";
                            echo "<div class ='sub2'>";
                            echo elgg_view_form('cv_add_a_cohort');
                            echo "</div>";
                        }
                        if (cv_is_cohort_owner($user, $cvcohort))
                        {
                            echo "<input type='checkbox' id='cv_check9' class = 'cv_collapsible'/>";
                            echo " <label for='cv_check9' >Edit a Cohort</label>";
                            echo " <div class ='sub2'>";
                            echo elgg_view_form('cv_edit_a_cohort');
                            echo "</div class ='sub2'>";
                        }
                        $owned_cohorts = cv_get_owned_cohorts($user);
                        //echo "~~~".sizeof($owned_cohorts);
                        if (sizeof($owned_cohorts) > 0)
                        {
                            echo "<input type='checkbox' id='cv_check10' class = 'cv_collapsible'/>";
                            echo "<label for='cv_check10' >Delete a Cohort</label>";
                            echo "<div class ='sub2'>";
                            echo elgg_view_form('cv_delete_a_cohort');
                            echo "</div>";
                        }
                        ?>
                    </div>
                </li>
            </ul>
        </div>
    </li>
</ul>
</br>