
<!--used to create the administration views that are presented to a professor-->

<ul>
    <li>
        <input type='checkbox' class='cv_collapsible' id ='cv_check1'/>
        <label for='cv_check1' >CourseView Administration</label>
        <div>
            <ul>
                <li class ='sub'>
                    <input type='checkbox' id='cv_check2' class='cv_collapsible'/>
                    <label for='cv_check2' >Edit the current menu item</label>
                    <div>
                        <?php echo elgg_view_form('cv_edit_menuitem'); ?>
                    </div>
                </li>
                <li class ='sub'>
                    <input type='checkbox' id='cv_check3' class = 'cv_collapsible'/>
                    <label for='cv_check3' >Add new menu item below the current menu item</label>
                    <div > 
                        <?php echo elgg_view_form('cv_add_menu_item'); ?>
                    </div>
                </li>
                <li class ='sub'>
                    <input type='checkbox' id='cv_check4' class = 'cv_collapsible'/>
                    <label for='cv_check4' >Manage Courses and Cohorts</label>
                    <div>
                        <input type='checkbox' id='cv_check5' class = 'cv_collapsible'/>
                        <label for='cv_check5' >Add a Course</label>
                        <div class ='sub2' >
                            <?php echo elgg_view_form('cv_create_course'); ?>
                        </div>
                        <input type='checkbox' id='cv_check6' class = 'cv_collapsible'/>
                        <label for='cv_check6'>Edit a Course</label>
                        <div class ='sub2'>
                            <?php echo elgg_view_form('cv_edit_a_course'); ?>
                        </div>
                        <input type='checkbox' id='cv_check7' class = 'cv_collapsible'/>
                        <label for='cv_check7' >Delete a Course</label>
                        <div class ='sub2'>
                            <?php echo elgg_view_form('cv_delete_course'); ?>
                        </div>
                        <input type='checkbox' id='cv_check8' class = 'cv_collapsible'/>
                        <label for='cv_check8' >Add a Cohort</label>
                        <div class ='sub2'>
                            <?php echo elgg_view_form('cv_add_a_cohort'); ?>
                        </div>
                        <input type='checkbox' id='cv_check9' class = 'cv_collapsible'/>
                        <label for='cv_check9' >..Edit a Cohort</label>
                        <div class ='sub2'>
                            <?php echo elgg_view_form('cv_edit_a_cohort'); ?>
                        </div class ='sub2'>
                        <input type='checkbox' id='cv_check10' class = 'cv_collapsible'/>
                        <label for='cv_check10' >Delete a Cohort</label>
                        <div class ='sub2'>
                            <?php echo elgg_view_form('cv_delete_a_cohort'); ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </li>
</ul>
</br>

