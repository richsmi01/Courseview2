
  <!--used to create the administration views that are presented to a professor-->

<ul>
    <li>
        <input type='checkbox' name='a' id='cp-2'>CourseView Administration
        <div>
            <ul>
                <li class ='sub'>
                    <input type='checkbox' name='a' >Edit the current menu item
                    <div>
                            <?php echo elgg_view_form('cv_edit_menuitem'); ?>
                    </div>
                </li>
                <li class ='sub'>
                    <input type='checkbox' >Add new menu item below the current menu item
                    <div > 
                        <?php echo elgg_view_form('cv_add_menu_item');?>
                    </div>
                </li>
                <li class ='sub'>
                    <input type='checkbox'  >Manage Courses and Cohorts
                    <div>
                        <?php
                        echo elgg_view_form('cv_create_course');
                        echo elgg_view_form('cv_edit_a_course');
                        echo elgg_view_form('cv_delete_course');
                        echo elgg_view_form('cv_add_a_cohort');
                        echo elgg_view_form('cv_edit_a_cohort');
                        echo elgg_view_form('cv_delete_a_cohort');
                        ?>
                    </div>
                </li>
            </ul>
        </div>
    </li>
</ul>
</br>

