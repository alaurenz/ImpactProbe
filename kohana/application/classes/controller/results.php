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
defined('SYSPATH') or die('No direct script access.');

class Controller_Results extends Controller {
    
    public function before() {
        parent::before();
        $this->model_results = new Model_Results;
        
        $this->chart_api_url = "http://chart.apis.google.com/chart";
        $this->date_format_chart = 'MMM-dd-yyyy';
        $this->default_cluster_threshold = 0.25; 
        $this->clusters_threshold_exact = 0.6; // Threshold used to cluster docs containing the exact or nearly exact text (for collapsing)
        $this->chart_w = 800; // (in px) > 800px may cause problems 
        $this->chart_h = 370; // (in px) > 370px may cause problems
        $this->min_slider_value = 5; // Min value for slider range for hiding large clusters 
        $this->max_slider_value = 500; // Max value for slider range for hiding large clusters 
        $this->slider_increments = 5; // Number of slider "snap" increments 
        $this->min_dot_size = 6; // So the smallest can actually be seen on plot
    }
    
    public function action_index()
    {
        echo '<a href="'.Url::base().'">&laquo; Back to home</a><p>Nothing to display.</p>';
    }

    public function action_view($project_id = 0)
    {
        $project_data = $this->model_results->get_project_data($project_id);
        
        // Verify that project exists
        if(count($project_data) == 0) {
            echo "<p>Project with this ID does not exist.</p>"; 
        } else {
            $project_data = array_pop($project_data);
            
            $view = View::factory('template');
            $view->page_title = $project_data['project_title']." - View Results";
            
            $view->page_content = View::factory('pages/results_basic');
            
            // Default results display
            $result_params = array(
                'date_from' => 0, 'date_to' => 0,
                'num_results' => 100,
                'order' => 'desc',
                'sort_by' => 'metadata.date_published',
                'download_mode' => 'summary_csv'
            );
             
            $form_errors = "";
            $download_results = 0;
            if($_POST) {
                // Form validation
                $post = Validate::factory($_POST);
                $post->rule('date_from', 'max_length', array(10))
                     ->rule('date_to', 'max_length', array(10));
                 
                $field_data = $post->as_array(); // For form re-population
                
                if ($post->check()) {
                    // Process results display parameters
                    $date_from_ex = explode("/", $field_data['date_from']);
                    $date_to_ex = explode("/", $field_data['date_to']);
                    if(count($date_from_ex) == 3 AND $date_from_ex[0] > 0 AND $date_from_ex[1] > 0 AND $date_from_ex[2] > 0)
                        $result_params['date_from'] = mktime(0, 0, 0, $date_from_ex[0], $date_from_ex[1], $date_from_ex[2]);
                    if(count($date_to_ex) == 3 AND $date_to_ex[0] > 0 AND $date_to_ex[1] > 0 AND $date_to_ex[2] > 0)
                        $result_params['date_to'] = mktime(0, 0, 0, $date_to_ex[0], $date_to_ex[1]+1, $date_to_ex[2]); // Add +1 to ay so it searches THROUGH given "To" date
                    
                    if($field_data['download'] == 'download') // Download button was clicked
                        $download_results = 1;
                    
                    $result_params['num_results'] = $field_data['num_results'];
                    $result_params['sort_by'] = $field_data['sort_by'];
                    $result_params['order'] = strtoupper($field_data['order']);
                    
                } else { 
                    $form_errors = $post->errors('results');
                } 
            } else {
                // Populate form w/ empty values
                $field_data = array(
                    'date_from' => '', 'date_to' => '',
                    'num_results' => $result_params['num_results'],
                    'sort_by' => $result_params['sort_by'],
                    'order' => $result_params['order'],
                    'download_mode' => $result_params['download_mode']
                );
            } 
            
            $total_results = $this->model_results->num_metadata_entries($project_id, $result_params['date_from'], $result_params['date_to']);
            
            // Get set of keyword_id's corresponding to each keyword/phrase in project
            $keywords_phrases = $this->model_results->get_keywords_phrases($project_data['project_id']);
            
            // Determine date published range
            if($total_results > 0) {
                $edge_date_to = $this->model_results->metadata_edge_date($project_id, 'most_recent');
                $edge_date_from = $this->model_results->metadata_edge_date($project_id, 'oldest');
                if($result_params['date_from'] > 0 AND $result_params['date_from'] > $edge_date_from)
                    $edge_date_from = $result_params['date_from'];
                if($result_params['date_to'] > 0 AND $result_params['date_to'] < $edge_date_to)
                    $edge_date_to = $result_params['date_to'];
                $date_published_range = date(Kohana::config('myconf.date_format'), $edge_date_from)." - ".date(Kohana::config('myconf.date_format'), $edge_date_to);
            } else {
                $date_published_range = "";
            }
            
            if($download_results) {
                // Generate result output file
                if($field_data['download_mode'] == 'summary_csv') {
                    $download_type = 'text/csv'; $download_ext = 'csv';
                    $download_file = Kohana::config('myconf.path.charts')."/results_$project_id.$download_ext";
                    
                    $result_params['num_results'] = $total_results; // So it outputs ALL results (within given date range)
                    $result_params['offset'] = 0;
                    list($results, $keyword_occurrence_totals) = $this->generate_results_array($project_id, $keywords_phrases, $result_params);
                    
                    $this->write_results_csv($download_file, $keywords_phrases, $results);
                } else {
                    $download_type = 'text/plain'; $download_ext = 'txt';
                    $download_file = Kohana::config('myconf.path.charts')."/results_$project_id.$download_ext";
                    
                    $results = $this->model_results->get_results_raw($project_id, $result_params);
                    $this->write_results_raw($download_file, $results);
                }
                
                // Begin download
                $download_filename = $field_data['download_mode']."_".date(Kohana::config('myconf.date_format'), $edge_date_from)."_".date(Kohana::config('myconf.date_format'), $edge_date_to);
                $this->request->redirect(Kohana::config('myconf.url.download')."?file=$download_file&type=$download_type&name=$download_filename.$download_ext&delete_file=1");
            } else {
                // Pagination
                $pagination = Pagination::factory(array(
                    'total_items'    => $total_results,
                    'items_per_page' => $result_params['num_results'],
                    'view'           => 'pagination/post_mod',
                ));
                $result_params['offset'] = $pagination->offset;
                
                // Get array of results to display
                list($results, $keyword_occurrence_totals) = $this->generate_results_array($project_id, $keywords_phrases, $result_params);
            }
            
            $num_results_shown = count($results);
            $field_data['num_results_shown'] = ($num_results_shown < $field_data['num_results']) ? $num_results_shown : $field_data['num_results'];
            
            $view->page_content->project_data              = $project_data;
            $view->page_content->field_data                = $field_data;
            $view->page_content->date_format               = Kohana::config('myconf.date_format');
            $view->page_content->results                   = $results;
            $view->page_content->total_results             = $total_results;
            $view->page_content->offset                    = $result_params['offset'];
            $view->page_content->date_published_range      = $date_published_range;
            $view->page_content->keywords_phrases          = $keywords_phrases;
            $view->page_content->keyword_occurrence_totals = $keyword_occurrence_totals;
            $view->page_content->errors                    = $form_errors;
            $view->page_content->page_links                = $pagination->render();
            $view->page_content->clustered = $this->model_results->cluster_log_exists($project_id);
            $this->request->response = $view;
        }
    }
    
