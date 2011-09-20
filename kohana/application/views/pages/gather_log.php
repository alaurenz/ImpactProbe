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
<script type="text/javascript">
    $(document).ready(function(){
        $('#accordion').accordion({ 
            header: "h3",
            autoHeight: false,
            clearStyle: true,
            collapsible: true
        });
        $('#accordion').accordion("activate", false);
    });
    function GoToPage(page_id) {
        $('#gather_log').attr('action', '?page=' + page_id);
        $('#gather_log').submit();
    }
</script>

<a href="<?= Url::base() ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>

<h3>Gather log - <?= $project_data['project_title'] ?></h3>

<form name="gather_log" id="gather_log" method="post" action="">
<? if($errors) { ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><? foreach ($errors as $error_text) { echo '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'.$error_text.'<br>'; } ?></p></div>
</div>
<? } ?>

<div class="ui-widget">
<div class="ui-state-default ui-corner-all" style="margin-top: 10px; padding: 0 .7em; padding:3px;"> 

<div style="float:left; margin-left:4px; margin-top:4px; width:22px; height:22px; background:url(<?= Kohana::config('myconf.url.images'); ?>search-icon-sm.png);"></div>
&nbsp;
Date:
<input type="text" class="datepicker" id="date_from" name="date_from" size="11" value="<?= $field_data['date_from'] ?>">
&#045;
<input type="text" class="datepicker" id="date_to" name="date_to" size="11" value="<?= $field_data['date_to'] ?>">

&nbsp;&nbsp;
Order:
<select name="order">
   <option value="desc" <? if($field_data['order'] == 'desc') { echo("selected"); } ?>>most recent</option>
   <option value="asc" <? if($field_data['order'] == 'asc') { echo("selected"); } ?>>oldest</option>
</select>
&nbsp;&nbsp;
Show:
<select name="num_results">
  <option value="1" <? if($field_data['num_results'] == 1) { echo("selected"); } ?>>5</option>
  <option value="10" <? if($field_data['num_results'] == 10) { echo("selected"); } ?>>10</option>
  <option value="25" <? if($field_data['num_results'] == 25) { echo("selected"); } ?>>25</option>
  <option value="50" <? if($field_data['num_results'] == 50) { echo("selected"); } ?>>50</option>
  <option value="100" <? if($field_data['num_results'] == 100) { echo("selected"); } ?>>100</option>
  <option value="250" <? if($field_data['num_results'] == 250) { echo("selected"); } ?>>250</option>
  <option value="500" <? if($field_data['num_results'] == 500) { echo("selected"); } ?>>500</option>
  <!--<option value="all" <? if($field_data['num_results'] == 'all') { echo("selected"); } ?>>all</option>-->
</select>
&nbsp;&nbsp;
<input type="submit" name="Submit" value="View">
<input type="reset" name="Reset" value="Reset" onClick="window.location.reload()">

</div>
</div>

</form>

<p>
<? if(count($results) > 0) { 
    echo $page_links; ?>
    
    <div id="accordion">
        <? foreach($results as $result) { ?>
            <div>
                <h3><a href="#"><?= date(Kohana::config('myconf.date_format'), $result['date']) ?> (<?= count($result['queries']) ?> queries)</a></h3>
                <div style="font-size:11px; padding:0;">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td align="left">&nbsp;</td>
                        <td align="left"><b>Query</b></td>
                        <td align="center"><b>&#035; of results</b></td>
                    </tr>
                    <? $i = 1; 
                    foreach($result['queries'] as $query) { ?>
                    <tr class="<? 
                        if($query['error'])
                            echo 'bg_red';
                        else
                            echo ($i % 2 == 0) ? 'bg_grey' : 'bg_white' ; ?>">
                        <td align="left"><?= $i ?></td>
                        <td align="left"><?= $query['search_query'] ?></td>
                        <td align="center"><?= $query['results_gathered'] ?></td>
                    </tr>
                    <? $i++; 
                    } ?>
                </table>
                </div>
            </div>
        <? $i++;
        } ?>
    </div>
<?  echo $page_links; 
} ?>
</p>
<br>
<a href="<?= Url::base() ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>