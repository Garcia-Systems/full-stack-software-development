<?php
return ['dependency'=>['url'=>env('DEPENDENCY_URL','http://127.0.0.1:8090'),'connect_timeout'=>(float)env('DEPENDENCY_CONNECT_TIMEOUT',0.3),'timeout'=>(float)env('DEPENDENCY_TIMEOUT',1.0)]];
