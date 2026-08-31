# Exit Status Values

Front-Interop is based in part on research into how existing PHP command line
tooling assigns integer exit status values. The projects surveyed are:

- [atoum](https://github.com/atoum/atoum/blob/master/classes/scripts/runner.php) (atoum)
- [Aura.Cli](https://github.com/auraphp/Aura.Cli/blob/3.x/src/Status.php) (aura)
- [CakePHP Console](https://github.com/cakephp/cakephp/blob/5.x/src/Console/CommandInterface.php) (cakephp)
- [Composer](https://github.com/composer/composer/blob/2.10.2/src/Composer/Installer.php) (composer)
- [Drush](https://github.com/drush-ops/drush/blob/13.x/src/Commands/DrushCommands.php) (drush)
- [Laravel Console](https://github.com/laravel/framework/blob/master/src/Illuminate/Console/Command.php) (laravel)
- [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/src/Console/Command/FixCommandExitStatusCalculator.php) (phpcsfixer)
- [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.x/src/Util/ExitCode.php) (phpcs)
- [phpcpd](https://github.com/sebastianbergmann/phpcpd/blob/6.0.3/src/CLI/Application.php) (phpcpd)
- [phploc](https://github.com/sebastianbergmann/phploc/blob/7.0.2/src/CLI/Application.php) (phploc)
- [PHPMD](https://github.com/phpmd/phpmd/blob/master/src/main/php/PHPMD/TextUI/Command.php) (phpmd)
- [PHPStan](https://github.com/phpstan/phpstan-src/blob/2.1.x/src/Command/AnalyseCommand.php) (phpstan)
- [PHPUnit](https://github.com/sebastianbergmann/phpunit/blob/11.5/src/TextUI/Command/Result.php) (phpunit)
- [Psalm](https://psalm.dev/docs/running_psalm/command_line_usage/) (psalm)
- [Symfony Console](https://github.com/symfony/console/blob/7.3/Command/Command.php) (symfony)
- [Tempest Console](https://github.com/tempestphp/tempest-framework/blob/main/packages/console/src/ExitCode.php) (tempest)
- [WP-CLI](https://github.com/wp-cli/wp-cli/blob/main/php/class-wp-cli.php) (wpcli)
- [Yii Console](https://github.com/yiisoft/yii2/blob/master/framework/console/ExitCode.php) (yii)

Six of these (`aura`, `cakephp`, `laravel`, `symfony`, `tempest`, `yii`) are
also among the 23 projects in [README-RESEARCH.md](./README-RESEARCH.md); the
other twelve are widely used PHP command line tools, included because their
exit values are determinable from source or documentation.

The `atoum`, `phpcpd`, and `phploc` packages are marked abandoned on
Packagist. They are surveyed regardless, on the grounds that a settled project
still records how its authors answered the question. The `drush` and `wpcli`
projects serve single application ecosystems rather than PHP generally, and
are surveyed on the same grounds.

The remaining 17 researched projects were not examined individually. At least
four of them do provide a first-party console: `lithium` has
`lithium\console\Dispatcher`, `fuelphp` has `fuel/oil`, `phpixie` has
`phpixie/console`, and `phalcon` has `Phalcon\Cli\Console`. Their omission is
a limit of this survey rather than an absence of material.

Five of the surveyed projects declare a dependency on `symfony/console`. The
`laravel` class `Illuminate\Console\Command` extends
`Symfony\Component\Console\Command\Command` and defines no values of its own,
so it duplicates `symfony` rather than corroborating it. The `composer`,
`phpcsfixer`, and `phpstan` projects build their commands on it but define
their own values throughout. The `psalm` dependency is narrower than the
others: the surveyed binary parses its arguments with `getopt` and terminates
with integer literals, and Symfony Console backs only the separate
`psalm-plugin` binary. The `phpstan` requirement is declared by `phpstan-src`,
the source repository cited above, rather than by the `phpstan/phpstan` PHAR
distribution, whose published `composer.json` is a stub. Of the twelve
remaining, `drush` reaches `symfony/console` transitively through its
`consolidation/*` dependencies. The 18 surveyed projects represent 17 distinct
schemes.

## Constant Names

The names each project gives its exit status values, where it names them at
all.

|                | `0`            | `1`                     |
| -------------- | -------------- | ----------------------- |
| atoum (1)      |                |                         |
| aura           | `SUCCESS`      | `FAILURE`               |
| cakephp        | `CODE_SUCCESS` | `CODE_ERROR`            |
| composer       | `ERROR_NONE`   | `ERROR_GENERIC_FAILURE` |
| drush          | `EXIT_SUCCESS` | `EXIT_FAILURE`          |
| laravel (3)    | `SUCCESS`      | `FAILURE`               |
| phpcs          | `OKAY`         | `FIXABLE`               |
| phpcsfixer (5) |                |                         |
| phpcpd (1)     |                |                         |
| phploc (1)     |                |                         |
| phpmd          | `EXIT_SUCCESS` | `EXIT_EXCEPTION`        |
| phpstan (1)    |                |                         |
| phpunit        | `SUCCESS`      | `FAILURE`               |
| psalm (2)      |                |                         |
| symfony        | `SUCCESS`      | `FAILURE`               |
| tempest        | `SUCCESS`      | `ERROR`                 |
| wpcli (4)      |                |                         |
| yii            | `OK`           | `UNSPECIFIED_ERROR`     |

Notes:

(1) The `atoum`, `phpcpd`, `phploc`, and `phpstan` projects return or pass
integer literals directly, with no named constants.

(2) The `psalm` project publishes its values as documentation rather than as
class constants; its entry here comes from that documentation. A grep of its
source finds no exit-status constants of any kind.

(3) The `laravel` class `Illuminate\Console\Command` extends
`Symfony\Component\Console\Command\Command` and inherits its constants rather
than declaring its own.

(4) The `wpcli` project names no values. Its `WP_CLI::error()` halts with `1`
by default and accepts any integer of `1` or greater; `WP_CLI::halt()` takes a
code from its caller and defines none itself.

(5) The `phpcsfixer` project names its values above `1` as class constants but
leaves `0` and `1` unnamed; cf. note (6) under § *Values Above `1`*.

Every project reserves `0` for success. All but two also reserve `1` for the
negative outcome they treat as ordinary, and where `1` is named the sense is
generic: `yii` calls it `UNSPECIFIED_ERROR`, `composer`
`ERROR_GENERIC_FAILURE`, `drush` `EXIT_FAILURE`, and `aura` and `symfony`
`FAILURE`. The exceptions are `phpcs`, whose `1` is `FIXABLE` on the 4.x line,
so a scan finding only non-fixable issues returns `2`; and `phpcsfixer`, whose
source reserves `1` "for environment constraints not matched" and puts its
ordinary outcome, files needing fixing, at `8`.

The `aura` and `yii` projects define nothing between their generic failure at
`1` and `USAGE` at `64`, adopting the `sysexits.h` range instead.

## Values Above `1`

The shapes taken by the values above `1`:

- `sequential`: small integers in the low range, from `2` upward
- `sysexits`: the `sysexits.h` range, starting at `64`
- `bitmask`: powers of two, intended to be combined
- `shell`: the shell's reserved conventions, at `126` and above
- `none`: no values above `1` are defined

|            | sequential | sysexits | bitmask | shell  | none  |
| ---------- | ---------- | -------- | ------- | ------ | ----- |
| atoum      | x (1)      |          |         |        |       |
| aura       |            | x        |         |        |       |
| cakephp    |            |          |         |        | x     |
| composer   | x (2)      |          |         |        |       |
| drush      | x (3)      |          |         |        |       |
| laravel    | x (4)      |          |         |        |       |
| phpcs      |            |          | x (5)   |        |       |
| phpcsfixer |            |          | x (6)   |        |       |
| phpcpd     |            |          |         |        | x (7) |
| phploc     |            |          |         |        | x (8) |
| phpmd      | x (9)      |          |         |        |       |
| phpstan    | x (10)     |          |         |        |       |
| phpunit    | x (11)     |          |         |        |       |
| psalm      | x (12)     |          |         |        |       |
| symfony    | x (13)     |          |         |        |       |
| tempest    | x (14)     |          |         | x (14) |       |
| wpcli      |            |          |         |        | x (15)|
| yii        |            | x        |         |        |       |

Notes:

(1) The `atoum` project exits `2` when an exception escapes the runner,
including an unknown command line argument, and `3` from its error handler
during autorun, against `1` for a failing test run. Exceptions raised inside
tests count toward the `1`, not the `2`. A configuration callable that throws
exits with that exception's own code, which may be `0`.

(2) The `composer` project defines `ERROR_DEPENDENCY_RESOLUTION_FAILED` as
`2`, `ERROR_NO_LOCK_FILE_FOR_PARTIAL_UPDATE` as `3`, `ERROR_LOCK_FILE_INVALID`
as `4`, `ERROR_AUDIT_FAILED` as `5`, `ERROR_PSR_AUTOLOAD_VIOLATION` as `6`,
and `ERROR_TRANSPORT_EXCEPTION` as `100`. Its documented process exit codes
list only `0`, `1`, and `2`. Separately, `StatusCommand` composes `1`, `2`,
and `4` additively over disjoint bits, so that subcommand alone takes a
bitmask shape.

(3) The `drush` project defines `EXIT_FAILURE_WITH_CLARITY` as `3`, skipping
`2` entirely; cf. § *Findings Versus Failures*.

(4) Inherited from `symfony`; cf. note (13).

(5) The `phpcs` project, on its 4.x line, defines `1` as `FIXABLE`, `2` as
`NON_FIXABLE`, `4` as `FAILED_TO_FIX`, `16` as `PROCESS_ERROR`, and `64` as
`REQUIREMENTS_NOT_MET`. Its `ExitCode::calculate()` composes `1`, `2`, and `4`
with `|=`, so the documented `3`, `5`, and `7` are cumulative rather than
distinct: `1+2`, `1+4`, and `1+2+4`. The values `16` and `64` are terminal and
do not combine. On the 3.x line the scheme is inverted and unnamed: `1` means
errors were found and none are fixable, `2` that some are.

(6) The `phpcsfixer` project defines `EXIT_STATUS_FLAG_HAS_INVALID_FILES` as
`4`, `EXIT_STATUS_FLAG_HAS_CHANGED_FILES` as `8`,
`EXIT_STATUS_FLAG_HAS_INVALID_CONFIG` as `16`,
`EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG` as `32`, and
`EXIT_STATUS_FLAG_EXCEPTION_IN_APP` as `64`. Only `4`, `8`, and `64` pass
through `calculate()`'s `|=`; `16` and `32` arrive as exception codes.

(7) The `phpcpd` project returns `count($clones) > 0 ? 1 : 0`, and returns `1`
for its error conditions as well.

(8) The `phploc` project returns `1` for argument-parsing failure and for
finding no files to analyze, and `0` otherwise.

(9) The `phpmd` project defines `EXIT_VIOLATION` as `2` and `EXIT_ERROR` as
`3`, against `EXIT_EXCEPTION` at `1`.

(10) The `phpstan` project returns `2` for one narrow condition: when the run
would otherwise have returned `0`, `failWithoutResultCache` is set, and the
result cache was not used. A run with analysis errors returns `1` even when
those other two conditions hold.

(11) The `phpunit` project defines `2` as `EXCEPTION`, which its
`ShellExitCodeCalculator` returns when tests errored; it overrides the `1` of
`FAILURE` that assertion failures and the various `--fail-on-*` conditions
produce. A fault in PHPUnit itself is `CRASH`, defined as `255`; cf. § *The
Ceiling*.

(12) The `psalm` project documents `2` as "completed successfully but found
some issues", against `1` as "there was a problem running Psalm".

(13) The `symfony` project defines `2` as `INVALID`, for invalid command
input.

(14) The `tempest` project defines `2` as `INVALID` and `25` as `CANCELLED`,
then adopts the shell's reserved conventions above `125`: `126` as
`CANNOT_EXECUTE`, `127` as `COMMAND_NOT_FOUND`, `128` as `INVALID_EXIT_CODE`,
and `130` as `TERMINATED`. It is marked twice above, under both shapes.

(15) The `wpcli` project names no values above `1`, and its `WP_CLI::halt()`
accepts any code a caller supplies. Two literals do appear outside the ordinary
command path: `3` when the Composer autoloader cannot be found, and `255`
passed through from a failed SSH connection.

Of the 18 projects, ten assign sequential values above `1`, two adopt the
`sysexits` offset, two use a bitmask, and four define nothing above `1` at
all; `tempest` alone also adopts the shell's reserved conventions, and is
counted in two columns. Discounting `laravel`, which inherits `symfony`'s
values, that is nine sequential, two `sysexits`, two bitmask, one shell, and
four none across the 17 distinct schemes.

## Findings Versus Failures

Several of the surveyed tools report on work they were asked to inspect, and
so must distinguish "the tool could not run" from "the tool ran, and here is
what it found." They agree that the distinction is worth making, and disagree
about which side gets `1`.

The test runners give `1` to the finding and push tool errors above it:

- The `phpunit` project reports assertion failures with `FAILURE` (`1`) and
  tests that errored with `EXCEPTION` (`2`), reserving `255` for a fault in
  PHPUnit itself.

- The `atoum` project exits `1` for a failing test run, exceptions raised
  inside tests included, and pushes `2` and `3` to exceptions escaping the
  runner and to its own error handler.

The analysis and application tools do the reverse, giving `1` to the failure
and the higher value to the finding:

- The `psalm` project exits `1` "when there was a problem running Psalm" and
  `2` "when it completed successfully but found some issues".

- The `phpmd` project returns `EXIT_EXCEPTION` (`1`) when an exception is
  caught, and `EXIT_VIOLATION` (`2`) when violations were detected. It is the
  one project with a failure on each side of its finding: `EXIT_ERROR` (`3`)
  reports processing errors, and is checked before violations are.

- The `composer` project documents `1` as "Generic/unknown error code" and `2`
  as "Dependency solving error code".

- The `drush` project names the distinction outright. Its
  `EXIT_FAILURE_WITH_CLARITY` (`3`) is documented as being "used to signal
  that the command completed successfully, but we still want to indicate a
  failure to the caller," against `EXIT_FAILURE` at `1`.

Two projects draw no distinction, leaving the two cases indistinguishable to a
caller. The `phpstan` project returns `1` for analysis errors and setup
failures alike; the `phpcpd` project returns `1` both when duplication is
found and when it cannot complete.

The split tracks what each tool treats as its ordinary negative outcome. For a
test runner that is a failing test, so failure takes `1` and anything else is
exceptional. For an analyzer it is being unable to run, so the finding is the
distinguished case and takes a higher value.

The test-runner arrangement is the one codified for several POSIX utilities,
where the finding takes `1` and errors are pushed to `2` and above: in `grep`,
`1` means no lines were selected; in `diff`, `1` means differences were found;
in both, `>1` means an error occurred.

## The Ceiling

Three surveyed projects police the range, by three different means, and none
stops at `254`.

The `symfony` project clamps. `Application::run()` reduces any exit code
greater than `255` to `255`. The clamp sits inside the `autoExit` branch,
however, so it governs only the value handed to
[`exit()`](https://php.net/exit) on that path; under `setAutoExit(false)` a
caller receives the unclamped integer, and the two signal-handling `exit()`
calls elsewhere in the class are unclamped as well.

The `tempest` project rejects. Its `ConsoleApplication` throws
`ExitCodeWasInvalid` when a code is below `0` or above `255`.

The `cakephp` project coerces, and does so on the value its caller receives
rather than on a value it is about to pass to [`exit()`](https://php.net/exit):
`CommandRunner::run()` returns the command's result if it falls between `0`
and `255`, and `CommandInterface::CODE_ERROR` otherwise.

All three admit `255`, which PHP itself reserves; cf.
[`exit()`](https://php.net/exit): "Exit codes should be in the range 0 to 254,
the exit code 255 is reserved by PHP and should not be used."

The `phpunit` project goes further and occupies it. `Result::CRASH` is defined
as `255`, and `Application` calls `exit(Result::CRASH)` when PHPUnit itself
fails rather than when a test does. No surveyed project treats `254` as the
ceiling, as PHP's own documentation advises.

## Returning and Exiting

The `symfony` method `Command::execute()` is declared `: int`, and
`Application::run()` returns that value to its caller. It also calls
`exit($exitCode)` itself when its `autoExit` property is set, which it is by
default; `setAutoExit(false)` disables that, leaving the caller to terminate.

Four projects terminate directly rather than returning a status. The `atoum`
project calls `exit()` from its autorun path, and `wpcli` exposes
`WP_CLI::halt()` and an `error()` that halts by default. The `psalm` and
`tempest` entry points are both declared `void`: `psalm` exits with literals
throughout, and `tempest`'s `ConsoleApplication::run()` ends in a
`Kernel::shutdown()` declared `never`.

Two projects diverge on the return type itself. The `cakephp` method
`CommandInterface::run()` is declared `: ?int` and documented as "Exit code or
null for success," admitting `null` alongside the integer. The `tempest`
console command signature is wider still, returning `ExitCode|int` and so
admitting an enum case where the others admit only a scalar.

## Considered But Not Included

The following were considered but not surveyed:

- Wrappers around a tool already surveyed, whose exit values are those of the
  tool they wrap: Pest and ParaTest (PHPUnit), Laravel Pint (PHP-CS-Fixer),
  and Artisan, Envoy, and Laravel Zero (Symfony Console).

- Tools with no exit status contract to survey: Laravel Sail, Laravel Herd,
  Laravel Valet, PHP's built-in web server, and PsySH.

- PHPCompatibility, which is a `phpcs` ruleset rather than a command of its
  own.

- Symfony CLI and local-php-security-checker, which are distributed as Go
  binaries rather than as PHP.

- Behat, Codeception, phpspec, Infection, Robo, Deployer, CaptainHook,
  GrumPHP, phpDocumentor, and Deptrac, each of which requires
  `symfony/console`. The `qossmic/deptrac` package is superseded by
  `deptrac/deptrac`, whose 18-package require block includes it.
  Including them would raise the project count without adding distinct
  schemes, for the same reason `laravel` is discounted from the counts above.
  Infection in particular documents `--ignore-msi-with-no-mutations` as
  forcing "a zero exit code even when the required MSI is not reached", which
  implies a non-zero value for an unmet threshold; because the value itself is
  not stated, it is omitted rather than inferred.

- Rector, whose published `composer.json` requires only `php` and
  `phpstan/phpstan`, so its lineage is not determinable without deeper
  inspection than this survey applied.

- Phive, which is published on Packagist but distributed for use as a
  standalone PHAR rather than as a Composer-installed dependency.

- Box, PHP_Depend, and Bref, which were not examined; no claim is made here
  about the values they use.
