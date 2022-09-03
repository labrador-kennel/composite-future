<?php

namespace Labrador\CompositeFuture;

use Amp\Cancellation;
use Amp\Future;
use function Amp\Future\await;
use function Amp\Future\awaitAll;
use function Amp\Future\awaitAny;
use function Amp\Future\awaitAnyN;
use function Amp\Future\awaitFirst;

final class CompositeFuture {

    /**
     * @param array<array-key, Future<mixed>> $futures
     * @param Cancellation|null $cancellation
     */
    public function __construct(
        private readonly array $futures,
        private readonly ?Cancellation $cancellation = null
    ) {}

    public function merge(CompositeFuture $compositeFuture) : CompositeFuture {
        return new CompositeFuture([...$this->futures, ...$compositeFuture->futures]);
    }

    public function await() : array {
        return await($this->futures, $this->cancellation);
    }

    public function awaitAll() : array {
        return awaitAll($this->futures, $this->cancellation);
    }

    public function awaitFirst() : mixed {
        return awaitFirst($this->futures, $this->cancellation);
    }

    public function awaitAny() : mixed {
        return awaitAny($this->futures, $this->cancellation);
    }

    /**
     * @param positive-int $count
     * @return array
     * @throws \Amp\CompositeException
     * @throws \Amp\CompositeLengthException
     */
    public function awaitAnyN(int $count) : array {
        return awaitAnyN($count, $this->futures, $this->cancellation);
    }

}