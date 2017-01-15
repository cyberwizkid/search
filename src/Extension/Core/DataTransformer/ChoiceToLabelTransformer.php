<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Core\DataTransformer;

use Rollerworks\Component\Search\DataTransformer;
use Rollerworks\Component\Search\Exception\TransformationFailedException;
use Rollerworks\Component\Search\Extension\Core\ChoiceList\ChoiceList;
use Rollerworks\Component\Search\Extension\Core\ChoiceList\View\ChoiceListView;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ChoiceToLabelTransformer implements DataTransformer
{
    private $choiceList;
    private $choiceListView;

    /**
     * Constructor.
     *
     * @param ChoiceList     $choiceList
     * @param ChoiceListView $choiceListView
     */
    public function __construct(ChoiceList $choiceList, ChoiceListView $choiceListView)
    {
        $this->choiceList = $choiceList;
        $this->choiceListView = $choiceListView;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($choice)
    {
        if (null === $this->choiceListView->choicesByLabel) {
            $this->choiceListView->initChoicesByLabel();
        }

        $value = current($this->choiceList->getValuesForChoices([$choice]));

        if (!array_key_exists($value, $this->choiceListView->labelsByValue)) {
            throw new TransformationFailedException(sprintf('The choice "%s" does not exist or is not unique', $choice));
        }

        return $this->choiceListView->labelsByValue[$value];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null !== $value && !is_string($value)) {
            throw new TransformationFailedException('Expected a string or null.');
        }

        if (null === $value || '' === $value) {
            return null;
        }

        if (null === $this->choiceListView->choicesByLabel) {
            $this->choiceListView->initChoicesByLabel();
        }

        if (!array_key_exists($value, $this->choiceListView->choicesByLabel)) {
            throw new TransformationFailedException(sprintf('The choice "%s" does not exist or is not unique', $value));
        }

        return $this->choiceListView->choicesByLabel[$value]->data;
    }
}
