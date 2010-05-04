#!/usr/bin/php -q
<?php

// creates static impl classes with the configuration variables pre filled in.
$config_file = dirname( __FILE__ ) . '/../etc/config.ini';

if ( ! is_file( $config_file ) ) {
   echo 'no such file: ' . $this->_config_file . "\n";
   exit();
}

if ( ! is_readable( $config_file ) ) {
   echo 'unable to read: ' . $this->_config_file . "\n";
   exit();
}

$config = parse_ini_file( $config_file, true );

// create and load the ctm class so that we can do evals on the further template items.
foreach ( $config as $config_class => $config_values ) {
   if ( $config_class == 'CTM_Config' || $config_class == 'CTM_Site_Config' ) {
      foreach ( $config_values as $config_name => $config_value ) {
         define( $config_class . '_' . strtoupper($config_name), $config_value );
      }
   }
}

// reparse the config file to get all the constants interpolated
$config = parse_ini_file( $config_file, true );

foreach ( $config as $config_class => $config_values ) {
   $class_file = writeToFile( $config_class, $config_values );
}

echo "Done!\n";
exit();

function writeToFile( $config_class, $config_values ) {
   echo "config_class: $config_class\n";

   $class_file = convertClassToFile( $config_class );
   echo "class_file  : $class_file\n";

   // now spool all of this to disk
   $fh = fopen( $class_file, 'w' );

   if ( ! is_resource( $fh ) ) {
      echo "Failed to open: $class_file\n";
      exit();
   }

   fwrite( $fh, '<?php' . "\n" );

   fwrite( $fh, 'class ' . $config_class . ' {' . "\n" );
   foreach ( $config_values as $config_var => $config_val ) {
      fwrite( $fh, '   public static function ' . strtoupper( $config_var ) . '() { return ' );
      fwrite( $fh, str_replace( "\n", '', var_export( $config_val, true ) ) );
      fwrite( $fh, "; }\n" );
   }

   fwrite( $fh, '}' . "\n" );

   return $class_file;

}

function convertClassToFile( $class ) {
   $file_name = $class;
   $file_name = str_replace( '_', '/', $class );
   $file_name = dirname( __FILE__ ) . '/../lib/' . $file_name . '.php';
   return $file_name;
}
