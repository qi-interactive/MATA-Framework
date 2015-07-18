<?php

/**
* @link http://www.matacms.com/
* @copyright Copyright (c) 2015 Qi Interactive Limited
* @license http://www.matacms.com/license/
*/

namespace mata\helpers;

class CommandLineHelper {

    /**
     * Executes a command using exec() in a dir specified
     * Returns command output as [$output]
     * Command return code can be found in [$returnValue]
     */
    public static function executeInDir($command, $runInDir, &$output, &$returnValue)
    {
        $cwd = getcwd();
        chdir($runInDir);
        self::execute($command, $output, $returnValue);
        chdir($cwd);
    }

    /**
     * Executes a command using exec()
     * Returns command output as [$output]
     * Command return code can be found in [$returnValue]
     */
    public static function execute($command, &$output, &$returnValue) {
        exec($command, $output, $returnValue);
    }
}
