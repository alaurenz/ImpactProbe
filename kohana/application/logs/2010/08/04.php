<?php defined('SYSPATH') or die('No direct script access.'); ?>

2010-08-04 09:04:13 --- ERROR: ErrorException [ 2 ]: fopen(/home/adrian/Documents/GSoC_2010/src/ProjectAware/lemur/docs/19/13036.txt): failed to open stream: Permission denied ~ APPPATH/classes/model/gather.php [ 49 ]
2010-08-04 09:10:07 --- ERROR: ErrorException [ 2 ]: chmod(): Operation not permitted ~ APPPATH/classes/model/gather.php [ 44 ]
2010-08-04 10:09:28 --- ERROR: ErrorException [ 2 ]: mkdir(): File exists ~ APPPATH/classes/model/gather.php [ 44 ]
2010-08-04 10:17:21 --- ERROR: ReflectionException [ -1 ]: Class controller_delete does not exist ~ SYSPATH/classes/kohana/request.php [ 1007 ]
2010-08-04 10:17:55 --- ERROR: ErrorException [ 1 ]: Call to undefined method Model_Params::create_cached_text_dir() ~ APPPATH/classes/controller/params.php [ 48 ]
2010-08-04 10:19:55 --- ERROR: ErrorException [ 8 ]: Undefined property: Controller_Params::$model_gather ~ APPPATH/classes/controller/params.php [ 48 ]
2010-08-04 11:00:34 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 128 bytes) ~ MODPATH/database/classes/kohana/database/mysql/result.php [ 67 ]
2010-08-04 11:00:52 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 128 bytes) ~ MODPATH/database/classes/kohana/database/mysql/result.php [ 67 ]
2010-08-04 11:00:56 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 75 bytes) ~ MODPATH/database/classes/kohana/database/mysql/result.php [ 67 ]
2010-08-04 11:01:16 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 75 bytes) ~ MODPATH/database/classes/kohana/database/mysql/result.php [ 67 ]
2010-08-04 11:13:48 --- ERROR: Database_Exception [ 1051 ]: Unknown table &#039;keyword_metadata&#039; [ SELECT `metadata`.*, `keyword_metadata`.*, `metadata_urls`.`url`, `api_sources`.`api_name` FROM `metadata` JOIN `metadata_urls` ON (`metadata`.`url_id` = `metadata_urls`.`url_id`) JOIN `api_sources` ON (`metadata`.`api_id` = `api_sources`.`api_id`) WHERE `metadata`.`project_id` = &#039;12&#039; ORDER BY `metadata`.`date_published` DESC, `keyword_metadata`.`meta_id` LIMIT 100 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 178 ]
2010-08-04 11:24:14 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 590052 bytes) ~ SYSPATH/classes/kohana/view.php [ 73 ]
2010-08-04 11:25:20 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 590052 bytes) ~ SYSPATH/classes/kohana/view.php [ 73 ]
2010-08-04 11:25:31 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 590052 bytes) ~ SYSPATH/classes/kohana/view.php [ 73 ]
2010-08-04 11:25:56 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 590052 bytes) ~ SYSPATH/classes/kohana/view.php [ 73 ]
2010-08-04 11:30:24 --- ERROR: ErrorException [ 8 ]: Undefined variable: i ~ APPPATH/classes/controller/results.php [ 85 ]
2010-08-04 11:45:13 --- ERROR: ErrorException [ 8 ]: Undefined variable: total_results ~ APPPATH/views/pages/results_basic.php [ 75 ]
2010-08-04 12:30:22 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 202 bytes) ~ SYSPATH/classes/kohana/profiler.php [ 44 ]