    // Get results & re-organize them so there is only one row per metadata entry & calculate keyword stats
    private function generate_results_array($project_id, $keywords_phrases, $result_params) {
        // Create array to count total occurrences of each keyword
        $keyword_occurrence_totals = array();
        foreach(array_keys($keywords_phrases) as $keyword_id) {
            $keyword_occurrence_totals[$keyword_id] = 0;
        }
        
        $results = array();
        $i = 1;
        /*if($result_params['num_results'] > 500)
            $result_params['limit'] = 500; //$result_params['num_results'];
        else*/
        $result_params['limit'] = $result_params['num_results'];
        
        //$result_params['offset'] = 0;
        //do { // Collect 500 database results at a time to prevent MySQL SELECT failure
            $results_db = $this->model_results->get_results($project_id, $result_params);
            
            /* TO DO: enable collapsable "re-tweets" on results basic view 
                      (Refer to action_cluster_view method for code to do tihs
                
                Count # of docs in each identical/exact cluster & collapse into single meta_id
                (if # docs in cluster sql table != total_results) -> cluster_exact
                if(cluster_size > 1)
                calculate # identical for each meta_id (set one as that value -> others as 0)
                when going through results: if(# identical < 1) -> get other docs in cluster 
            
            $clusters_identical = array();
            //$cluster_data = $this->model_results->get_cluster_summary_exact($project_id, $cluster_id, $params, 0);
            $i = 0;
            $last_cluster_id = 0;
            $last_meta_id = 0;
            foreach($cluster_data as $cluster) {
                if($cluster['cluster_id_exact'] != $last_cluster_id) {
                    $clusters_identical[$cluster['meta_id']] = array($cluster['text']);
                    $last_cluster_id = $cluster['cluster_id_exact'];
                    $last_meta_id = $cluster['meta_id'];
                } else {
                    array_push($clusters_identical[$last_meta_id], $cluster['text']);
                    $clusters_identical[$cluster['meta_id']] = 0;
                }
                $i++;
            }*/
            
            $result_params['offset'] += $result_params['limit'];
            foreach($results_db as $row) {
                
                // Do not generate full results display (only summary) if displaying 'all' results at once
                if($result_params['num_results'] > 0) {
                    /*if($i > $result_params['num_results'])
                        break;*/
                    
                    $results[$row['meta_id']] = array(
                        'meta_id' => $row['meta_id'],
                        'url' => $row['url'],
                        'api_name' => $row['api_name'],
                        'date_retrieved' => $row['date_retrieved'],
                        'date_published' => $row['date_published'],
                        'total_words' => $row['total_words'],
                        'keywords_phrases' => array()
                    );
                    
                    $keyword_metadata = $this->model_results->get_keyword_metadata($row['meta_id']);
                    foreach($keyword_metadata as $meta_row) {
                        array_push($results[$row['meta_id']]['keywords_phrases'], array(
                            'keyword_id' => $meta_row['keyword_id'],
                            'keyword' => $keywords_phrases[$meta_row['keyword_id']],
                            'num_occurrences' => $meta_row['num_occurrences']
                        ));
                        
                        $keyword_occurrence_totals[$meta_row['keyword_id']] += $meta_row['num_occurrences'];
                    }
                    $i++;
                }
            }
            
        //} while($num_sub_results == $result_params['limit']);
        return array($results, $keyword_occurrence_totals);
    }
    
    // Save csv file of results 
    private function write_results_csv($download_file, $keywords_phrases, $results) {
        $fh_download_file = fopen($download_file, 'w') or die("$download_file: cannot open file for writing");
        fwrite($fh_download_file, "Date published,Date retrieved,URL");
        // Create a col to store occurrances of each keyword/phrase
        foreach($keywords_phrases as $keyword_phrase) {
            fwrite($fh_download_file, ",$keyword_phrase");
        }
        fwrite($fh_download_file, "\n");
        foreach($results as $result) {
            $date_published = ($result['date_published'] > 0) ? date(Kohana::config('myconf.date_format'), $result['date_published']) : 'N/A';
            $date_retrieved = date(Kohana::config('myconf.date_format'), $result['date_retrieved']);
            fwrite($fh_download_file, "$date_published,$date_retrieved,".$result['url']);
            // Put occurrances of each keyword/phrase in its own column
            foreach(array_keys($keywords_phrases) as $keyword_id) {
                // Check if keyword occurrances value exists for this entry (if not occurrances = 0)
                $num_occurrances_s = 0;
                foreach($result['keywords_phrases'] as $keyword_phrase_s) {
                    if($keyword_phrase_s['keyword_id'] == $keyword_id)
                        $num_occurrances_s = $keyword_phrase_s['num_occurrences'];
                }
                fwrite($fh_download_file, ",$num_occurrances_s");
            }
            fwrite($fh_download_file, "\n");
        } 
        fclose($fh_download_file);
    }
    
    // Save file with raw text (and some metadata) from results 
    private function write_results_raw($download_file, $results) {
        $fh_download_file = fopen($download_file, 'w') or die("$download_file: cannot open file for writing");
        foreach($results as $result) {
            //print_r($result);
            $date_published = ($result['date_published'] > 0) ? date(Kohana::config('myconf.date_format'), $result['date_published']) : 'N/A';
            $date_retrieved = date(Kohana::config('myconf.date_format'), $result['date_retrieved']);
            fwrite($fh_download_file, "Date published: $date_published\nDate retrieved: $date_retrieved\nURL: ".$result['url']."\n".$result['text']."\n\n");
        } 
        fclose($fh_download_file);
    }
    
    // TO DO: allow user to view document with keywords_phrases highlighted
    public function action_view_document($project_id = 0)
    {
        $meta_id = ($_GET['meta_id'] > 0) ? $_GET['meta_id'] : 0;
        $view = View::factory('pages/view_document');
        
        $text = $this->model_results->get_cached_text($meta_id);
        /* 
        //TO DO: bold each keyword/phrase in document  
        $keywords_phrases = $this->model_results->get_keywords_phrases($project_id);
        foreach($keywords_phrases as $keyword_phrase) {
            $text = preg_replace("/\b($keyword_phrase)\b/ie", "<b>$keyword_phrase</b>", $text); // Doesn't work!
        }
        */
        $text = $this->parse_urls($text);
        $view->text = $text;
        $this->request->response = $view;
    } 
    
