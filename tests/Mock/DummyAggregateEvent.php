<?php

declare(strict_types=1);

/**
 * This file is part of `unixslayer/event-store`.
 * (c) 2021 Piotr Zając <piotr.zajac@unixslayer.pl>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Unixslayer\EventSourcing\Mock;

use Ramsey\Uuid\UuidInterface;
use Unixslayer\EventSourcing\AggregateEvent;

final class DummyAggregateEvent extends AggregateEvent
{
    public static function occur(UuidInterface $aggregateId, array $payload): DummyAggregateEvent
    {
        return new static($aggregateId, $payload);
    }
}
