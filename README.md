# Front-Interop Standard Interface Package

[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg?style=flat-square)](https://github.com/php-pds/skeleton)
[![PDS Composer Script Names](https://img.shields.io/badge/pds-composer--script--names-blue?style=flat-square)](https://github.com/php-pds/composer-script-names)

Front-Interop provides an interoperable package of standard interfaces for
front controller functionality in any execution context (HTTP, CLI, etc.). It
reflects, refines, and reconciles the common practices identified within
[several pre-existing projects][README-RESEARCH.md].

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED",  "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [BCP 14][] ([RFC 2119][], [RFC 8174][]).

This package attempts to adhere to the [Package Development Standards](https://php-pds.com/) approach to [naming and versioning](https://php-pds.com/#naming-and-versioning).

## Interfaces

This package defines the following interfaces:

- [_FrontController_][] affords an entry point into the outermost presentation layer in any execution context (HTTP, CLI, etc.).

- [_FrontTypeAliases_][] provides custom PHPStan types to aid static analysis.

### _FrontController_

[_FrontController_][] affords an entry point into the outermost presentation
layer in any execution context (HTTP, CLI, etc.).

- Directives:

    - Implementations MUST gracefully handle all [_Throwable_][]s.

- Notes:

    - **Handle all possible exceptions.** The logic calling the front
      controller should not have to deal with any exceptions bubbling up from
      it. The implementation may accomplish this by catching [_Throwable_][]
      directly, by registering a [`set_exception_handler()`][] callback, or
      by some other means.

#### _FrontController_ Methods

- ```php
  public function run() : front_exit_status_int;
  ```
    - Runs the front controller.

    - Directives:

        - Implementations MUST report success by returning an integer `0`.

        - Implementations MUST report non-success by returning an integer
          between `1` and `254` (inclusive).

    - Notes:

        - **The return value is intended as an exit status code.** Exit
          status codes may be received initially by the in-process logic
          that invoked `run()` (bootstrap scripts, test harnesses, etc.),
          and may ultimately be received by a parent process (shell,
          supervisor, init system, CI runner, monitoring tool, or
          similar) via [`exit()`][]. Whether or not the exit status
          is consumed by the calling code or parent process depends
          on the execution environment: php-fpm and mod_php typically
          have no consumer, whereas worker loops, supervised long-running
          processes, runtime layers, and CI harnesses do.

        - **"Success" and "non-success" are context-dependent.** In an HTTP
          context, "success" typically means that the request was
          processed and a response was emitted regardless of the HTTP
          status code, whereas "non-success" may indicate that a
          [_Throwable_][] had to be handled by the _FrontController_
          itself. In a command line context, "success" typically
          means that the command completed without errors, whereas
          "non-success" may be one of several error conditions
          (cf. the [`sysexits.h`][] conventions where applicable).

        - **The exit status code `255` is reserved by PHP itself.** Cf.
          [`exit()`][]: "Exit codes should be in the range 0 to 254, the
          exit code 255 is reserved by PHP and should not be used."

### _FrontTypeAliases_

[_FrontTypeAliases_][] provides custom PHPStan types to aid static analysis.

- ```
  front_exit_status_int int<0,254>
  ```
    - An `int` exit status code: `0` for success, `1` to `254` for
      non-success. The value `255` is reserved by PHP itself.

## Implementations

Implementations MAY define additional class members not defined in these interfaces.

Notes:

- **Reference implementations** are available at <https://github.com/front-interop/impl>.

## Q & A

### Why `run()`?

The researched projects use one of six verbs for the front controller's main
method: `dispatch()`, `execute()`, `handle()`, `__invoke()`, `run()`, or
`start()`. Of these, `run()` is the clear majority at 12 of 23 projects;
the remaining five verbs together account for the other 11. Front-Interop
follows the majority practice.

### Why does `run()` return `int`?

Of the 23 researched projects:

- 11 return `void` or `null` and handle response-sending themselves;
- 7 return a response object for the calling code to send;
- 3 return some other value (`$this` or `mixed`);
- 1 (Tempest) `never` returns; and
- 1 (Symfony) returns an `int` exit code.

The majority of researched projects in an HTTP execution context have the
front controller handle response-sending itself and return nothing.

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
many different execution contexts, and keeps the interface machine-friendly.

### Why handle all [_Throwable_][]s?

Of the 23 researched projects, 21 handle exceptions in some way. The specific
handling location varies between projects: 4 in the bootstrap, 2 in the front
controller itself, and the remainder somewhere deeper in the call stack. For
that remainder, either the bootstrap or the front controller defines or
registers the handling logic.

Of the 21 projects that handle exceptions, 19 handle all types of
[_Throwable_][], 1 handles all types of [_Exception_][], and 1 handles only
specific exception subtypes.

Front-Interop observes that a front controller invocation occurs at
the outermost boundary of the presentation layer. This is the last point at
which any uncaught [_Throwable_][]s may be handled gracefully. The choice then is
whether they are handled by the bootstrap script, or by the front controller
proper.

In the interest of keeping such handling within a class, Front-Interop directs
that _FrontController_ itself must act as (or delegate to) a final backstop
against [_Throwable_][]s. There may be other handling subsystems in the logic called
by the _FrontController_, but any [_Throwable_][] that escapes them will be handled
by the _FrontController_ or its delegate.

* * *

[_Exception_]: https://php.net/Exception
[_FrontController_]: #frontcontroller
[_FrontTypeAliases_]: #fronttypealiases
[_Throwable_]: https://php.net/Throwable
[`exit()`]: https://php.net/exit
[`set_exception_handler()`]: https://php.net/set_exception_handler
[`sysexits.h`]: https://man7.org/linux/man-pages/man3/sysexits.h.3head.html
[BCP 14]: https://datatracker.ietf.org/doc/bcp14/
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
