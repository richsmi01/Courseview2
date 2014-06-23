<?php

/*
 * Edit a menu item.  This can include changing the name, changing the 
 * ident level, deleting the menu item, or moving the menu item up or down
 */
elgg_load_library('elgg:courseview');
$cv_cohort_guid = ElggSession::offsetGet('cvcohortguid');
$cvmenuitem = get_entity(ElggSession::offsetGet('cvmenuguid'));
$menuitems = cv_get_menu_items_for_cohort($cv_cohort_guid);
switch (get_input('buttonchoice'))
{
    case 'Indent':
        $cvmenuitem->indent = $cvmenuitem->indent + 1;
        $cvmenuitem->save();
        break;
    
    case 'Outdent':
        if ($cvmenuitem->indent > 1)
        {
            $cvmenuitem->indent = $cvmenuitem->indent - 1;
        }
        break;

    case 'Move Up':
        $trailer;
        foreach ($menuitems as $menuitem)
        {
            if ($menuitem->menuorder == $cvmenuitem->menuorder)
            {
                break;
            }
            $trailer = $menuitem;
        }
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
        $leader->menuorder = $cvmenuitem->menuorder;
        $cvmenuitem->menuorder = $cvmenuitem->menuorder + 1;
        $cvmenuitem->save();
        $leader->save();
        break;

    case 'Change Name':
        $cvmenuitemname = get_input('cvmodulename');
        $cvmenuitem->name = $cvmenuitemname;
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

forward($cvmenuitem->getURL());
