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
          (inclusive) to distinguish among negative outcomes other than the
          ordinary one; the meanings of such values are explicitly undefined
          herein.

        - Implementations MUST NOT end the execution of `run()` by calling
          [`exit()`][] or [`die()`][].

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

Of the six verbs the researched projects use for the front controller's main
method (`dispatch()`, `execute()`, `handle()`, `__invoke()`, `run()`, and
`start()`), `run()` is an outright majority, at 12 of 23 projects.
Front-Interop follows that majority.

### Why does `run()` return `int`?

Of the 23 researched projects:

- 11 return `void` or `null` and handle response-sending themselves;
- 7 return a response object for the calling code to send;
- 3 return some other value (`$this` or `mixed`);
- 1 (Tempest) `never` returns; and
- 1 (Symfony) returns an `int` exit code.

A front controller in another execution context, however, may need to return
an integer exit status code to its caller or parent process. Such contexts
include, among others:

- command line invocations
- continuous integration runners
- long-running processes
- queue workers
- test harnesses
- worker loops

Providing two interfaces, one returning `void` and another returning `int`,
would cover both cases, but would split setup and expectations between them.

Front-Interop therefore departs from the most common practice and directs
that `run()` return an integer exit status code. That is unusual for a front
controller in an HTTP context, but the implementation burden is trivial, and
it lets the same machine-friendly interface serve many execution contexts.

### Why recommend `1` for an ordinary negative outcome?

All 27 PHP command line projects surveyed in [README-RETURNS.md][] reserve
`0` for success. All but three also reserve `1` for the negative outcome they
treat as ordinary, and where `1` is named, the sense is mostly generic: `yii`
calls it `UNSPECIFIED_ERROR`, `drush` `EXIT_FAILURE`, and `aura` and `symfony`
`FAILURE`, while `phpmd`'s `EXIT_EXCEPTION` names a cause. The three
exceptions are `phpcs`, whose `1` on its 4.x line is `FIXABLE`, one of three
flags its `ExitCode::calculate()` composes bitwise; `phpcsfixer`, whose source
reserves `1` "for environment constraints not matched" and puts its ordinary
outcome, files needing fixing, at `8`; and `nette`, which documents only `0`
and `2` and leaves `1` without a meaning. Those three exceptions are why
Front-Interop recommends `1` rather than requiring it.

The projects differ on what the ordinary outcome is largely because they do
different kinds of work. Test runners give the low values to the finding, and
push tool errors above them; for example, `phpunit` reports failing tests with
`1` and `2`, and reserves `255` for a fault in PHPUnit itself. Several analysis
tools do the reverse: `psalm` exits `1` "when there was a problem running
Psalm" and `2` "when it completed successfully but found some issues".

Front-Interop therefore recommends the value and leaves its meaning to each
implementation: a `1` reports whichever negative outcome the implementation
treats as ordinary. For some implementations that may be a [_Throwable_][]
they had to handle themselves; for others, such as those handling queries,
an empty result.

### Why leave exit status codes `2` through `254` undefined?

Above `1` the surveyed schemes split five ways. Thirteen assign small
sequential values, two adopt the [`sysexits.h`][] range beginning at `64`,
two use a combinable bitmask, three adopt the shell's reserved conventions
at `126` and above, and seven define nothing above `1` at all. Those figures
cover 25 distinct schemes, because `laravel` and `laminas` inherit
`symfony`'s, and sum to 27 because `joomla` and `tempest` are each counted in
two of the groups.

Sequential values are a bare majority, but the projects that use them do not
agree on what `2` means. It means invalid input to `symfony` and `tempest`, a
wrong command line to `nette`, a test that errored to `phpunit`, issues found
to `psalm`, a rule violation to `phpmd`, a dependency solving failure to
`composer`, an unused result cache to `phpstan`, and a server error to
`bear`; within `joomla`, it means something different from one command to
the next. A caller could not rely on what `2` means, so the interface leaves
`2` through `254` explicitly undefined.

A consumer can rely portably only on the distinction between `0` and
everything else; what any other value means is up to the implementation.

### Why stop at `254`?

The `front_exit_status_int` alias bounds the return at `int<0,254>`. The
ceiling comes from PHP rather than from practice; cf. [`exit()`][]: "Exit
codes should be in the range 0 to 254, the exit code 255 is reserved by PHP
and should not be used."

Surveyed practices differ. Four of the projects in [README-RETURNS.md][]
check the range of exit codes, and all four allow `255`. Three go further
and use it: `phpunit` defines `Result::CRASH` as `255` and exits with it when
PHPUnit itself fails, `laminas` exits with it when it cannot find the
application's container, and the library under `flightphp` exits with it
when a command throws. Where nothing checks, a value above `255` wraps
around: `kohana`'s console exits with an error's PHP level as its status, so
an `E_DEPRECATED` there ends with `0`, the status for success. Only `lithium`
documents `254` as the ceiling, and no surveyed project enforces it.

Front-Interop defers to PHP, for a reason particular to a front controller.
With no handler registered, PHP exits `255` when a [_Throwable_][] escapes
uncaught, so `255` is already the signal that something reached the top of
the stack unhandled. A front controller intentionally returning `255` would
be indistinguishable from one that failed to return at all, which is the
outcome the directives exist to prevent.

That signal can still appear after `run()` returns. If an object the front
controller held is released afterward and its destructor throws a
[_Throwable_][], no `try` in the implementation can catch it, and the
directives do not cover it. A run that reported `0` can therefore still end
with a `255` process status: the return value says what `run()` did, and the
process status says how the process ended.

### Why must implementations return rather than exit?

Of the 23 researched projects, 20 return control to their caller on the
success path with no [`exit()`][] or [`die()`][]. Of the other three,
`symfony`'s and `lightmvc`'s `run()` also return before their callers exit,
`symfony`'s runtime with the returned integer; only `tempest`'s never
returns control at all.

Front-Interop directs that implementations return rather than exit, as the
majority already do. A worker loop, queue worker, or test harness needs `run()`
to hand control back so it can continue, retry, or assert on the result. An
implementation that calls [`exit()`][] inside `run()` ends the script before the
caller gets control back, so the caller never sees the status.

### Why must no [_Throwable_][] escape `run()`?

Of the 23 researched projects, 21 handle exceptions in some way: 19 catch
every [_Throwable_][], one catches only [_Exception_][]s, and one catches only
specific subtypes; cf. [README-EXCEPTIONS.md][].

A front controller invocation sits at the outermost boundary of the
presentation layer, the last point at which an uncaught [_Throwable_][] can
be handled gracefully. The choice is whether the bootstrap script handles it
or the front controller proper.

To keep that handling within a class, Front-Interop prohibits a
[_Throwable_][] from leaving `run()`, making the _FrontController_ the final
backstop. Other handling subsystems can exist in the logic it calls;
anything escaping them stops at the _FrontController_ rather than reaching
the caller.

A handler registered with [`set_exception_handler()`][] cannot take the place
of that backstop. By the time the handler runs, `run()` has already unwound,
and the caller never gets control back; PHP ignores the handler's return
value, and the script exits `0`. The handler could call [`exit()`][] with a
status, but that ends the script before the caller can act on it. Two of the
surveyed projects, `fatfree` and `flightphp`, handle a [_Throwable_][] only
through such a handler.

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
[README-EXCEPTIONS.md]: ./README-EXCEPTIONS.md
[README-RESEARCH.md]: ./README-RESEARCH.md
[README-RETURNS.md]: ./README-RETURNS.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