    public function action_trendline($project_id = 0)
    {
        $project_data = $this->model_results->get_project_data($project_id);
        // Verify that project exists
        if(count($project_data) == 0) {
            echo "<p>Project with this ID does not exist.</p>"; 
        } else {
            $project_data = array_pop($project_data);
            
            $view = View::factory('template');
            $view->page_title = $project_data['project_title']." - View Trendline";
            
            $view->page_content = View::factory('pages/trendline_view');
            
            // Default results display
            $result_params = array(
                'date_from' => 0, 'date_to' => 0,
                'display_mode' => 'consensus'
            );
            
            $form_errors = "";
            $download_results = 0;
            if($_POST) {
                // Form validation
                $post = Validate::factory($_POST);
                $post->rule('date_from', 'max_length', array(10))
                     ->rule('date_to', 'max_length', array(10));
                 
                $field_data = $post->as_array(); // For form re-population
                
                if ($post->check()) {
                    // Process results display parameters
                    $date_from_ex = explode("/", $field_data['date_from']);
                    $date_to_ex = explode("/", $field_data['date_to']);
                    if(count($date_from_ex) == 3 AND $date_from_ex[0] > 0 AND $date_from_ex[1] > 0 AND $date_from_ex[2] > 0)
                        $result_params['date_from'] = mktime(0, 0, 0, $date_from_ex[0], $date_from_ex[1], $date_from_ex[2]);
                    if(count($date_to_ex) == 3 AND $date_to_ex[0] > 0 AND $date_to_ex[1] > 0 AND $date_to_ex[2] > 0)
                        $result_params['date_to'] = mktime(0, 0, 0, $date_to_ex[0], $date_to_ex[1]+1, $date_to_ex[2]); // Add +1 to day so it searches THROUGH given "To" date
                    
                    $result_params['display_mode'] = $field_data['display_mode'];
                    
                    if(array_key_exists('Download', $field_data)) // Download button was clicked
                        $download_results = 1;
                    
                } else { 
                    $form_errors = $post->errors('results');
                } 
            } else {
                // Populate form w/ empty values
                $field_data = array(
                    'date_from' => '', 'date_to' => '',
                    'display_mode' => $result_params['display_mode']
                );
            } 
            
            // Get chart data & params
            if($result_params['date_from'] == 0)
                $result_params['date_from'] = $this->model_results->metadata_edge_date($project_id, 'oldest');
            if($result_params['date_to'] == 0)
                $result_params['date_to'] = $this->model_results->metadata_edge_date($project_id, 'most_recent');
            
            if($download_results) {
                // Generate .csv file
                $csv_file = Kohana::config('myconf.path.charts')."/trendline_$project_id.csv";
                $fh_csv_file = fopen($csv_file, 'w') or die("$csv_file: cannot open file for writing");

                if($result_params['display_mode'] == 'by_keyword') {
                    fwrite($fh_csv_file, $this->get_trendline_data_by_keyword($project_id, $result_params['date_from'], $result_params['date_to'], 'csv'));
                } else {
                    fwrite($fh_csv_file, $this->get_trendline_data_consensus($project_id, $result_params['date_from'], $result_params['date_to'], 'csv'));
                }
                fclose($fh_csv_file);
                
                // Begin download
                $csv_filename = ($result_params['display_mode'] == 'by_keyword') ? 'trendline_by_keyword_' : 'trendline_consensus_';
                $csv_filename .= date(Kohana::config('myconf.date_format'), $result_params['date_from'])."_".date(Kohana::config('myconf.date_format'), $result_params['date_to']);
                $this->request->redirect(Kohana::config('myconf.url.download')."?file=$csv_file&type=text/csv&name=$csv_filename.csv&delete_file=1");
                
            } else {
                // Generate chart data (as javascript)
                if($result_params['display_mode'] == 'by_keyword') {
                    $chart_data_js = $this->get_trendline_data_by_keyword($project_id, $result_params['date_from'], $result_params['date_to'], 'chart_js');
                } else {
                    $chart_data_js = $this->get_trendline_data_consensus($project_id, $result_params['date_from'], $result_params['date_to'], 'chart_js');
                }
            } 
            
            $view->page_content->errors = $form_errors;
            $view->page_content->field_data = $field_data;
            $view->page_content->project_data = $project_data;
            $view->page_content->chart_data_js = $chart_data_js;
            $view->page_content->chart_dimensions = "width: ".$this->chart_w."px; height: ".$this->chart_h."px;";
            $view->page_content->date_range = date(Kohana::config('myconf.date_format'), $result_params['date_from'])." - ".date(Kohana::config('myconf.date_format'), $result_params['date_to']);
            $view->page_content->date_format_chart = $this->date_format_chart;
            $this->request->response = $view;
        }
    }
    
    private function get_trendline_data_consensus($project_id = 0, $date_from, $date_to, $output_mode = 'chart_js')
    {
        $trendline_data = ($output_mode == 'csv') ? "Date,Number of results\n" : "data.addColumn('date', 'Date');\ndata.addColumn('number', 'Consensus:');\ndata.addRows([\n";
        
        // Gather total number of metadata entries from each day
        $cur_date = mktime(0, 0, 0, date("m", $date_from), date("d", $date_from), date("Y", $date_from)); 
        $secs_in_day = 24*60*60;
        while($cur_date <= $date_to) {
            $num_metadata_entries = $this->model_results->num_metadata_entries($project_id, $cur_date, ($cur_date+$secs_in_day));
            if($output_mode == 'csv') {
                $trendline_data .= date(Kohana::config('myconf.date_format'), $cur_date).",$num_metadata_entries\n";
            } else {
                // Date format (year, month (0-11), day)
                $trendline_data .= "[new Date(".date("Y", $cur_date).",".(date("m", $cur_date)-1).",".date("d", $cur_date)."), $num_metadata_entries],";
            }
            $cur_date += $secs_in_day;
        }
        if($output_mode == 'chart_js')
            $trendline_data = substr($trendline_data, 0, -1)."]);\n";
        return $trendline_data;
    }
    
    private function get_trendline_data_by_keyword($project_id = 0, $date_from, $date_to, $output_mode = 'chart_js')
    {
        $trendline_data = ($output_mode == 'csv') ? "Date," : "data.addColumn('date', 'Date');\n";
        
        $keywords_phrases = $this->model_results->get_keywords_phrases($project_id);
        foreach($keywords_phrases as $keyword_phrase) {
            if($output_mode == 'csv') {
                $trendline_data .= "$keyword_phrase,";
            } else {
                $trendline_data .= "data.addColumn('number', '$keyword_phrase:');\n";
            } 
        }
        if($output_mode == 'csv') {
            $trendline_data = substr($trendline_data, 0, -1); // Remove trailing comma
            $trendline_data .= "\n";
        } else {
            $trendline_data .= "data.addRows([\n";
        }
        
        // Gather total number of metadata entries from each day
        $cur_date = mktime(0, 0, 0, date("m", $date_from), date("d", $date_from), date("Y", $date_from)); 
        $secs_in_day = 24*60*60;
        while($cur_date <= $date_to) {
            if($output_mode == 'csv') {
                $trendline_data .= date(Kohana::config('myconf.date_format'), $cur_date).",";
            } else {
                // Date format (year, month (0-11), day)
                $trendline_data .= "[new Date(".date("Y", $cur_date).",".(date("m", $cur_date)-1).",".date("d", $cur_date)."),";
            }
            foreach(array_keys($keywords_phrases) as $keyword_id) {
                $num_metadata_entries = $this->model_results->num_metadata_entries_by_keyword($project_id, $keyword_id, $cur_date, ($cur_date+$secs_in_day));
                $trendline_data .= "$num_metadata_entries,";
            }
            $trendline_data = substr($trendline_data, 0, -1); // Remove trailing comma
            $trendline_data .= ($output_mode == 'csv') ? "\n" : "],";
            $cur_date += $secs_in_day; 
        }
        if($output_mode == 'chart_js')
            $trendline_data = substr($trendline_data, 0, -1)."]);\n";
        return $trendline_data;
    }
    
    public function action_cluster($project_id = 0, $cluster_threshold = 0, $date_from = 0, $date_to = 0, $time_plot_id = 0, $redirect = 1)
    {
        // NOTE: the lower $cluster_threshold => the less clusters
        if($cluster_threshold == 0)
            $cluster_threshold = $this->default_cluster_threshold;
        $cluster_order = 'arbitrarily';
        if($_GET) {
            if(array_key_exists('cluster_threshold', $_GET)) {
                $_GET['cluster_threshold'] = trim($_GET['cluster_threshold']);
                if(is_numeric($_GET['cluster_threshold']))
                    $cluster_threshold = $_GET['cluster_threshold'];
            }
            if($_GET['cluster_order'] == 'cluster_size')
                $cluster_order = 'cluster_size';
            // redirect to cluster_time & pass date & threshold to GET_
            if(array_key_exists('cluster_time', $_GET)) {
                $this->request->redirect("results/cluster_time/$project_id?cluster_order=$cluster_order&cluster_threshold=$cluster_threshold&date_from=".$_GET['date_from']."&date_to=".$_GET['date_to']);
            }
        }
        
        // parameterize clustering by date
        if($time_plot_id > 0) {
            // get doc ids within given date range...
            $doc_subset = $this->model_results->get_metadata_ids($project_id, $date_from, $date_to);
            //print date(Kohana::config('myconf.date_format'), $date_from)." - ".date(Kohana::config('myconf.date_format'), $date_to);
            
            if(count($doc_subset) > 0) {
                $cluster_data = $this->cluster_docs($project_id, $cluster_threshold, $doc_subset);
                $cluster_data_exact = $this->cluster_docs($project_id, $this->clusters_threshold_exact, $doc_subset);
                // Add cluster data for this plot to database
                $this->model_results->insert_clusters($cluster_data, $project_id, $time_plot_id);
                $this->model_results->insert_clusters_exact($cluster_data_exact, $time_plot_id);
            }
        } else {
            $cluster_data = $this->cluster_docs($project_id, $cluster_threshold);
            $cluster_data_exact = $this->cluster_docs($project_id, $this->clusters_threshold_exact); // Generate clusters where each cluster only contains identical documents
            // Delete old cluster data & add new cluster data to database
            $this->model_results->delete_clusters($project_id);
            $this->model_results->insert_clusters($cluster_data, $project_id);
            $this->model_results->insert_clusters_exact($cluster_data_exact);
            
            if($cluster_threshold == $this->default_cluster_threshold) {
                $this->model_results->update_cluster_log(array(
                    'project_id' => $project_id,
                    'threshold' => $cluster_threshold,
                    'order' => $cluster_order,
                    'num_docs' => count($cluster_data),
                    'date_clustered' => time()
                ));
            }
            
            // Delete chart files if they exist
            /*$chart_file = Kohana::config('myconf.path.charts')."/cluster_$project_id.gch";
            if(file_exists($chart_file)) unlink($chart_file);*/
            if(is_dir(Kohana::config('myconf.path.charts')."/cluster_$project_id"))
                system("rm -r ".Kohana::config('myconf.path.charts')."/cluster_$project_id", $return_code);
            
            // Redirect to cluster view
            if($redirect)
                $this->request->redirect("results/cluster_view/$project_id?cluster_order=$cluster_order&cluster_threshold=$cluster_threshold");
        }
    }
    
