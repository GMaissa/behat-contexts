<?php
/**
 * This file is part of the GMaissa Behat Contexts package
 *
 * @package   GMaissa\BehatContexts
 * @author    Guillaume Maïssa <guillaume@maissa.fr>
 * @copyright 2016 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT MIT
 */
namespace GMaissa\BehatContexts\Steps;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Defines application features from the specific context.
 */
trait FormTrait
{
    /**
     * Check options list of a select element
     *
     * @param string $select select element to check
     * @param TableNode $options options list that should be found on the select element
     *
     * @throws ElementNotFoundException if the given element is not found on the page
     * @throws ElementNotFoundException if a given option is not found
     *
     * @Then the select :select should contain the following options:
     */
    public function selectShouldContainFollowingOptions($select, TableNode $options)
    {
        $this->spin(function () use ($select, $options) {
            $selectField = $this->getSession()->getPage()->findField($select);

            if (null === $selectField) {
                throw new ElementNotFoundException($this->getSession(), 'select field', 'id|name|label|value', $select);
            }
            foreach ($options->getHash() as $item) {
                $optionField = $selectField->find(
                    'named',
                    array('option', $item['option'])
                );

                if (null === $optionField) {
                    throw new ElementNotFoundException(
                        $this->getSession(),
                        'select option field',
                        'id|name|label|value',
                        $item['option']
                    );
                }
            }
            return true;
        }, sprintf('Cannot find all options for select "%s"', $select));
    }

    /**
     * Check options list of a select element
     *
     * @param string $select select element to check
     * @param string $options options list that should be found on the select element
     *
     * @throws ElementNotFoundException if the given element is not found on the page
     * @throws ElementNotFoundException if a given option is not found
     *
     * @Then the select :select should contain options :options
     */
    public function selectShouldContainOptions($select, $options)
    {
        $this->spin(function () use ($select, $options) {
            $selectField = $this->getSession()->getPage()->findField($select);

            if (null === $selectField) {
                throw new ElementNotFoundException($this->getSession(), 'select field', 'id|name|label|value', $select);
            }
            foreach (explode(',', $options) as $option) {
                $optionField = $selectField->find(
                    'named',
                    array('option', trim($option))
                );

                if (null === $optionField) {
                    throw new ElementNotFoundException(
                        $this->getSession(),
                        'select option field',
                        'id|name|label|value',
                        $option
                    );
                }
            }
            return true;
        }, sprintf('Cannot find all "%s" options for select "%s"', $options, $select));
    }

    /**
     * Checks, that option from select with specified id|name|label|value is selected.
     *
     * @param string $option option that should be selected
     * @param string $select select element to check
     *
     * @throws ElementNotFoundException if the given element is not found on the page
     * @throws ExpectationException if the given option is not selected
     *
     * @Then /^the "(?P<option>(?:[^"]|\\")*)" option from "(?P<select>(?:[^"]|\\")*)" (?:is|should be) selected/
     * @Then /^the option "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)" (?:is|should be) selected$/
     * @Then /^"(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)" (?:is|should be) selected$/
     */
    public function optionShouldBeSelected($option, $select)
    {
        $selectField = $this->getSession()->getPage()->findField($select);
        if (null === $selectField) {
            throw new ElementNotFoundException($this->getSession(), 'select field', 'id|name|label|value', $select);
        }

        $optionField = $selectField->find('named', array(
            'option',
            $option,
        ));

        if (null === $optionField) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'select option field',
                'id|name|label|value',
                $option
            );
        }

        if (!$optionField->isSelected()) {
            throw new ExpectationException(
                'Select option with value|text "'.$option.'" is not selected in the select "'.$select.'"',
                $this->getSession()
            );
        }
    }
}
