# Front-Interop Standard Interface Package

Front-Interop provides an interoperable package of standard interfaces for
front controller functionality in any execution context (HTTP, CLI, etc.). It
reflects, refines, and reconciles the common practices identified within
[several pre-existing projects][README-RESEARCH.md].

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED",  "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [BCP 14][] ([RFC 2119][], [RFC 8174][]).

## Interfaces

This package defines the following interface:

- [_FrontController_] affords an entry point into the outermost presentation layer in any execution context (HTTP, CLI, etc.).

### _FrontController_

[_FrontController_] affords an entry point into the outermost presentation
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

- Directives:

    - Implementations MAY define additional class members not defined in these
      interfaces.

- Notes:

    - **Reference implementations** may be found at
      <https://github.com/front-interop/impl>.

## Q & A

### Why does `run()` return `int`?

Most of the researched front controllers handle response-sending internally and
return `void`. One (Symfony) handles response-sending internally and returns an
`int` exit code. The remainder return a response to be sent by the calling logic
(typically a bootstrap script).

As such, pre-release review indicated that the front controller in an HTTP
execution context should handle sending the response, as do the majority of
projects. However, a front controller in a CLI execution context will need to
return an exit code.

While providing two interfaces (one to return `void` and another to return
`int`) would cover both cases, it leads to inconsistencies in setup and
expectations.

Thus, contra the majority of `void` returns, Front-Interop directs that `run()`
should return an integer exit code. This is an unusual practice for front
controllers in an HTTP execution context, but imposes only a trivial
implementation burden. Doing so allows the same interface to be used in CLI and
other execution contexts, and keeps the interface machine-friendly.

* * *

[_Throwable_]: https://php.net/Throwable
[_FrontController_]: #frontcontroller
[BCP 14]: https://www.rfc-editor.org/info/bcp14
[PSR-11]: https://www.php-fig.org/psr/psr-11/
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
[`set_error_handler()`]: https://php.net/set_error_handler
[`set_exception_handler()`]: https://php.net/set_exception_handler
