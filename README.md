# Labrador Composite Future

An object that represents a type-safe collection of `Amp\Future` objects and methods to act on that collection.

## Installation

[Composer]() is the only supported method for installing Labrador packages.

```
composer require cspray/labrador-composite-future
```

## Getting Started

The object provided by this library is intended to provide a type that can be used in type-hints to provide more 
information when dealing with a collection of Futures. When the `CompositeFuture` is used a return type hint you're also 
able to construct your code to read in a manner similar to handling a single Future. As always, the best way to 
get started is to look at some code!

```php
<?php

namespace Acme\Demo;

use Amp\Future;
use Labrador\CompositeFuture\CompositeFuture;

function futuresGeneratingMethod() : CompositeFuture {
    $futures = ['a' => Future::complete(1), 'b' => Future::complete(2), 'c' => Future::error(new \Exception('something went wrong'))];
    return new CompositeFuture($futures);
}

$futures = futuresGeneratingMethod();
// Returns an array with keys equal to the index of the Future and the value to the Future resolution
// Will throw an exception when an error is encountered
$futures->await();

// Also has access to the following methods, which follow the same documentation as their corresponding
// Amp\Future functions.
$futures->awaitAll();
$futures->awaitAny();
$futures->awaitAnyN(2);
$futures->awaitFirst();

?>
```

## Motivation

This library is intentionally a very simple object-oriented wrapper around the utility functions for working with a collection 
of Futures. The motivation is to provide a semantic type-hint for dealing with this type of collection. In the above example, 
without the `CompositeFuture`, we would basically have 2 options:

1) Await the Futures inside the function itself; this may not be ideal because each invocation may need to handle awaiting differently.
2) Type-hint returning an array from our method; this may not be ideal because it isn't as semantic as the type-hint. 

## Governance

All Labrador packages adhere to the rules laid out in the [Labrador Governance repo](https://github.com/labrador-kennel/governance)
