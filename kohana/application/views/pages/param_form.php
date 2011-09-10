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
        
        $('#keyword_tabs, #rss_feed_tabs').tabs();
        
        $('#add_preloaded_rss_btn').click(function() {
            // move selected keywords from deactivated to active combobox
            $('#preloaded_rss_feeds option:selected').each(function () {
                var rss_feed_url = $(this).val();
                $('#rss_feeds').addOption(rss_feed_url, $(this).text());
            });
        });
        
        jQuery.fn.getCheckboxVal = function(){
            var vals = [];
            var i = 0;
            this.each(function(){
                vals[i++] = jQuery(this).val();
            });
            return vals;
        }
        
        $('#add_keyword_btn').click(function() {
            var new_keyword = $('#add_keyword_text').val().replace(/["]/g,'').trim(); // Remove quotes(") and trim whitespace
            if(new_keyword) {
                if($('#exact_phrase').is(':checked')) {
                    new_keyword = '"' + new_keyword + '"';
                    $('#exact_phrase').attr('checked', false);
                }
                $('#keywords_phrases').addOption(new_keyword, new_keyword); // add new keyword to combo box
                $('#add_keyword_text').val(""); // clear 'add keyword' textfield
            }
        });
        
        $('#remove_keyword_btn').click(function() {
            // remove selected keywords from combobox
            $('#keywords_phrases option:selected').each(function () {
                $('#keywords_phrases').removeOption($(this).val());
            });
        });
        
        $('#add_neg_keyword_btn').click(function() {
            var new_keyword = $('#add_neg_keyword_text').val().replace(/["]/g,'').trim(); // Remove quotes(") and trim whitespace
            if(new_keyword) {
                if($('#neg_exact_phrase').is(':checked')) {
                    new_keyword = '"' + new_keyword + '"';
                    $('#neg_exact_phrase').attr('checked', false);
                }
                $('#negative_keywords').addOption(new_keyword, new_keyword); // add new keyword to combo box
                $('#add_neg_keyword_text').val(""); // clear 'add keyword' textfield
            }
        });
        
        $('#remove_neg_keyword_btn').click(function() {
            // remove selected keywords from combobox
            $('#negative_keywords option:selected').each(function () {
                $('#negative_keywords').removeOption($(this).val());
            });
        });
        
        <? if($mode == "Modify") { ?>
        $('#deactivate_keyword_btn').click(function() {
            // move selected keywords from active to deactivated combobox
            $('#keywords_phrases option:selected').each(function () {
                var keyword_phrase = $(this).val();
                $('#keywords_phrases').removeOption(keyword_phrase);
                if(isInteger(keyword_phrase)) {
                    // Only move to deactivated if this keyword was added previously
                    $('#deactivated_keywords_phrases').addOption(keyword_phrase, $(this).text());
                }
            });
        });
        $('#reactivate_keyword_btn').click(function() {
            // move selected keywords from deactivated to active combobox
            $('#deactivated_keywords_phrases option:selected').each(function () {
                var keyword_phrase = $(this).val();
                $('#deactivated_keywords_phrases').removeOption(keyword_phrase);
                $('#keywords_phrases').addOption(keyword_phrase, $(this).text());
            });
        });
        <? } ?>
        
        $('#api_rss_feed').click(function() {
            if($('#api_rss_feed').is(':checked')) {
                $('#rss_feed_form').css('display', '');
            } else {
                $('#rss_feed_form').css('display', 'none');
            }
        });
        
        $('#add_rss_feed_btn').click(function() {
            var new_rss_feed = $('#add_rss_feed_text').val().trim();
            if(new_rss_feed) {
                if($('#searchable').is(':checked')) {
                    new_rss_feed = 'Searchable: ' + new_rss_feed;
                    $('#searchable').attr('checked', false);
                }
                $('#rss_feeds').addOption(new_rss_feed, new_rss_feed); // add new RSS feed to combo box
                $('#add_rss_feed_text').val(""); // clear 'add rss feed' textfield
            }
        });
        
        $('#remove_rss_feed_btn').click(function() {
            // remove selected rss_feeds from combobox
            $('#rss_feeds option:selected').each(function () {
                $('#rss_feeds').removeOption($(this).val());
            });
        });
        
        <? if($mode == "Modify") { ?>
        $('#deactivate_rss_feed_btn, #deactivate_rss_feed_btn2').click(function() {
            // move selected RSS feeds from active to deactivated combobox
            $('#rss_feeds option:selected').each(function () {
                var rss_feed = $(this).val();
                $('#rss_feeds').removeOption(rss_feed);
                if(isInteger(rss_feed)) {
                    // Only move to deactivated if this RSS feed was added previously
                    $('#deactivated_rss_feeds').addOption(rss_feed, $(this).text());
                }
            });
        });
        $('#reactivate_rss_feed_btn').click(function() {
            // move selected keywords from deactivated to active combobox
            $('#deactivated_rss_feeds option:selected').each(function () {
                var rss_feed = $(this).val();
                $('#deactivated_rss_feeds').removeOption(rss_feed);
                $('#rss_feeds').addOption(rss_feed, $(this).text());
            });
        });
        <? } ?>
        
        $('#params_form').submit(function() {
            // Select all keywords & RSS feeds on form submit (so they are added to $field_data array)
            $('#keywords_phrases *').attr("selected","selected"); 
            $('#rss_feeds *').attr("selected","selected");
            if($('#gather_now').is(':checked')) {
                $('#submit_btn').attr('value', 'Loading...this may take a while'); 
            } else {
                $('#submit_btn').attr('value', 'Loading...'); 
            }
            $('#submit_btn').attr('disabled', 'disabled'); // Disable submit button
            <? if($mode == "Modify") { ?>
                $('#deactivated_keywords_phrases *').attr("selected","selected");
                $('#deactivated_rss_feeds *').attr("selected","selected");
                $('#negative_keywords *').attr("selected","selected");
            <? } ?>
        });
        
        // REALLY BAD/REDUNDANT...
        $('#add_rss_feed_text').focus(function () {
            if($(this).val() == "Enter feed URL...") {
                $(this).attr('value', '');
            } 
        });
        $('#add_rss_feed_text').focusout(function () {
            if(!$(this).val()) {
                $(this).attr('value', 'Enter feed URL...');
            } 
        });
        $('#add_keyword_text').focus(function () {
            if($(this).val() == "Enter keyword(s)...") {
                $(this).attr('value', '');
            } 
        });
        $('#add_keyword_text').focusout(function () {
            if(!$(this).val()) {
                $(this).attr('value', 'Enter keyword(s)...');
            } 
        });
        $('#add_neg_keyword_text').focus(function () {
            if($(this).val() == "Enter negative keyword(s)...") {
                $(this).attr('value', '');
            } 
        });
        $('#add_neg_keyword_text').focusout(function () {
            if(!$(this).val()) {
                $(this).attr('value', 'Enter negative keyword(s)...');
            } 
        });
        // END REALLY BAD/REDUNDANT...
    });

    function isInteger(s) {
        return (s.toString().search(/^-?[0-9]+$/) == 0);
    }
