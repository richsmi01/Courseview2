<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$cvmenuguid = ElggSession::offsetGet('cvmenuguid');
$cohortguid = ElggSession::offsetGet('cvcohortguid');
//echo "professorcontentview<br>";
if (cv_isprof(elgg_get_logged_in_user_entity()))
{

    echo elgg_view('courseview/cvfiltercontent');

}

$content = elgg_get_entities_from_relationship(array(
    'relationship_guid' => $cvmenuguid,
    'relationship' => 'content'));

foreach ($content as $menuitem)
{
    if (cv_isprof($user))
    {
        echo elgg_echo('<div class="editcourse"><a class ="uparrowcontainer" href="http://sheridancollege.ca"><div class="uparrow" ></div></a>');
        echo elgg_echo('<a class ="downarrowcontainer" href="http://sheridancollege.ca"><div class="downarrow"></div></a>');
        echo elgg_echo('</div>');
    }
    echo elgg_echo(elgg_view_entity($menuitem, array(full_view => false)));
}
?>
