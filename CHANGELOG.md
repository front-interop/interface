# Change Log

## 1.0.0-beta2

Refine the directives in light of exit status research.

- Direct that implementations must return a value between `0` and `254`
  (inclusive).

- Direct that implementations must not terminate the process in place of
  returning from `run()`, rather than that they must not avoid returning.

- Direct that implementations must not allow a `Throwable` to escape `run()`,
  in place of directing that they gracefully handle all `Throwable`s.
  Catching no longer implies a non-`0` return; what `run()` returns after the
  catch is up to the implementation.

- Note that the caller receives a return value, never a `Throwable`, not
  even one arising within a `catch` block.

- Recommend returning `1` for an ordinary negative outcome, and leave the
  meanings of `2` through `254` explicitly undefined.

- Add README-RETURNS.md, a survey of exit status conventions in 18 PHP
  command line projects, as a second source from which the directives are
  derived.

- Expand README-EXCEPTIONS.md with a survey of handler registration beyond
  the bootstrap-and-one-hop scope, and remove its opening summary; the
  cross-cutting patterns section already carries those points.

- Correct the handler findings in README-EXCEPTIONS.md; a global handler
  fires only after a `Throwable` escapes, so fatfree and flightphp fail the
  directive, and six projects register a handler in scope, not four.

- Correct the flightphp exit-path finding in README-RESEARCH.md; of the two
  self-reached exits, only bear's sits outside the front controller.

- Add README questions on the reason to return and not exit, on the `1`
  recommendation, on the undefined `2` through `254`, and on the `254`
  ceiling.

- Explain in the README why a registered `set_exception_handler()` cannot
  replace the `run()` backstop.

- Remove the "Why an instance method?" question from the README.

- Remove the `255` note from `run()`; the README now gives the reason for
  the `254` ceiling, and the type alias keeps the statement.

- Remove the note on which execution contexts consume an exit status.

- Documentation, typographical, and tooling refinements.

- No API changes.

## 1.0.0-beta1

Incorporate notes and from public review.

- Direct that `run()` must not `exit()`, `die()`, or otherwise avoid returning.

- No API changes.

## 1.0.0-alpha1

Ready for public review.