    // Generates cluster param, performs clustering -> returns array of cluster_data
    private function cluster_docs($project_id, $cluster_threshold, $doc_subset = array())
    {
        $this->build_lemur_index($project_id, $doc_subset);
        
        // NOTE: must delete clusterIndex.cl in order to re-cluster (which is created in dir where script was executed)
        $this->cluster_params = $this->params_dir."/cluster.params";
        $fh_cluster_params = fopen($this->cluster_params, 'w') or die($this->cluster_params.': cannot open file for writing');
        fwrite($fh_cluster_params, "<parameters>\n\t<index>".$this->index_dir."</index>\n\t<threshold>$cluster_threshold</threshold>\n</parameters>");
        fclose($fh_cluster_params);
        
        chdir($this->index_dir); // clusterIndex.cl file will be created here
        $system_cmd = Kohana::config('myconf.lemur.bin')."/Cluster ".$this->cluster_params; // 2>&1 = put stderr in stdout
        exec($system_cmd, $cluster_data, $return_code);
        if($return_code != 0) {
            echo "Error when running command &lt;$system_cmd&gt;: $return_code<br>";
            exit;
        }
        return $cluster_data;
    }

    // Build Lemur Index from a directory of cached text documents
    private function build_lemur_index($project_id, $doc_subset = array()) 
    {
        // Ensure dir of text docs directory exists
        $this->docs_dir = Kohana::config('myconf.lemur.docs')."/$project_id";
        if(!is_dir($this->docs_dir)) {
            echo $this->docs_dir.": directory does not exist. Cannot continue.<br>";
            exit;
        }
        
        // For clustering date range: create index for only the given subset of docs
        if(count($doc_subset) > 0) {
            $this->docs_dir = Kohana::config('myconf.lemur.docs')."/$project_id/subset";
            if(is_dir($this->docs_dir)) {
                $system_cmd = "rm -r ".$this->docs_dir;
                system($system_cmd, $return_code);
                if($return_code != 0) {
                    echo "Error when running command &lt;$system_cmd&gt;: $return_code<br>"; exit;
                }
            }
            mkdir($this->docs_dir);
            foreach($doc_subset as $doc_id) {
                $system_cmd = "cp ".Kohana::config('myconf.lemur.docs')."/$project_id/".$doc_id['meta_id'].".txt ".$this->docs_dir;
                system($system_cmd, $return_code);
                if($return_code != 0) {
                    echo "Error when running command &lt;$system_cmd&gt;: $return_code<br>"; exit;
                }
            } 
        }
        
        // Create params directory if it does not exist already
        $this->params_dir = Kohana::config('myconf.lemur.params')."/$project_id";
        if(!is_dir($this->params_dir))
            mkdir($this->params_dir);
        
        if($dh_docs = opendir($this->docs_dir)) {
            // Create list of documents to index (overwrite existing)
            $this->docs_list = $this->params_dir.'/index.list';
            $fh_doclist = fopen($this->docs_list, 'w') or die($this->docs_list.': cannot open file for writing');
            while (false !== ($doc_filename = readdir($dh_docs))) {
                if ($doc_filename != "." AND $doc_filename != ".." AND !is_dir($this->docs_dir."/$doc_filename"))
                    fwrite($fh_doclist, $this->docs_dir."/$doc_filename\n");
            }
            closedir($dh_docs); 
            fclose($fh_doclist);
        }
        
        $this->index_dir = Kohana::config('myconf.lemur.indexes')."/$project_id";
        // Remove old index directory (containing clusterIndex.cl) otherwise we will get duplicate entries
        if(is_dir($this->index_dir)) {
            $system_cmd = "rm -r ".$this->index_dir;
            system($system_cmd, $return_code);
            if($return_code != 0) {
                echo "Error when running command &lt;$system_cmd&gt;: $return_code<br>";
                exit;
            }
        }
        
        // Generate index params & build index
        $this->index_params = $this->params_dir."/index.params";
        $fh_index = fopen($this->index_params, 'w') or die($this->index_params.': cannot open file for writing');
        fwrite($fh_index, "<parameters>\n\t<index>".$this->index_dir."</index>\n\t<indexType>indri</indexType>\n\t<memory>512000000</memory>\n\t<dataFiles>".$this->docs_list."</dataFiles>\n\t<stopwords>".Kohana::config('myconf.lemur.stopwords_list')."</stopwords>\n\t<docFormat>trec</docFormat>\n\t<stemmer>krovetz</stemmer>\n</parameters>");
        fclose($fh_index);
        
        // Ensure directory where indexes are created (Kohana::config('myconf.lemur.indexes')) has 777 permissions (writeable)
        $system_cmd = Kohana::config('myconf.lemur.bin')."/BuildIndex ".$this->index_params." > /dev/null"; // 2>&1 = put stderr in stdout
        system($system_cmd, $return_code);
        if($return_code != 0) {
            echo "Error when running command &lt;$system_cmd&gt;: $return_code<br>";
            exit;
        }
    }
    
    // Go through all clustered documents and test which contain negative keyword(s) being tested
    // TO DO: make this more efficient & do code re-use later
    private function process_negative_keywords($project_id, $field_data)
    {
        // Unmark all documents marked during previous testing
        $this->model_results->remove_marked_documents($project_id);
        
        // Trim whitespace from negative keywords entered into test box
        $negative_keywords = explode(",", $field_data['negative_keywords_input']);
        for($i=0; $i<count($negative_keywords); $i++)
            $negative_keywords[$i] = trim($negative_keywords[$i]);
        
        $cluster_db = $this->model_results->get_clusters($project_id);
        
        foreach($cluster_db as $row) {
            $meta_id = $row['meta_id'];
            $cached_text = $this->model_results->get_cached_text($meta_id);
            
            if($this->negative_keyword_exists($negative_keywords, $cached_text)) {
                // Mark document as containing "test" negative keyword(s)
                $this->model_results->mark_document($project_id, $meta_id);
                
                if($field_data['negative_keywords_action'] == 'apply')
                    $this->model_results->delete_keyword_data_all($project_id, $meta_id);
            }
        }
    }
    //Returns TRUE if any of the given $negative_keywords exist in given $text 
    private function negative_keyword_exists(Array $negative_keywords, $text)
    {
        foreach($negative_keywords as $negative_keyword) {
            $num_occurances = 0;
            if(substr($negative_keyword, 0, 1) == '"' AND substr($negative_keyword, -1, 1) == '"') {
                // Phrase is quoted (exact): find total number of occurances
                $negative_keyword = str_replace('"', '', $negative_keyword); // remove quotes
                $num_occurances = preg_match_all("/\b(".$negative_keyword.")\b/ie", $text, $matches);
            } else {
                // Phrase NOT quoted: make sure post contains ALL words in phrase
                $keywords_phrases_arr = explode(" ", $negative_keyword); 
                $num_occurances = 1;
                foreach($keywords_phrases_arr as $keyword_phrase_sub) {
                    $num_occurances_sub = preg_match_all("/\b(".$keyword_phrase_sub.")\b/ie", $text, $matches);
                    if(!$num_occurances_sub) {
                        $num_occurances = 0;
                        break;
                    }
                }
            }
            if($num_occurances > 0)
            	return TRUE;
        }
        return FALSE;
    }
    
