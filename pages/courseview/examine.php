<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$params =get_input('params'); 



$object_guid = $params[1];
$object = get_entity ($object_guid);


echo "Working on...".$object->title;
//$object->subtype="cvcourse";
//$object->save();

$containerobject = get_entity($object->container_guid);
$ownerobject = get_entity($object->owner_guid);

echo 'Object Name: '.$object->name.'<br>';
echo 'Object Title: '.$object->title.'<br>';
echo 'Object Type: '. $object->getType() .'<br>';
echo 'Object Subtype: '.$object->getSubtype().'<br>';
echo 'Object Guid: '.$object->guid.'<br>';
echo 'Object cv_acl: '.$object->cv_acl.'<br>';
echo '<br>';
    
echo 'Container Name: '.$containerobject->name.'<br>';
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