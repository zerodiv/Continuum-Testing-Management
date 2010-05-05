<?php
class CTM_Config {
   public static function BASE_DIR() { return dirname(__FILE__) . '/../..'; }
   public static function SUITE_DIR() { return sys_get_temp_dir() . '/ctm_test_runs'; }
   public static function DEFAULT_TIMEZONE() { return 'America/Los_Angeles'; }
   public static function TIME_FORMAT() { return 'Y/m/d H:i'; }
}
