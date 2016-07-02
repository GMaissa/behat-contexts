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

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Screenshot Context class
 */
trait WindowSizeTrait
{
    /**
     * Browser window width
     * @var integer
     */
    private static $windowWidth;

    /**
     * Browser window height
     * @var integer
     */
    private static $windowHeight;

    /**
     * Set the window size parameters
     *
     * @param array $parameters context parameters
     */
    protected function setWindowSize($parameters)
    {
        self::$windowWidth  = isset($parameters['window_width']) ? $parameters['window_width'] : false;
        self::$windowHeight = isset($parameters['window_height']) ? $parameters['window_height'] : false;
    }

    /**
     * Get the window size parameters
     *
     * @return array
     */
    protected function getWindowSize()
    {
        return [
            'width'  => self::$windowWidth,
            'height' => self::$windowHeight,
        ];
    }

    /**
     * Resize browser window before each scenario
     *
     * @BeforeScenario
     */
    public function resizeWindow()
    {
        try {
            $windowSize = $this->getWindowSize();
            $this->getSession()->resizeWindow($windowSize['width'], $windowSize['height']);
        } catch (UnsupportedDriverActionException $e) {
            // Need this for unsupported drivers
        }
    }
}
