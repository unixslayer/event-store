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

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Unixslayer\EventSourcing\AggregateEvent;
use Unixslayer\EventStore\EventData;
use Unixslayer\EventStore\Serializer\Hydrator\Exception\HydratorNotFoundException;

class HydratorTest extends TestCase
{
    public function testItShouldThrowAnExceptionWhenNoHydratorSupportsGivenData(): void
    {
        $eventData = EventData::fromArray([
            'message_name' => EventData::class,
            'uuid' => 'f9f276a7-9426-40dd-b239-b5958d6531fa',
            'created_at' => new \DateTimeImmutable('now'),
            'payload' => [],
            'metadata' => [
                '_messageName' => AggregateEvent::class,
            ],
        ]);

        $this->expectException(HydratorNotFoundException::class);
        $hydrator = new Hydrator();
        $hydrator->fromEventData($eventData);
    }

    public function testItShouldUseInjectedHydrators(): void
    {
        $hydrator = new Hydrator(new GenericEventHydrator());

        $messageData = [
            'message_name' => EventData::class,
            'uuid' => 'f9f276a7-9426-40dd-b239-b5958d6531fa',
            'created_at' => new \DateTimeImmutable('now'),
            'payload' => [],
            'metadata' => [
                '_messageName' => AggregateEvent::class,
                '_eventVersion' => 1,
            ],
        ];
        $eventData = EventData::fromArray($messageData);

        $event = $hydrator->fromEventData($eventData);

        static::assertEquals(Uuid::fromString($messageData['uuid']), $event->uuid());
    }

    public function testItShouldUseInjectedHydratorsWithProperVersion(): void
    {
        $hydrator = new Hydrator(new GenericEventHydrator());

        $messageData = [
            'message_name' => EventData::class,
            'uuid' => 'f9f276a7-9426-40dd-b239-b5958d6531fa',
            'created_at' => new \DateTimeImmutable('now'),
            'payload' => [],
            'metadata' => [
                '_messageName' => AggregateEvent::class,
                '_eventVersion' => 2,
            ],
        ];
        $eventData = EventData::fromArray($messageData);

        $this->expectException(HydratorNotFoundException::class);
        $hydrator->fromEventData($eventData);
    }
}
