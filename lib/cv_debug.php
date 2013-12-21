<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function cv_debug ($message, $filter="stuff", $sev =0)

{
   // echo"entering debug";
    if ($sev > 1000 || $filter =="cvinterceptupdate")
    {
        $timestamp = date("m/d/y--H:i:s");
        if (is_array($message)|| is_object($message))
        {
            //var_dump ($message)  ;
            $a = var_export ($message,true);
            error_log("CV#! !$timestamp - $a \n", 3, "courseview_log.txt");
        }
        error_log("CV# $timestamp - $message \n", 3, "courseview_log.txt");
        
//         if (is_array($data))
//        $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
//    else
//        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
//
//    echo $output;
        
    } 
  
}


?>
