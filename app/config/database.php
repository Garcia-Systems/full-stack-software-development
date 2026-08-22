<?php
return ['default' => env('DB_CONNECTION', 'mysql'), 'connections' => [
'mysql' => ['driver'=>'mysql','host'=>env('DB_HOST','db'),'port'=>env('DB_PORT','3306'),'database'=>env('DB_DATABASE','relaydesk'),'username'=>env('DB_USERNAME','relaydesk'),'password'=>env('DB_PASSWORD','relaydesk'),'charset'=>'utf8mb4','collation'=>'utf8mb4_unicode_ci','prefix'=>'','strict'=>true],
'sqlite' => ['driver'=>'sqlite','database'=>env('DB_DATABASE', database_path('database.sqlite')),'prefix'=>'','foreign_key_constraints'=>true],
], 'migrations'=>'migrations'];
