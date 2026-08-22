<?php
return ['default'=>env('QUEUE_CONNECTION','database'),'connections'=>['database'=>['driver'=>'database','connection'=>null,'table'=>'jobs','queue'=>'default','retry_after'=>10,'after_commit'=>true],'sync'=>['driver'=>'sync']],'failed'=>['driver'=>'database-uuids','database'=>null,'table'=>'failed_jobs']];
