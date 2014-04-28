<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$status = ElggSession::offsetGet('courseview');
if ($status)
{
    ElggSession::offsetSet('courseview', false);
    forward("activity"); 
}
else
{
    ElggSession::offsetSet('courseview', true);
}