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
            $('#submit_btn_time').attr('value', 'Clustering...'); 
            $('#submit_btn_time').attr('disabled', 'disabled'); // Disable submit button
        });
        
        $('#slider').slider({
            value: <?= $slider['min'] ?>,
            min: <?= $slider['min'] ?>,
            max: <?= $slider['max'] ?>,
            step: <?= $slider['step'] ?>,
            slide: function( event, ui ) {
                setSliderPos(ui.value);
            }
        });
        $('#amount').val(end_dates[<?= $slider['min'] ?>] + " - " + end_dates[<?= $slider['min'] ?>]);
    });
    
    var end_dates = new Array();
    <? for($i = $slider['min']; $i < $slider['increments']; $i++) {
        echo "end_dates[$i]='".$slider_end_dates[$i]."'; ";
    } ?>
    function setSliderPos(time_id) {
        for(i = <?= $slider['min'] ?>; i < <?= $slider['increments'] ?>; i++) {
            $('#scatter'+i).hide();
        }
        $('#amount').val(end_dates[<?= $slider['min'] ?>] + " - " + end_dates[time_id]);
        $('#scatter' + time_id).show();
    }
    
    function animateSlider(time_id) {
        if(time_id < <?= $slider['increments'] ?>) {
            setSliderPos(time_id);
            $('#slider').slider('value', time_id);
            var code = "animateSlider("+(time_id+1)+")";
            window.setTimeout(code, 1000);
        } else {
            setSliderPos(<?= $slider['min'] ?>);
            $('#slider').slider('value', <?= $slider['min'] ?>);
        }
    }
    
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

<a href="<?= Url::base(TRUE).'results/cluster_view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>

<div style="position:relative; width:100%;">
<h3>Clusters over time - <?= $project_data['project_title'] ?></h3>
<div style="position:relative; float:left; width:450px; height:auto;">
    
    <form name="recluster_form" id="recluster_form" method="get" action="<?= Url::base().'index.php/results/cluster_time/'.$project_data['project_id'] ?>">
    <? /*if($total_results != $cluster_log['num_docs']) { ?>
    <b>Last clustered:</b> <?= date(Kohana::config('myconf.date_format'), $cluster_log['date_clustered']) ?> (<b><?= $cluster_log['num_docs'] ?></b> of <?= $total_results ?> total documents)
    <br><span style="color:#0000FF;"><b>NOTE:</b> data has been collected or deleted since last clustering was performed. You must recluster to see the new data on the plot below.</span><br><? } */ ?>
    <b>Threshold:</b>&nbsp;
    <input name="cluster_threshold" type="text" id="cluster_threshold" value="<?= $field_data['cluster_threshold'] ?> " size="3" maxlength="8">
    <select name="cluster_order">
    <option value="arbitrarily"<? if($field_data['cluster_order'] == 'arbitrarily') { echo " selected"; } ?>>scatter clusters</option>
    <option value="cluster_size"<? if($field_data['cluster_order'] == 'cluster_size') { echo " selected"; } ?>>order by cluster size</option>
    </select>
    <br>
    Cluster from
    <input type="text" class="datepicker" id="date_from" name="date_from" size="11" value="<?=  $field_data['date_from'] ?>">
    to
    <input type="text" class="datepicker" id="date_to" name="date_to" size="11" value="<?= $field_data['date_to'] ?>">
    <input type="submit" id="submit_btn_time" name="submit_btn_time" value="Recluster">
    <input name="cluster_time" id="cluster_time" type="hidden" value="1">
    </form>
</div>

<div style="position:relative; padding-top:5px; float:left; width:800px;">
    <div style="position:relative; width:760px; height:42px;"> 
        <div id="slider_text"> 
        <label for="amount">Showing clusters for data published </label> 
        <input type="text" id="amount" style="width:250px; border:0; color:#f6931f; font-weight:bold;" />
        </div> 
        <div id="slider"></div> 
    </div>
    <div style="position:relative; float:left; width:750px; height:18px;">
        <a href="#" onclick="animateSlider(<?= $slider['min'] ?>)" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-play"></span>Animate</a>
    </div>
    
    <div style="position:relative; float:left; width:750px; height:380px;">
        <div id="scatter<?= $slider['min'] ?>" style="position:absolute; left:0; top:0;"><?= $chart_html[$slider['min']] ?></div> 
        <? for($i = ($slider['min']+1); $i < $slider['increments']; $i++) {
            echo '<div id="scatter'.$i.'" style="position:absolute; left:0; top:0; display:none;">'.$chart_html[$i].'</div>'; 
        }
        ?>
    </div>

    <div style="position:relative; float:left; width:760px; height:35px;">
        <a href="<?= Url::base(TRUE).'results/view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>
    </div>
    
</div>

</div>