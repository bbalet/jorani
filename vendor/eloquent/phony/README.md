# Phony

*Mocks, stubs, and spies for PHP.*

[![Current version image][version-image]][current version]
[![Current build status image][build-image]][current build status]
[![Current Windows build status image][windows-build-image]][current windows build status]
[![Tested against HHVM][hhvm-image]][current hhvm build status]
[![Current coverage status image][coverage-image]][current coverage status]

[build-image]: https://img.shields.io/travis/eloquent/phony/master.svg?style=flat-square "Current build status for the master branch"
[coverage-image]: https://img.shields.io/codecov/c/github/eloquent/phony/master.svg?style=flat-square "Current test coverage for the master branch"
[current build status]: https://travis-ci.org/eloquent/phony
[current coverage status]: https://codecov.io/github/eloquent/phony
[current hhvm build status]: https://travis-ci.org/eloquent/phony
[current version]: https://packagist.org/packages/eloquent/phony
[current windows build status]: https://ci.appveyor.com/project/eloquent/phony
[hhvm-image]: https://img.shields.io/badge/hhvm-tested-brightgreen.svg?style=flat-square "Tested against HHVM"
[version-image]: https://img.shields.io/packagist/v/eloquent/phony.svg?style=flat-square "This project uses semantic versioning"
[windows-build-image]: https://img.shields.io/appveyor/ci/eloquent/phony/master.svg?label=windows&style=flat-square "Current Windows build status for the master branch"

[![Example verification output][verification-video-thumbnail]][verification output video]

[verification output video]: https://asciinema.org/a/79430
[verification-video-thumbnail]: https://asciinema.org/a/79430.png "Example verification output"

## Installation and documentation

- Available as [Composer] package [eloquent/phony].
- Read the [documentation].

[composer]: http://getcomposer.org/
[eloquent/phony]: https://packagist.org/packages/eloquent/phony

## What is *Phony*?

*Phony* is a PHP library for creating [test doubles]. *Phony* supports a wide
range of PHP versions, from 5.3 through to 7.1, as well as [HHVM].

[test doubles]: https://en.wikipedia.org/wiki/Test_double

## Why use *Phony*?

### Support for language features, old and new

*Phony* is committed to supporting new PHP features as they emerge. Features
that require workarounds in other mocking frameworks (such as
passed-by-reference arguments), typically "just work" with *Phony*.

Amongst other features, *Phony* supports:

- PHP from 5.3 onward
- [HHVM]
- Generator [stubbing][generator stubbing] and
  [verification][generator verification]
- [Mocking of traits]
- [Stubbing of functions and methods with PHP 7 return types]
- Modern [variable-length argument lists] using the `...` token
- [Setting of passed-by-reference arguments], and reference arguments in general

[generator stubbing]: http://eloquent-software.com/phony/latest/#stubbing-generators
[generator verification]: http://eloquent-software.com/phony/latest/#generator-and-iterable-verification
[mocking of traits]: http://eloquent-software.com/phony/latest/#mocking-basics
[setting of passed-by-reference arguments]: http://eloquent-software.com/phony/latest/#setting-passed-by-reference-arguments
[stubbing of functions and methods with php 7 return types]: http://eloquent-software.com/phony/latest/#default-values-for-return-types
[variable-length argument lists]: http://php.net/functions.arguments#functions.variable-arg-list

### Zero-configuration integrations

Integrating *Phony* with your favorite test framework is as simple as
[choosing the correct namespace to import]. Complimentary features make
integrations seamless and intuitive:

- [No configuration or bootstrap code necessary]
- [Most test frameworks need no special treatment]
- Tight integration with [PHPUnit][phpunit integration],
  [Pho][pho integration] and [SimpleTest][simpletest integration]
- [Can be used standalone, too]
- Supports matchers from [Counterpart][counterpart matchers],
  [Hamcrest][hamcrest matchers], [PHPUnit][phpunit matchers],
  [SimpleTest][simpletest matchers], and even other mocking frameworks like
  [Phake][phake matchers], [Prophecy][prophecy matchers], and
  [Mockery][mockery matchers]

Interested in better integration with other test frameworks? So are we! Just
open a [GitHub issue] if there's something we can do.

