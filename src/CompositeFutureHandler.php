<?php

namespace Labrador\CompositeFuture;

use function Amp\Future\await;

enum CompositeFutureHandler {
    case Await;
    case AwaitFirst;
    case AwaitAny;
    case AwaitAnyN;
    case AwaitAll;

    public function invoke(CompositeFuture $future, mixed ...$args) : mixed {
        return match($this) {
            self::Await => $future->await(),
            self::AwaitFirst => $future->awaitFirst(),
            self::AwaitAny => $future->awaitAny(),
            self::AwaitAnyN => $future->awaitAnyN((int) ($args[0] ?? 1)),
            self::AwaitAll => $future->awaitAll()
        };
    }

}