</script>

<a href="<?= Url::base() ?>" class="button_sm button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-w"></span>Back</a>

<h3><?= $mode ?> Monitoring Project</h3>

<form name="params_form" id="params_form" action="<?= Url::base(TRUE) ?>params/<?= ($mode == "New") ? "new" : "modify/".$field_data['project_id'] ?>" method="post">

<? if($errors) { ?>
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><? foreach ($errors as $error_text) { echo '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'.$error_text.'<br>'; } ?></p></div>
</div>
<? } ?>

<p>
<b>Project Title</b><br>
<input type="text" name="project_title" id="project_title" value="<?= $field_data['project_title'] ?>" style="width:315px;">
</p>

<b>Keywords and Phrases</b><br>

<div id="keyword_tabs" style="width:<?= ($mode == "Modify") ? '675' : '345' ?>px; font-size:12px;">
    <ul>
        <li><a href="#keyword_tabs-1">Positive</a></li>
        <li><a href="#keyword_tabs-2">Negative</a></li>
    </ul>
    
    <div id="keyword_tabs-1">
        <table width="660" border="0" cellspacing="0" cellpadding="3">
        <tr>
        <td align="left">
        <? if($mode == "Modify") { ?><b>Active keywords/phrases</b><br><? } ?>
        <input type="text" name="add_keyword_text" id="add_keyword_text" value="Enter keyword(s)..."  style="width:180px;">
        <label for="exact_phrase"><input name="exact_phrase" id="exact_phrase" type="checkbox" value="1">exact</label>
        <input type="button" id="add_keyword_btn" name="add_keyword_btn" value="&#043;">
    <? if($mode == "New") { ?>
        <input type="button" id="remove_keyword_btn" name="remove_keyword_btn" value="&#8722;">
        <br>
        <select id="keywords_phrases" name="keywords_phrases[]" multiple="multiple">
        <? if(array_key_exists('keywords_phrases', $field_data)) {
                foreach($field_data['keywords_phrases'] as $keyword_phrase) {
                    echo '<option value="'.$keyword_phrase.'">'.$keyword_phrase.'</option>';
                }
            } ?>
        </select>
    <? } elseif($mode == "Modify") { ?>
        <input type="button" id="deactivate_keyword_btn" name="deactivate_keyword_btn" value="&#8722;">
        <br>
        <select id="keywords_phrases" name="keywords_phrases[]" multiple="multiple">
        <? if(array_key_exists('keywords_phrases', $field_data)) {
                foreach($field_data['keywords_phrases'] as $keyword_phrase_id) {
                    if(array_key_exists($keyword_phrase_id, $field_data['keyword_phrase_data'])) {
                        $quotes = ($field_data['keyword_phrase_data'][$keyword_phrase_id]['exact_phrase']) ? '"' : '';
                        echo '<option value="'.$keyword_phrase_id.'">'.$quotes.$field_data['keyword_phrase_data'][$keyword_phrase_id]['keyword_phrase'].$quotes.'</option>';
                    } else {
                        echo '<option>'.$keyword_phrase_id.'</option>';
                    } 
                }
            } ?>
        </select>
        </td>
        <td align="left">
            <b>Deactivated keywords/phrases</b><br>
            <input type="button" id="reactivate_keyword_btn" name="reactivate_keyword_btn" value="Reactivate">
            <br>
            <select id="deactivated_keywords_phrases" name="deactivated_keywords_phrases[]" multiple="multiple">
                <? if(array_key_exists('deactivated_keywords_phrases', $field_data)) {  
                    foreach($field_data['deactivated_keywords_phrases'] as $keyword_phrase_id) {
                        $quotes = ($field_data['keyword_phrase_data'][$keyword_phrase_id]['exact_phrase']) ? '"' : '';
                        echo '<option value="'.$keyword_phrase_id.'">'.$quotes.$field_data['keyword_phrase_data'][$keyword_phrase_id]['keyword_phrase'].$quotes.'</option>';
                    }
                } ?>
            </select>
        </td>
     <? } ?>
        </tr>
        </table>
    </div>
    
    <div id="keyword_tabs-2">
        <table width="600" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <td align="left">
            <input type="text" name="add_neg_keyword_text" id="add_neg_keyword_text" value="Enter negative keyword(s)..." style="width:180px;">
            <label for="neg_exact_phrase"><input name="neg_exact_phrase" id="neg_exact_phrase" type="checkbox" value="1">exact</label>
            <input type="button" id="add_neg_keyword_btn" name="add_neg_keyword_btn" value="&#043;">
            <input type="button" id="remove_neg_keyword_btn" name="remove_neg_keyword_btn" value="&#8722;">
            <br>
            <select id="negative_keywords" name="negative_keywords[]" multiple="multiple">
                <? if(array_key_exists('negative_keywords', $field_data)) {
                    foreach($field_data['negative_keywords'] as $negative_keyword) {
                        if($mode == "New") {
                            echo '<option value="'.$negative_keyword.'">'.$negative_keyword.'</option>';
                        } elseif($mode == "Modify") {
                            // $negative_keyword is the keyword_id
                            if(array_key_exists($negative_keyword, $field_data['keyword_phrase_data'])) {
                                $quotes = ($field_data['keyword_phrase_data'][$negative_keyword]['exact_phrase']) ? '"' : '';
                                echo '<option value="'.$negative_keyword.'">'.$quotes.$field_data['keyword_phrase_data'][$negative_keyword]['keyword_phrase'].$quotes.'</option>';
                            } else {
                                echo '<option>'.$negative_keyword.'</option>';
                            }
                        }
                    }
                } ?>
            </select>
        </tr>
        </table>
    </div>

