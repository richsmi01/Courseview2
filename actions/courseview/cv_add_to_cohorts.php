<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

elgg_load_library('elgg:courseview');

 $cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');

 
$test = get_input ('test');
//echo $test['key'];
//var_dump ($test);



$cvobject = get_entity(get_input(objectguid));
$menuitems = cv_get_student_menu_items_by_cohort ($cvobject->container_guid);
$count =0;
foreach ($menuitems as $item )
{
    //echo 'menu item: '.$item->guid." -- ". $item->name."- "."rs".$count.'--'.get_input('rs'.$count)   .  "<br>";
    $test = get_input('rs'.$item->guid);
    
   // echo "<br>$item->guid--- $test <br>";
     $relationship = 'content'.$cv_cohort_guid;

//        echo 'cvmenuguid:  '.$cvmenuguid.'<br>';
//        echo 'relationship:  '.$relationship.'<br>';
//        echo 'new object guid:  '.$object->guid.'<br>';
    if (get_input($item->guid))
    {
        
      $one=$item->guid;
      $two=$relationship;
      $three= $cvobject->guid;
      //echo'<br>$$$$$$$$$$$$$$$$$$$$$$$$$$$$$<br>';
      //echo '<br>'.$one.'--'.$two.'--'.$three.'<br>';
   
//        add_entity_relationship($one, $two, $three);
    }
    $count++;
}
//exit;
?>