    public function action_cluster_view($project_id = 0)
    {
        $project_data = $this->model_results->get_project_data($project_id);
        $cluster_log = $this->model_results->get_cluster_log($project_id);
        
        if(count($project_data) == 0 OR count($cluster_log) == 0) {
            echo "<p>Project with this ID does not exist.</p>"; 
        } else {
            $project_data = array_pop($project_data);
            $cluster_log = array_pop($cluster_log);
            $cluster_params = array(
                'order' => $cluster_log['order'],
                'threshold' => $this->default_cluster_threshold,
                'default_threshold' => $this->default_cluster_threshold,
                'num_docs' => $cluster_log['num_docs'],
                'date_clustered' => $cluster_log['date_clustered']
            );
            if(array_key_exists('cluster_order', $_GET)) 
                $cluster_params['order'] = $_GET['cluster_order'];
            if(array_key_exists('cluster_threshold', $_GET)) 
                $cluster_params['threshold'] = $_GET['cluster_threshold'];
            
            $total_results = $this->model_results->get_total_results($project_id);
            
            // START negative keyword processing
            $rebuild_chart = 0;
            if($_POST) {
                $post = Validate::factory($_POST);
                $this->field_data = $post->as_array();
                $post->rule('negative_keywords_input', 'not_empty');
                if ($post->check()) {
                    $this->process_negative_keywords($project_id, $this->field_data);
                    $rebuild_chart = 1;
                    if($this->field_data['negative_keywords_action'] == 'apply') {
                        $model_params = new Model_Params;
                        $keywords_phrases_neg = explode(",", $this->field_data['negative_keywords_input']);
                        $model_params->insert_keywords($project_id, $keywords_phrases_neg, 1);
                        // Recluster
                        $this->action_cluster($project_id, $this->default_cluster_threshold, 0, 0, 0, 1);
                    }
                }
            } else {
                // Unmark all documents marked during previous testing (if any exist)
                if($this->model_results->marked_document_exists($project_id)) {
                    $this->model_results->remove_marked_documents($project_id);
                    $rebuild_chart = 1;
                }
                $this->field_data = array(
                    'negative_keywords_input' => ''
                );
            } // END negative keyword processing
            
            // re-cluster if NOT default settings
            if($cluster_params['threshold'] != $this->default_cluster_threshold OR $cluster_params['order'] != $cluster_log['order'])
                $this->action_cluster($project_id, $cluster_params['threshold'], 0, 0, 0, 0);
                
            // Mess with this later for FAST loading
            $rebuild_chart = 1;
            
            $view = View::factory('template');
            $view->page_title = $project_data['project_title']." - View Clusters";
            
            $view->page_content = View::factory('pages/cluster_view');
            
            $clusters = $this->get_cluster_metadata($project_id);
            
            // Find max & min cluster sizes (for normalization)
            // TO DO: store these values in cluster log for FASTER LOADING...check other stuff below
            $clusters_sorted_asc = $this->order_array_numeric($clusters, 'num_docs', 'ASC');
            $min_cluster_size_data = current($clusters_sorted_asc);
            $min_cluster_size = $min_cluster_size_data['num_docs'];
            $max_cluster_size_data = end($clusters_sorted_asc);
            $max_cluster_size = $max_cluster_size_data['num_docs'];
            
            // Check if user selected to order points by cluster size
            if($cluster_params['order'] == 'cluster_size')
                $clusters = $clusters_sorted_asc;
            unset($clusters_sorted_asc); // ..To save memory
            
            // Calculate slider properties
            $slider['increments'] = $this->slider_increments; // For passing to view
            $slider['min'] = $this->min_slider_value;
            $slider['max'] = $this->max_slider_value;
            if($max_cluster_size < $this->max_slider_value)
                $slider['max'] = ceil($max_cluster_size/10)*10; // Ceil to nearest 10
            $slider['step'] = round((($slider['max'] / $this->slider_increments)/5),0)*5; // Round to nearest 5
            if($slider['step'] == 0)
                $slider['step'] = 1;
            
            // Overflow check...
            $actual_slider_max = 5 + $slider['step']*$this->slider_increments;
            if($actual_slider_max > $slider['max'])
                $slider['max'] = $actual_slider_max;
            // Underflow check...
            $leftover = $slider['max'] - (5 + $slider['step']*$this->slider_increments);
            if($leftover > 0)
                $slider['max'] = $slider['max'] - $leftover;
            
            $cur_cutoff = 5;
            for($i = 0; $i < $this->slider_increments; $i++) {
                $slider_cutoffs[$i] = $cur_cutoff;
                $cur_cutoff += $slider['step'];
            }
            $slider_cutoffs[$this->slider_increments] = $max_cluster_size;
            
            // Find total number of singleton clusters
            $singleton_clusters = 0;
            foreach($clusters as $cluster) {
                if($cluster['num_docs'] == 1)
                    $singleton_clusters++;
            }
            
            // Get chart data & params
            //$chart_file = Kohana::config('myconf.path.charts')."/cluster_$project_id.gch";
            $singleton_cluster_marked = 0;
            $chart_dir = Kohana::config('myconf.path.charts')."/cluster_$project_id";
            if(floatval($cluster_params['threshold']) == $this->default_cluster_threshold) {
                $chart_dir = Kohana::config('myconf.path.charts')."/cluster_default_$project_id";
            }
            
            if($rebuild_chart OR !is_dir($chart_dir)) {
                // Generate chart data
                $num_clusters = 0;
                for($i = 0; $i <= $this->slider_increments; $i++) {
                    $x_vals[$i] = ""; $y_vals[$i] = ""; $size_vals[$i] = ""; $color_vals[$i] = ""; $cluster_ids[$i] = ""; $cluster_sizes[$i] = "";
                }
                
                foreach($clusters as $cluster_id => $cluster_data) {
                    if($cluster_data['num_docs'] > 1) {
                        if(array_key_exists('hide_unaffected', $this->field_data) AND $cluster_data['num_marked'] == 0)
                            continue;
                        
                        $num_clusters++;
                        
                        // Normalize dot sizes they are between 0-100
                        if($max_cluster_size == $min_cluster_size) {
                            $dot_size_normalized = 50; // All clusters have size 1
                        } else {
                            $dot_size_normalized = (round((($cluster_data['num_docs'] - $min_cluster_size)/($max_cluster_size - $min_cluster_size)), 2))*100;
                            if($dot_size_normalized < $this->min_dot_size)
                                $dot_size_normalized = $this->min_dot_size;
                        }
                        
                        $cluster_quality = round(($cluster_data['total_score'] / $cluster_data['num_docs']), 2);
                        
                        for($i = 0; $i <= $this->slider_increments; $i++) {
                            if($cluster_data['num_docs'] <= $slider_cutoffs[$i]) {
                                $cluster_ids[$i] .= "$cluster_id,";
                                $cluster_sizes[$i] .= $cluster_data['num_docs'].",";
                                $x_vals[$i] .= "$num_clusters,";
                                $y_vals[$i] .= "$cluster_quality,";
                                $size_vals[$i] .= "$dot_size_normalized,";
                                
                                // to do: make this section cleaner:
                                // get color of cluster (for testing negative keywords)
                                if($cluster_data['num_docs'] == $cluster_data['num_marked']) {
                                    $color_vals[$i] .= "ff0000|";
                                } elseif($cluster_data['num_marked'] > 0) {
                                    $hex_value = $this->percentage_to_hex_color($cluster_data['num_marked'] / $cluster_data['num_docs']);
                                    $color_vals[$i] .= "$hex_value|";
                                } else {
                                    $color_vals[$i] .= "0000ff|";
                                }
                            }
                        }
                    } else {
                        if($cluster_data['num_marked'] > 0)
                            $singleton_cluster_marked = 1;
                    }  
                }
                
                if(!is_dir($chart_dir)) { mkdir($chart_dir); } // or die ("$chart_dir: cannot create directory");
                $x_axis_midpoint = round(($num_clusters/2));
                for($i = 0; $i <= $this->slider_increments; $i++) {
                    // Remove trailing commas
                    $x_vals[$i] = substr($x_vals[$i], 0, -1); $y_vals[$i] = substr($y_vals[$i], 0, -1); $size_vals[$i] = substr($size_vals[$i], 0, -1); $color_vals[$i] = substr($color_vals[$i], 0, -1); $cluster_ids[$i] = substr($cluster_ids[$i], 0, -1); $cluster_sizes[$i] = substr($cluster_sizes[$i], 0, -1); 
                    
                    $chart_data = array(
                        "type" => "s",
                        "axes" => "x,x,y,y",
                        "axis_labels" => "1:|Cluster number|3:|Lowest quality|Highest quality",
                        "label_pos" => "1,50|3,0,100",
                        "size" => $this->chart_w."x".$this->chart_h, // `width` x `height` (in px)
                        "range" => "0,$num_clusters,0,1,1,100", // min,max(x-axis), min,max(y-axis), min,max(dot size)
                        "range_display" => "0,1,$num_clusters|2,0,1", // axis_id,min,max|...
                        "dot_style" => "o,0000FF,0,,80",
                        "data" => "t:".$x_vals[$i]."|".$y_vals[$i]."|".$size_vals[$i], // x-values | y-values | dot size (0-100)
                        "colors" => $color_vals[$i]
                    );
                    
                    // Save chart data as text file (.gch) to be read by show_chart.php
                    $fh_chartfile = fopen("$chart_dir/scatter$i.gch", 'w') or die("$chart_dir/scatter$i.gch: cannot open file for writing");
                    fwrite($fh_chartfile, "cht=".$chart_data['type']."\nchs=".$chart_data['size']."\nchxt=".$chart_data['axes']."\nchxl=".$chart_data['axis_labels']."\nchxp=".$chart_data['label_pos']."\nchds=".$chart_data['range']."\nchxr=".$chart_data['range_display']."\nchm=".$chart_data['dot_style']."\nchd=".$chart_data['data']."\nchco=".$chart_data['colors']."\nmpids=$cluster_ids[$i]\nmps=$cluster_sizes[$i]");
                    fclose($fh_chartfile);
                }
            }
            
            // Generate HTML for charts
            for($i = 0; $i <= $this->slider_increments; $i++) {
                $chid = md5(uniqid(rand(), true)); // Chart ID sent to Google Chart API
                $chart_html[$i] = '<div><img src="'.Kohana::config('myconf.url.show_chart').'?datafile='.$chart_dir.'/scatter'.$i.'.gch&chid='.$chid.'" width="'.$this->chart_w.'" height="'.$this->chart_h.'" class="mapper" usemap="#chart_map'.$i.'"></div>
<map name="chart_map'.$i.'">'.$this->generate_cluster_map($project_id, $chid, "$chart_dir/scatter$i.gch").'</map>';
            }
            
            $view->page_content->project_data = $project_data;
            $view->page_content->field_data = $this->field_data;
            $view->page_content->cluster_params = $cluster_params;
            $view->page_content->total_results = $total_results;
            $view->page_content->singleton_clusters = $singleton_clusters;
            $view->page_content->singleton_cluster_marked = $singleton_cluster_marked;
            $view->page_content->chart_html = $chart_html;
            $view->page_content->slider = $slider;
            $this->request->response = $view;
        }
    }
    
