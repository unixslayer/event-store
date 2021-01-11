<?php

declare(strict_types=1);

/**
 * This file is part of `unixslayer/event-store`.
 * (c) 2021 Piotr Zając <piotr.zajac@unixslayer.pl>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Unixslayer\EventStore\Serializer\Hydrator;

use Unixslayer\EventSourcing\AggregateEvent;
use Unixslayer\EventStore\EventData;

interface HydratorInterface
{
    public function supports(EventData $eventData): bool;

    public function toEvent(EventData $eventData): AggregateEvent;
}
