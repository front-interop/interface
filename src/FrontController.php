<?php
declare(strict_types=1);

namespace FrontInterop\Interface;

/**
 * [_FrontController_][] affords an entry point into the outermost presentation
 * layer in any execution context (HTTP, CLI, etc.).
 *
 * - Directives:
 *
 *     - Implementations MUST gracefully handle all [_Throwable_][]s.
 *
 * - Notes:
 *
 *     - **Handle all possible exceptions.** The logic calling the front
 *       controller should not have to deal with any exceptions bubbling up from
 *       it. This may be accomplished by catching all [_Throwable_][]s or using
 *       [`set_exception_handler()`][].
 */
interface FrontController
{
    /**
     * Runs the front controller.
     *
     * - Directives:
     *
     *     - Implementations MUST return a meaningful exit code.
     *
     * - Notes:
     *
     *     - **Return `0` on success, `1` (or another non-zero exit code) on
     *       failure.** Because this interface is intended to be usable in any
     *       execution context, it should be machine-friendly. Returning an exit
     *       code helps to make it so.
     */
    public function run() : int;
}
