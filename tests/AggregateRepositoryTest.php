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
use Prooph\EventStore\InMemoryEventStore;
use Ramsey\Uuid\Uuid;
use Unixslayer\EventStore\Mock\Aggregate;
use Unixslayer\EventStore\Mock\DummyAggregate;
use Unixslayer\EventStore\Mock\Repository;
use Unixslayer\EventStore\Serializer\Hydrator\GenericEventHydrator;
use Unixslayer\EventStore\Serializer\Hydrator\Hydrator;
use Unixslayer\EventStore\Serializer\Transformer\GenericEventTransformer;
use Unixslayer\EventStore\Serializer\Transformer\Transformer;

final class AggregateRepositoryTest extends TestCase
{
    private Repository $repository;

    protected function setUp(): void
    {
        $transformer = new Transformer(new GenericEventTransformer());
        $hydrator = new Hydrator(new GenericEventHydrator());
        $this->repository = new Repository(new InMemoryEventStore(), $transformer, $hydrator);
    }

    public function testRepositoryCanSaveAndLoadAggregateRoot(): void
    {
        $uuid = Uuid::uuid4();
        $aggregate = Aggregate::new($uuid);
        $aggregate->increaseCounter();
        $this->repository->saveAggregateRoot($aggregate);

        static::assertEmpty($aggregate->recordedEvents());

        $savedAggregate = $this->repository->getAggregateRoot($uuid);

        static::assertEquals($aggregate, $savedAggregate);

        //saving the same aggregate twice doesn't fail
        $this->repository->saveAggregateRoot($aggregate);
        //repository returns null if aggregate not exists
        static::assertNull($this->repository->getAggregateRoot(Uuid::uuid4()));
    }

    public function testRepositoryThrowsExceptionForInvalidAggregate(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $aggregate = DummyAggregate::new();

        $this->repository->saveAggregateRoot($aggregate);
    }

    public function testItReturnsNullWhenStreamNotFound(): void
    {
        $repository = new Repository(new InMemoryEventStore(), new Transformer(), new Hydrator());
        $aggregate = $repository->getAggregateRoot(Uuid::uuid1());

        static::assertNull($aggregate);
    }
}
