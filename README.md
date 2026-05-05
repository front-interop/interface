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

- [_FrontController_][] affords an entry point into the outermost presentation layer in any execution context (HTTP, CLI, etc.).

### _FrontController_

[_FrontController_][] affords an entry point into the outermost presentation
layer in any execution context (HTTP, CLI, etc.).

- Directives:

    - Implementations MUST gracefully handle all [_Throwable_][]s.

- Notes:

    - **Handle all possible exceptions.** The logic calling the front
      controller should not have to deal with any exceptions bubbling up from
      it. This may be accomplished by catching all [_Throwable_][]s or using
      [`set_exception_handler()`][].

#### _FrontController_ Methods

- ```php
  public function run() : int;
  ```
    - Runs the front controller.

    - Directives:

        - Implementations MUST return a meaningful exit code.

    - Notes:

        - **Return `0` on success, `1` (or another non-zero exit code) on
          failure.** Because this interface is intended to be usable in any
          execution context, it should be machine-friendly. Returning an exit
          code helps to make it so.

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
execution context should handle sending the response, as do the majority of
projects. However, a front controller in a CLI execution context will need to
return an exit code.

While providing two interfaces (one to return `void` and another to return
`int`) would cover both cases, it leads to inconsistencies in setup and
expectations.

Thus, contra the most common `void`/`null` return, Front-Interop directs that
`run()` should return an integer exit code. This is an unusual practice for
front controllers in an HTTP execution context, but imposes only a trivial
implementation burden. Doing so allows the same interface to be used in CLI and
other execution contexts, and keeps the interface machine-friendly.

### Why does the front controller handle all `Throwable`s?

A front controller sits at the outermost edge of the presentation layer:
the only code calling `run()` is a bootstrap script. That script has no
meaningful way to recover from arbitrary errors raised inside the
application — output to the response stream (HTTP body or CLI) has
typically already begun, and the bootstrap lacks the application context
to render a meaningful error page or message.

Requiring the front controller to handle all `Throwable`s internally —
either by catching them or by registering [`set_exception_handler()`][] —
keeps the bootstrap simple and consistent across implementations: it can
always assume `run()` returns an exit code, never throws.

* * *

[_Throwable_]: https://php.net/Throwable
[_FrontController_]: #frontcontroller
[BCP 14]: https://datatracker.ietf.org/doc/bcp14/
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
[`set_exception_handler()`]: https://php.net/set_exception_handler
