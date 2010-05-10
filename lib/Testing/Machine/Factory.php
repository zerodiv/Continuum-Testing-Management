<?php

class Testing_Machine_Factory
{
    /**
     * Factory method to create machine instances
     *
     * @todo Figure out mac detection
     * @return Testing_Machine
     */
    public static function factory()
    {
        $os = php_uname('s');

        if (stripos($os, 'win') !== false) {
            require_once 'Testing/Machine/Windows.php';
            return new Testing_Machine_Windows();
        }

        if (stripos($os, 'linux') !== false) {
            require_once 'Testing/Machine/Linux.php';
            return new Testing_Machine_Linux();
        }

        if (stripos($os, 'mac') !== false) {  // ?????????????
            require_once 'Testing/Machine/Mac.php';
            return new Testing_Machine_Mac();
        }

        throw new Exception('Could not identify OS.');
    }

}

?>
