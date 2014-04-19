<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
elgg_load_library('elgg:courseview');
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');

$cvmenuitem = get_entity(ElggSession::offsetGet('cvmenuguid'));
//echo "menu item indent ".$cvmenuitem->indent;
//echo "TEST:" . get_input('buttonchoice');
//echo "menu item indent ".$cvmenuitem->indent;
$menuitems = cv_get_menu_items_for_cohort($cv_cohort_guid);
switch (get_input('buttonchoice'))
{
    case 'Indent':
        //echo 'indent has been selected';


        $cvmenuitem->indent = $cvmenuitem->indent + 1;
        $cvmenuitem->save();
//        if ($cvmenuitem->indent == '-')
//        {
//            $cvmenuitem->indent = '.';
//        } elseif ($cvmenuitem->indent == '.')
//        {
//            $cvmenuitem->indent = '+';
//        }
        break;
    case 'Outdent':
        // echo 'outdent has been selected';
        if ($cvmenuitem->indent > 1)
        {
            $cvmenuitem->indent = $cvmenuitem->indent - 1;
        }
        //$cvmenuitem->save();
//        if ($cvmenuitem->indent == '+')
//        {
//            $cvmenuitem->indent = '.';
//        } elseif ($cvmenuitem->indent == '.')
//        {
//            $cvmenuitem->indent = '-';
//        }
        break;

    case 'Move Up':
        //echo 'move up selected';
        $trailer;
        foreach ($menuitems as $menuitem)
        {
            if ($menuitem->menuorder == $cvmenuitem->menuorder)
            {
                break;
            }
            $trailer = $menuitem;
        }
        //echo $trailer->name;
        $trailer->menuorder = $cvmenuitem->menuorder;
        $cvmenuitem->menuorder = $cvmenuitem->menuorder - 1;
        $cvmenuitem->save();
        $trailer->save();
        break;
    case 'Move Down':
        $done = false;
        $leader;
        foreach ($menuitems as $menuitem)
        {
            $leader = $menuitem;
            if ($done)
            {
                break;
            }
            if ($menuitem->menuorder == $cvmenuitem->menuorder)
            {
                $done = true;
            }
        }
        //echo $leader->name;

        $leader->menuorder = $cvmenuitem->menuorder;
        $cvmenuitem->menuorder = $cvmenuitem->menuorder + 1;
        $cvmenuitem->save();
        $leader->save();
        break;
    // echo 'move down selected';


    case 'Change Name':
        // echo 'Change Name has been selected';
        $cvmenuitemname = get_input('cvmodulename');
        $cvmenuitem->name = $cvmenuitemname;

        //remove later - just for debugging
        $cvmenutype = get_input('cvmenutype');
        $cvmenuitem->menutype = $cvmenutype;


        $cvmenuitem->save();
        break;

    case 'Delete Menu Item':

        $prevmenuorder = $cvmenuitem->menuorder - 1;
        $nextselectedmenuitem = $menuitems[$prevmenuorder];
        for ($a = $cvmenuitem->menuorder; $a < sizeof($menuitems); $a++)
        {
            $menuitems[$a]->menuorder = $menuitems[$a]->menuorder - 1;
            echo $a . "-" . $menuitems[$a]->name . "<br>";
        }
        $menuorder = $cvmenuitem->menuorder;
        $cvmenuitem->delete();
        $cvmenuitem = get_entity($nextselectedmenuitem->guid);      
}

//forward (elgg_get_site_url() . "courseview/cv_contentpane/$cvcohortguid/$cvmenuitem->guid");
forward($cvmenuitem->getURL());
