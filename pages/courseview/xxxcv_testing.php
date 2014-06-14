<script type="text/javascript" src="C:\xampp\htdocs\elgg\mod\extended_tinymce\vendor\tinymce\js\tinymce\tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste moxiemanager"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
});
</script>

<form method="post" action="somepage">
    <textarea name="content" style="width:50%"></textarea>
</form>


<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo 'CV_Testing!';



//set_time_limit(0);
//
//$ia = elgg_set_ignore_access (true);
//
//$options = array (
//    'type' => 'user',
//   // 'metadata_name_value_pairs' =>array ('name'=>'banned', 'value'=>'yes', 'operand'=>'='),
//    'limit' => false,
//);
//
//
// //$batch = new ElggBatch ('elgg_get_entities_from _metadata', $options, null, 25, false);
//
//$batch = elgg_get_entities_from_metadata ($options);
//        echo 'number of users: '.sizeof($batch);
//        foreach ($batch as $user)
//        {
//            if ($user->banned==yes)
//            {
//                echo 'banned user: '.$user->name.' '.$user->banned.'<br>';
//               // $user->delete();
//            }
// else {
//      echo 'not banned user: '.$user->name.' '.$user->banned.'<br>';
// }
//        }
//        
//        elgg_set_ignore_access($ia);

// $object1 = new ElggObject();
//    $object1->subtype = 'test';
//    $object1->save();
//    
//    $object2 = new ElggObject();
//    $object2->subtype = 'test';
//    $object2->save();
//    
//    add_entity_relationship($object1->guid, "test", $object2->guid);
//     var_dump(get_entity_relationships($object1->guid,FALSE));
//     
//    var_dump( '!!!!!!!!'.check_entity_relationship($object1->guid, 'test', $object2->guid));
//    
//    exit;
//    
////    $cvmenu->name = 'Prog 1000';
////    $cvmenu->owner_guid = $user->guid;
////    $cvmenu->container_guid = $cvcourse->guid;
////    $cvmenu->access_id = ACCESS_PUBLIC;
////    $cvmenu->save();
////    $cvmenu->menutype = "folder";
////    $cvmenu->meta1 = "closed";
////    $cvmenu->menuorder = 0;
////    $cvmenu->indent="";
////now, connect it to the course
//  add_entity_relationship($cvcourse->guid, "menu", $cvmenu->guid);
?>
