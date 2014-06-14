<?php

$params =get_input('params'); 
 //$ignore_acess = elgg_set_ignore_access(true); // grants temporary permission overrides

$object_guid = $params[1];
echo 'Guid being inspected:  '.$object_guid.'<br>';
$object = get_entity ($object_guid);

echo "Working on...".$object->guid.'<br>';
//$object->subtype="cvcourse";
//$object->save();
//var_dump ($object);
//$temp = elgg_get_metadata(array('guid'=>$object->guid, 'limit'=>false));
//var_dump($temp);
$containerobject = get_entity($object->container_guid);
$ownerobject = get_entity($object->owner_guid);
echo "Type:  ".$object->type."<br>";
echo 'Membership:  '.$object->membership.'<br>';
echo 'Access ID: '.$object->access_id.'<br>';
echo 'Site Guid: '.$object->site_guid.'<br>';
echo 'Name: '.$object->name.'<br>';
echo 'Title: '.$object->title.'<br>';
echo 'Subtype: '.$object->getSubtype().'<br>';
echo 'Guid: '.$object->guid.'<br>';
echo 'cv_acl: '.$object->cv_acl.'<br>';
echo 'cvcohort?: '.$object->cvcohort.'<br>';
echo '<br>';

    
echo 'Container Name: '.$containerobject->name.'<br>';
echo 'Container Membership:  '.$containerobject->membership.'<br>';
echo 'Access ID: '.$containerobject->access_id.'<br>';
echo 'Site Guid: '.$containerobject->site_guid.'<br>';
echo 'Container Title: '.$containerobject->title.'<br>';
echo 'Container Type: '.$containerobject->getType().'<br>';
echo 'Container Subtype: '.$containerobject->getSubtype().'<br>';
echo 'Container Guid: '.$containerobject->guid.'<br>';
echo 'Container cv_acl: '.$containerobject->cv_acl.'<br>';
echo '<br>';

echo 'Owner Name: '.$ownerobject->name.'<br>';
echo 'Owner Title: '.$ownerobject->title.'<br>';
echo 'Owner Type: '.$ownerobject->getType().'<br>';
echo 'Owner Subtype: '.$ownerobject->getSubtype().'<br>';
echo 'Owner Guid: '.$ownerobject->guid.'<br>';
echo 'Owner cv_acl: '.$objectobject->cv_acl.'<br>';
echo '<br>';


var_dump ($object);

$meta_data1 =  get_metadata_for_entity ($object_guid);
//echo 'acl:  '.$metadata['acl']
var_dump ($meta_data1);

echo '-------------------------------container object-------------------------------------------';

var_dump ($containerobject);

$meta_data =  get_metadata_for_entity ($containerobject);
var_dump ($meta_data);

echo '-------------------------------owner object-------------------------------------------';

var_dump ($containerobject);

$meta_data =  get_metadata_for_entity ($ownerobject);
var_dump ($meta_data);
 //elgg_set_ignore_access($ignore_acess); // restore permissions