[can be used standalone, too]: http://eloquent-software.com/phony/latest/#standalone-usage
[choosing the correct namespace to import]: http://eloquent-software.com/phony/latest/#integration-with-test-frameworks
[counterpart matchers]: http://eloquent-software.com/phony/latest/#counterpart-matchers
[hamcrest matchers]: http://eloquent-software.com/phony/latest/#hamcrest-matchers
[integrates seamlessly with other testing libraries and tools]: http://eloquent-software.com/phony/latest/#integration-with-test-frameworks
[mockery matchers]: http://eloquent-software.com/phony/latest/#mockery-matchers
[most test frameworks need no special treatment]: http://eloquent-software.com/phony/latest/#integration-with-test-frameworks
[no configuration or bootstrap code necessary]: http://eloquent-software.com/phony/latest/#integration-with-test-frameworks
[phake matchers]: http://eloquent-software.com/phony/latest/#phake-matchers
[pho integration]: http://eloquent-software.com/phony/latest/#pho-usage
[phpunit integration]: http://eloquent-software.com/phony/latest/#phpunit-usage
[phpunit matchers]: http://eloquent-software.com/phony/latest/#phpunit-constraints
[prophecy matchers]: http://eloquent-software.com/phony/latest/#prophecy-argument-tokens
[simpletest integration]: http://eloquent-software.com/phony/latest/#simpletest-usage
[simpletest matchers]: http://eloquent-software.com/phony/latest/#simpletest-expectations

### Refined verification output

*Phony* has received a great deal of work and refinement where the rubber meets
the road; in its [verification output]:

![Example verification output][verification image]

*Phony*'s output includes succinct but detailed information that is focused on
helping you find the cause of the failure as fast as possible. Where
appropriate, the output uses [difference comparison and color] to further
highlight important details.

[verification image]: http://eloquent-software.com/phony/latest/img/verification.svg
[verification output]: http://eloquent-software.com/phony/latest/#understanding-verification-output
[difference comparison and color]: http://eloquent-software.com/phony/latest/#understanding-verification-output

### First-class support for functions and callbacks

*Phony* is designed to provide support for testing both object-based, and
function-based systems. In *Phony*, object mocks are built upon full-featured
function level [stubs] and [spies].

This focus on support for procedural programming allows *Phony* to handle many
situations that cannot be handled by solely class-based mocking frameworks.
Since *Phony*'s class-level support is based upon its function-level support,
the interfaces are consistent, and require very little extra knowledge to use.

[spies]: http://eloquent-software.com/phony/latest/#spies
[stubs]: http://eloquent-software.com/phony/latest/#stubs

### Extensive feature set

*Phony* has an extensive and powerful feature set. For detailed information on
a particular feature, select one of the following:

- [Mock objects]
    - [Mocking of classes, interfaces, and traits]
    - [Mocking of multiple types simultaneously]
    - [Partial mocks]
    - [Proxy mocks for `final` classes]
    - [Mock classes with custom methods and properties]
    - [Constructor bypassing, and manual constructor calling]
    - [Static method mocking]
    - [Mocking of fluent interfaces]
    - [Customizable class names]
    - [Mock builders for advanced usage]
- [Stubbing]
    - Returning of [specific values][returning values],
      [arguments][returning arguments], and [more][returning self]
    - [Throwing of exceptions]
    - [Replacement of behavior with another callback]
    - [Conditional use of the original function or method]
    - [Returning of generators with customizable behavior]
    - [Multiple actions can be performed in a single invocation]
- [Spying]
    - Verify-after-running style produces more durable tests than outdated
      [expect-run-verify] style
    - Verification of [calls][verifying calls] and
      [call arguments][verifying arguments]
    - Verification of [return values][verifying return values] and
      [thrown exceptions][verifying thrown exceptions]
    - Verification of [generators][verifying generators]
    - [Verification of other iterables]
    - [Order verification]
    - [Individual call level verification]
    - [Retrieval of call arguments]
- And [more][documentation]...

