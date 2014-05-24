<?php
/**
 * Page Layout
 * ***********Changed for EasyTheme (line 25)****************
 * Contains CSS for the page shell and page layout
 *
 * Default layout: 990px wide, centered. Used in default page shell
 *
 */
?>

#cv_hidden_course_list 
{
    display:none;
}

#make_a_cohort {

display:none;

}

#make_a_cohort + label
{
    background: url('<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/icons.png') no-repeat;
    padding-left:20px;
    background-position:0 -50px;
}

#make_a_cohort:checked ~ div
{ 
    display:block;
}

#make_a_cohort:checked + label
{
    background-position:0 -70px;
}


#menutogglebutton
{
    margin: 5px auto;
    display:block;
}
.cv-menu-toggle
{
margin 10px;
}

/*The CourseView title that is presented if no cohorts are active */
#cv_title
{
    font-weight:bold;
    text-align: center;
    color:blue;
    text-shadow:grey 1px 1px 1px;
    font-size:150%;
}

/*used to make the CourseView active text in the groups list page green*/
.cv_enabled
{
    color:darkgreen  !important;
    font-weight: bold !important;
}

#cv_center
{
    text-align: center;
    padding:0px;
    display:block;
    margin:auto;
}



cv_settings
{
    color:blue;
}

#courseview_sidebar, #courseview_sidebar_filter, #courseview_sidebar_create, #courseview_sidebar_menu
{
    border:grey 2px ridge;
    padding: 5px;
}
#courseview_sidebar
{
    margin:7px;
}
#courseview_sidebar_filter, #courseview_sidebar_create
{
    padding-top: 5px;
    margin: 4px 0px;   
}

.smalltext{
    width: 30px;
}

.newContent {
    height:20px;
    width:33px;
    background-color: lightgreen;
    border:1px solid black;
    box-shadow: black 2px 2px 2px;
    -webkit-transform: rotate(-30deg);
    transform: rotate(-30deg);
    -ms-transform: rotate(-30deg);
    font-weight: bold;
    position:relative;
    top:0px;
    left:-6px;

}

#notHidden
{
    z-index:102;    
    color:white;
    font-weight:bold;
    text-align:center;
    font-size:300%;
    height:inherit;
    position:fixed;
    top:50px;
    right:50px;
    text-shadow: black 2px 2px 2px;
}

body {
    overflow-x:hidden;
}

.sub3{
    padding-left:15px;
    margin-top: 8px;
    display:block;
}

.css-treeview ul, .css-treeview li
{
    padding: 0;
    margin: 0;
    list-style: none;
}

.css-treeview input
{
    position: absolute;
    opacity: 0;  
}


#courseview_sidebar_menu li{
overflow:hidden;
}

.css-treeview input.cvinsert{
    opacity: 1;
}
.css-treeview
{
    font: normal 11px "Segoe UI", Arial, Sans-serif;
    -moz-user-select: none;
    -webkit-user-select: none;
    user-select: none;
    overflow-y:clip;
    overflow-x:clip;
    text-overflow: "...";
}

.css-treeview a
{
    color: #00f;
    text-decoration: none;

}

/*I added this to make branch nodes push in a little bit--Rich.*/
.css-treeview  .indent {
    margin-left: 15px;
}

.css-treeview a:hover
{
    text-decoration: underline;
}

.css-treeview input + label + ul
{
    margin: 0 0 0 22px;
}

.css-treeview input ~ ul
{
    display: none;
}

.css-treeview label,
.css-treeview label::before
{
    cursor: pointer;
}

.css-treeview input:disabled + label
{
    cursor: default;
    opacity: .6;

}

.css-treeview input:checked:not(:disabled) ~ ul
{
    display: block;
}

.css-treeview label,
.css-treeview label:before
{
    background: url('<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/icons.png') no-repeat;
}

.css-treeview label,
.css-treeview a,
.css-treeview label::before
{
    display: inline-block;
    height: 16px;
    line-height: 16px;
    vertical-align: middle;
}

.css-treeview label 
{
    background-position: 18px 0;
}

