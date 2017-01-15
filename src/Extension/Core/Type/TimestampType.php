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

namespace Rollerworks\Component\Search\Extension\Core\Type;

use Rollerworks\Component\Search\AbstractFieldType;
use Rollerworks\Component\Search\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Rollerworks\Component\Search\FieldConfig;
use Rollerworks\Component\Search\Value\Compare;
use Rollerworks\Component\Search\Value\Range;
use Rollerworks\Component\Search\ValueComparator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class TimestampType extends AbstractFieldType
{
    /**
     * @var ValueComparator
     */
    protected $valueComparison;

    /**
     * Constructor.
     *
     * @param ValueComparator $valueComparison
     */
    public function __construct(ValueComparator $valueComparison)
    {
        $this->valueComparison = $valueComparison;
    }

    /**
     * {@inheritdoc}
     */
    public function buildType(FieldConfig $config, array $options)
    {
        $config->setValueComparison($this->valueComparison);
        $config->setValueTypeSupport(Range::class, true);
        $config->setValueTypeSupport(Compare::class, true);

        $config->setViewTransformer(
            new DateTimeToTimestampTransformer(
                $options['model_timezone'],
                $options['view_timezone']
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'model_timezone' => null,
            'view_timezone' => null,
        ]);
    }
}
