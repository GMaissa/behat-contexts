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
    $defaultCovDir = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'coverage';
    $coverageDir = getenv("COVERAGE_DIR") !== false ? getenv("COVERAGE_DIR") : $defaultCovDir;

    $coverage->stop();
    $writer     = new PHP_CodeCoverage_Report_PHP;
    $objectName = 'proc_' . getmypid() . '_' . uniqid();
    $writer->process(
        $coverage,
        $coverageDir . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $objectName . '.cov'
    );
}
