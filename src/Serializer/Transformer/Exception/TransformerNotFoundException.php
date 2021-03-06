<?php

declare(strict_types=1);

/**
 * This file is part of `unixslayer/event-store`.
 * (c) 2021 Piotr Zając <piotr.zajac@unixslayer.pl>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Unixslayer\EventStore\Serializer\Transformer\Exception;

use Unixslayer\EventSourcing\AggregateEvent;

final class TransformerNotFoundException extends \RuntimeException
{
    public static function for(AggregateEvent $event): self
    {
        $message = sprintf('No transformer found for "%s"', get_class($event));

        return new self($message);
    }
}
