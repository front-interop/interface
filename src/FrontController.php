<?php
declare(strict_types=1);

namespace FrontInterop\Interface;

/**
 * [_FrontController_][] affords an entry point into the outermost presentation
 * layer in any execution context (HTTP, CLI, etc.).
 *
 * @phpstan-import-type front_exit_status_int from FrontTypeAliases
 */
interface FrontController
{
    /**
     * Runs the front controller.
     *
     * - Directives:
     *
     *     - Implementations MUST return a value between `0` and `254`
     *       (inclusive).
     *
     *     - Implementations MUST return a value of `0` to report success.
     *
     *     - Implementations MUST return a value between `1` and `254`
     *       (inclusive) to report a negative outcome.
     *
     *     - Implementations SHOULD return a value of `1` to report an
     *       ordinary negative outcome.
     *
     *     - Implementations MAY return a value between `2` and `254`
     *       (inclusive) to distinguish among negative outcomes; the meanings
     *       of such values are explicitly undefined herein.
     *
     *     - Implementations MUST NOT terminate the process in place of
     *       returning from `run()`, whether by [`exit()`][], [`die()`][], or
     *       otherwise.
     *
     *     - Implementations MUST NOT allow a [_Throwable_][] to escape `run()`.
     *
     * - Notes:
     *
     *     - **The return value is intended as an exit status code.** Exit
     *       status codes may be received initially by the in-process logic
     *       that invoked `run()` (bootstrap scripts, test harnesses, etc.),
     *       and may ultimately be received by a parent process (shell,
     *       supervisor, init system, CI runner, monitoring tool, or
     *       similar) via [`exit()`][].
     *
     *     - **What counts as success is context-dependent.** In an HTTP
     *       context, returning `0` typically means that the request was
     *       processed and a response was emitted regardless of the HTTP status
     *       code. In a command line context, returning `0` typically means the
     *       command completed its work.
     *
     *     - **A negative outcome is not always an error.** A non-`0` return
     *       value reports something other than success: usually an error
     *       condition, but perhaps a partial failure, an empty result, or any
     *       other outcome the implementation wants to distinguish. Which of
     *       these counts as the ordinary negative outcome, if any, is
     *       implementation-specific.
     *
     *     - **A caught [_Throwable_][] does not require a non-`0` return.**
     *       For example, a front controller that catches a [_Throwable_][]
     *       and emits an HTTP `500` has arguably done its work, and may
     *       return `0`; another may hold that emitting that `500` is itself
     *       a negative outcome and return `1`.
     *
     *     - **The caller receives a return value, never a [_Throwable_][].**
     *       That holds for a [_Throwable_][] thrown from within a `catch`
     *       block as well as for the one that the `catch` was handling.
     *
     * @return front_exit_status_int
     */
    public function run() : int;
}