</div>

<p><b>Enable/Disable Data Source APIs</b><br>
<? $rss_feed_chkbox_html = "";
foreach($api_sources as $api_source) { 
    $chkbox_html = '<label for="api_'.$api_source['gather_method_name'].'"><input name="api_'.$api_source['gather_method_name'].'" id="api_'.$api_source['gather_method_name'].'" type="checkbox" value="1"'; 
    if(array_key_exists('api_'.$api_source['gather_method_name'], $field_data)) 
        $chkbox_html .= ' checked="true"'; 
    $chkbox_html .= '> '.$api_source['api_name'].'</label><br>';
    
    // Make sure RSS feed is placed at the end of the list
    if($api_source['gather_method_name'] == 'rss_feed')
        $rss_feed_chkbox_html = $chkbox_html;
    else
        echo $chkbox_html;
} 
echo $rss_feed_chkbox_html; 

// Generate RSS feed selectbox HTML
$preloaded_rss_feeds_html = '<select id="preloaded_rss_feeds" name="preloaded_rss_feeds[]" multiple="multiple">';
$last_cat = "";
foreach($preloaded_rss_feeds as $rss_feed) {
    if($rss_feed['cat_name'] != $last_cat) {
        if(!$last_cat) $preloaded_rss_feeds_html .= '</optgroup>'; // Close tag from previous group
        $preloaded_rss_feeds_html .= '<optgroup label="'.$rss_feed['cat_name'].'">';
        $last_cat = $rss_feed['cat_name'];
    }
    $preloaded_rss_feeds_html .= '<option value="';
    if($rss_feed['is_searchable']) $preloaded_rss_feeds_html .= 'Searchable: '; 
    //$preloaded_rss_feeds_html .= $rss_feed['url'].'">'.$rss_feed['feed_name'].'</option>';
    $preloaded_rss_feeds_html .= $rss_feed['url'].'">'.$rss_feed['url'].'</option>';
} 
$preloaded_rss_feeds_html .= '</optgroup>
</select>';
?>

