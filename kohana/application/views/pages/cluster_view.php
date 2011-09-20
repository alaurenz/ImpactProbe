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
        $('#recluster_form').submit(function() {
            // Validate threshold value
            if(!isNumeric($('#cluster_threshold').val())) {
                alert("Threshold is invalid.");
                return false;
            }
            var submit_btn = "#submit_btn";
            if($('#cluster_time').is(':checked')) {
                submit_btn = "#submit_btn_time";
            }
            $(submit_btn).attr('value', 'Clustering...'); 
            $(submit_btn).attr('disabled', 'disabled'); // Disable submit button
        });
        
        $('#cluster_time').click(function() {
            if($('#cluster_time').is(':checked')) {
                $('#cluster_time_form').css('display', '');
            } else {
                $('#cluster_time_form').css('display', 'none');
            }
        });
        
        $('#slider').slider({
            value: <?= $slider['max'] ?>,
            min: <?= $slider['min'] ?>,
            max: <?= $slider['max'] ?>,
            step: <?= $slider['step'] ?>,
            slide: function( event, ui ) {
                for(i=0; i<<?= $slider['increments'] ?>; i++) {
                    var range_value = <?= $slider['min'] ?> + i*<?= $slider['step'] ?>;
                    $('#scatter'+range_value).hide();
                }
                $('#scatterall').hide();
                
                if(ui.value == <?= $slider['max'] ?>) {
                    $('#amount').val("all");
                    $('#scatterall').show();
                } else {
                    $('#amount').val(ui.value);
                    $('#scatter'+ui.value).show();
                }
            }
        });
        $('#amount').val("all");
        //$( "#amount" ).val( "$" + $( "#slider" ).slider( "value" ) );
        
        $('#negative_keywords_test').click(function() {
            $('#negative_keywords_form').attr('action', '?cluster_order=<?= $cluster_params['order'] ?>&cluster_threshold=<?= $cluster_params['threshold'] ?>');
            $('#negative_keywords_form').submit();
        });
        $('#negative_keywords_apply').click(function() {
            if(confirm("Are you sure you want to apply the negative keyword(s)?\nWARNING: all data marked for deletion will be permanently deleted.")) {
                $('#negative_keywords_action').val('apply');
                $('#negative_keywords_form').submit();
            }
        });
        $('#negative_keywords_cancel').click(function() {
            $('#recluster_form').submit();
        });
        <? if($field_data['negative_keywords_input'] != '' AND $field_data['negative_keywords_input'] != 'Enter negative keyword(s)...') { ?>
        $('#hide_unaffected').click(function() {
            $('#negative_keywords_form').submit();
        });
        <? } ?>
        
        // BAD/REDUNDANT CODE
        $('#negative_keywords_input').focus(function () {
            if($(this).val() == "Enter negative keyword(s)...") {
                $(this).attr('value', '');
            } 
        });
        $('#negative_keywords_input').focusout(function () {
            if(!$(this).val()) {
                $(this).attr('value', 'Enter negative keyword(s)...');
            } 
        });
        // END BAD/REDUNDANT CODE
    });
    
    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    function startLyteframe(title, url) { 
        var anchor = this.document.createElement('a'); 
        anchor.setAttribute('rev', 'width: 545px; height: 490px; scrolling: auto;'); 
        anchor.setAttribute('title', title); 
        anchor.setAttribute('href', url); 
        anchor.setAttribute('rel', 'lyteframe');
        myLytebox.start(anchor, false, true);
    }
</script>

<style> 
#toggle_box { width: 324px; text-align:center; height: auto; padding: 0.4em; position: relative; }
#toggle_box h3 { margin: 0; padding: 0.4em; text-align: center; }
</style> 

<a href="<?= Url::base(TRUE).'results/view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>

