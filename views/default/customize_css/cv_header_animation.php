<?php
?>

.elgg-page-header {
    position:fixed;
    z-index:100;
    top:25px;
    width:100%;
    border-bottom: 1px  black solid;
    border-top:1px black solid;
}
 .elgg-page-topbar{
    position:fixed;
    z-index:101;
    top:0px;
    width:100%;
}

.sky {
    height:480px;
    background:#007fd5;
    position:relative;
    overflow:hidden;
    -webkit-animation:sky_background 70s ease-out infinite;
    -moz-animation:sky_background 70s ease-out infinite;
    -o-animation:sky_background 70s ease-out infinite;
    -webkit-transform:translate3d(0,0,0);
    -moz-transform:translate3d(0,0,0);
    -o-transform:translate3d(0,0,0)
}


.moon {
    background:url("http://montanaflynn.me/lab/css-clouds/images/moon.png");
    position:absolute;
    left:0;
    height:100%;
    width:300%;
    -webkit-animation:moon 70s linear infinite;
    -moz-animation:moon 70s linear infinite;
    -o-animation:moon 70s linear infinite;
    -webkit-transform:translate3d(0,0,0);
    -moz-transform:translate3d(0,0,0);
    -o-transform:translate3d(0,0,0)
}


#cvcloud1 {
    background: url(<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/cloud_onea.png) ;
    position:absolute;
    left:0;
    top:0;
    height:100%;
   // overflow:hidden;
    width:300%;
    -webkit-animation:cloud_one 70s linear infinite;
    -moz-animation:cloud_one 70s linear infinite;
    -o-animation:cloud_one 70s linear infinite;
    -webkit-transform:translate3d(0,0,0);
    -moz-transform:translate3d(0,0,0);
    -o-transform:translate3d(0,0,0)
}

#cvcloud2 {
    background: url(<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/cloud_twoa.png) ;
    position:absolute;
    left:0;
    top:0;
    height:100%;
    width:300%;
   // overflow:hidden;
    -webkit-animation:cloud_two 95s linear infinite;
    -moz-animation:cloud_two 95s linear infinite;
    -o-animation:cloud_two 95s linear infinite;
    -webkit-transform:translate3d(0,0,0);
    -moz-transform:translate3d(0,0,0);
    -o-transform:translate3d(0,0,0)
}

#cvcloud3 {
    background: url(<?php echo elgg_get_site_url(); ?>mod/courseview/imgs/cloud_threea.png) ;
    position:absolute;
    left:0;
    top:0;
    height:100%;
   // overflow:hidden;
    width:300%;
    -webkit-animation:cloud_three 120s linear infinite;
    -moz-animation:cloud_three 120s linear infinite;
    -o-animation:cloud_three 120s linear infinite;
    -webkit-transform:translate3d(0,0,0);
    -moz-transform:translate3d(0,0,0);
    -o-transform:translate3d(0,0,0)
}

@-webkit-keyframes sky_background {
    0% {
        background:#007fd5;
        color:#007fd5
    }

    50% {
        background:#000;
        color:#a3d9ff
    }

    100% {
        background:#007fd5;
        color:#007fd5
    }
}

@-webkit-keyframes moon {
    0% {
        opacity: 0;
        left:-200%;
            -moz-transform: scale(0.5);
        -webkit-transform: scale(0.5);
    }

    50% {
        opacity: 1;
        -moz-transform: scale(1);
        left:0%;
            bottom: 250px;
        -webkit-transform: scale(1);
    }

    100% {
        opacity: 0;
        bottom:500px;
        -moz-transform: scale(0.5);
        -webkit-transform: scale(0.5);
    }
}

@-webkit-keyframes cloud_one {
    0% {
        left:0
    }

    100% {
        left:-200%
    }
}

@-webkit-keyframes cloud_two {
    0% {
        left:0
    }

    100% {
        left:-200%
    }
}

@-webkit-keyframes cloud_three {
    0% {
        left:0
    }

    100% {
        left:-200%
    }
}

@-moz-keyframes sky_background {
    0% {
        background:#007fd5;
        color:#007fd5
    }

    50% {
        background:#000;
        color:#a3d9ff
    }

    100% {
        background:#007fd5;
        color:#007fd5
    }
}

@-moz-keyframes moon {
    0% {
        opacity: 0;
        left:-200%
            -moz-transform: scale(0.5);
        -webkit-transform: scale(0.5);
    }

    50% {
        opacity: 1;
        -moz-transform: scale(1);
        left:0%
            bottom:250px;
        -webkit-transform: scale(1);
    }

    100% {
        opacity: 0;
        bottom:500px;
        -moz-transform: scale(0.5);
        -webkit-transform: scale(0.5);
    }
}

@-moz-keyframes cloud_one {
    0% {
        left:0
    }

    100% {
        left:-200%
    }
}

@-moz-keyframes cloud_two {
    0% {
        left:0
    }

    100% {
        left:-200%
    }
}

@-moz-keyframes cloud_three {
    0% {
        left:0
    }

    100% {
        left:-200%
    }
}

#courseview_sidebar {
margin-top: 3px;
margin-bottom: 8px;
}
.elgg-page-body{
margin-top: 115px;
}
.elgg-page-header {
    position:fixed;
    z-index:100;
    top:25px;
    width:100%;
    border-bottom: 1px  black solid;
    border-top:1px black solid;
}
 .elgg-page-topbar{
    position:fixed;
    z-index:101;
    top:0px;
    width:100%;
}
