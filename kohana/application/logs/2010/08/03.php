<?php defined('SYSPATH') or die('No direct script access.'); ?>

2010-08-03 00:45:28 --- ERROR: ErrorException [ 64 ]: Cannot redeclare Model_Results::metadata_edge_date() ~ APPPATH/classes/model/results.php [ 49 ]
2010-08-03 00:46:18 --- ERROR: Database_Exception [ 1054 ]: Unknown column &#039;meta_id&#039; in &#039;field list&#039; [ SELECT COUNT(meta_id) AS `total` FROM `metadata_urls` WHERE (`project_id` = 12 AND `date_published` &gt;= 1278831600 AND `date_published` &lt; 1278918000) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 178 ]
2010-08-03 00:47:35 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 64 bytes) ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2010-08-03 00:47:45 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 64 bytes) ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2010-08-03 08:43:44 --- ERROR: ErrorException [ 1 ]: Allowed memory size of 16777216 bytes exhausted (tried to allocate 64 bytes) ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2010-08-03 08:44:16 --- ERROR: Database_Exception [ 1146 ]: Table &#039;project_aware.metadata, cached_text, keyword_metadata&#039; doesn&#039;t exist [ DELETE FROM `metadata, cached_text, keyword_metadata` WHERE `metadata`.`project_id` = &#039;keyword_metadata.meta_id&#039; AND `metadata`.`meta_id` = &#039;cached_text.meta_id&#039; AND `metadata`.`project_id` = &#039;1&#039; ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 178 ]
2010-08-03 08:46:06 --- ERROR: ErrorException [ 1 ]: Call to undefined method Database_Query_Builder_Delete::delete() ~ APPPATH/classes/model/params.php [ 18 ]
2010-08-03 08:52:44 --- ERROR: Database_Exception [ 1109 ]: Unknown table &#039;metadata&#039; in MULTI DELETE [ DELETE metadata.*, cached_text.*, keyword_metadata.* FROM metadata m, cached_text c, keyword_metadata k WHERE (m.meta_id = c.meta_id AND m.meta_id = k.meta_id AND m.project_id = 1) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 178 ]