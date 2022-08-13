<?php

namespace Labrador\CompositeFuture;

use Amp\Cancellation;
use function Amp\Future\await;
use function Amp\Future\awaitAll;
use function Amp\Future\awaitAny;
use function Amp\Future\awaitAnyN;
use function Amp\Future\awaitFirst;

final class CompositeFuture {

    private iterable $futures;
    private ?Cancellation $cancellation;

    public function __construct(iterable $futures, Cancellation $cancellation = null) {
        $this->futures = $futures;
        $this->cancellation = $cancellation;
    }

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

    public function awaitAnyN(int $count) : array {
        return awaitAnyN($count, $this->futures, $this->cancellation);
    }

}