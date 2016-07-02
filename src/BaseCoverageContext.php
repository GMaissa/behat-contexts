<?php
/**
 * This file is part of the GMaissa Behat Contexts package
 *
 * @package   GMaissa\BehatContexts
 * @author    Guillaume Maïssa <guillaume@maissa.fr>
 * @copyright 2016 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace GMaissa\BehatContexts;

use Behat\MinkExtension\Context\RawMinkContext;
use SebastianBergmann\FinderFacade\FinderFacade;
use PHP_CodeCoverage;
use PHP_CodeCoverage_Report_HTML;

/**
 * Code Coverage Trait
 */
class BaseCoverageContext extends RawMinkContext
{
    /**
     * Path where coverage files will be stored
     * @var string
     */
    private static $coverageDir = '/tmp/coverage';

    /**
     * List of report formats to generate
     * @var array
     */
    private static $reportFormats = array();

    /**
     * @param array $parameters context parameters
     */
    public function __construct($parameters)
    {
        $this->setCoverageDir($parameters);
        $this->setReportFormats($parameters);
    }

    /**
     * Set a list of report formats to generate
     *
     * @param array $parameters context parameters
     */
    private function setReportFormats($parameters)
    {
        if (isset($parameters['report_formats'])) {
            self::$reportFormats = $parameters['report_formats'];
        }
    }

    /**
     * Set directory where coverage files will be stored
     *
     * @param array $parameters context parameters
     */
    private function setCoverageDir($parameters)
    {
        if (isset($parameters['coverage_dir'])) {
            self::$coverageDir = $parameters['coverage_dir'];
        }
    }

    /**
     * Generate coverage reports
     *
     * @AfterSuite
     */
    public static function generateCoverageReports()
    {
        $mergedCoverage = new PHP_CodeCoverage;

        $finder = new FinderFacade(
            array(self::$coverageDir . '/tmp'),
            array(),
            array('*.cov')
        );

        foreach ($finder->findFiles() as $file) {
            $coverage = include $file;
            $mergedCoverage->merge($coverage);
            unset($coverage);
        }

        if (in_array('html', self::$reportFormats)) {
            $writer = new PHP_CodeCoverage_Report_HTML;
            $writer->process($mergedCoverage, self::$coverageDir . '/html');
        }
    }
}