    public function action_cluster_time($project_id = 0)
    {
        $project_data = $this->model_results->get_project_data($project_id);
        if(count($project_data) == 0) {
            echo "<p>Project with this ID does not exist.</p>"; 
        } else {
            $project_data = array_pop($project_data);
            
            $view = View::factory('template');
            $view->page_title = $project_data['project_title']." - View clusters over time";
            
            $view->page_content = View::factory('pages/cluster_time');
            
            // Default results display
            $result_params = array(
                'date_from' => 0, 'date_to' => 0,
                'cluster_threshold' => $this->default_cluster_threshold,
                'cluster_order' => 'arbitrarily'
            );
            
            if($_GET) 
                $_POST = $_GET;
            $form_errors = "";
            if($_POST) {
                $post = Validate::factory($_POST);
                $post->rule('date_from', 'max_length', array(10))
                     ->rule('date_to', 'max_length', array(10));
                 
                $this->field_data = $post->as_array(); // For form re-population
                if ($post->check()) {
                    $date_from_ex = explode("/", $this->field_data['date_from']);
                    $date_to_ex = explode("/", $this->field_data['date_to']);
                    if(count($date_from_ex) == 3 AND $date_from_ex[0] > 0 AND $date_from_ex[1] > 0 AND $date_from_ex[2] > 0)
                        $result_params['date_from'] = mktime(0, 0, 0, $date_from_ex[0], $date_from_ex[1], $date_from_ex[2]);
                    if(count($date_to_ex) == 3 AND $date_to_ex[0] > 0 AND $date_to_ex[1] > 0 AND $date_to_ex[2] > 0)
                        $result_params['date_to'] = mktime(0, 0, 0, $date_to_ex[0], $date_to_ex[1]+1, $date_to_ex[2]); // Add +1 to day so it searches THROUGH given "To" date
                    
                    // do entire range if no date range entered
                    if($result_params['date_from'] == 0)
                        $result_params['date_from'] = $this->model_results->metadata_edge_date($project_id, 'oldest');
                    if($result_params['date_to'] == 0)
                        $result_params['date_to'] = $this->model_results->metadata_edge_date($project_id, 'most_recent');
                    
                    $result_params['cluster_threshold'] = $this->field_data['cluster_threshold'];
                    $result_params['cluster_order'] = $this->field_data['cluster_order'];
                } else { 
                    $form_errors = $post->errors('results');
                } 
            } else {
                // Populate form w/ empty values
                $this->field_data = $result_params;
            }
            
            // Delete ALL old time plot clusters
            $this->model_results->delete_clusters_time($project_id);
            
            $day_interval = 1;
            $time_increment = $day_interval * 24 * 60 * 60; // Increment by `$day_interval` day(s)
            
            // Find max & min cluster sizes (for normalization)
            $last_chart_i = ceil( ($result_params['date_to'] - $result_params['date_from']) / ($day_interval * 24 * 60 * 60) );
            $this->action_cluster($project_id, $result_params['cluster_threshold'], $result_params['date_from'], $result_params['date_to'], $last_chart_i, 0);
            
            $last_chart_clusters = $this->get_cluster_metadata($project_id, $last_chart_i); 
            $clusters_sorted_asc = $this->order_array_numeric($last_chart_clusters, 'num_docs', 'ASC');
            $min_cluster_size_data = current($clusters_sorted_asc);
            $min_cluster_size = $min_cluster_size_data['num_docs'];
            $max_cluster_size_data = end($clusters_sorted_asc);
            $max_cluster_size = $max_cluster_size_data['num_docs'];
            unset($clusters_sorted_asc); // ..To save memory
            
            // Starting w/ 1st date cluster data & advance at specified day interval (in # of days)
            $i = 1; // SAME AS: $time_plot_id
            $current_end_date = $result_params['date_from'];
            $slider_end_dates = array();
            do {
                $current_end_date += $time_increment; 
                $slider_end_dates[$i] = date(Kohana::config('myconf.date_format'), $current_end_date - 1);
                
                if($i == $last_chart_i) {
                    $clusters = $last_chart_clusters;
                } else {
                    $this->action_cluster($project_id, $result_params['cluster_threshold'], $result_params['date_from'], $current_end_date, $i, 0);
                    $clusters = $this->get_cluster_metadata($project_id, $i);
                }
                
                // Check if user selected to order points by cluster size
                if($result_params['cluster_order'] == 'cluster_size')
                    $clusters = $this->order_array_numeric($clusters, 'num_docs', 'ASC');
                unset($clusters_sorted_asc); // ..To save memory
                
                // Get chart data & params
                $chart_dir = Kohana::config('myconf.path.charts')."/cluster_time_$project_id";
                
                //if(!is_dir($chart_dir)) {
                
                // Generate chart data
                $num_clusters = 0;
                $x_vals[$i] = ""; $y_vals[$i] = ""; $size_vals[$i] = ""; $color_vals[$i] = ""; $cluster_ids[$i] = ""; $cluster_sizes[$i] = "";
                
                foreach($clusters as $cluster_id => $cluster_data) {
                    if($cluster_data['num_docs'] > 1) {
                        $num_clusters++;
                        
                        // Normalize dot sizes they are between 0-100
                        if($max_cluster_size == $min_cluster_size) {
                            $dot_size_normalized = 50; // All clusters have size 1
                        } else {
                            $dot_size_normalized = (round((($cluster_data['num_docs'] - $min_cluster_size)/($max_cluster_size - $min_cluster_size)), 2))*100;
                            if($dot_size_normalized < $this->min_dot_size)
                                $dot_size_normalized = $this->min_dot_size;
                        }
                        
                        $cluster_quality = round(($cluster_data['total_score'] / $cluster_data['num_docs']), 2);
                        $cluster_ids[$i] .= "$cluster_id,";
                        $cluster_sizes[$i] .= $cluster_data['num_docs'].",";
                        $x_vals[$i] .= "$num_clusters,";
                        $y_vals[$i] .= "$cluster_quality,";
                        $size_vals[$i] .= "$dot_size_normalized,";
                        /*
                        **** Might be cool later to see clusters change w/ clusters containing certain keywords being highlighted (+ shade according to percentage of keyword)
                        *
                        // to do: make this section cleaner:
                        // get color of cluster (for testing negative keywords)
                        if($cluster_data['num_docs'] == $cluster_data['num_marked']) {
                            $color_vals[$i] .= "ff0000|";
                        } elseif($cluster_data['num_marked'] > 0) {
                            $hex_value = $this->percentage_to_hex_color($cluster_data['num_marked'] / $cluster_data['num_docs']);
                            $color_vals[$i] .= "$hex_value|";
                        } else {
                            $color_vals[$i] .= "0000ff|";
                        }*/
                        $color_vals[$i] .= "0000ff|";
                            
                    } else {
                        //if($cluster_data['num_marked'] > 0)
                            //$singleton_cluster_marked = 1;
                    }
                }
                // Remove trailing commas
                $x_vals[$i] = substr($x_vals[$i], 0, -1); $y_vals[$i] = substr($y_vals[$i], 0, -1); $size_vals[$i] = substr($size_vals[$i], 0, -1); $color_vals[$i] = substr($color_vals[$i], 0, -1); $cluster_ids[$i] = substr($cluster_ids[$i], 0, -1); $cluster_sizes[$i] = substr($cluster_sizes[$i], 0, -1); 
                
                $chart_data = array(
                    "type" => "s",
                    "axes" => "x,x,y,y",
                    "axis_labels" => "1:|Cluster number|3:|Lowest quality|Highest quality",
                    "label_pos" => "1,50|3,0,100",
                    "size" => $this->chart_w."x".$this->chart_h, // `width` x `height` (in px)
                    "range" => "0,$num_clusters,0,1,1,100", // min,max(x-axis), min,max(y-axis), min,max(dot size)
                    "range_display" => "0,1,$num_clusters|2,0,1", // axis_id,min,max|...
                    "dot_style" => "o,0000FF,0,,80",
                    "data" => "t:".$x_vals[$i]."|".$y_vals[$i]."|".$size_vals[$i], // x-values | y-values | dot size (0-100)
                    "colors" => $color_vals[$i]
                );
                
                // Save chart data as text file (.gch) to be read by show_chart.php
                if(!is_dir($chart_dir))
                    mkdir($chart_dir);
                $fh_chartfile = fopen("$chart_dir/scatter$i.gch", 'w') or die("$chart_dir/scatter$i.gch: cannot open file for writing");
                fwrite($fh_chartfile, "cht=".$chart_data['type']."\nchs=".$chart_data['size']."\nchxt=".$chart_data['axes']."\nchxl=".$chart_data['axis_labels']."\nchxp=".$chart_data['label_pos']."\nchds=".$chart_data['range']."\nchxr=".$chart_data['range_display']."\nchm=".$chart_data['dot_style']."\nchd=".$chart_data['data']."\nchco=".$chart_data['colors']."\nmpids=$cluster_ids[$i]\nmps=$cluster_sizes[$i]");
                fclose($fh_chartfile);
                
                //}
                
                // Generate HTML for each chart
                $chid = md5(uniqid(rand(), true)); // Chart ID sent to Google Chart API
                $chart_html[$i] = '<div><img src="'.Kohana::config('myconf.url.show_chart').'?datafile='.$chart_dir.'/scatter'.$i.'.gch&chid='.$chid.'" width="'.$this->chart_w.'" height="'.$this->chart_h.'" class="mapper" usemap="#chart_map'.$i.'"></div>
<map name="chart_map'.$i.'">'.$this->generate_cluster_map($project_id, $chid, "$chart_dir/scatter$i.gch", $i).'</map>';
                $i++; 
            } while(($current_end_date + 1) < ($result_params['date_to']));
            
            // Calculate slider properties
            $slider['increments'] = $i; // For passing to view
            $slider['min'] = 1;
            $slider['max'] = $i-1;
            $slider['step'] = $day_interval;
            
            $view->page_content->project_data = $project_data;
            $view->page_content->field_data = $this->field_data;
            //$view->page_content->total_results = $total_results;
            //$view->page_content->singleton_clusters = $singleton_clusters;
            //$view->page_content->singleton_cluster_marked = $singleton_cluster_marked;
            $view->page_content->chart_html = $chart_html;
            $view->page_content->slider_end_dates = $slider_end_dates;
            $view->page_content->slider = $slider;
            $this->request->response = $view;
        }
    }
    
