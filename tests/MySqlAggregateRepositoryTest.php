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

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Unixslayer\EventStore\Mock\Aggregate;
use Unixslayer\EventStore\Mock\Repository;
use Unixslayer\EventStore\Serializer\Hydrator\GenericEventHydrator;
use Unixslayer\EventStore\Serializer\Hydrator\Hydrator;
use Unixslayer\EventStore\Serializer\Transformer\GenericEventTransformer;
use Unixslayer\EventStore\Serializer\Transformer\Transformer;

/** @group database */
final class MySqlAggregateRepositoryTest extends TestCase
{
    public function testRepositoryCanSaveAggregateRoot(): void
    {
        $transformer = new Transformer(new GenericEventTransformer());
        $hydrator = new Hydrator(new GenericEventHydrator());
        $repository = new Repository(TestUtils::mySqlEventStore(), $transformer, $hydrator);

        $uuid = Uuid::uuid4();
        $aggregate = Aggregate::new($uuid);
        $aggregate->increaseCounter();
        $repository->saveAggregateRoot($aggregate);

        static::assertEmpty($aggregate->recordedEvents());

        $savedAggregate = $repository->getAggregateRoot($uuid);

        static::assertEquals($aggregate, $savedAggregate);
    }
}
