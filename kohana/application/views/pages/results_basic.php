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
        $("#cluster_btn").click(function() {
            $("#cluster_btn").attr('value', 'Clustering...'); 
            $("#cluster_btn").attr('disabled', 'disabled'); // Disable submit button
            window.location.replace("<?= Url::base().'index.php/results/cluster/'.$project_data['project_id'] ?>");
        });
        
        $('#download_btn').click(function() {
            $('#download').val("download");
            $('#results_basic').submit();
        });
    });
    function GoToPage(page_id) {
        $('#results_basic').attr('action', '?page=' + page_id);
        $('#download').val('');
        $('#results_basic').submit();
    }
    function SortBy(sort_by, order) {
        $('#sort_by').val(sort_by);
        $('#order').val(order);
        $('#results_basic').submit();
    }
</script>

<a href="<?= Url::base() ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>

<h3>Results - <?= $project_data['project_title'] ?></h3>

<? if($clustered) { ?>
<a href="<?= Url::base() ?>index.php/results/cluster_view/<?= $project_data['project_id'] ?>" name="cluster_view_btn" id="cluster_view_btn" class="button_noicon button_hover ui-state-default ui-corner-all">View Clusters</a>
<? } else { ?>
<input type="button" name="cluster_btn" id="cluster_btn" class="button_noicon button_hover ui-state-default ui-corner-all" value="Cluster All">
<? } ?>
<a href="<?= Url::base() ?>index.php/results/trendline/<?= $project_data['project_id'] ?>" name="trendline_view_btn" id="trendline_view_btn" class="button_lg button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-image"></span>View Trendline</a>

<form name="results_basic" id="results_basic" method="post" action="">
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

<!--&nbsp;&nbsp;
Order:
<select name="order">
   <option value="desc" <? if($field_data['order'] == 'desc') { echo("selected"); } ?>>most recent</option>
   <option value="asc" <? if($field_data['order'] == 'asc') { echo("selected"); } ?>>oldest</option>
</select>-->
&nbsp;&nbsp;
Show:
<select name="num_results">
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
<input type="hidden" id="sort_by" name="sort_by" value="<?= $field_data['sort_by'] ?>">
<input type="hidden" id="order" name="order" value="<?= $field_data['order'] ?>">
</div>
</div>

<select name="download_mode">
   <option value="summary_csv" <? if($field_data['download_mode'] == 'summary_csv') { echo("selected"); } ?>>Summary table as .csv</option>
   <option value="raw_text" <? if($field_data['download_mode'] == 'raw_text') { echo("selected"); } ?>>Raw text</option>
</select>

<a href="#" id="download_btn" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span>Download</a>
<input type="hidden" id="download" name="download" value="">
<i>NOTE: unless a date range is entered above all <?= $total_results ?> results will be downloaded</i>

</form>

