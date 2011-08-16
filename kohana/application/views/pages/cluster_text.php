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
<title>Clusters - <?= Kohana::config('myconf.site_name') ?></title>
<meta http-equiv="Content-Type" content="text/html; utf-8">

<script src="<?= Kohana::config('myconf.url.js'); ?>jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="<?= Kohana::config('myconf.url.js'); ?>jquery.selectboxes.min.js" type="text/javascript"></script>
<script src="<?= Kohana::config('myconf.url.js'); ?>jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
<script src="<?= Kohana::config('myconf.url.js'); ?>jquery-ui-global.js" type="text/javascript"></script>

<link rel="stylesheet" href="<?= Kohana::config('myconf.url.css'); ?>main.css" type="text/css">
<link rel="stylesheet" href="<?= Kohana::config('myconf.url.css'); ?>dark-hive/jquery-ui-1.8.14.custom.css" type="text/css">
<style type="text/css">
#cell_container {
    position:relative; float:left;
    width:512px;
    padding-bottom:8px;
    padding-left:8px;
}
#cell_container .cell_unit {
    position:relative; float:left;
    width:498px;
    padding:6px;
}
#cell_container .left {
    position:relative; float:left;
    width:36px; padding-right:4px; 
    vertical-align:top; text-align:right; font-size:14px;
}
#cell_container .right {
    position:relative; float:left;
    width:460px;
}
#accordion {
    font-size:11px; 
}
#accordion .inner {
    padding:0;
}
.white_text a {
    color:#000000;
}
</style>
</head>

<script type="text/javascript">
    $(document).ready(function(){
        $('#accordion').accordion({ 
            header: "h3",
            collapsible: true,
            autoHeight: false,
            clearStyle: true
        });
        $('#accordion').accordion("activate", false);
    });
</script>

<div id="cell_container">
<? $num_clusters = count($cluster_data); 
    if(!$singleton_display) { ?>
        <form name="cluster_text" id="cluster_text" method="post" action="">
        <? if($errors) { ?>
        <div class="ui-widget">
            <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
            <p><? foreach ($errors as $error_text) { echo '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'.$error_text.'<br>'; } ?></p></div>
        </div>
        <? } ?>

        <b>Show: </b>
        <select name="num_results">
        <option value="10" <? if($field_data['num_results'] == 10) { echo("selected"); } ?>>10</option>
        <option value="25" <? if($field_data['num_results'] == 25) { echo("selected"); } ?>>25</option>
        <option value="50" <? if($field_data['num_results'] == 50) { echo("selected"); } ?>>50</option>
        <option value="100" <? if($field_data['num_results'] == 100) { echo("selected"); } ?>>100</option>
        <option value="all" <? if($field_data['num_results'] == 'all') { echo("selected"); } ?>>all</option>
        </select>
        <select name="score_order">
        <option value="desc" <? if($field_data['score_order'] == 'desc') { echo("selected"); } ?>>most representative</option>
        <option value="asc" <? if($field_data['score_order'] == 'asc') { echo("selected"); } ?>>least representative</option>
        </select>
        <input type="submit" name="Submit" value="View">
        </form><? 
    } ?>
    
    <div id="accordion">
 <? if($num_clusters > 0) {
        $i = 1;
        foreach($cluster_data as $cluster) { 
            $num_identical = 1;
            if(!$singleton_display)
                $num_identical = count($cluster['identical_clusters']);
            
            if($cluster['marked'])
                $bg_color = 'bg_red';
            else
                $bg_color = ($i % 2 == 0) ? 'bg_white' : 'bg_grey';
            
            if($num_identical == 1) { 
                /* OLD: <div class="cell_unit <?= $bg_color ?>"><?= $cluster['text'] ?></div> */ ?>
                <table width="100%" border="0" cellspacing="0" cellpadding="5" class="<?= $bg_color ?>">
                <tr>
                    <td><?= $cluster['text'] ?></td>
                </tr>
                </table>
         <? } else { 
                $header_bg_html = ($bg_color == 'bg_red') ? ' style="background:#FF0000;" class="white_text"' : ''; ?>
                <div>
                    <h3<?= $header_bg_html?>><a href="#"<? if($bg_color == 'bg_red') echo ' style="color:#000000;"'; ?>><?= '<b>'.$num_identical.' x </b> '.array_pop($cluster['identical_clusters']) ?></a></h3>
                    <div class="inner">
                    <? $j = 1; 
                    foreach($cluster['identical_clusters'] as $cluster_identical) { 
                        if($cluster['marked'])
                            $bg_color_inner = 'bg_red';
                        else
                            $bg_color_inner = ($j % 2 == 0) ? 'bg_light_blue' : 'bg_white';
                        echo '<div class="cell_unit '.$bg_color_inner.'">'.$cluster_identical.'</div>';
                        $j++;
                    } ?>
                    </div>
                </div><?
            } 
            $i++;
        }
    } else {
        echo 'No clusters to display.';
    } ?>
    </div>
    </div>
</div>
</body>
</html>