[conditional use of the original function or method]: http://eloquent-software.com/phony/latest/#forwarding-to-the-original-callable
[constructor bypassing, and manual constructor calling]: http://eloquent-software.com/phony/latest/#calling-a-constructor-manually
[customizable class names]: http://eloquent-software.com/phony/latest/#builder.named
[function-level spies]: http://eloquent-software.com/phony/latest/#spies
[function-level stubs]: http://eloquent-software.com/phony/latest/#stubs
[individual call level verification]: http://eloquent-software.com/phony/latest/#calls
[integrates seamlessly with other testing libraries and tools]: http://eloquent-software.com/phony/latest/#integration-with-test-frameworks
[mock builders for advanced usage]: http://eloquent-software.com/phony/latest/#mock-builders
[mock classes with custom methods and properties]: http://eloquent-software.com/phony/latest/#ad-hoc-mocks
[mock objects]: http://eloquent-software.com/phony/latest/#mocks
[mocking of classes, interfaces, and traits]: http://eloquent-software.com/phony/latest/#mocking-basics
[mocking of fluent interfaces]: http://eloquent-software.com/phony/latest/#returning-the-self-value
[mocking of multiple types simultaneously]: http://eloquent-software.com/phony/latest/#mocking-multiple-types
[multiple actions can be performed in a single invocation]: http://eloquent-software.com/phony/latest/#answers-that-perform-multiple-actions
[order verification]: http://eloquent-software.com/phony/latest/#order-verification
[partial mocks]: http://eloquent-software.com/phony/latest/#partial-mocks
[proxy mocks for `final` classes]: http://eloquent-software.com/phony/latest/#proxy-mocks
[replacement of behavior with another callback]: http://eloquent-software.com/phony/latest/#using-a-callable-as-an-answer
[retrieval of call arguments]: http://eloquent-software.com/phony/latest/#verifying-that-a-spy-was-called-with-specific-arguments
[returning arguments]: http://eloquent-software.com/phony/latest/#returning-arguments
[returning of generators with customizable behavior]: http://eloquent-software.com/phony/latest/#stubbing-generators
[returning self]: http://eloquent-software.com/phony/latest/#returning-the-self-value
[returning values]: http://eloquent-software.com/phony/latest/#returning-values
[spying on callables]: http://eloquent-software.com/phony/latest/#spying-on-an-existing-callable
[spying]: http://eloquent-software.com/phony/latest/#spies
[static method mocking]: http://eloquent-software.com/phony/latest/#static-mocks
[stubbing callables]: http://eloquent-software.com/phony/latest/#stubbing-an-existing-callable
[stubbing]: http://eloquent-software.com/phony/latest/#stubs
[throwing of exceptions]: http://eloquent-software.com/phony/latest/#throwing-exceptions
[verification of other iterables]: http://eloquent-software.com/phony/latest/#generator-and-iterable-verification
[verifying arguments]: http://eloquent-software.com/phony/latest/#verifying-that-a-spy-was-called-with-specific-arguments
[verifying calls]: http://eloquent-software.com/phony/latest/#verifying-that-a-call-was-made
[verifying generators]: http://eloquent-software.com/phony/latest/#generator-and-iterable-verification
[verifying return values]: http://eloquent-software.com/phony/latest/#verifying-spy-return-values
[verifying thrown exceptions]: http://eloquent-software.com/phony/latest/#verifying-spy-exceptions

## Help

For help with a difficult testing scenario, questions regarding how to use
*Phony*, or to report issues with *Phony* itself, please open a [GitHub issue]
so that others may benefit from the outcome.

Alternatively, [@ezzatron] may be contacted directly via [Twitter].

[twitter]: https://twitter.com/ezzatron

## Usage

For detailed usage, see the [documentation].

### Example test suites

See the [phony-examples] repository.

[phony-examples]: https://github.com/eloquent/phony-examples

### Standalone usage

```php
use function Eloquent\Phony\mock;

$handle = mock('ClassA');
$handle->methodA->with('argument')->returns('value');

$mock = $handle->get();

assert($mock->methodA('argument') === 'value');
$handle->methodA->calledWith('argument');
```

### [Peridot] usage

```php
use function Eloquent\Phony\mock;

describe('Phony', function () {
    it('integrates with Peridot', function () {
        $handle = mock('ClassA');
        $handle->methodA->with('argument')->returns('value');

        $mock = $handle->get();

        expect($mock->methodA('argument'))->to->equal('value');
        $handle->methodA->calledWith('argument');
    });
});
```

There is also a [Phony for Peridot] plugin that provides auto-wired mocks:

