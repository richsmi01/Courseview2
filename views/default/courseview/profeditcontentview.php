<?php
/*
 * used to create the administration views that are presented to a professor
 */
?>
<ul>
    <li>
        <input type='checkbox' name='a' id='cp-2'>CourseView Administration
        <div>
            <ul>
                <li class ='sub'>
                    <input type='checkbox' name='a' >Edit the current menu item
                    <div>
                        <?php echo elgg_view_form('editmenuitem'); ?>
                    </div>
                </li>

                <li class ='sub'>
                    <input type='checkbox' >Add new menu item below the current menu item
                    <div > 
                        <?php echo elgg_view_form('addmenuitem'); ?>
                    </div>
                </li>
                <li class ='sub'>
                    <input type='checkbox'  >Manage Courses and Cohorts
                    <div>
                        <?php
                        echo elgg_view_form('createcourse');
                        echo elgg_view_form('cveditacourse');
                        echo elgg_view_form('deletecourse');
                        echo elgg_view_form('addacohort');
                        echo elgg_view_form('editacohort');
                        echo elgg_view_form('deleteacohort');
                        
                        ?>
                    </div>
                </li>
            </ul>
        </div>
    </li>
</ul>
</br>

