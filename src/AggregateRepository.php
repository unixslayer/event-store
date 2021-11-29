<?php

declare(strict_types=1);

/**
 * This file is part of `unixslayer/event-store`.
 * (c) 2021 Piotr Zając <piotr.zajac@unixslayer.pl>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Unixslayer\EventStore;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\Exception\StreamNotFound;
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Metadata\Operator;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Ramsey\Uuid\UuidInterface;
use Unixslayer\EventSourcing\AggregateEvent;
use Unixslayer\EventSourcing\AggregateRoot;
use Unixslayer\EventStore\Serializer\Hydrator\Hydrator;
use Unixslayer\EventStore\Serializer\Transformer\Transformer;

abstract class AggregateRepository
{
    private EventStore $eventStore;
    private Transformer $transformer;
    private Hydrator $hydrator;

    public function __construct(EventStore $eventStore, Transformer $transformer, Hydrator $hydrator)
    {
        $this->eventStore = $eventStore;
        $this->transformer = $transformer;
        $this->hydrator = $hydrator;
    }

    public function saveAggregateRoot(AggregateRoot $aggregateRoot): void
    {
        if (!$this->canHandle($aggregateRoot)) {
            throw new \InvalidArgumentException(sprintf('"%s" cannot handle "%s".', $this::class, get_debug_type($aggregateRoot)));
        }

        $events = $aggregateRoot->recordedEvents();
        if (empty($events)) {
            return;
        }

        $events = array_reduce($events, function (array $carrier, AggregateEvent $aggregateEvent) {
            $event = $aggregateEvent->withAddedMetadata('_aggregateType', $this->aggregateType());
            $carrier[] = $this->transformer->toEventData($event);

            return $carrier;
        }, []);

        $streamEvents = new \ArrayIterator($events);

        $streamName = new StreamName($this->streamName());

        try {
            $this->eventStore->appendTo($streamName, $streamEvents);
        } catch (StreamNotFound $e) {
            //if event stream was not found, repository will tell EventStore to create new one saving events
            $stream = new Stream($streamName, $streamEvents);
            $this->eventStore->create($stream);
        }
    }

    public function getAggregateRoot(UuidInterface $aggregateId): ?AggregateRoot
    {
        $streamName = new StreamName($this->streamName());
        $metadataMatcher = new MetadataMatcher();
        $aggregateType = $this->aggregateType();
        $metadataMatcher = $metadataMatcher->withMetadataMatch('_aggregateType', Operator::EQUALS(), $aggregateType);
        $metadataMatcher = $metadataMatcher->withMetadataMatch('_aggregateId', Operator::EQUALS(), (string)$aggregateId);

        try {
            $streamEvents = $this->eventStore->load($streamName, 1, null, $metadataMatcher);
        } catch (StreamNotFound $e) {
            return null;
        }

        if (!$streamEvents->valid()) {
            return null;
        }

        $aggregateEvents = array_reduce(iterator_to_array($streamEvents), function (array $carrier, EventData $eventData) {
            $carrier[] = $this->hydrator->fromEventData($eventData);

            return $carrier;
        }, []);

        return $this->recreateAggregate($aggregateEvents);
    }

    abstract protected function aggregateType(): string;

    abstract protected function canHandle(AggregateRoot $aggregateRoot): bool;

    abstract protected function streamName(): string;

    abstract protected function recreateAggregate(array $aggregateEvents): AggregateRoot;
}
