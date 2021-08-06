<?php

declare(strict_types=1);

/**
 * This file is part of `unixslayer/event-store`.
 * (c) 2021 Piotr Zając <piotr.zajac@unixslayer.pl>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Unixslayer\EventStore\Serializer\Transformer;

use Unixslayer\EventSourcing\AggregateEvent;

final class GenericEventTransformer implements TransformerInterface
{
    public function supports(AggregateEvent $event): bool
    {
        return $event->metadata()['_eventVersion'] === $this->version();
    }

    public function uuid(AggregateEvent $event): string
    {
        return $event->uuid()->toString();
    }

    public function messageName(AggregateEvent $event): string
    {
        return get_class($event);
    }

    public function creationDate(AggregateEvent $event): \DateTimeImmutable
    {
        return $event->createdAt();
    }

    public function version(): int
    {
        return 1;
    }

    public function payload(AggregateEvent $event): array
    {
        return $event->payload();
    }

    public function metadata(AggregateEvent $event): array
    {
        $metadata = $event->metadata();
        $metadata['_messageName'] = get_class($event);
        $metadata['_eventVersion'] = $this->version();

        return $metadata;
    }
}
