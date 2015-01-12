<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) 2012-2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search;

use Rollerworks\Component\Search\Exception\BadMethodCallException;
use Rollerworks\Component\Search\Value\Compare;
use Rollerworks\Component\Search\Value\PatternMatch;
use Rollerworks\Component\Search\Value\Range;
use Rollerworks\Component\Search\Value\SingleValue;

/**
 * ValuesBag holds all the values per-type.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ValuesBag implements \Countable, \Serializable
{
    protected $excludedValues = array();
    protected $ranges = array();
    protected $excludedRanges = array();
    protected $comparisons = array();
    protected $singleValues = array();
    protected $patternMatchers = array();
    protected $valuesCount = 0;

    /**
     * @var ValuesError[]
     */
    protected $errors = array();

    /**
     * @var bool
     */
    private $locked = false;

    /**
     * @return SingleValue[]
     */
    public function getSingleValues()
    {
        return $this->singleValues;
    }

    /**
     * @param SingleValue $value
     *
     * @return static
     */
    public function addSingleValue(SingleValue $value)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->singleValues[] = $value;
        $this->valuesCount++;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSingleValues()
    {
        return !empty($this->singleValues);
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public function removeSingleValue($index)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        if (isset($this->singleValues[$index])) {
            unset($this->singleValues[$index]);

            $this->valuesCount--;
        }

        return $this;
    }

    /**
     * @param SingleValue $value
     *
     * @return static
     */
    public function addExcludedValue(SingleValue $value)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->excludedValues[] = $value;
        $this->valuesCount++;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasExcludedValues()
    {
        return !empty($this->excludedValues);
    }

    /**
     * @return SingleValue[]
     */
    public function getExcludedValues()
    {
        return $this->excludedValues;
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public function removeExcludedValue($index)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        if (isset($this->excludedValues[$index])) {
            unset($this->excludedValues[$index]);

            $this->valuesCount--;
        }

        return $this;
    }

    /**
     * @param Range $range
     *
     * @return static
     */
    public function addRange(Range $range)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->ranges[] = $range;
        $this->valuesCount++;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRanges()
    {
        return count($this->ranges) > 0;
    }

    /**
     * @return Range[]
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public function removeRange($index)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        if (isset($this->ranges[$index])) {
            unset($this->ranges[$index]);

            $this->valuesCount--;
        }

        return $this;
    }

    /**
     * @param Range $range
     *
     * @return static
     */
    public function addExcludedRange(Range $range)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->excludedRanges[] = $range;
        $this->valuesCount++;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasExcludedRanges()
    {
        return !empty($this->excludedRanges);
    }

    /**
     * @return Range[]
     */
    public function getExcludedRanges()
    {
        return $this->excludedRanges;
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public function removeExcludedRange($index)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        if (isset($this->excludedRanges[$index])) {
            unset($this->excludedRanges[$index]);

            $this->valuesCount--;
        }

        return $this;
    }

    /**
     * @param Compare $value
     *
     * @return static
     */
    public function addComparison(Compare $value)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->comparisons[] = $value;
        $this->valuesCount++;

        return $this;
    }

    /**
     * @return Compare[]
     */
    public function getComparisons()
    {
        return $this->comparisons;
    }

    /**
     * @return bool
     */
    public function hasComparisons()
    {
        return !empty($this->comparisons);
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public function removeComparison($index)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        if (isset($this->comparisons[$index])) {
            unset($this->comparisons[$index]);

            $this->valuesCount--;
        }

        return $this;
    }

    /**
     * @return PatternMatch[]
     */
    public function getPatternMatchers()
    {
        return $this->patternMatchers;
    }

    /**
     * @param PatternMatch $value
     *
     * @return static
     */
    public function addPatternMatch(PatternMatch $value)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->patternMatchers[] = $value;
        $this->valuesCount++;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPatternMatchers()
    {
        return !empty($this->patternMatchers);
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public function removePatternMatch($index)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        if (isset($this->patternMatchers[$index])) {
            unset($this->patternMatchers[$index]);

            $this->valuesCount--;
        }

        return $this;
    }

    /**
     * @param ValuesError $error
     *
     * @return static
     */
    public function addError(ValuesError $error)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->errors[$error->getHash()] = $error;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @return ValuesError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param ValuesError $error
     *
     * @return bool
     */
    public function hasError(ValuesError $error)
    {
        return isset($this->errors[$error->getHash()]);
    }

    /**
     * @param ValuesError $error
     *
     * @return static
     */
    public function removeError(ValuesError $error)
    {
        if (isset($this->errors[$error->getHash()])) {
            unset($this->errors[$error->getHash()]);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->valuesCount;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->excludedValues,
            $this->ranges,
            $this->excludedRanges,
            $this->comparisons,
            $this->singleValues,
            $this->patternMatchers,
            $this->valuesCount,
            $this->errors,
            $this->locked
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->excludedValues,
            $this->ranges,
            $this->excludedRanges,
            $this->comparisons,
            $this->singleValues,
            $this->patternMatchers,
            $this->valuesCount,
            $this->errors,
            $this->locked
        ) = $data;
    }

    /**
     * Sets the values data is locked.
     *
     * After calling this method, setter methods can be no longer called.
     *
     * @param bool $locked
     *
     * @throws BadMethodCallException when the data is locked
     */
    public function setDataLocked($locked = true)
    {
        if ($this->locked) {
            $this->throwLocked();
        }

        $this->locked = $locked;
    }

    /**
     * Returns whether the field's data is locked.
     *
     * A field with locked data is restricted to the data passed in
     * this configuration.
     *
     * @return bool Whether the data is locked.
     */
    public function isDataLocked()
    {
        return $this->locked;
    }

    protected function throwLocked()
    {
        throw new BadMethodCallException(
            'ValuesBag setter methods cannot be accessed anymore once the data is locked.'
        );
    }
}
