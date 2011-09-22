<?php
/*******************************************************************

Copyright 2010, Adrian Laurenzi

This file is part of ImpactProbe.

ImpactProbe is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
at your option) any later version.

ImpactProbe is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with ImpactProbe. If not, see <http://www.gnu.org/licenses/>.

*******************************************************************/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?= $page_title ?> - <?= Kohana::config('myconf.app_name') ?></title>
<meta http-equiv="Content-Type" content="text/html; utf-8">
<meta name="description" content="">
<meta name="keywords" content="">
<link rel="stylesheet" href="<?= Kohana::config('myconf.url.css'); ?>main.css" type="text/css">
<link rel="stylesheet" href="<?= Kohana::config('myconf.url.css'); ?>dark-hive/jquery-ui-1.8.14.custom.css" type="text/css">
<script src="<?= Kohana::config('myconf.url.js'); ?>jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="<?= Kohana::config('myconf.url.js'); ?>jquery.selectboxes.min.js" type="text/javascript"></script>
<script src="<?= Kohana::config('myconf.url.js'); ?>jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
<script src="<?= Kohana::config('myconf.url.js'); ?>jquery-ui-global.js" type="text/javascript"></script>

<script type="text/javascript" src="<?= Kohana::config('myconf.url.js'); ?>mapper.js"></script>

<script type="text/javascript" language="javascript" src="<?= Kohana::config('myconf.url.js'); ?>lytebox.js"></script>
<link rel="stylesheet" href="<?= Kohana::config('myconf.url.css'); ?>lytebox.css" type="text/css" media="screen" />
<style type="text/css">
#lbClose.grey { background: url(<?= Kohana::config('myconf.url.images'); ?>close_grey.png) no-repeat; }
#lbLoading {
    position: absolute; top: 45%; left: 0%; height: 32px; width: 100%; text-align: center; line-height: 0; background: url(<?= Kohana::config('myconf.url.images'); ?>loading.gif) center no-repeat;
}
#header {
    background:url(<?= Kohana::config('myconf.url.images'); ?>header_bg.jpg);
    height:124px;
    width:100%;
}
</style>
</head>

<script type="text/javascript">
$(document).ready(function(){
    $("#tutorials_menu").hover(
    function () {
        $(this).animate({
            height: "240px"
        }, 500 );
    }, 
    function () {
        $(this).animate({
            height: "35px"
        }, 500 );
    });
});

function popupWindow(url) {
    newwindow = window.open(url,'name','height=623,width=990');
    if (window.focus) {newwindow.focus()}
    return false;
}
</script>

<div id="header">
    <a href="<?= Url::base() ?>"><img src="<?= Kohana::config('myconf.url.images'); ?>impact_probe_logo.jpg" width="322" height="124" alt="Impact Probe" border="0"></a>
    
    <div id="tutorials_menu">
        <div class="icon"><img src="<?= Kohana::config('myconf.url.images'); ?>info_icon.png" width="25" height="25" alt="Tutorials" border="0"></div>
        <div class="text"><b>New user?</b><br>Check out the video tutorials</div>
        
        <div class="links">
            <b>Data collection</b>
            <ol>
                <li><a href="<?= Url::base() ?>/tutorials/index.php?title=Tutorial 1: Starting a new project&filename=starting_a_new_project" onclick="return popupWindow('<?= Url::base() ?>/tutorials/index.php?title=Tutorial 1: Starting a new project&filename=starting_a_new_project')">Starting a new project</a></a></li>
                <li><a href="<?= Url::base() ?>/tutorials/index.php?title=Tutorial 2: Modifying an existing project&filename=modifying_an_existing_project" onclick="return popupWindow('<?= Url::base() ?>/tutorials/index.php?title=Tutorial 2: Modifying an existing project&filename=modifying_an_existing_project')">Modifying an existing project</a></li>
                <li><a href="<?= Url::base() ?>/tutorials/index.php?title=Tutorial 3: Data collection log&filename=data_collection_log" onclick="return popupWindow('<?= Url::base() ?>/tutorials/index.php?title=Tutorial 3: Data collection log&filename=data_collection_log')">Data collection log</a></li>
            </ol>
            <b>Data analysis</b>
            <ol start="4">
                <li><a href="<?= Url::base() ?>/tutorials/index.php?title=Tutorial 4: Basic data analysis&filename=basic_results_view" onclick="return popupWindow('<?= Url::base() ?>/tutorials/index.php?title=Tutorial 4: Basic data analysis&filename=basic_results_view')">Basic</a></li>
                <li><a href="<?= Url::base() ?>/tutorials/index.php?title=Tutorial 5: Clustering and data relevancy optimization&filename=clustering_and_data_relevancy_optimization" onclick="return popupWindow('<?= Url::base() ?>/tutorials/index.php?title=Tutorial 5: Clustering and data relevancy optimization&filename=clustering_and_data_relevancy_optimization')">Clustering and data relevancy optimization</a></li>
                <li><a href="<?= Url::base() ?>/tutorials/index.php?title=Tutorial 6: Trendline&filename=trendline" onclick="return popupWindow('<?= Url::base() ?>/tutorials/index.php?title=Tutorial 6: Trendline&filename=trendline')">Trendline</a></li>
            </ol>
        </div>
        <div style="clear:both;"></div>
    </div>

</div>

<div id="container">
<?= $page_content ?>
</div>

<div style="clear:both;"></div>
<div id="footer">ImpactProbe &copy; <?= date("Y") ?> &#124; <a href="https://github.com/alaurenz/ImpactProbe" target="_blank">GitHub repository</a></div>

</body>
</html>