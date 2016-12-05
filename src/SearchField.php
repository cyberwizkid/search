<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search;

use Rollerworks\Component\Search\Exception\BadMethodCallException;
use Rollerworks\Component\Search\Value\ValuesBag;

/**
 * SearchField.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SearchField implements FieldConfigInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ResolvedFieldTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool[]
     */
    private $supportedValueTypes = [
        ValuesBag::VALUE_TYPE_RANGE => false,
        ValuesBag::VALUE_TYPE_COMPARISON => false,
        ValuesBag::VALUE_TYPE_PATTERN_MATCH => false,
    ];

    /**
     * @var ValueComparisonInterface
     */
    private $valueComparison;

    /**
     * @var bool
     */
    private $locked = false;

    /**
     * @var DataTransformerInterface|null
     */
    private $viewTransformer;

    /**
     * @var DataTransformerInterface|null
     */
    private $normTransformer;

    /**
     * Constructor.
     *
     * @param string                     $name
     * @param ResolvedFieldTypeInterface $type
     * @param array                      $options
     *
     * @throws \InvalidArgumentException When the name is invalid
     */
    public function __construct($name, ResolvedFieldTypeInterface $type, array $options = [])
    {
        if ('' === $name || !preg_match('/^[a-zA-Z][a-zA-Z0-9_\-]*$/D', $name)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The name "%s" contains illegal characters. Name must start with a letter '.
                    'and only contain letters, digits, numbers, underscores ("_") and hyphens ("-").',
                    $name
                )
            );
        }

        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadMethodCallException
     */
    public function supportValueType($type)
    {
        if (!isset($this->supportedValueTypes[$type])) {
            throw new BadMethodCallException(
                sprintf(
                    'Unable to find configured-support for unknown value type "%s".',
                    $type
                )
            );
        }

        return $this->supportedValueTypes[$type];
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadMethodCallException
     */
    public function setValueTypeSupport($type, $enabled)
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'SearchField setter methods cannot be accessed anymore once the data is locked.'
            );
        }

        if (!isset($this->supportedValueTypes[$type])) {
            throw new BadMethodCallException(
                sprintf(
                    'Unable to configure support for unknown value type "%s".',
                    $type
                )
            );
        }

        $this->supportedValueTypes[$type] = (bool) $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadMethodCallException
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadMethodCallException
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadMethodCallException when the data is locked
     *
     * @return self
     */
    public function setValueComparison(ValueComparisonInterface $comparisonObj)
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'SearchField setter methods cannot be accessed anymore once the data is locked.'
            );
        }

        $this->valueComparison = $comparisonObj;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueComparison()
    {
        return $this->valueComparison;
    }

    /**
     * {@inheritdoc}
     */
    public function setViewTransformer(DataTransformerInterface $viewTransformer = null)
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'SearchField setter methods cannot be accessed anymore once the data is locked.'
            );
        }

        $this->viewTransformer = $viewTransformer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewTransformer()
    {
        return $this->viewTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function setNormTransformer(DataTransformerInterface $viewTransformer = null)
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'SearchField setter methods cannot be accessed anymore once the data is locked.'
            );
        }

        $this->normTransformer = $viewTransformer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNormTransformer()
    {
        return $this->normTransformer;
    }

    /**
     * Sets the field's data is locked.
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
            throw new BadMethodCallException(
                'SearchField setter methods cannot be accessed anymore once the data is locked.'
            );
        }

        $this->locked = $locked;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataLocked()
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name, $default = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function createView()
    {
        if (!$this->locked) {
            throw new BadMethodCallException(
                'Unable to create SearchFieldView when configuration is not locked.'
            );
        }

        $view = new SearchFieldView();

        $this->type->buildFieldView($view, $this, $this->options);

        return $view;
    }
}
