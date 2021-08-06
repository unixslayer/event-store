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

final class GenericEventHydrator implements HydratorInterface
{
    public function supports(EventData $eventData): bool
    {
        return $eventData->metadata()['_eventVersion'] === $this->version();
    }

    public function toEvent(EventData $eventData): AggregateEvent
    {
        $messageName = $eventData->metadata()['_messageName'];

        return AggregateEvent::fromEventData($messageName, $eventData->uuid(), $eventData->createdAt(), $eventData->metadata(), $eventData->payload());
    }

    public function version(): int
    {
        return 1;
    }
}
