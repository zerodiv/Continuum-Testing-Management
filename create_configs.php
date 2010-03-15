#!/usr/bin/php -q
<?php

// creates static impl classes with the configuration variables pre filled in.
$config_file = './etc/config.ini';

if ( ! is_file( $config_file ) ) {
   echo 'no such file: ' . $this->_config_file . "\n";
   exit();
}

if ( ! is_readable( $config_file ) ) {
   echo 'unable to read: ' . $this->_config_file . "\n";
   exit();
}

$config = parse_ini_file( $config_file, true );

foreach ( $config as $config_class as $config_values ) {
   echo "config_class: $config_class\n";
}

/*
                 $config_class_name = $class . '_Config';
                 
fwrite( $fh, 'class ' . $config_class_name . ' {' . "\n" );
foreach ( $this->_config[ $class ] as $config_var => $config_val ) {
fwrite( $fh, '   public static function ' . strtoupper( $config_var ) . '() { return ' );
fwrite( $fh, str_replace( "\n", '', var_export( $config_val, true ) ) );
fwrite( $fh, "; }\n" );
}
fwrite( $fh, '}' . "\n" );
*/
