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

This package defines the following interface:

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

As such, pre-release review indicated that the front controller in an HTTP
execution context should handle sending the response itself and return nothing,
as do the majority of projects.

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
`run()` should return an integer exit status code. This is an unusual practice
for front controllers in an HTTP execution context, but imposes only a trivial
implementation burden. Doing so allows the same interface to be used across
many different execution contexts, and keeps the interface machine-friendly.

### Why handle all `Throwable`s?

Of the 23 researched projects, 21 handle exceptions in some way. The specific
handling location varies between projects: 4 in the bootstrap, 2 in the front
controller itself, and the remainder somewhere deeper in the call stack. For
that remainder, either the bootstrap or the front controller defines or
registers the handling logic.

Of the 21 projects that handle exceptions, 19 handle all types of `Throwable`,
1 handles all types of `Exception`, and 1 handles only specific exception
subtypes.

Front-Interop observes that a front controller invocation occurs at
the outermost boundary of the presentation layer. This is the last point at
which any uncaught `Throwable`s may be handled gracefully. The choice then is
whether they are handled by the bootstrap script, or by the front controller
proper.

In the interest of keeping such handling within a class, Front-Interop directs
that _FrontController_ itself must act as (or delegate to) a final backstop
against `Throwable`s. There may be other handling subsystems in the logic called
by the _FrontController_, but any `Throwable` that escapes them will be handled
by the _FrontController_ or its delegate.

* * *

[_Throwable_]: https://php.net/Throwable
[_FrontController_]: #frontcontroller
[BCP 14]: https://datatracker.ietf.org/doc/bcp14/
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
[`set_exception_handler()`]: https://php.net/set_exception_handler
[exit()]: https://php.net/exit
