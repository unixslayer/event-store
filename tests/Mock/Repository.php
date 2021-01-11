<?php

declare(strict_types=1);

/**
 * This file is part of `unixslayer/event-store`.
 * (c) 2021 Piotr Zając <piotr.zajac@unixslayer.pl>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Unixslayer\EventStore\Mock;

use Prooph\EventStore\StreamName;
use Unixslayer\EventStore\AggregateRepository;

final class Repository extends AggregateRepository
{
    protected function aggregateType(): string
    {
        return Aggregate::class;
    }

    protected function streamName(): StreamName
    {
        return new StreamName('aggregate');
    }
}