.css-treeview label:before
{
    content: "";
    width: 16px;
    margin: 0 22px 0 0;
    vertical-align: middle;
    background-position: 0 -32px;
}

.css-treeview input:checked + label::before
{
    background-position: 0 -16px;
}

/* webkit adjacent element selector bugfix */
@media screen and (-webkit-min-device-pixel-ratio:0)
{
    .css-treeview 
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

#editbox {
    border: solid 1px black;
    padding:10px;
    box-shadow:black 2px 2px 2px;
    margin:3px;
}

/* This will hide the edit course material unless the prof has checked the edit course checkbox.*/
#editcourse {
    visibility:hidden;
    height:0px;
}
#editcoursecheckbox~label{
    color:red;
}

#editcoursecheckbox:checked ~#editcourse {
    visibility:visible;
    height:auto;
    color:blue;

}

.editcourse {
    float:left;
    width:30px;
    height:30px;

}

.grey {
    color:grey;
    font-style:italic;
}

/*
 can probably be deleted
.uparrow {
    height:15px;
    width: 15px;
    background-color:blue;
    //border: 1px solid black;
    margin:1px;
    background: url(<?php echo elgg_get_site_url(); ?>mod/courseview/uparrow.png);

}
.downarrow {
    height:15px;
    width: 15px;
    // background-color:red;
    //border: 1px solid black;
    margin:1px;
    background: url(<?php echo elgg_get_site_url(); ?>mod/courseview/downarrow.png);
    background-height:15px;
}
*/
#cvfolderdescription {
    font-size: 400%;
    text-align:center;
    -webkit-animation: flyin 2s ease-in-out;
    position:relative;
}


.cvminiview 
{

    border:solid black 1px;
    padding:10px;
    margin:10px;
}
.cvminiview em
{
    font-weight:bold;
    color:red;
}

.sub {
    margin:5px 5px 5px 15px;
    padding-left:15px;
}

.bluesub {
    margin:15px 5px 5px 15px;
    color:blue;
}

.sub2 {
    margin:5px 5px 5px 30px;
}
.cvcurrent {
    font-style: italic;
}

#cvwelcome {
    font-size:300%;
    text-align: center;
    font-weight: bold;
    -webkit-animation: blurFadeIn 2s ease-in-out;
    animation:glow 10s ease-in-out infinite;
    position: relative;

    text-shadow: 0 1px 0 #ccc,
        0 2px 0 #c9c9c9,
        0 3px 0 #bbb,
        0 4px 0 #b9b9b9,
        0 5px 0 #aaa,
        0 6px 1px rgba(0,0,0,.1),
        0 0 5px rgba(0,0,0,.1),
        0 1px 3px rgba(0,0,0,.3),
        0 3px 5px rgba(0,0,0,.2),
        0 5px 10px rgba(0,0,0,.25),
        0 10px 10px rgba(0,0,0,.2),
        0 20px 20px rgba(0,0,0,.15);
}


@-webkit-keyframes blurFadeIn{
    0%{
        opacity: 0;
        text-shadow: 0 1px 0 #ccc,
            0 2px 0 #c9c9c9,
            0 3px 0 #bbb,
            0 4px 0 #b9b9b9,
            0 5px 0 #aaa,
            0 6px 1px rgba(0,0,0,.1),
            0 0 5px rgba(0,0,0,.1),
            0 1px 3px rgba(0,0,0,.3),
            0 3px 5px rgba(0,0,0,.2),
            0 5px 10px rgba(0,0,0,.25),
            0 10px 10px rgba(0,0,0,.2),
            0 20px 20px rgba(0,0,0,.15);

        -webkit-transform: scale(3.3);
        top:400px;
    }

    100%{
        opacity: 1;
        text-shadow: 0 1px 0 #ccc,
            0 2px 0 #c9c9c9,
            0 3px 0 #bbb,
            0 4px 0 #b9b9b9,
            0 5px 0 #aaa,
            0 6px 1px rgba(0,0,0,.1),
            0 0 5px rgba(0,0,0,.1),
            0 1px 3px rgba(0,0,0,.3),
            0 3px 5px rgba(0,0,0,.2),
            0 5px 10px rgba(0,0,0,.25),
            0 10px 10px rgba(0,0,0,.2),
            0 20px 20px rgba(0,0,0,.15);

        -webkit-transform: scale(1);
        top:0px;
    }
}


