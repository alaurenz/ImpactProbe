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
<script type='text/javascript' src='http://www.google.com/jsapi'></script>
<script type='text/javascript'>
    google.load('visualization', '1', {'packages':['annotatedtimeline']});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = new google.visualization.DataTable();
        <?= $chart_data_js ?>
        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
        chart.draw(data, {displayAnnotations: true, 'dateFormat': '<?= $date_format_chart ?>'});
    }
</script>

<a href="<?= Url::base(TRUE).'results/view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>

<h3>Trendline - <?= $project_data['project_title'] ?></h3>
<b>Showing results published</b>: <?= $date_range ?>

<form name="trendline_form" id="trendline_form" method="post" action="">

<? if($errors) { ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><? foreach ($errors as $error_text) { echo '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'.$error_text.'<br>'; } ?></p></div>
</div>
<? } ?>

<div class="ui-widget">
<div class="ui-state-default ui-corner-all" style="position:relative; z-index: 2; margin-top: 10px; padding: 0 .7em; padding:3px;"> 
&nbsp;
Date:
<input type="text" class="datepicker" id="date_from" name="date_from" size="11" value="<?= $field_data['date_from'] ?>">
&#045;
<input type="text" class="datepicker" id="date_to" name="date_to" size="11" value="<?= $field_data['date_to'] ?>">
&nbsp;&nbsp;
<select name="display_mode">
  <option value="consensus" <? if($field_data['display_mode'] == "consensus") { echo("selected"); } ?>>consensus</option>
  <option value="by_keyword" <? if($field_data['display_mode'] == "by_keyword") { echo("selected"); } ?>>by keyword</option>
</select>
<input type="submit" name="View" value="View">
<input type="reset" name="Reset" value="Reset" onClick="window.location.reload()">
<input type="submit" name="Download" value="Download as .csv">

</div>
</div>

<b>NOTE:</b> the &quot;consensus&quot; value represents the total number of entries found which contain any of the search keywords. So when viewing the results broken down by individual keywords the total of those values does not necessarily represent the consensus value (because a single entry may contain multiple keywords).
</form>

<div style="position:relative; z-index: 10;">
<div id="chart_div" style="position:relative; z-index: 1; <?= $chart_dimensions ?>"></div>
</div>

<p><a href="<?= Url::base(TRUE).'results/view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a></p>