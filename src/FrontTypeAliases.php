<?php
declare(strict_types=1);

namespace FrontInterop\Interface;

/**
 * [_FrontTypeAliases_][] provides custom PHPStan types to aid static analysis.
 *
 * - ```
 *   front_exit_status_int int<0,254>
 *   ```
 *     - An `int` exit status code: `0` for success, `1` to `254` otherwise.
 *       The value `255` is reserved by PHP itself.
 *
 * @phpstan-type front_exit_status_int int<0,254>
 */
interface FrontTypeAliases
{
}
