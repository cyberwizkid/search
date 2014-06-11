<?php

/**
 * This file is part of RollerworksSearch Component package.
 *
 * (c) 2012-2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Core\DataTransformer;

use Rollerworks\Component\Search\DataTransformerInterface;
use Rollerworks\Component\Search\Exception\TransformationFailedException;
use Rollerworks\Component\Search\Extension\Core\ChoiceList\ChoiceListInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ChoiceToValueTransformer implements DataTransformerInterface
{
    private $choiceList;

    /**
     * Constructor.
     *
     * @param ChoiceListInterface $choiceList
     */
    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($choice)
    {
        return (string) $this->choiceList->getValueForChoice($choice);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null !== $value && !is_scalar($value)) {
            throw new TransformationFailedException('Expected a scalar.');
        }

        // These are now valid ChoiceList values, so we can return null
        // right away
        if ('' === $value || null === $value) {
            return null;
        }

        $choice = $this->choiceList->getChoiceForValue($value);

        if (null === $choice) {
            throw new TransformationFailedException(sprintf('The choice "%s" does not exist.', $value));
        }

        return '' === $choice ? null : $choice;
    }
}