<div id="rss_feed_form"<? if(!array_key_exists('api_rss_feed', $field_data)) echo ' style="display:none;"'; ?>>

<table width="716" border="0" cellspacing="0" cellpadding="3">
<tr>
    <td align="left">
        <div style="height:45px;">
            <b>Active RSS Feeds</b><br>
            <input type="text" name="add_rss_feed_text" id="add_rss_feed_text" value="Enter feed URL..." style="width:130px;">
            <label for="searchable"><input name="searchable" id="searchable" type="checkbox" value="1">searchable (<a href=" <?= Url::base() ?>index.php/params/help_searchable" rel="lyteframe" title="Help: Searchable RSS Feeds" rev="width: 465px; height: 280px; scrolling: no;">?</a>)</label>
            <input type="button" id="add_rss_feed_btn" name="add_rss_feed_btn" value="&#043;">
<? if($mode == "New") { ?>
            <input type="button" id="remove_rss_feed_btn" name="remove_rss_feed_btn" value="&#8722;">
        </div>
        
        <select id="rss_feeds" name="rss_feeds[]" multiple="multiple">
            <? if(array_key_exists('rss_feeds', $field_data)) {
                foreach($field_data['rss_feeds'] as $rss_feed) {
                    echo '<option value="'.$rss_feed.'">'.$rss_feed.'</option>';
                }
            } ?>
        </select>
    </td>

    </td>
    <td align="center" valign="middle" width="32">
        <div style="height:45px;"></div>
        
        <ul id="icons" class="ui-widget ui-helper-clearfix">
            <li id="add_preloaded_rss_btn" class="button_hover ui-state-default ui-corner-all" title="Add RSS feed"><span class="ui-icon ui-icon-carat-1-w"></span></li>
        </ul>
    </td>
    <td colspan="4" align="left">
        <div style="height:45px;">
        <b>Pre-loaded RSS Feeds</b><br>
        <i>Some text description here...</i>
        </div>
        <?= $preloaded_rss_feeds_html ?>
    </td>

<? } elseif($mode == "Modify") { ?>
        <input type="button" id="deactivate_rss_feed_btn" name="deactivate_rss_feed_btn" value="&#8722;">
    </div>
    
    <select id="rss_feeds" name="rss_feeds[]" multiple="multiple">
        <? if(array_key_exists('rss_feeds', $field_data)) {
            foreach($field_data['rss_feeds'] as $feed_id) {
                if(array_key_exists($feed_id, $field_data['rss_feed_data'])) {
                    $searchable = ($field_data['rss_feed_data'][$feed_id]['searchable']) ? 'Searchable: ' : '';
                    echo '<option value="'.$feed_id.'">'.$searchable.$field_data['rss_feed_data'][$feed_id]['url'].'</option>';
                } else {
                    echo '<option>'.$feed_id.'</option>';
                }
            }
        } ?>
    </select>
    </td>
    <td colspan="5" align="left">
        
        <div id="rss_feed_tabs" style="width:365px; font-size:12px;">
            <ul>
                <li><a href="#rss_feed_tabs-1">Deactivated</a></li>
                <li><a href="#rss_feed_tabs-2">Preloaded</a></li>
            </ul>
            
            <div id="rss_feed_tabs-1" style="padding-top:12px; padding-bottom:5px; padding-left:0px;">
                <table width="400" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    </td>
                    <td align="center" valign="middle">
                        <ul id="icons" class="ui-widget ui-helper-clearfix">
                            <li id="reactivate_rss_feed_btn" class="button_hover ui-state-default ui-corner-all" title="Reactivate RSS feed"><span class="ui-icon ui-icon-carat-1-w"></span></li>
                            <br>
                            <li id="deactivate_rss_feed_btn2" class="button_hover ui-state-default ui-corner-all" title="Deactivate RSS feed"><span class="ui-icon ui-icon-carat-1-e"></span></li>
                        </ul>
                    </td>
                    <td align="left">
                        <select id="deactivated_rss_feeds" name="deactivated_rss_feeds[]" multiple="multiple">
                            <? if(array_key_exists('deactivated_rss_feeds', $field_data)) {  
                                foreach($field_data['deactivated_rss_feeds'] as $feed_id) {
                                    $searchable = ($field_data['rss_feed_data'][$feed_id]['searchable']) ? 'Searchable: ' : '';
                                    echo '<option value="'.$feed_id.'">'.$searchable.$field_data['rss_feed_data'][$feed_id]['url'].'</option>';
                                }
                            } ?>
                        </select>
                    </td>
                </tr>
                </table>
            </div>
            
            <div id="rss_feed_tabs-2" style="padding-top:12px; padding-bottom:5px; padding-left:0px;">
                <table width="400" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    </td>
                    <td align="center" valign="middle">
                        <ul id="icons" class="ui-widget ui-helper-clearfix">
                            <li id="add_preloaded_rss_btn" class="button_hover ui-state-default ui-corner-all" title="Reactivate RSS feed"><span class="ui-icon ui-icon-carat-1-w"></span></li>
                            <br>
                            <div style="height:33px;"></div>
                        </ul>
                    </td>
                    <td align="left">
                        <?= $preloaded_rss_feeds_html ?>
                    </td>
                </tr>
                </table>
                
            </div>
        </div>
        
        
    </td>
<? } ?>
</tr>
</table>