    private function percentage_to_hex_color($decimal)
    {
        $theColorBegin = 0x0000ff;
        $theColorEnd = 0xff0000;
        $theNumSteps = 100;
        
        $percent_val = round($decimal, 2) * 100; // Get percentage as a number between 0 and 100
        //if($percent_val == 1)
            //return sprintf("%06X", $theColorEnd);
        
        $theR0 = ($theColorBegin & 0xff0000) >> 16;
        $theG0 = ($theColorBegin & 0x00ff00) >> 8;
        $theB0 = ($theColorBegin & 0x0000ff) >> 0;
        $theR1 = ($theColorEnd & 0xff0000) >> 16;
        $theG1 = ($theColorEnd & 0x00ff00) >> 8;
        $theB1 = ($theColorEnd & 0x0000ff) >> 0;
        
        $theR = $this->interpolate($theR0, $theR1, $percent_val, $theNumSteps);
        $theG = $this->interpolate($theG0, $theG1, $percent_val, $theNumSteps);
        $theB = $this->interpolate($theB0, $theB1, $percent_val, $theNumSteps);
        $theVal = ((($theR << 8) | $theG) << 8) | $theB;
        $hex_color = sprintf("%06X", $theVal);
        return $hex_color;
    }
    // Helper function for percentage_to_hex_color
    private function interpolate($pBegin, $pEnd, $pStep, $pMax) {
        if ($pBegin < $pEnd)
            return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
        else
            return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
    }
    
