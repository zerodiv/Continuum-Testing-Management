<?php
class Light_MVC_Config {
   public static function DEFAULT_TIMEZONE() { return 'America/Los_Angeles'; }
   public static function TIME_FORMAT() { return 'Y/m/d H:i'; }
   public static function BASE_DIR() { return dirname(__FILE__) . '/../../../web'; }
   public static function BASE_URL() { return 'http://continuum.localhost'; }
   public static function SITE_TITLE() { return 'Continuum Test Management (CTM)'; }
   public static function SESSION_NAME() { return 'ctm_session'; }
   public static function CSS_FILES() { return array (  0 => 'common.css',); }
}
