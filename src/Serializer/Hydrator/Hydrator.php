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
use Unixslayer\EventStore\Serializer\Hydrator\Exception\HydratorNotFoundException;

class Hydrator
{
    private array $hydrators;

    public function __construct(HydratorInterface ...$hydrators)
    {
        $this->hydrators = $hydrators;
    }

    public function fromEventData(EventData $eventData): AggregateEvent
    {
        foreach ($this->hydrators as $hydrator) {
            if (!$hydrator->supports($eventData)) {
                continue;
            }

            return $hydrator->toEvent($eventData);
        }

        throw new HydratorNotFoundException();
    }
}
