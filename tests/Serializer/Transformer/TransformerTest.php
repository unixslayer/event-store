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

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Unixslayer\EventStore\Fixtures\DummyEvent;
use Unixslayer\EventStore\Serializer\Transformer\Exception\TransformerNotFoundException;

class TransformerTest extends TestCase
{
    public function testItShouldThrowAnExceptionWhenNoTransformerSupportsGivenEvent(): void
    {
        $this->expectException(TransformerNotFoundException::class);
        $transformer = new Transformer();
        $transformer->toEventData(DummyEvent::occur(Uuid::uuid4()));
    }

    public function testItShouldUseInjectedTransformers(): void
    {
        $event = DummyEvent::occur(Uuid::fromString('d404113f-3b3e-4596-944e-7faaec2195a6'));

        $transformer = new Transformer(new GenericEventTransformer());

        $eventData = $transformer->toEventData($event);

        static::assertEquals('d404113f-3b3e-4596-944e-7faaec2195a6', $eventData->metadata()['_aggregateId']);
        static::assertEquals(DummyEvent::class, $eventData->metadata()['_messageName']);
    }
}
