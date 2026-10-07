# Front-Interop Standard Interface Package

[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg?style=flat-square)](https://github.com/php-pds/skeleton)
[![PDS Composer Script Names](https://img.shields.io/badge/pds-composer--script--names-blue?style=flat-square)](https://github.com/php-pds/composer-script-names)

Front-Interop provides an interoperable package of standard interfaces for
front controller functionality in any execution context (HTTP, CLI, etc.). It
reflects, refines, and reconciles the common practices identified within
[several pre-existing projects][README-RESEARCH.md], along with the exit
status conventions of [several command line projects][README-RETURNS.md].

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [BCP 14][] ([RFC 2119][], [RFC 8174][]).

This package attempts to adhere to the [Package Development Standards](https://php-pds.com/) approach to [naming and versioning](https://php-pds.com/#naming-and-versioning).

## Interfaces

This package defines the following interfaces:

{{= list }}

{{= docs }}

## Implementations

Implementations MAY define additional class members not defined in these interfaces.

Notes:

- **Reference implementations** are available at <https://github.com/front-interop/impl>.

## Q & A

### Why `run()`?

The researched projects use one of six verbs for the front controller's main
method: `dispatch()`, `execute()`, `handle()`, `__invoke()`, `run()`, or
`start()`. Of these, `run()` is an outright majority at 12 of 23 projects;
the remaining five verbs together account for the other 11. Front-Interop
follows the majority practice.

### Why does `run()` return `int`?

Of the 23 researched projects:

- 11 return `void` or `null` and handle response-sending themselves;
- 7 return a response object for the calling code to send;
- 3 return some other value (`$this` or `mixed`);
- 1 (Tempest) `never` returns; and
- 1 (Symfony) returns an `int` exit code.

The largest group of researched projects, 11 of 23, have the front controller
handle response-sending itself and return nothing.

However, Front-Interop observes that a front controller in a different execution
context may need to return an integer exit status code to its caller or parent
process. These contexts include, among others:

- command line invocations
- continuous integration runners
- long-running processes
- queue workers
- test harnesses
- worker loops

While providing two interfaces (one to return `void` and another to return
`int`) would cover both cases, it leads to inconsistencies in setup and
expectations.

Thus, contra the most common `void` or `null` return, Front-Interop directs that
`run()` returns an integer exit status code. This is an unusual practice
for front controllers in an HTTP execution context, but imposes only a trivial
implementation burden. Doing so allows the same interface to be used across
many execution contexts, and keeps the interface machine-friendly.

### Why recommend `1` for an ordinary negative outcome?

All 18 PHP command line projects surveyed in [README-RETURNS.md][] reserve
`0` for success. All but two also reserve `1` for the negative outcome they
treat as ordinary, and where `1` is named, the sense is generic: `yii` calls it
`UNSPECIFIED_ERROR`, `drush` `EXIT_FAILURE`, and `aura` and `symfony`
`FAILURE`. The exceptions are `phpcs`, whose `1` is `FIXABLE`, one of three
flags its `ExitCode::calculate()` composes bitwise; and `phpcsfixer`, whose
source reserves `1` "for environment constraints not matched" and puts its
ordinary outcome, files needing fixing, at `8`. Those two exceptions are why
Front-Interop recommends `1` rather than requiring it.

The projects differ on what the ordinary outcome is because they do different
kinds of work. Test runners give the low values to the finding, and push tool
errors above them; for example, `phpunit` reports failing tests with `1` and
`2`, and reserves `255` for a fault in PHPUnit itself. Analysis tools do the
reverse: `psalm` exits `1` "when there was a problem running Psalm" and `2`
"when it completed successfully but found some issues".

Front-Interop therefore recommends the value and leaves the meaning to each
implementation. A `1` reports whichever negative outcome an implementation
treats as ordinary, and the interface does not say which that must be. For
some implementations it may be a [_Throwable_][] they had to handle
themselves; for others, such as those handling queries, it may be an empty
result.

### Why leave exit status codes `2` through `254` undefined?

Above `1` the surveyed schemes split five ways. Nine assign small
sequential values, two adopt the [`sysexits.h`][] range beginning at `64`,
two use a combinable bitmask, one adopts the shell's reserved conventions
at `126` and above, and four define nothing above `1` at all; `tempest` is
counted in two of those groups, so the five figures cover 17 distinct
schemes.

Sequential values are a bare majority, but the projects that use them do not
agree on what `2` means. It means invalid input to `symfony` and `tempest`, a
test that errored to `phpunit`, issues found to `psalm`, a rule violation to
`phpmd`, a dependency solving failure to `composer`, and an unused result cache
to `phpstan`. A caller could not rely on what `2` means, so the interface
leaves `2` through `254` explicitly undefined.

What a consumer can rely on portably is the distinction between `0` and
everything else; the meaning of any particular non-`0` value is determined by
the implementation.

### Why stop at `254`?

The `front_exit_status_int` alias bounds the return at `int<0,254>`. The
ceiling comes from PHP rather than from practice; cf. [`exit()`][]: "Exit
codes should be in the range 0 to 254, the exit code 255 is reserved by PHP
and should not be used."

Surveyed practice differs. Three of the projects in [README-RETURNS.md][]
check the range of exit codes, and all three allow `255`. The `phpunit`
project goes further: it defines `Result::CRASH` as `255` and exits with it
when PHPUnit itself fails. No surveyed project stops at `254`.

Front-Interop defers to PHP, for a reason particular to a front controller.
With no handler registered, PHP exits `255` when a [_Throwable_][] escapes
uncaught, so `255` is already the signal that something reached the top of
the stack unhandled. A front controller intentionally returning `255` would
be indistinguishable from one that failed to return at all, which is the
outcome the directives exist to prevent.

That signal can still appear after `run()` has returned. An object that the
front controller held may be released only after `run()` returns. If the
destructor of that object throws a [_Throwable_][], no `try` in the
implementation can catch it, and the directives do not cover it. A run that
reported `0` can therefore still end with a `255` process status. The return
value says what `run()` did; the process status says how the process ended.

### Why must implementations return rather than exit?

Of the 23 researched projects, 21 return control to their caller on the
success path with no [`exit()`][] or [`die()`][]. Of the remaining two,
`symfony`'s `run()` also returns, with its runtime then exiting with the
returned integer, while only `tempest`'s never returns control at all.

Front-Interop directs that implementations return rather than exit, as the
majority already do. A worker loop, queue worker, or test harness needs `run()`
to hand control back so it can continue, retry, or assert on the result. An
implementation that calls [`exit()`][] inside `run()` ends the script before the
caller gets control back, so the caller never sees the status.

### Why must no [_Throwable_][] escape `run()`?

Of the 23 researched projects, 21 handle exceptions in some way; of those,
19 handle all types of [_Throwable_][], 1 handles all types of
[_Exception_][], and 1 handles only specific exception subtypes.

Front-Interop observes that a front controller invocation occurs at the
outermost boundary of the presentation layer. This is the last point at which
any uncaught [_Throwable_][]s may be handled gracefully. The choice then is
whether they are handled by the bootstrap script, or by the front controller
proper.

In the interest of keeping such handling within a class, Front-Interop
prohibits a [_Throwable_][] from leaving `run()`, making the _FrontController_
the final backstop. Other handling subsystems can exist in the logic it calls;
anything escaping them stops at the _FrontController_ rather than reaching
the caller.

A handler registered with [`set_exception_handler()`][] cannot take the place
of that backstop. PHP discards the value the handler returns, so the exit
status is `0` whatever the handler returns. By the time the handler runs,
`run()` has already unwound, and the caller never gets control back. The
handler could call [`exit()`][] with a status, but that ends the script before
the caller can act on it.

* * *

[_Exception_]: https://php.net/Exception
[_FrontController_]: #frontcontroller
[_FrontTypeAliases_]: #fronttypealiases
[_Throwable_]: https://php.net/Throwable
[`die()`]: https://php.net/die
[`exit()`]: https://php.net/exit
[`set_exception_handler()`]: https://php.net/set_exception_handler
[`sysexits.h`]: https://man7.org/linux/man-pages/man3/sysexits.h.3head.html
[BCP 14]: https://datatracker.ietf.org/doc/bcp14/
[README-RESEARCH.md]: ./README-RESEARCH.md
[README-RETURNS.md]: ./README-RETURNS.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
