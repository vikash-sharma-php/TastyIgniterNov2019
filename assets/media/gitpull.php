<?php
$command = 'git stash && git pull origin master && chmod -R 777 storage/ extensions/ bootstrap/';
exec($command . ' 2>&1', $output, $return_var);
ob_flush();
ob_start();
var_dump($output);
file_put_contents("../../storage/logs/git.log", ob_get_flush());
