#!/usr/bin/php -q
<?php

require_once( dirname( __FILE__ ) . '/../bootstrap.php' );
require_once( 'CTM/Test/Runner.php' );

$test_runner_obj = new CTM_Test_Runner();
$test_runner_obj->run();
