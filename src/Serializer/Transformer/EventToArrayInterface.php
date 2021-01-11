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
use Unixslayer\EventStore\EventData;

interface EventToArrayInterface
{
    public function toEventData(AggregateEvent $event): EventData;
}