<div style="position:relative; width:100%;">
<h3>Clustering - <?= $project_data['project_title'] ?></h3>
<div style="position:relative; float:left; width:450px; height:auto;">
    
    <form name="recluster_form" id="recluster_form" method="get" action="<?= Url::base().'index.php/results/cluster/'.$project_data['project_id'] ?>">
    <? if($total_results != $cluster_params['num_docs'] AND $cluster_params['threshold'] == $cluster_params['default_threshold']) { ?>
    <b>Last clustered:</b> <?= date(Kohana::config('myconf.date_format'), $cluster_params['date_clustered']) ?> (<b><?= $cluster_params['num_docs'] ?></b> of <?= $total_results ?> total documents)
    <br><span style="color:#0000FF;"><b>NOTE:</b> data has been collected or deleted since last clustering was performed. You must recluster to see the new data on the plot below.</span><br><? } ?>
    <b>Threshold:</b>
    <input name="cluster_threshold" type="text" id="cluster_threshold" value="<?= $cluster_params['threshold'] ?> " size="3" maxlength="8">
    <select name="cluster_order">
    <option value="arbitrarily"<? if($cluster_params['order'] == 'arbitrarily') { echo " selected"; } ?>>scatter clusters</option>
    <option value="cluster_size"<? if($cluster_params['order'] == 'cluster_size') { echo " selected"; } ?>>order by cluster size</option>
    </select>
    <input type="submit" id="submit_btn" name="submit_btn" value="Recluster">
    <br>
    <label for="cluster_time"><input name="cluster_time" id="cluster_time" type="checkbox" value="1"> Show how clusters change over time</label>
    <div id="cluster_time_form" style="position:relative; display:none;">
    From <input type="text" class="datepicker" id="date_from" name="date_from" size="11">
    to
    <input type="text" class="datepicker" id="date_to" name="date_to" size="11">
    <input type="submit" id="submit_btn_time" name="submit_btn_time" value="Cluster">
    </div>
    </form>

    <? if($singleton_clusters > 0) { ?>
    <p><a href="javascript:startLyteframe('Singleton clusters (<?= $singleton_clusters ?> total)', '<?= Url::base().'index.php/results/singleton_clusters/'.$project_data['project_id'] ?>')" class="button_noicon button_hover ui-state-default ui-corner-all"<? if($singleton_cluster_marked) echo ' style="background:#FF0000;"'; ?>>View singleton clusters (<?= $singleton_clusters ?>)</a></p>
    <? } ?>
</div>

<div style="position:relative; float:left; width:310px; height:auto;">
    <form name="negative_keywords_form" id="negative_keywords_form" method="post" action="<?= Url::base().'index.php/results/cluster_view/'.$project_data['project_id'] ?>">
    
    <div id="toggle_box" class="ui-widget-content ui-corner-all"> 
    <h3 class="ui-widget-header ui-corner-all">Relevancy optimization tool</h3> 
    <div style="padding-top:5px;">
        <input class="ui-state-default ui-corner-all" name="negative_keywords_input" type="text" id="negative_keywords_input" value="<?= ($field_data['negative_keywords_input']) ? $field_data['negative_keywords_input'] : 'Enter negative keyword(s)...'; ?>"  style="width:180px; font-size:12px;"<? if($field_data['negative_keywords_input'] != '' AND $field_data['negative_keywords_input'] != 'Enter negative keyword(s)...') echo ' readonly="readonly"'; ?>>
        <? if($field_data['negative_keywords_input'] != '' AND $field_data['negative_keywords_input'] != 'Enter negative keyword(s)...') { ?>
            <a href="#" id="negative_keywords_apply" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-check"></span>Apply</a>
            <a href="#" id="negative_keywords_cancel" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-closethick"></span>Cancel</a>
        <? } else { ?>
            <a href="#" id="negative_keywords_test" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowreturn-1-e"></span>Test</a>
        <? } ?>
        <br><span style="font-size:11px;">Separate negative keyword combinations with commas</span>
        <br><label for="hide_unaffected"><input name="hide_unaffected" id="hide_unaffected" type="checkbox" value="1"<? if(array_key_exists('hide_unaffected', $field_data)) echo ' checked'; ?>> Hide clusters that are not affected</label>
    </div>
    
    <? if($field_data['negative_keywords_input'] != '' AND $field_data['negative_keywords_input'] != 'Enter negative keyword(s)...') { ?>
    <div style="width:305px; padding-left:10px; padding-top:5px;">
        <img src="<?= Kohana::config('myconf.url.images'); ?>negative_keywords_gradient.gif" width="305" height="54">
    </div>
    <? } ?>
    </div>
    <input type="hidden" name="negative_keywords_action" id="negative_keywords_action" value="">
    </form>
</div>

<div style="position:relative; padding-top:5px; float:left; width:800px;">
    <? if($slider['step'] != 0 AND $slider['max'] > ($slider['increments']*$slider['step'])) { ?>
    <div style="position:relative; width:760px; height:36px;"> 
        <div id="slider_text"> 
        <label for="amount">Hide clusters with more than </label> 
        <input type="text" id="amount" style="width:23px; border:0; color:#f6931f; font-weight:bold;" /> documents
        </div> 
        <div id="slider"></div> 
    </div>
    <? } ?>
    <div style="position:relative; float:left; width:750px; height:380px;">
        <? for($i = 0; $i < $slider['increments']; $i++) { 
            $range_value = $slider['min'] + $i*$slider['step'];
            echo '<div id="scatter'.$range_value.'" style="position:absolute; left:0; top:0; display:none;">'.$chart_html[$i].'</div>'; 
        }
        echo '<div id="scatterall" style="position:absolute; left:0; top:0;">'.$chart_html[$slider['increments']].'</div>'; 
        ?>
    </div>

    <div style="position:relative; float:left; width:760px; height:35px;">
        <a href="<?= Url::base(TRUE).'results/view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>
    </div>
    
</div>

</div>