</div></p>

<p><b>Gather interval</b>
<select id="gather_interval" name="gather_interval">
    <option value="daily"<? if($field_data['gather_interval'] == 'daily') echo " selected"; ?>>daily</option>
    <option value="twice_daily"<? if($field_data['gather_interval'] == 'twice_daily') echo " selected"; ?>>twice daily</option>
    <option value="weekly"<? if($field_data['gather_interval'] == 'weekly') echo " selected"; ?>>weekly</option>
    <option value="twice_weekly"<? if($field_data['gather_interval'] == 'twice_weekly') echo " selected"; ?>>twice weekly</option>
    <option value="monthly"<? if($field_data['gather_interval'] == 'monthly') echo " selected"; ?>>monthly</option>
    <option value="twice_monthly"<? if($field_data['gather_interval'] == 'twice_monthly') echo " selected"; ?>>twice monthly</option>
</select></p>

<label for="gather_now"><input name="gather_now" id="gather_now" type="checkbox" value="1"<? if(array_key_exists('gather_now', $field_data)) echo ' checked="true"'; ?>> <?= ($mode == 'New') ? 'Immediately activate project and start gathering data' : 'Start gathering data immediately' ?></label><br> 
<input type="submit" id="submit_btn" name="submit_btn" value="<?= ($mode == "New") ? "Submit" : "Modify" ?>">
</form>