<?php

namespace Labrador\CompositeFuture;

use Amp\DeferredFuture;
use Amp\Future;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Labrador\CompositeFuture\CompositeFuture
 */
class CompositeFutureTest extends TestCase {

    public function testAwaitSuccessful() : void {
        $futures = [
            Future::complete('a'),
            Future::complete('b'),
            Future::complete('c')
        ];

        $resolved = (new CompositeFuture($futures))->await();

        self::assertSame(['a', 'b', 'c'], $resolved);
    }

    public function testAwaitHasError() : void {
        $futures = [
            Future::complete(),
            Future::error(new \RuntimeException('From a future error'))
        ];

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('From a future error');

        (new CompositeFuture($futures))->await();
    }

    public function testAwaitFirst() : void {
        $deferred = new DeferredFuture();
        $futures = [
            $deferred->getFuture(),
            Future::complete('Target future completed')
        ];

        $resolved = (new CompositeFuture($futures))->awaitFirst();

        $deferred->complete('Defered future completed');

        self::assertSame('Target future completed', $resolved);
    }

    public function testAwaitAny() : void {
        $futures = [
            Future::error(new \RuntimeException('First future')),
            Future::error(new \RuntimeException('Second future')),
            Future::complete('Third future'),
            Future::complete('Fourth future')
        ];

        $resolved = (new CompositeFuture($futures))->awaitAny();

        self::assertSame('Third future', $resolved);
    }

    public function testAwaitAnyN() : void {
        $futures = [
            'one' => Future::error(new \RuntimeException()),
            'two' => Future::complete('First success'),
            'three' => Future::error(new \RuntimeException()),
            'four' => Future::complete('Second success')
        ];

        $resolved = (new CompositeFuture($futures))->awaitAnyN(2);

        self::assertSame(['two' => 'First success', 'four' => 'Second success'], $resolved);
    }

    public function testAwaitAll() : void {
        $futures = [
            'a' => Future::complete('a'),
            'b' => Future::error($b = new \RuntimeException()),
            'c' => Future::complete('c'),
            'd' => Future::error($d = new \RuntimeException())
        ];

        $resolved = (new CompositeFuture($futures))->awaitAll();

        self::assertSame([
            ['b' => $b, 'd' => $d], ['a' => 'a', 'c' => 'c']
        ], $resolved);
    }

    public function testCompositeFutureMergeIsImmutable() : void {
        $a = new CompositeFuture([]);
        $c = $a->merge($b = new CompositeFuture([]));

        self::assertNotSame($a, $c);
        self::assertNotSame($b, $c);
    }

    public function testCompositeFutureMergeRunsAllFutures() : void {
        $a = new CompositeFuture(['a' => Future::complete('a')]);
        $b = $a->merge(new CompositeFuture(['b' => Future::complete('b')]));
        $c = $b->merge(new CompositeFuture(['c' => Future::complete('c')]));

        $resolved = $c->await();

        self::assertSame(['a' => 'a', 'b' => 'b', 'c' => 'c'], $resolved);
    }

}