<? if($total_results > 0) { 
echo $page_links; ?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" style="border:1px solid #000;">
    <tr class="table_header">
        <td colspan="7" align="center"><b>Summary</b></td>
    </tr>
    <tr>
        <td colspan="5" align="left">
            Showing <b><?= ($field_data['num_results_shown'] > $total_results) ? $total_results : $field_data['num_results_shown'] ?></b> of <b><?= $total_results ?></b> results<br>
            <b>Published between:</b> <?= $date_published_range ?>
        </td>
        <td colspan="4" align="left">
            <? $total_keywords = 0;
            $keyword_breakdown = "";
            foreach($keywords_phrases as $keyword_id => $keyword_phrase) { 
                $keyword_breakdown .= "<b>$keyword_phrase:</b> $keyword_occurrence_totals[$keyword_id]<br>";
                $total_keywords += $keyword_occurrence_totals[$keyword_id];
            } ?>
            <span style="text-decoration:underline;">Keyword Breakdown (Total: <?= $total_keywords ?>)</span><br>
            <?= $keyword_breakdown ?>
        </td>
    </tr>
    <? if($field_data['num_results'] > 0) { ?>
    <tr class="table_header">
        <td>&nbsp;</td>
        <td align="center">
            <ul id="icons" class="ui-widget" style="width:140px;">
            <? $sort_by_click = 'desc';
            if($field_data['sort_by'] == 'metadata.date_published') { ?>
                <LI><span class="ui-icon
                <? if($field_data['order'] == 'desc') {
                    $sort_by_click = 'asc';
                    echo 'ui-icon-triangle-1-s';
                } else {
                    echo 'ui-icon-triangle-1-n';
                } ?>" onclick="SortBy('metadata.date_published', '<?= $sort_by_click ?>')"></span></LI>
            <? } ?>
            <LI><span style="color:#FFF; font-size:12px;" onclick="SortBy('metadata.date_published', '<?= $sort_by_click ?>')"><b>Date Published</b></span></LI>
            </ul>
        </td>
        <td align="center">
            <ul id="icons" class="ui-widget" style="width:140px;">
            <? $sort_by_click = 'desc';
            if($field_data['sort_by'] == 'metadata.date_retrieved') { ?>
                <LI><span class="ui-icon
                <? if($field_data['order'] == 'desc') {
                    $sort_by_click = 'asc';
                    echo 'ui-icon-triangle-1-s';
                } else {
                    echo 'ui-icon-triangle-1-n';
                } ?>" onclick="SortBy('metadata.date_retrieved', '<?= $sort_by_click ?>')"></span></LI>
            <? } ?>
            <LI><span style="color:#FFF; font-size:12px;" onclick="SortBy('metadata.date_retrieved', '<?= $sort_by_click ?>')"><b>Date Retrieved</b></span></LI>
            </ul>
        </td>
        <td align="center">
            <ul id="icons" class="ui-widget" style="width:110px;">
            <? $sort_by_click = 'desc';
            if($field_data['sort_by'] == 'num_identical') { ?>
                <LI><span class="ui-icon
                <? if($field_data['order'] == 'desc') {
                    $sort_by_click = 'asc';
                    echo 'ui-icon-triangle-1-s';
                } else {
                    echo 'ui-icon-triangle-1-n';
                } ?>" onclick="SortBy('num_identical', '<?= $sort_by_click ?>')"></span></LI>
            <? } ?>
            <LI><span style="color:#FFF; font-size:12px;" onclick="SortBy('num_identical', '<?= $sort_by_click ?>')"><b>&#035; Identical</b></span></LI>
            </ul>
        </td>
        <td align="left">
            <ul id="icons" class="ui-widget" style="width:110px;">
            <? $sort_by_click = 'asc';
            if($field_data['sort_by'] == 'api_sources.api_name') { ?>
                <LI><span class="ui-icon
                <? if($field_data['order'] == 'desc') {
                    echo 'ui-icon-triangle-1-s';
                } else {
                    $sort_by_click = 'desc';
                    echo 'ui-icon-triangle-1-n';
                } ?>" onclick="SortBy('api_sources.api_name', '<?= $sort_by_click ?>')"></span></LI>
            <? } ?>
            <LI><span style="color:#FFF; font-size:12px;" onclick="SortBy('api_sources.api_name', '<?= $sort_by_click ?>')"><b>Source</b></span></LI>
            </ul>
        </td>
        <td align="left"><span style="color:#FFF;"><b>Keyword Metadata</b></span></td>
        <td align="center">&nbsp;</td>
    </tr>
    <?  $i = $offset + 1;
        foreach($results as $result) { ?>
        <tr class="<?= ($i % 2 == 0) ? 'bg_grey' : 'bg_white' ; ?>">
            <td align="left"><?= $i ?></td>
            <td align="center"><? if($result['date_published'] > 0) echo date($date_format, $result['date_published']); ?></td>
            <td align="center"><?= date($date_format, $result['date_retrieved']) ?></td>
            <td align="center"><?= '' ?></td>
            <td align="left"><?= $result['api_name'] ?></td>
            <td align="left">
            <? foreach($result['keywords_phrases'] as $keyword_phrase) {
                echo $keyword_phrase['keyword'].": ".$keyword_phrase['num_occurrences']."<br>";
            } ?>
            </td>
            <td align="center">
                <ul id="icons" class="ui-widget ui-helper-clearfix">
                    <li class="button_hover ui-state-default ui-corner-all" title="Open URL"><a href="<?= $result['url'] ?>" target="_blank"><span class="ui-icon ui-icon-extlink"></span></a></li>
                    <li class="button_hover ui-state-default ui-corner-all" title="View text"><?= '<a href="'.Url::base().'index.php/results/view_document/'.$project_data['project_id'].'?meta_id='.$result['meta_id'].'" rel="lyteframe" title="Viewing raw text" rev="width: 500px; height: 400px; scrolling: yes;">' ?><span class="ui-icon ui-icon-comment"></span></a></li>
                </ul>
            </td>
        </tr>
        <? $i++;
        } 
    } ?>
</table>
<? 
echo $page_links;

} else {
    echo "<p>No results to display</p>";
} ?>

<p><a href="<?= Url::base() ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a></p>