```php
use function Eloquent\Phony\on;

describe('Phony for Peridot', function () {
    it('supports auto-wiring', function (ClassA $mock) {
        $handle = on($mock);
        $handle->methodA->with('argument')->returns('value');

        expect($mock->methodA('argument'))->to->equal('value');
        $handle->methodA->calledWith('argument');
    });
});
```

[peridot]: http://peridot-php.github.io/
[phony for peridot]: https://github.com/eloquent/peridot-phony

### [Pho] usage

```php
use function Eloquent\Phony\Pho\mock;

describe('Phony', function () {
    it('integrates with Pho', function () {
        $handle = mock('ClassA');
        $handle->methodA->with('argument')->returns('value');

        $mock = $handle->get();

        expect($mock->methodA('argument'))->toBe('value');
        $handle->methodA->calledWith('argument');
    });
});
```

[pho]: https://github.com/danielstjules/pho

### [PHPUnit] usage

```php
use Eloquent\Phony\Phpunit\Phony;

class PhonyTest extends PHPUnit_Framework_TestCase
{
    public function testIntegration()
    {
        $handle = Phony::mock('ClassA');
        $handle->methodA->with('argument')->returns('value');

        $mock = $handle->get();

        $this->assertSame('value', $mock->methodA('argument'));
        $handle->methodA->calledWith('argument');
    }
}
```

### [SimpleTest] usage

```php
use Eloquent\Phony\Simpletest\Phony;

class PhonyTest extends UnitTestCase
{
    public function testIntegration()
    {
        $handle = Phony::mock('ClassA');
        $handle->methodA->with('argument')->returns('value');

        $mock = $handle->get();

        $this->assertSame($mock->methodA('argument'), 'value');
        $handle->methodA->calledWith('argument');
    }
}
```

## Inception

Please forgive me if this section is opinionated, or if I recall some particular
detail of a framework incorrectly. But if you want a TL;DR for why I created
*Phony*, basically:

- [Phake] doesn't support [traits], and once upon a time, *didn't* support
  [HHVM].
- [PHPUnit] and [Mockery] both implement [expect-run-verify] style mocks, which
  are evil.
- [Prophecy] doesn't support the features that I need.
- [SimpleTest] is ancient.
- I hadn't heard of anything else that sounded promising.

The first mocking framework I used was *probably* [SimpleTest]'s. Unfortunately,
that's a long time ago now, and I didn't really understand mocking at the time.
And to top it off, I can't even remember how [SimpleTest]'s mocks functioned.
So let's skip ahead to the first mocking framework I really explored in depth,
which was [PHPUnit]'s.

When I first discovered [PHPUnit]'s mocks, they were revolutionary to me. They
allowed me to test things *really* thoroughly, in ways I didn't even know were
possible previously. Although [PHPUnit]'s mocking was likely a port of some
existing Java mocking system, it was the framework that first opened my eyes to
the real potential of test doubles.

Unfortunately it wasn't all sunshine and roses. [PHPUnit]'s mocks were difficult
to use, and especially, because of the [expect-run-verify] style interface, they
were difficult to *re*-use. There was no way to "un-expect" something in a
particular test, and when something failed, the true cause of the failure was
often difficult to determine.

While searching for a better solution, I stumbled across [Phake], which was
inspired by Java's *extremely* popular [Mockito] mocking framework. [Phake] was,
and still is, an excellent mocking framework. Both [Mockito] and [Phake] eschew
the [expect-run-verify] pattern, to great benefit.

By treating stubbing and verification as separate concepts, [Phake] essentially
fixed all of the problems I had with [PHPUnit]'s mocks. Mocks could be re-used
easily, and when a test failed, the cause was (usually) clear. I was sold on the
evils of [expect-run-verify], and swore never to go back.

I believe it was around this time that I heard of [Mockery]. Although I was
fairly happy with [Phake], it *did* have a few little oddities, such as the way
it deals with by-reference arguments, and mocking of [traits] was not possible.
So I checked out [Mockery], but was immediately put off by its use of
expectations; which I felt would have been a huge step backwards.

In fairness, it's possible that [Mockery] supports other mocking methods, but
since the "primary" way it works seems to be based around [expect-run-verify],
I've never considered it a viable candidate, and have never used it.

