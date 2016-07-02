<?php
/**
 * This file is part of the GMaissa Behat Contexts package
 *
 * @package   GMaissa\BehatContexts
 * @author    Guillaume Maïssa <guillaume@maissa.fr>
 * @copyright 2016 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT MIT
 */

$env         = getenv("APPLICATION_ENV") !== false ? getenv("APPLICATION_ENV") : 'prod' ;
$allowedEnvs = ['test', 'behat'];

if (in_array($env, $allowedEnvs)) {
    $rootDir = dirname(dirname(dirname(dirname(__DIR__))));

    require $rootDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    $coverage = new PHP_CodeCoverage;
    $filter   = $coverage->filter();
    $filter->addFileToBlacklist(__FILE__);
    $filter->addFileToBlacklist(__DIR__ . DIRECTORY_SEPARATOR . 'auto_append.php');
//    $filter->addDirectoryToBlacklist($rootDir . DIRECTORY_SEPARATOR . 'vendor');
//    $filter->addDirectoryToBlacklist($rootDir . DIRECTORY_SEPARATOR . 'app');
//    $filter->addDirectoryToBlacklist($rootDir . DIRECTORY_SEPARATOR . 'ezpublish');
//    $filter->addDirectoryToBlacklist($rootDir . DIRECTORY_SEPARATOR . 'web');
    $filter->addDirectoryToWhitelist($rootDir . DIRECTORY_SEPARATOR . 'src');

    $coverage->setAddUncoveredFilesFromWhitelist(true);
    $coverage->setProcessUncoveredFilesFromWhitelist(true);
    $coverage->start($_SERVER['SCRIPT_FILENAME']);
}
