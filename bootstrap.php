<?php

// --------------------------------------------------------------------------------
// Default boostrap.php
//
// This file configures the php environment to include our include path. If your php
// environment does not allow modifying the include path at runtime you will need to 
// modify your php.ini include_path settings accordingly.
// --------------------------------------------------------------------------------
$include_path = get_include_path();
set_include_path( dirname(__FILE__) . '/lib:' . $include_path );
