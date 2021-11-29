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

use Unixslayer\EventSourcing\AggregateRoot;
use Unixslayer\EventStore\AggregateRepository;

final class Repository extends AggregateRepository
{
    private const AGGREGATE_STREAM_NAME = 'aggregate';

    protected function aggregateType(): string
    {
        return self::AGGREGATE_STREAM_NAME;
    }

    protected function streamName(): string
    {
        return self::AGGREGATE_STREAM_NAME;
    }

    protected function canHandle(AggregateRoot $aggregateRoot): bool
    {
        return $aggregateRoot instanceof Aggregate;
    }

    protected function recreateAggregate(array $aggregateEvents): AggregateRoot
    {
        return Aggregate::fromHistory($aggregateEvents);
    }
}
