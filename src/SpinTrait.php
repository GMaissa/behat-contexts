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

use GMaissa\BehatContexts\Exception\Timeout;

/**
 * Trait class to manage step spin
 */
trait SpinTrait
{
    /**
     * Step timeout
     * @var integer
     */
    protected static $timeout;

    /**
     * Retrieve the waiting timeout
     *
     * @return int the timeout in milliseconds
     */
    public static function getTimeout()
    {
        return static::$timeout;
    }

    /**
     * Set the waiting timeout
     *
     * @param array $parameters context parameters
     */
    protected function setTimeout($parameters)
    {
        static::$timeout = isset($parameters['timeout']) ? $parameters['timeout'] : 60;
    }

    /**
     * This method executes $callable every second.
     * If its return value is evaluated to true, the spinning stops and the value is returned.
     * If the return value is falsy, the spinning continues until the loop limit is reached,
     * In that case a TimeoutException is thrown.
     *
     * @param callable $callable closure to be called
     * @param string $message error message to display
     *
     * @throws Timeout
     *
     * @return mixed
     */
    public function spin($callable, $message)
    {
        $timeout = self::$timeout;
        $start = microtime(true);
        $end = $start + $timeout;
        $logThreshold = (int)$timeout * 0.8;
        $previousException = null;
        $result = null;
        $looping = false;

        do {
            if ($looping) {
                sleep(1);
            }
            try {
                $result = $callable($this);
            } catch (\Exception $e) {
                $previousException = $e;
            }
            $looping = true;
        } while (microtime(true) < $end && !$result && !$previousException instanceof Timeout);

        if (null === $message) {
            $message = (null !== $previousException) ? $previousException->getMessage() : 'no message';
        }
        if (!$result) {
            $infos = sprintf('Spin : timeout of %d excedeed, with message : %s', $timeout, $message);
            throw new Timeout($infos, 0, $previousException);
        }
        $elapsed = microtime(true) - $start;
        if ($elapsed >= $logThreshold) {
            printf('[%s] Long spin (%d seconds) with message : %s', date('y-md H:i:s'), $elapsed, $message);
        }
        return $result;
    }
}
