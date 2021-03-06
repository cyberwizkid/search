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

namespace Rollerworks\Component\Search;

use Rollerworks\Component\Search\Value\ValuesGroup;

/**
 * SearchPreCondition contains a condition that must be fulfilled at all times.
 *
 * A Pre-condition is applied as `(SearchPreCondition) AND (SearchCondition)`.
 *
 * Caution: It's important for QueryGenerator to always apply
 * the pre-condition even if the SearchCondition itself is empty!
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class SearchPreCondition
{
    private $values;

    public function __construct(ValuesGroup $valuesGroup)
    {
        $this->values = $valuesGroup;
    }

    public function getValuesGroup(): ValuesGroup
    {
        return $this->values;
    }
}
