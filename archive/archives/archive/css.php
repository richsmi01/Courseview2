<?php
/*
 * 	This is a php file, yet it contains css
 * 	This is normal for Elgg, it's part of the views system
 * 	Simply add your css rules to this file
 * 	Remember to clear your cache, or you may not see the changes right away
 * 	Cache can be cleared using the button on the administration page
 * 	or by visiting <your_url>/upgrade.php
 * 
 * 	For your reference, the full elgg default css is included
 * 	in views/default/customize_css/reference
 * 
 * 	It is spread over a number of php files, but they are structured by what
 * 	the css is affecting.
 * 
 * 	Remember that themes and other plugins also override the default css
 * 	if you find that your changes aren't working check the order of the plugins
 * 	and check your $priority setting in start.php
 * 
 * 	(also check that you're creating/modifying the correct rules)
 */
?>

div.customize_css_example_rule {
	display: block;
	width: 200px;
	height: 200px;
	background-color: #ff0000;
}

.elgg-page-topbar {
	background: yellow;
                      box-shadow:black 2px 2px 2px;
}

            .cv-treeview ul,
            .cv-treeview li
            {
                padding: 0;
                margin: 0;
                list-style: none;
            }

            .cv-treeview input
            {
                position: absolute;
                opacity: 0;
            }

            .cv-treeview
            {
                font: normal 11px "Segoe UI", Arial, Sans-serif;
                -moz-user-select: none;
                -webkit-user-select: none;
                user-select: none;
            }

            .cv-treeview a
            {
                color: #00f;
                text-decoration: none;
            }
            /*I added this to make branch nodes push in a little bit--Rich.*/
            .cv-treeview  .indent {
                margin-left: 40px;
            }

            .cv-treeview a:hover
            {
                text-decoration: underline;
            }

            .cv-treeview input + label + ul
            {
                margin: 0 0 0 22px;
            }

            .cv-treeview input ~ ul
            {
                display: none;
            }

            .cv-treeview label,
            .cv-treeview label::before
            {
                cursor: pointer;
            }

            .cv-treeview input:disabled + label
            {
                cursor: default;
                opacity: .6;
               
            }

            .cv-treeview input:checked:not(:disabled) ~ ul
            {
                display: block;
            }

            .cv-treeview label,
            .cv-treeview label::before
            {
               /* background: url("localhost/elgg/courseview/imgs/icons.png") no-repeat;*/
              /*background: url("../../imgs/icons.png") no-repeat;*/
             /*   background: url("/imgs/icons.png") no-repeat; */
             /* background: url("icons.png") no-repeat;*/
           
           /*       background: url ("'.  elgg_get_site_url().'mod/courseview/_graphics/icons.png") no-repeat;*/
               background: url("../../../../icons.png");
            }

            .cv-treeview label,
            .cv-treeview a,
            .cv-treeview label::before
            {
                display: inline-block;
                height: 16px;
                line-height: 16px;
                vertical-align: middle;
            }

            .cv-treeview label
            {
                background-position: 18px 0;
            }

            .cv-treeview label::before
            {
                content: "";
                width: 16px;
                margin: 0 22px 0 0;
                vertical-align: middle;
                background-position: 0 -32px;
            }

            .cv-treeview input:checked + label::before
            {
                background-position: 0 -16px;
            }

            /* webkit adjacent element selector bugfix */
            @media screen and (-webkit-min-device-pixel-ratio:0)
            {
                .cv-treeview 
                {
                    -webkit-animation: webkit-adjacent-element-selector-bugfix infinite 1s;
                }

                @-webkit-keyframes webkit-adjacent-element-selector-bugfix 
                {
                    from 
                    { 
                    padding: 0;
                } 
                to 
                { 
                    padding: 0;
                }
            }
            }

.cvtreeaddtocohort {

color :red;
}