At some point around this time I worked on a Node.js project, and explored a
handful of the most popular Node mocking frameworks, before settling on the
excellent [Sinon.JS]. Its focus on callback-based stubbing and spying, and its
extensive verification API would eventually influence *Phony* heavily.

It wasn't until [HHVM] became a viable option in the PHP world that I really had
to consider using something other than [Phake]. I had wanted for a while to
start experimenting with [HHVM] in my projects. Unfortunately [Phake] had issues
that prevented the test suite from even *running* under [HHVM], so it was
immediately a problem.

One of my co-workers had intended to work on [HHVM] support for [Phake], but
unfortunately it seemed at that time as though work on [Phake] had stagnated,
and it took over a month just to get a [PR] accepted that added [HHVM] as a test
target. To [Phake]'s credit, [HHVM] *is* now supported, and hindsight has taught
me that HHVM support is **hard**.

One project that showed promise was [Prophecy], an "opinionated" mocking
framework that arose from the [phpspec] testing framework. While I disliked its
use of abstract terms like "prophesize" and "reveal" for method names, the core
idea of a separate object instance that can be used to control the actual mock
worked really well. So well, in fact, that I would eventually end up using this
concept in *Phony*.

Importantly, [Prophecy] already supported [HHVM], and seemed like a great fit to
replace [Phake]; until I ran into the "opinionated" side of [Prophecy]'s nature.
One thing that [Prophecy] does *not* support is order verification. For example;
verifying that your code opens a file before writing to it. It seemed to me to
be a feature whose benefits are self-evident, but the [Prophecy] developers
unfortunately [did not agree].

So, fool that I am, I thought "How hard can it be?" and started work on *Phony*,
a mocking framework designed to combine the strengths of its predecessors, and
allow testing under [HHVM] without compromise.

New versions of PHP came along and introduced new language features, and *Phony*
adapted to meet the requirements of testing these features. I was also fortunate
enough to be part of a development team at my day job, who were willing to be
the test bed for *Phony*, and it received a lot of real-world usage that
contributed immensely to *Phony*'s stability and eventual feature set.

Of course it turned into a much longer journey than I first expected, and
*Phony* continues to be a challenging project to maintain. But for me, it's an
invaluable tool that I use almost every day, and I hope it can be the same for
you.

Thanks for listening.

> Erin ([@ezzatron])

[did not agree]: https://github.com/phpspec/prophecy/issues/130
[expect-run-verify]: http://monkeyisland.pl/2008/02/01/deathwish/
[mockery]: http://docs.mockery.io/
[pr]: https://github.com/mlively/Phake/pull/133

## Thanks

Special thanks to the following people:

- [@jmalloc], [@koden-km], [@parkertr], and [@darianbr] for their invaluable
  help as test subjects.
- [@szczepiq], and everyone who contributed to [Mockito].
- [@mlively], and everyone who contributed to [Phake].
- [@cjohansen], and everyone who contributed to [Sinon.JS].
- [@everzet], and everyone who contributed to [Prophecy].
- [@sebastianbergmann], and everyone who contributed to [PHPUnit].

[@cjohansen]: https://github.com/cjohansen
[@darianbr]: https://github.com/darianbr
[@everzet]: https://github.com/everzet
[@ezzatron]: https://github.com/ezzatron
[@jmalloc]: https://github.com/jmalloc
[@koden-km]: https://github.com/koden-km
[@mlively]: https://github.com/mlively
[@parkertr]: https://github.com/parkertr
[@sebastianbergmann]: https://github.com/sebastianbergmann
[@szczepiq]: https://github.com/szczepiq

## License

For the full copyright and license information, please view the [LICENSE file].

[license file]: LICENSE

<!-- References -->

[documentation]: http://eloquent-software.com/phony/latest/
[github issue]: https://github.com/eloquent/phony/issues
[hhvm]: http://hhvm.com/
[mockito]: http://mockito.org/
[phake]: http://phake.readthedocs.org/
[phpspec]: http://phpspec.readthedocs.org/
[phpunit]: https://phpunit.de/
[prophecy]: https://github.com/phpspec/prophecy
[simpletest]: https://github.com/simpletest/simpletest
[sinon.js]: http://sinonjs.org/
[traits]: http://php.net/traits
