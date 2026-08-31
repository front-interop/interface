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

- [_FrontController_][] affords an entry point into the outermost presentation layer in any execution context (HTTP, CLI, etc.).

- [_FrontTypeAliases_][] provides custom PHPStan types to aid static analysis.

### _FrontController_

[_FrontController_][] affords an entry point into the outermost presentation
layer in any execution context (HTTP, CLI, etc.).

#### _FrontController_ Methods

- ```php
  public function run() : front_exit_status_int;
  ```
    - Runs the front controller.

    - Directives:

        - Implementations MUST return a value between `0` and `254`
          (inclusive).

        - Implementations MUST return a value of `0` to report success.

        - Implementations MUST return a value between `1` and `254`
          (inclusive) to report a negative outcome.

        - Implementations SHOULD return a value of `1` to report an
          ordinary negative outcome.

        - Implementations MAY return a value between `2` and `254`
          (inclusive) to distinguish among negative outcomes; the meanings
          of such values are explicitly undefined herein.

        - Implementations MUST NOT terminate the process in place of
          returning from `run()`, whether by [`exit()`][], [`die()`][], or
          otherwise.

        - Implementations MUST NOT allow a [_Throwable_][] to escape `run()`.

    - Notes:

        - **The return value is intended as an exit status code.** Exit
          status codes may be received initially by the in-process logic
          that invoked `run()` (bootstrap scripts, test harnesses, etc.),
          and may ultimately be received by a parent process (shell,
          supervisor, init system, CI runner, monitoring tool, or
          similar) via [`exit()`][].

        - **What counts as success is context-dependent.** In an HTTP
          context, returning `0` typically means that the request was
          processed and a response was emitted regardless of the HTTP status
          code. In a command line context, returning `0` typically means the
          command completed its work.

        - **A negative outcome is not always an error.** A non-`0` return
          value reports something other than success: usually an error
          condition, but perhaps a partial failure, an empty result, or any
          other outcome the implementation wants to distinguish. Which of
          these counts as the ordinary negative outcome, if any, is
          implementation-specific.

        - **A caught [_Throwable_][] does not require a non-`0` return.**
          For example, a front controller that catches a [_Throwable_][]
          and emits an HTTP `500` has arguably done its work, and may
          return `0`; another may hold that emitting that `500` is itself
          a negative outcome and return `1`.

        - **The caller receives a return value, never a [_Throwable_][].**
          That holds for a [_Throwable_][] thrown from within a `catch`
          block as well as for the one that the `catch` was handling.

### _FrontTypeAliases_

[_FrontTypeAliases_][] provides custom PHPStan types to aid static analysis.

- ```
  front_exit_status_int int<0,254>
  ```
    - An `int` exit status code: `0` for success, `1` to `254` otherwise.
      The value `255` is reserved by PHP itself.

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

The projects do not agree on what kind of thing that ordinary outcome is.
Test runners give the low values to the finding, and push tool errors above
them; for example, `phpunit` reports failing tests with `1` and `2`, and
reserves `255` for a fault in PHPUnit itself. Analysis tools do the reverse:
`psalm` exits `1` "when there was a problem running Psalm" and `2` "when it
completed successfully but found some issues".

Front-Interop therefore recommends the value but not its meaning. A `1` reports
whichever negative outcome an implementation treats as ordinary, and the
interface does not say which that must be. For some implementations it may be
a [_Throwable_][] they had to handle themselves; for others, such as those
handling queries, it may be an empty result.

### Why leave exit status codes `2` through `254` undefined?

Above `1` the surveyed schemes split five ways. Nine assign small
sequential values, two adopt the [`sysexits.h`][] range beginning at `64`,
two use a combinable bitmask, one adopts the shell's reserved conventions
at `126` and above, and four define nothing above `1` at all; `tempest` is
counted in two of those groups, so the five figures cover 17 distinct
schemes.

Sequential values are a bare majority, but agreeing on a *value* is not agreeing
on a *meaning*. Among the projects that define `2`, it means invalid input to
`symfony` and `tempest`, a test that errored to `phpunit`, issues found to
`psalm`, a rule violation to `phpmd`, a dependency solving failure to
`composer`, and an unused result cache to `phpstan`. Codifying the shape would
give a caller a portable value with no portable meaning. The interface
therefore leaves `2` through `254` explicitly undefined.

What a consumer can rely on portably is the distinction between `0` and
everything else; the meaning of any particular non-`0` value is determined by
the implementation.

### Why stop at `254`?

The `front_exit_status_int` alias bounds the return at `int<0,254>`. The
ceiling comes from PHP rather than from practice; cf. [`exit()`][]: "Exit
codes should be in the range 0 to 254, the exit code 255 is reserved by PHP
and should not be used."

Surveyed practice runs the other way. Of the projects in
[README-RETURNS.md][], the three that police the range all admit `255`, and
`phpunit` occupies it, defining `Result::CRASH` as `255` for a fault in
PHPUnit itself. No surveyed project stops at `254`.

Front-Interop defers to PHP, for a reason particular to a front controller.
With no handler registered, PHP exits `255` when a [_Throwable_][] escapes
uncaught, so `255` is already the signal that something reached the top of
the stack unhandled. A front controller returning `255` deliberately would
be indistinguishable from one that failed to return at all, which is the
outcome the directives exist to prevent.

That signal stays available after `run()` has returned. A [_Throwable_][]
arising once the front controller has finished executing, from the destructor
of an object it held, released only after `run()` had returned, is beyond the
reach of any `try` the implementation could write, and so beyond the reach of
the directives. A run
that reported `0` can therefore still end in a `255` process status: the two
answer different questions, one about the work `run()` did, the other about
how the process ended.

### Why must implementations return rather than exit?

Of the 23 researched projects, 21 return control to their caller on the
success path with no [`exit()`][] or [`die()`][]. Of the remaining two,
`symfony`'s `run()` also returns, with its runtime then exiting with the
returned integer, while only `tempest`'s never returns control at all.

Front-Interop directs that implementations return rather than exit, as the
majority already do. The value of an exit status code comes from letting the
caller decide what to do with it. A worker loop, queue worker, or test
harness needs `run()` to hand control back so it can continue, retry, or
assert on the result. An implementation that calls [`exit()`][] inside
`run()` prevents those uses, terminating the process before the caller
regains control.

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

A handler registered with [`set_exception_handler()`][] cannot substitute for
that backstop. PHP discards a handler's return value, so a handler that
returns a status still leaves the process exiting `0`; and `run()` has already
unwound by the time the handler fires, so the caller of `run()` never regains
control. The only
mechanism left is to call [`exit()`][], which terminates the process before
the caller can act on the status.

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
