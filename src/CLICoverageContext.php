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

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use SebastianBergmann\FinderFacade\FinderFacade;
use PHP_CodeCoverage;
use PHP_CodeCoverage_Report_HTML;

/**
 * Code Coverage Trait
 */
class CLICoverageContext extends BaseCoverageContext
{
    /**
     * Coverage data
     * @var PHP_CodeCoverage
     */
    private static $coverage;

    /**
     * List of directories to be blacklisted from coverage
     * @var array
     */
    private static $blacklistDirs;

    /**
     * List of directories to be whitelisted from coverage
     * @var array
     */
    private static $whitelistDirs;

    /**
     * @param array $parameters context parameters
     */
    public function __construct($parameters)
    {
        parent::__construct($parameters);

        $this->setBlacklistDirs($parameters);
        $this->setWhitelistDirs($parameters);
    }

    /**
     * Set the list of dirs to include in coverage
     *
     * @param array $parameters context parameters
     */
    private function setWhitelistDirs($parameters)
    {
        self::$whitelistDirs = isset($parameters['whitelist_dirs']) ? $parameters['whitelist_dirs'] : array();
    }

    /**
     * Set the list of dirs to include in coverage
     *
     * @param array $parameters context parameters
     */
    private function setBlacklistDirs($parameters)
    {
        self::$blacklistDirs = isset($parameters['blacklist_dirs']) ? $parameters['blacklist_dirs'] : array();
    }

    /**
     * Setup coverage data
     *
     * @BeforeSuite
     */
    public static function setupCoverage()
    {
        $filter = new \PHP_CodeCoverage_Filter();
        foreach (self::$blacklistDirs as $blacklistDir) {
            $filter->addDirectoryToBlacklist($blacklistDir);
        }
        foreach (self::$whitelistDirs as $whitelistDir) {
            $filter->addDirectoryToWhitelist($whitelistDir);
        }
        self::$coverage = new \PHP_CodeCoverage(null, $filter);
    }

    /**
     * Get coverage key from current scenario
     *
     * @param BeforeScenarioScope $scope before scenario scope
     *
     * @return string
     */
    private function getCoverageKeyFromScope(BeforeScenarioScope $scope)
    {
        $name = $scope->getFeature()->getTitle() . '::' . $scope->getScenario()->getTitle();

        return $name;
    }

    /**
     * Start code coverage when scenario starts
     *
     * @param BeforeScenarioScope $scope before scenario scope
     *
     * @BeforeScenario
     */
    public function startCoverage(BeforeScenarioScope $scope)
    {
        self::$coverage->start($this->getCoverageKeyFromScope($scope));
    }

    /**
     * Stop code coverage when scenario ends
     *
     * @AfterScenario
     */
    public function stopCoverage()
    {
        self::$coverage->stop();
    }
}