@-webkit-keyframes lightSpeedIn {
    0% {
        -webkit-transform: translateX(100%) skewX(-30deg);
        -ms-transform: translateX(100%) skewX(-30deg);
        transform: translateX(100%) skewX(-30deg);
        opacity: 0;
    }

    60% {
        -webkit-transform: translateX(-20%) skewX(30deg);
        -ms-transform: translateX(-20%) skewX(30deg);
        transform: translateX(-20%) skewX(30deg);
        opacity: 1;
    }

    80% {
        -webkit-transform: translateX(0%) skewX(-15deg);
        -ms-transform: translateX(0%) skewX(-15deg);
        transform: translateX(0%) skewX(-15deg);
        opacity: 1;
    }

    100% {
        -webkit-transform: translateX(0%) skewX(0deg);
        -ms-transform: translateX(0%) skewX(0deg);
        transform: translateX(0%) skewX(0deg);
        opacity: 1;
    }
}

@-webkit-keyframes moving{
    0% 
    { 
        margin:50px auto;
        left: 1500px;
        -webkit-transform: rotate(-9deg);
    }

    100% { margin:50px auto;
           -webkit-transform: rotate(-9deg);}
}

@-webkit-keyframes swinging{
    0% { -webkit-transform: rotate(0);
         margin:50px auto }
    5% { -webkit-transform: rotate(10deg); }
    10% { -webkit-transform: rotate(-9deg); }
    15% { -webkit-transform: rotate(8deg); }
    20% { -webkit-transform: rotate(-7deg); }
    25% { -webkit-transform: rotate(6deg); }
    30% { -webkit-transform: rotate(-5deg); }
    35% { -webkit-transform: rotate(4deg); }
    40% { -webkit-transform: rotate(-3deg); }
    45% { -webkit-transform: rotate(2deg); }
    50% { -webkit-transform: rotate(0); } /* Come to rest at 50%. The rest is just stillness */
    100% { -webkit-transform: rotate(0);
           margin:50px auto }
}

.disabled {
    color:grey;
    font-style:italic;
}

.studentcontentitem > li, .profcontentitem{
    padding: 10px;
    background-color:rgba(192,192,192,0.1);
    /*background-color:lightgrey;*/
    border: solid 1px;
    margin:4px;
    box-shadow: black 2px 2px 2px;

    position:relative;
}

#contentitem  {
    padding:5px 5px 5px 15px;
    border:solid black 1px;
    margin: 2px;
}

#contentitem p 
{
    padding-left:15px;
}


@-webkit-keyframes zoom{
    0% 
    { 
        opacity: 0;

        background-color:grey;
        -webkit-transform: scale(5)
    }

    100% { opacity:1;

           -webkit-transform: scale(1)
               background-color:rgba(192,192,192,0.1);
    }

}

@-webkit-keyframes flyin{
    0% 
    { 
        opacity: 0;
        left: -100px;
    }

    100% { opacity:1;

           left:0px;
    }

}

.cv_collapsible
{
    display:none;
}


.cv_collapsible + label
{
    background: url('<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/icons.png') no-repeat;
    padding:0px 3px 0px 16px;
    background-position:0 -50px;
    font-weight:bold;
    //color:blue;
    display:block;
}

.cv_collapsible:checked + label
{
    background: url('<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/icons.png') no-repeat;
    padding:0px 3px 0px 16px;
    background-position:0 -68px;
    font-weight:bold;
}

.cv_collapsible +label + div{
    display: none;
}

.cv_collapsible:checked +label + div {
    display: block;
}

.professor_item {
    padding:0px 3px 0px 16px;
    background: url('<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/icons.png') no-repeat;
    background-position:0 -88px;
}

.student_item{
    padding:0px 3px 0px 16px;
}
#paddown{

    padding-bottom: 5px;
}