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

interface TransformerInterface
{
    public function supports(AggregateEvent $event): bool;

    public function uuid(AggregateEvent $event): string;

    public function messageName(AggregateEvent $event): string;

    public function creationDate(AggregateEvent $event): \DateTimeImmutable;

    public function version(): int;

    public function payload(AggregateEvent $event): array;

    public function metadata(AggregateEvent $event): array;
}
