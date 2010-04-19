<?php

class Light_CommandLine_Option_Type {
   public static function TYPE_BOOLEAN() { return 'bool'; }
   public static function TYPE_STRING() { return 'string'; }
   public static function GET_ALL_TYPES() {
      return array( 
            Light_CommandLine_Option_Type::TYPE_BOOLEAN,
            Light_CommandLine_Option_Type::TYPE_STRING
      );
   }
}
