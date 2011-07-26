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
            if(!isNumeric($("#cluster_threshold").val())) {
                alert("Threshold is invalid.");
                return false;
            }
            $("#submit_btn").attr('value', 'Clustering...'); 
            $("#submit_btn").attr('disabled', 'disabled'); // Disable submit button
        });
        
        $("#negative_keywords_input").focus(function () {
            if($(this).val() == "Enter negative keywords...") {
                $(this).attr('value', '');
            } 
        });
        $("#negative_keywords_input").focusout(function () {
            if(!$(this).val()) {
                $(this).attr('value', 'Enter negative keywords...');
            } 
        });
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
#toggle_box { width: 260px; height: auto; padding: 0.4em; position: relative; }
#toggle_box h3 { margin: 0; padding: 0.4em; text-align: center; }
</style> 

<a href="<?= Url::base(TRUE).'results/view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>

<div style="position:relative; width:100%;">
<h3>Clustering - <?= $project_data['project_title'] ?></h3>
<div style="position:relative; float:left; width:450px; height:auto;">
    
    <form name="recluster_form" id="recluster_form" method="post" action="<?= Url::base().'index.php/results/cluster/'.$project_data['project_id'] ?>">
    <p><b>Last clustered:</b> <?= $cluster_log['date_clustered'] ?> (<?= $cluster_log['num_docs'] ?> documents)<br>
    <b>Threshold:</b>
    <input name="cluster_threshold" type="text" id="cluster_threshold" value="<?= $cluster_log['threshold'] ?> " size="3" maxlength="8">
    <select name="cluster_order">
    <option value="arbitrarily"<? if($cluster_log['order'] == 'arbitrarily') { echo " selected"; } ?>>scatter clusters</option>
    <option value="cluster_size"<? if($cluster_log['order'] == 'cluster_size') { echo " selected"; } ?>>order by cluster size</option>
    </select>
    <input type="submit" id="submit_btn" name="submit_btn" value="Recluster"></p>
    </form>

    <? if($singleton_clusters > 0) { ?>
    <p><a href="javascript:startLyteframe('Singleton clusters (<?= $singleton_clusters ?> total)', '<?= Url::base().'index.php/results/singleton_clusters/'.$project_data['project_id'] ?>')" class="button_noicon button_hover ui-state-default ui-corner-all">View singleton clusters (<?= $singleton_clusters ?>)</a></p>
    <? } ?>
</div>

<div style="position:relative; float:left; width:300px; height:auto;">
    <form name="test_negative_keywords_form" id="test_negative_keywords_form" method="post" action="<?= Url::base().'index.php/results/cluster_view/'.$project_data['project_id'] ?>">
    
    <div id="toggle_box" class="ui-widget-content ui-corner-all"> 
    <h3 class="ui-widget-header ui-corner-all">Relevancy optimization tool</h3> 
    <div style="padding:5px;">
        <input class="ui-state-default ui-corner-all" name="negative_keywords_input" type="text" id="negative_keywords_input" value='<?= ($field_data['negative_keywords_input']) ? $field_data['negative_keywords_input'] : 'Enter negative keywords...'; ?>' size="26">
        <a href="#" onclick="document.test_negative_keywords_form.submit()" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowreturn-1-e"></span>Test</a>
    </div>
    <? if($field_data['negative_keywords_input'] != '' AND $field_data['negative_keywords_input'] != 'Enter negative keywords...') { ?>
    <div style="padding:2px;">
        <img src="<?= Kohana::config('myconf.url.images'); ?>cluster_color_gradient_txt.png" width="256" height="53">
    </div>
    <? } ?>
    <!--<br>&nbsp;<span style="font-size:11px;">Separate keyword combinations with commas</span>
    <input type="submit" id="apply_btn" name="apply_btn" value="Apply">-->  
    </div> 
    </form>
</div>


<div style="position:relative; float:left; width:800px;">
    <?= $chart_html ?>
    
    <p><a href="<?= Url::base(TRUE).'results/view/'.$project_data['project_id'] ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a></p>
</div>

</div>

