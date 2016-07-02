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

use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ElementTextException;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Component\Filesystem\Filesystem;
use GMaissa\BehatContexts\Steps\FormTrait;

/**
 * Defines application features from the specific context.
 */
class BrowserContext extends MinkContext
{
    use KernelDictionary;

    use SpinTrait;

    use WindowSizeTrait;

    use FormTrait;


    /**
     * @param array $parameters context parameters
     */
    public function __construct(array $parameters)
    {
        $this->setTimeout($parameters);
        $this->setWindowSize($parameters);
    }

    /**
     * Control if a given text is present on a given element
     *
     * @param string $text text that should be found
     * @param string $selector element to check
     *
     * @throws ElementNotFoundException if the given element is not found on the page
     * @throws ElementTextException if the given text was not found
     *
     * @Then I should see :text in the :selector elements
     */
    public function iShouldSeeInTheElements($text, $selector)
    {
        $elements = $this->getSession()->getPage()->findAll('css', $selector);
        if (count($elements) == 0) {
            throw new ElementNotFoundException($this->getSession(), 'element', 'id|name|label|value', $selector);
        }

        foreach ($elements as $id => $element) {
            $num = $id + 1;
            $actual = $element->getText();
            $regex = '/'.preg_quote($text, '/').'/ui';

            $message = sprintf(
                'The text "%s" was not found in the text of the element "%s" num %s.',
                $text,
                $selector,
                $num
            );

            if (!preg_match($regex, $actual)) {
                throw new ElementTextException($message, $this->getSession()->getDriver(), $element);
            }
        }
    }

    /**
     * Control if a given text is not present on a given element
     *
     * @param string $text text that should not be found
     * @param string $selector element to check
     *
     * @throws ElementNotFoundException if the given element is not found on the page
     * @throws ElementTextException if the given text was found
     *
     * @Then I should not see :text in the :selector elements
     */
    public function iShouldNotSeeInTheElements($text, $selector)
    {
        $elements = $this->getSession()->getPage()->findAll('css', $selector);
        if (count($elements) == 0) {
            throw new ElementNotFoundException($this->getSession(), 'element', 'id|name|label|value', $selector);
        }

        foreach ($elements as $id => $element) {
            $num = $id + 1;
            $actual = $element->getText();
            $regex = '/'.preg_quote($text, '/').'/ui';

            $message = sprintf(
                'The text "%s" was found in the text of the element "%s" num %s.',
                $text,
                $selector,
                $num
            );

            if (preg_match($regex, $actual)) {
                throw new ElementTextException($message, $this->getSession()->getDriver(), $element);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function selectOption($select, $option)
    {
        $this->spin(function () use ($select, $option) {
            parent::selectOption($select, $option);

            return true;
        }, sprintf('Cannot select option "%s" for field "%s"', $option, $select));
    }
}
