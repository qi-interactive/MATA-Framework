<?php

/**
* @link http://www.matacms.com/
* @copyright Copyright (c) 2015 Qi Interactive Limited
* @license http://www.matacms.com/license/
*/

namespace mata\helpers;

class CommandLineHelper {

    /**
     * Executes a command using exec()
     * Returns command output as [$output]
     * Command return code can be found in [$returnValue]
     * Command can be run in a directory by specifying [$runInDir]
     */
    public static function execute($command, &$output, &$returnValue, $runInDir = null)
    {
        $cwd = getcwd();
        chdir(Yii::getAlias("@runtime/client-tests"));
        exec($command, $output, $returnValue);
        chdir($cwd);
    }
}
