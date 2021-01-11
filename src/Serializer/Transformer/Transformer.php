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
use Unixslayer\EventStore\Serializer\Transformer\Exception\TransformerNotFoundException;

class Transformer implements EventToArrayInterface
{
    /**
     * @var TransformerInterface[]
     */
    private array $transformers;

    public function __construct(TransformerInterface ...$transformers)
    {
        $this->transformers = $transformers;
    }

    public function toEventData(AggregateEvent $event): EventData
    {
        foreach ($this->transformers as $transformer) {
            if (!$transformer->supports($event)) {
                continue;
            }

            return EventData::fromArray([
                'uuid' => $transformer->uuid($event),
                'message_name' => EventData::class,
                'created_at' => $transformer->creationDate($event),
                'payload' => $transformer->payload($event),
                'metadata' => $transformer->metadata($event),
            ]);
        }

        throw TransformerNotFoundException::for($event);
    }
}