    // Get clustering data & re-organize results so there is only one row per metadata entry & calculate stats
    private function get_cluster_metadata($project_id, $time_plot_id = 0) 
    {
        $cluster_db = $this->model_results->get_clusters($project_id, $time_plot_id);
        $clusters = array();
        foreach($cluster_db as $row) {
            if(array_key_exists($row['cluster_id'], $clusters)) {
                // Add cluster data to existing cluster entry
                array_push($clusters[$row['cluster_id']]['docs'], array($row['meta_id'] => $row['score']));
                $clusters[$row['cluster_id']]['total_score'] += $row['score'];
                $clusters[$row['cluster_id']]['num_docs']++;
            } else {
                // Add new cluster entry
                $clusters[$row['cluster_id']] = array(
                    'docs' => array($row['meta_id'] => $row['score']),
                    'total_score' => $row['score'],
                    'num_docs' => 1,
                    'num_marked' => 0
                );
            }
            if($this->model_results->document_is_marked($row['meta_id']))
                $clusters[$row['cluster_id']]['num_marked']++;
        }
        return $clusters;
    }
    
    // Generate chart image map HTML for cluster plot (make plot clickable)
    private function generate_cluster_map($project_id, $chid, $chart_file, $time_plot_id = 0) 
    {
        $api_url = $this->chart_api_url."?chid=$chid";

        // Open chart file and extract data
        $file_handle = fopen($chart_file, "r");
        $chart_params = array();
        while (!feof($file_handle)) {
            $line = rtrim(fgets($file_handle));
            $param_ex = explode("=", $line);
            $param_name = $param_ex[0]; 
            $param_vals = $param_ex[1];
            if($param_name == "mpids") { 
                // List of cluster_ids in order displayed on chart
                $cluster_ids = explode(",", $param_vals);
            } else if($param_name == "mps") {
                // List of cluster sizes (number of documents) in order displayed on chart
                $cluster_sizes = explode(",", $param_vals);
            } else { 
                // Parameter is chart param 
                $chart_params[$param_name] = $param_vals;
                if($param_name == "chd") {
                    $chd_ex = explode("|", substr($param_vals, 2));
                    $cluster_scores = explode(",", $chd_ex[1]);
                }
            }
        }
        fclose($file_handle);
        $chart_params['chof'] = 'json'; // tell API to return image map HTML
        
        // Send the POST request, parse json data, & compile image map HTML
        $response = Remote::get($api_url, array(
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($chart_params)
        ));
        
        $image_map_html = '';
        $json = json_decode($response, true);
        $num_results = count($json['chartshape']);
        if($num_results > 0) {
            $i = 0;
            foreach($json['chartshape'] as $map_item) {
                if($map_item['type'] == "CIRCLE") {
                    $coords_str = implode(",", $map_item['coords']);
                    $title = $cluster_sizes[$i]." documents (score: ".$cluster_scores[$i].")";
                    $href = "javascript:startLyteframe('".$title."', '".Url::base()."index.php/results/cluster_summary/$project_id?cluster_id=".$cluster_ids[$i]."&time_plot_id=$time_plot_id')";
                    $image_map_html .= '<area name="'.$map_item['name'].'" shape="'.$map_item['type'].'" class="noborder icolorff0000" coords="'.$coords_str.'" href="'.$href.'" title="'.$title.'">';
                    $i++;
                }
            }
        }
        return $image_map_html;
    }
    
    public function action_singleton_clusters($project_id = 0)
    {
        $view = View::factory('pages/cluster_text');
        
        $clusters = $this->get_cluster_metadata($project_id);
        $singleton_clusters = array();
        foreach($clusters as $cluster) {
            if($cluster['num_docs'] == 1) {
                $meta_id = key($cluster['docs']);
                $text = $this->model_results->get_cached_text($meta_id);
                array_push($singleton_clusters, array(
                    'meta_id' => $meta_id,
                    'text' => $text,
                    'marked' => $this->model_results->document_is_marked($meta_id)
                ));
            }
        }
        
        $view->singleton_display = 1;
        $view->cluster_data = $singleton_clusters;
        $this->request->response = $view;
    }
    
    public function action_cluster_summary($project_id)
    {
        $cluster_id = ($_GET['cluster_id'] > 0) ? $_GET['cluster_id'] : 0;
        $time_plot_id = ($_GET['time_plot_id'] > 0) ? $_GET['time_plot_id'] : 0;
        $view = View::factory('pages/cluster_text');
        
        // Default results display
        $params = array(
            'num_results' => 'all',
            'score_order' => 'desc'
        );
        
        $form_errors = "";
        if($_POST) {
            $post = Validate::factory($_POST);
            $field_data = $post->as_array(); // For form re-population
            
            // TO DO: Form validation
            //if ($post->check()) { } else { $form_errors = $post->errors('results'); }
            
            // Process results display parameters
            $params['num_results'] = $field_data['num_results'];
            $params['score_order'] = strtoupper($field_data['score_order']);
        } else {
            // Populate form w/ empty values
            $field_data = array(
                'num_results' => $params['num_results'],
                'score_order' => $params['score_order']
            );
        } 
        
        // Count # of docs in each identical/exact cluster & collapse into single meta_id
        $clusters_identical = array();
        $cluster_data = $this->model_results->get_cluster_summary_exact($project_id, $cluster_id, $params, $time_plot_id);
        $i = 0;
        $last_cluster_id = 0;
        $last_meta_id = 0;
        foreach($cluster_data as $cluster) {
            if($cluster['cluster_id_exact'] != $last_cluster_id) {
                $clusters_identical[$cluster['meta_id']] = array($cluster['text']);
                $last_cluster_id = $cluster['cluster_id_exact'];
                $last_meta_id = $cluster['meta_id'];
            } else {
                array_push($clusters_identical[$last_meta_id], $cluster['text']);
                $clusters_identical[$cluster['meta_id']] = 0;
            }
            $i++;
        }
        
        $cluster_data = $this->model_results->get_cluster_summary($project_id, $cluster_id, $params, $time_plot_id);
        $i = 0;
        foreach($cluster_data as $cluster) {
            if($clusters_identical[$cluster['meta_id']] > 0) {
                $cluster_data[$i]['marked'] = $this->model_results->document_is_marked($cluster['meta_id']);
                $cluster_data[$i]['identical_clusters'] = $clusters_identical[$cluster['meta_id']];
            } else {
                unset($cluster_data[$i]);
            }
            $i++;
        }
        
        $view->singleton_display = 0;
        $view->field_data = $field_data;
        $view->errors = $form_errors;
        $view->cluster_data = $cluster_data;
        $this->request->response = $view;
    }
    
    function parse_urls($text, $maxurl_len = 35, $target = '_blank')
    {
        if(preg_match_all('/((ht|f)tps?:\/\/([\w\.]+\.)?[\w-]+(\.[a-zA-Z]{2,4})?[^\s\r\n\(\)"\'<>\,\!]+)/si', $text, $urls)) {
            $offset1 = ceil(0.65 * $maxurl_len) - 2;
            $offset2 = ceil(0.30 * $maxurl_len) - 1;
            foreach (array_unique($urls[1]) AS $url) {
                if ($maxurl_len AND strlen($url) > $maxurl_len)
                    $urltext = substr($url, 0, $offset1) . '...' . substr($url, -$offset2);
                else
                    $urltext = $url;
                
                $text = str_replace($url, '<a href="'. $url .'" target="'. $target .'" title="'. $url .'">'. $urltext .'</a>', $text);
            }
        }
        return $text;
    }
    
    private function order_array_numeric($array, $key, $order = "ASC") 
    { 
        $tmp = array(); 
        foreach($array as $akey => $array2) { 
            $tmp[$akey] = $array2[$key]; 
        } 
        
        if($order == "DESC")
            arsort($tmp, SORT_NUMERIC);
        else 
            asort($tmp, SORT_NUMERIC);

        $tmp2 = array();
        foreach($tmp as $key => $value) { 
            $tmp2[$key] = $array[$key]; 
        }
        return $tmp2; 
    } 
}