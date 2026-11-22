# Exit Status Values

Front-Interop is based in part on research into how existing PHP command line
tooling assigns integer exit status values. The projects surveyed are:

- [atoum](https://github.com/atoum/atoum/blob/master/classes/scripts/runner.php) (atoum)
- [Aura.Cli](https://github.com/auraphp/Aura.Cli/blob/3.x/src/Status.php) (aura)
- [BEAR.Cli](https://github.com/bearsunday/BEAR.Cli/blob/1.0.0/src/ResourceCommand.php) (bear)
- [CakePHP Console](https://github.com/cakephp/cakephp/blob/5.x/src/Console/CommandInterface.php) (cakephp)
- [Composer](https://github.com/composer/composer/blob/2.10.2/src/Composer/Installer.php) (composer)
- [Drush](https://github.com/drush-ops/drush/blob/13.x/src/Commands/DrushCommands.php) (drush)
- [Flight Runway](https://github.com/flightphp/runway/blob/v1.2.6/runway) (flightphp)
- [FuelPHP Oil](https://github.com/fuel/oil/blob/1.8.2/classes/command.php) (fuelphp)
- [Joomla Console](https://github.com/joomla-framework/console/blob/3.0.3/src/Application.php) (joomla)
- [Kohana Minion](https://github.com/kohana/minion/blob/v3.3.6/classes/Kohana/Minion/Exception.php) (kohana)
- [laminas-cli](https://github.com/laminas/laminas-cli/blob/1.16.0/bin/laminas) (laminas)
- [Laravel Console](https://github.com/laravel/framework/blob/master/src/Illuminate/Console/Command.php) (laravel)
- [Leaf Sprout](https://github.com/leafsphp/sprout/blob/v5.1.2/src/Sprout/App.php) (leafphp)
- [Lithium Console](https://github.com/UnionOfRAD/lithium/blob/1.2/console/Command.php) (lithium)
- [Nette Command Line](https://github.com/nette/command-line/blob/v2.0.0/readme.md) (nette)
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
- [Yii Console](https://github.com/yiisoft/yii-console/blob/master/src/ExitCode.php) (yii)

Fifteen of these are first-party consoles of projects among the 23 in
[README-RESEARCH.md](./README-RESEARCH.md), published by the project itself
whether or not its skeleton installs them; because `laminas-cli` serves both
`laminas` and `mezzio`, they account for 16 of the 23. Of the other seven,
`fatfree` routes command line requests through the same `run()` it uses for
HTTP, `klein` provides no console, and the consoles of `lightmvc`, `phalcon`,
`phpixie`, `silex`, and `slim` are described under § *Considered But Not
Included*. The remaining twelve surveyed projects are widely used PHP command
line tools, included because their exit values are determinable from source
or documentation.

The `atoum`, `phpcpd`, and `phploc` packages, and `kohana`'s `kohana/minion`,
are marked abandoned on Packagist. They are surveyed anyway, because their
exit values still show how their authors handled the question. The `drush`
and `wpcli` projects serve single application ecosystems rather than PHP
generally, and are surveyed anyway.

Seven of the surveyed projects declare a dependency on `symfony/console`. The
`laravel` class `Illuminate\Console\Command` extends
`Symfony\Component\Console\Command\Command` and defines no values of its own,
so it duplicates `symfony` rather than corroborating it. The `laminas` console
runs a Symfony `Application` and, apart from one value of its own taken up
under § *The Ceiling*, duplicates `symfony` as well. The `joomla` console
pairs Symfony's input and output classes with an `Application` and an
`AbstractCommand` of its own, and Joomla's commands define their own values
above `1`. The `composer`, `phpcsfixer`, and `phpstan` projects build their
commands on Symfony Console but define their own values throughout. The
`psalm` dependency is narrower than the others: the surveyed binary parses its
arguments with `getopt` and terminates with integer literals, and Symfony
Console backs only the separate `psalm-plugin` binary. The `phpstan`
requirement is declared by `phpstan-src`, the source repository cited above,
rather than by the `phpstan/phpstan` PHAR distribution, whose published
`composer.json` is a stub. Among the rest, `drush` reaches `symfony/console`
transitively through its `consolidation/*` dependencies. Discounting `laravel`
and `laminas`, the 27 surveyed projects represent 25 distinct schemes.

## Constant Names

The names each project gives its exit status values, where it names them at
all.

|                | `0`            | `1`                     |
| -------------- | -------------- | ----------------------- |
| atoum (1)      |                |                         |
| aura           | `SUCCESS`      | `FAILURE`               |
| bear (1)       |                |                         |
| cakephp        | `CODE_SUCCESS` | `CODE_ERROR`            |
| composer       | `ERROR_NONE`   | `ERROR_GENERIC_FAILURE` |
| drush          | `EXIT_SUCCESS` | `EXIT_FAILURE`          |
| flightphp (6)  |                |                         |
| fuelphp (1)    |                |                         |
| joomla (7)     | `SUCCESS`      | `FAILURE`               |
| kohana (8)     |                |                         |
| laminas (3)    | `SUCCESS`      | `FAILURE`               |
| laravel (3)    | `SUCCESS`      | `FAILURE`               |
| leafphp (1)    |                |                         |
| lithium (5)    |                |                         |
| nette (2)      |                |                         |
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

(1) The `atoum`, `bear`, `fuelphp`, `leafphp`, `phpcpd`, `phploc`, and
`phpstan` projects return or pass integer literals directly, with no named
constants.

(2) The `nette` and `psalm` projects publish their values as documentation
rather than as class constants; their entries here come from that
documentation. A grep of `psalm`'s source finds no exit-status constants of
any kind, and `nette`'s `ParseResult::run()` returns whatever the command's
handler returns, or `0` when it returns nothing.

(3) Inherited from `symfony`; cf. the introduction above. The `laminas`
skeleton's installer offers `laminas-cli` as an option, and `mezzio`
receives it as a development dependency of `mezzio-tooling`.

(4) The `wpcli` project names no values. Its `WP_CLI::error()` halts with `1`
by default and accepts any integer of `1` or greater; `WP_CLI::halt()` takes a
code from its caller and defines none itself.

(5) The `lithium` and `phpcsfixer` projects name their values above `1` as
class constants but leave `0` and `1` unnamed; cf. their notes under
§ *Values Above `1`*. A `lithium` command may instead return a boolean:
`true` or `null` sets the status to `0`, while `false` leaves it at `1`, as an
exception does.

(6) The `flightphp` console, Runway, is built on the `adhocore/cli` library.
Runway's own commands return the literals `0` and `1`; the library adds
`127` and `255`. Neither names any value.

(7) Joomla's commands mostly use Symfony's constants: 22 of the 32 in the CMS
return `Command::SUCCESS` and `Command::FAILURE`, while `joomla/console`
itself names no values. Seven others declare constants of their own, per
command, such as `REMOVE_SUCCESSFUL` and `REMOVE_FAILED`. The remaining
three return literals or, in the case of `tasks:run`, the scheduler's task
status codes; cf. § *Values Above `1`*.

(8) The `kohana` console, Minion, names no values. It is a module that the
stock bootstrap ships commented out.

Every project reserves `0` for success. All but three also reserve `1` for
the negative outcome they treat as ordinary, and where `1` is named the sense
is mostly generic: `yii` calls it `UNSPECIFIED_ERROR`, `composer`
`ERROR_GENERIC_FAILURE`, `drush` `EXIT_FAILURE`, and `aura` and `symfony`
`FAILURE`, while `phpmd`'s `EXIT_EXCEPTION` names a cause, as does one
`joomla` command's `CONFIG_GET_GROUP_NOT_FOUND`. The three exceptions are
`phpcs`, whose `1` is `FIXABLE` on the 4.x line, so a scan finding only
non-fixable issues returns `2`; `phpcsfixer`, whose source reserves `1` "for
environment constraints not matched" and puts its ordinary outcome, files
needing fixing, at `8`; and `nette`, which documents only `0` and `2` and
leaves `1` without a meaning.

The `aura` and `yii` projects define nothing between their generic failure at
`1` and `USAGE` at `64`, adopting the `sysexits.h` range instead.

## Values Above `1`

The shapes taken by the values above `1`:

- `sequential`: small integers in the low range, from `2` upward
- `sysexits`: the `sysexits.h` range, starting at `64`
- `bitmask`: powers of two, intended to be combined
- `shell`: the shell's reserved conventions, at `126` and above
- `none`: no values above `1` are defined

|            | sequential | sysexits | bitmask | shell  | none   |
| ---------- | ---------- | -------- | ------- | ------ | ------ |
| atoum      | x (1)      |          |         |        |        |
| aura       |            | x        |         |        |        |
| bear       | x (2)      |          |         |        |        |
| cakephp    |            |          |         |        | x      |
| composer   | x (3)      |          |         |        |        |
| drush      | x (4)      |          |         |        |        |
| flightphp  |            |          |         | x (5)  |        |
| fuelphp    |            |          |         |        | x (6)  |
| joomla     | x (7)      |          |         | x (7)  |        |
| kohana     |            |          |         |        | x (8)  |
| laminas    | x (9)      |          |         |        |        |
| laravel    | x (9)      |          |         |        |        |
| leafphp    |            |          |         |        | x (10) |
| lithium    | x (11)     |          |         |        |        |
| nette      | x (12)     |          |         |        |        |
| phpcs      |            |          | x (13)  |        |        |
| phpcsfixer |            |          | x (14)  |        |        |
| phpcpd     |            |          |         |        | x (15) |
| phploc     |            |          |         |        | x (16) |
| phpmd      | x (17)     |          |         |        |        |
| phpstan    | x (18)     |          |         |        |        |
| phpunit    | x (19)     |          |         |        |        |
| psalm      | x (20)     |          |         |        |        |
| symfony    | x (21)     |          |         |        |        |
| tempest    | x (22)     |          |         | x (22) |        |
| wpcli      |            |          |         |        | x (23) |
| yii        |            | x        |         |        |        |

Notes:

(1) The `atoum` project exits `2` when an exception escapes the runner,
including an unknown command line argument, and `3` from its error handler
during autorun, against `1` for a failing test run. Exceptions raised inside
tests count toward the `1`, not the `2`. A configuration callable that throws
exits with that exception's own code, which may be `0`.

(2) The `bear` console returns `2` for a server error, a response of `500`
or above, and for any unexpected exception; cf. § *Findings Versus
Failures*.

(3) The `composer` project defines `ERROR_DEPENDENCY_RESOLUTION_FAILED` as
`2`, `ERROR_NO_LOCK_FILE_FOR_PARTIAL_UPDATE` as `3`, `ERROR_LOCK_FILE_INVALID`
as `4`, `ERROR_AUDIT_FAILED` as `5`, `ERROR_PSR_AUTOLOAD_VIOLATION` as `6`,
and `ERROR_TRANSPORT_EXCEPTION` as `100`. Its documented process exit codes
list only `0`, `1`, and `2`. Separately, `StatusCommand` composes `1`, `2`,
and `4` additively over disjoint bits, so that subcommand alone takes a
bitmask shape.

(4) The `drush` project defines `EXIT_FAILURE_WITH_CLARITY` as `3`, skipping
`2` entirely; cf. § *Findings Versus Failures*.

(5) The `flightphp` console exits `127` when no command matches, from
`adhocore/cli`, echoing the shell's `127` for a command not found. Its
`255` is taken up under § *The Ceiling*.

(6) The `fuelphp` console defines nothing above `1`, but `oil test` exits with
PHPUnit's own exit code, ORed with that of a preceding `cd`, so PHPUnit's
values reach the caller. Its `fromdb` task ends with a bare `exit()`, and so
with `0`, after reporting an error.

(7) Joomla's commands assign small values above `1` per command:
`ExtensionRemoveCommand` runs from `REMOVE_NOT_FOUND` (`2`) to
`REMOVE_INVALID_TYPE` (`6`), while `2` is `CONFIG_VALIDATION_FAILED` in
`SetConfigurationCommand`, `ERR_UPDATE_FAILED` in `UpdateCoreCommand`, and an
invalid argument in `TasksStateCommand`. A command disabled by an event
listener returns `RETURN_CODE_DISABLED` (`113`), as in `symfony`. The
`tasks:run` command returns the scheduler's status codes: `125` when no task
is found or due; for a single task, the status it ended with, such as `3`
(failed to run), `123` (will resume), or `127` (routine missing, echoing the
shell's command not found); and for several tasks, `0` however they ended.

(8) The `kohana` console, Minion, exits with the code of whatever exception
reaches its handler, substituting `1` only for `0`. Kohana's error handler
converts PHP errors into exceptions whose code is the error level, so
`E_WARNING` exits `2` and `E_NOTICE` `8`; cf. § *The Ceiling* for the levels
above `255`.

(9) Inherited from `symfony`; cf. note (21). The `laminas` console also exits
`255` when it cannot find the application's container; cf. § *The Ceiling*.

(10) The `leafphp` console, Sprout, returns `1` for an unknown command or a
missing required option, and otherwise whatever its command returns. It took
that form in 5.1.2, released 2026-11-09. Until then a fresh `leafphp`
skeleton resolved Sprout 0.9.2, whose `run()` handed a command's result to
the skeleton's `loadConsole()`, which discarded it and exited `0` after any
run that did not throw.

(11) The `lithium` project defines `EXIT_NO_TEST` as `4`, which its `test`
command returns when no test is mapped to the file given. A command it cannot
find exits `0`: the dispatcher returns the error message in place of a
status, and [`exit()`](https://php.net/exit) prints a string argument rather
than using it as a status.

(12) The `nette` project documents `2` for a wrong command line, which its
parser reports as a `UsageException`, calling it "the usual way to report a
wrong command line".

(13) The `phpcs` project, on its 4.x line, defines `1` as `FIXABLE`, `2` as
`NON_FIXABLE`, `4` as `FAILED_TO_FIX`, `16` as `PROCESS_ERROR`, and `64` as
`REQUIREMENTS_NOT_MET`. Its `ExitCode::calculate()` composes `1`, `2`, and `4`
with `|=`, so the documented `3`, `5`, and `7` are cumulative rather than
distinct: `1+2`, `1+4`, and `1+2+4`. The values `16` and `64` are terminal and
do not combine. On the 3.x line the scheme is inverted and unnamed: `1` means
errors were found and none are fixable, `2` that some are.

(14) The `phpcsfixer` project defines `EXIT_STATUS_FLAG_HAS_INVALID_FILES` as
`4`, `EXIT_STATUS_FLAG_HAS_CHANGED_FILES` as `8`,
`EXIT_STATUS_FLAG_HAS_INVALID_CONFIG` as `16`,
`EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG` as `32`, and
`EXIT_STATUS_FLAG_EXCEPTION_IN_APP` as `64`. Only `4`, `8`, and `64` pass
through `calculate()`'s `|=`; `16` and `32` arrive as exception codes.

(15) The `phpcpd` project returns `count($clones) > 0 ? 1 : 0`, and returns `1`
for its error conditions as well.

(16) The `phploc` project returns `1` for argument-parsing failure and for
finding no files to analyze, and `0` otherwise.

(17) The `phpmd` project defines `EXIT_VIOLATION` as `2` and `EXIT_ERROR` as
`3`, against `EXIT_EXCEPTION` at `1`.

(18) The `phpstan` project returns `2` for one narrow condition: when the run
would otherwise have returned `0`, `failWithoutResultCache` is set, and the
result cache was not used. A run with analysis errors returns `1` even when
those other two conditions hold.

(19) The `phpunit` project defines `2` as `EXCEPTION`, which its
`ShellExitCodeCalculator` returns when tests errored; it overrides the `1` of
`FAILURE` that assertion failures and the various `--fail-on-*` conditions
produce. A fault in PHPUnit itself is `CRASH`, defined as `255`; cf. § *The
Ceiling*.

(20) The `psalm` project documents `2` as "completed successfully but found
some issues", against `1` as "there was a problem running Psalm".

(21) The `symfony` project defines `2` as `INVALID`, for invalid command
input, and `ConsoleCommandEvent::RETURN_CODE_DISABLED` as `113`, for a
command that an event listener has disabled.

(22) The `tempest` project defines `2` as `INVALID` and `25` as `CANCELLED`,
then adopts the shell's reserved conventions above `125`: `126` as
`CANNOT_EXECUTE`, `127` as `COMMAND_NOT_FOUND`, `128` as `INVALID_EXIT_CODE`,
and `130` as `TERMINATED`.

(23) The `wpcli` project names no values above `1`, and its `WP_CLI::halt()`
accepts any code a caller supplies. Two literals do appear outside the ordinary
command path: `3` when the Composer autoloader cannot be found, and `255`
passed through from a failed SSH connection.

Discounting `laravel` and `laminas`, which inherit `symfony`'s values, the
27 projects represent 25 distinct schemes: 13 sequential, two `sysexits`,
two bitmask, three shell, and seven none. Those figures sum to 27 because
`joomla` and `tempest` are each counted under both sequential and shell.

## Findings Versus Failures

Several of the surveyed tools report on work they were asked to inspect, and
so must distinguish "the tool could not run" from "the tool ran, and here is
what it found." Most of them do, but they disagree about which side gets `1`.

The test runners give `1` to the finding and push tool errors above it, and
one analyzer does the same:

- The `phpunit` project reports assertion failures with `FAILURE` (`1`) and
  tests that errored with `EXCEPTION` (`2`), reserving `255` for a fault in
  PHPUnit itself.

- The `atoum` project exits `1` for a failing test run, exceptions raised
  inside tests included, and pushes `2` and `3` to exceptions escaping the
  runner and to its own error handler.

- The `phpcs` project, on its 4.x line, reports fixable issues with `FIXABLE`
  (`1`) and non-fixable ones with `NON_FIXABLE` (`2`), reserving
  `PROCESS_ERROR` (`16`) for a processing error.

Several analysis and application tools do the reverse, giving `1` to the
failure and the higher value to the finding:

- The `psalm` project exits `1` "when there was a problem running Psalm" and
  `2` "when it completed successfully but found some issues".

- The `phpmd` project returns `EXIT_EXCEPTION` (`1`) when an exception is
  caught, and `EXIT_VIOLATION` (`2`) when violations were detected. It is one
  of two projects with a failure on each side of its finding: `EXIT_ERROR`
  (`3`) reports processing errors, and is checked before violations are.

- The `phpcsfixer` project reserves `1` "for environment constraints not
  matched" and flags files needing fixing with `8`. An exception in the
  application sets `64`, so it too has a failure on each side of its finding.

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

The split follows what each tool treats as its ordinary negative outcome. For a
test runner that is a failing test, so failure takes `1` and anything else is
exceptional. For most analyzers it is being unable to run, so the finding is
the distinguished case and takes a higher value.

The test-runner arrangement is the one codified for several POSIX utilities,
where the finding takes `1` and errors are pushed to `2` and above: in `grep`,
`1` means no lines were selected; in `diff`, `1` means differences were found;
in both, `>1` means an error occurred.

Among the consoles, `bear` draws the same line for a resource request. A
client error, a response of `400` or above, returns `1`, as does a missing
required option; a server error, `500` or above, returns `2`, as does any
other exception. The refused request is the ordinary negative outcome, and
a fault in the application is pushed above it.

## The Ceiling

Four surveyed projects check the range of exit codes, by three different
means, and none stops at `254`.

The `symfony` project clamps. `Application::run()` reduces any exit code
greater than `255` to `255`, but only inside its `autoExit` branch, on the
value it hands to [`exit()`](https://php.net/exit). Under
`setAutoExit(false)` a caller receives the unclamped integer, and the class's
two signal-handling `exit()` calls are unclamped too:
`ConsoleSignalEvent::setExitCode()` rejects a code outside `0` through `255`,
but a signalable command's `handleSignal()` may then replace it unchecked.

The `joomla` project clamps in the same way and in the same place. Its
`Application::execute()` reduces a code greater than `255` to `255` only when
it is about to exit, and under `setAutoExit(false)` it returns nothing at
all; cf. § *Returning and Exiting*.

The `tempest` project rejects. Its `ConsoleApplication` throws
`ExitCodeWasInvalid` when a code is below `0` or above `255`.

The `cakephp` project coerces, and does so on the value its caller receives
rather than on a value it is about to pass to [`exit()`](https://php.net/exit):
`CommandRunner::run()` returns the command's result if it falls between `0`
and `255`, and `CommandInterface::CODE_ERROR` otherwise.

All four admit `255`, which PHP itself reserves; cf.
[`exit()`](https://php.net/exit): "Exit codes should be in the range 0 to 254,
the exit code 255 is reserved by PHP and should not be used."

Three projects go further and use `255` themselves. The `phpunit` project
defines `Result::CRASH` as `255`, and `Application` calls
`exit(Result::CRASH)` when PHPUnit itself fails rather than when a test does.
The `laminas` console exits `255` when it cannot find the application's
container. The `flightphp` console, through `adhocore/cli`, starts every run
at `255` and exits with it when the command throws, matching the status PHP
itself gives an uncaught exception. The `wpcli` project passes a `255`
through as well, from a failed SSH connection.

Apart from those four checks, nothing in the surveyed projects keeps a value
above `255` from reaching [`exit()`](https://php.net/exit), where the status
keeps only its lowest eight bits: `exit(256)` ends the process with `0`, and
`exit(1146)` with `122`. The `laminas` console turns off Symfony's clamp with
`setAutoExit(false)` and passes the integer it receives straight to `exit()`,
so an exception whose code is `256` exits `0` there.

The `kohana` console meets such values in ordinary use. Minion exits with the
code of the exception that reaches its handler, substituting `1` for `0`
under a comment that reads "Never exit "0" after an exception." Kohana's
error handler, however, gives each exception it raises the PHP error level
as its code, and every level a handler can receive above `E_NOTICE` is a
multiple of `256`, so an `E_USER_ERROR` or an `E_DEPRECATED` exits `0`. The
MySQL driver in Kohana's `database` module likewise passes MySQL's error
number through as the code, so that `1146`, for a missing table, exits
`122`.

The `lithium` project alone documents `254` as the ceiling, as PHP's own
documentation advises: each of its `_stop()` methods, which wrap `exit()`,
describes its argument as an "integer range 0 to 254". Its console
`Response`, which carries the status that the console hands to `exit()`,
says instead that "only status codes in the range of 0-255 can be properly
evaluated", and neither checks the value. No surveyed project enforces
`254`.

## Returning and Exiting

The `symfony` method `Command::execute()` is declared `: int`, and
`Application::run()` returns that value to its caller. It also calls
`exit($exitCode)` itself when its `autoExit` property is set, which it is by
default; `setAutoExit(false)` disables that, leaving the caller to terminate.

The `leafphp` console takes the same approach with an argument in place of
a property: `run(bool $exit = true): int` exits with the code unless its
caller passes `false`, and returns it otherwise. The `flightphp` console's
`adhocore/cli` library passes the code to an exit callback, which calls
`exit()` unless the caller supplies a replacement, and returns whatever that
callback returns. The `joomla` console borrows Symfony's `setAutoExit()`, but
its `execute()` is documented `@return void`, so turning auto-exit off
discards the code rather than returning it.

Four consoles return a status to an entry script that exits with it. The
script that `bear` generates for each resource exits with the `exitCode` of
the `CommandResult` its command returns; the `laminas` script turns off
Symfony's auto-exit and calls `exit()` itself; the `lithium` script exits
with the `status` of the response its dispatcher returns; and `nette`
documents `exit($args->run())`, where `run()` returns the handler's own
return value.

Six other projects terminate directly, and offer no way to return a status
instead. The `atoum` project calls `exit()` from its autorun path, and `wpcli`
exposes `WP_CLI::halt()` and an `error()` that halts by default. The `psalm` and
`tempest` entry points are both declared `void`: `psalm` exits with literals
throughout, and `tempest`'s `ConsoleApplication::run()` ends in a
`Kernel::shutdown()` declared `never`. The `fuelphp` and `kohana` consoles
exit only on an exception, Oil with `1` and Minion with the exception's code.
Both print a task's return value rather than using it as a status, so any
other run ends with `0`, including a Minion task whose options fail
validation.

Several projects diverge on the return type itself. The `cakephp` method
`CommandInterface::run()` is declared `: ?int` and documented as "Exit code or
null for success," admitting `null` alongside the integer. The `tempest`
console command signature returns `ExitCode|int`, so it admits an enum case as
well as an integer. A `lithium` command may return a boolean, as noted under
§ *Constant Names*. The `nette` parser accepts an integer or nothing from its
handler, treating nothing as `0`, and throws a `LogicException` for anything
else, while the `flightphp` console's library treats any result that is not
an integer as `0`.

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
  `symfony/console`; for Deptrac, that is `deptrac/deptrac`, which supersedes
  `qossmic/deptrac`. Including them would raise the project count without
  adding distinct schemes, for the same reason `laravel` and `laminas` are
  discounted above. Infection documents `--ignore-msi-with-no-mutations` as
  forcing "a zero exit code even when the required MSI is not reached", which
  implies a non-zero value for an unmet threshold; since the value is not
  stated, it is omitted rather than inferred.

- Rector, whose published `composer.json` requires only `php` and
  `phpstan/phpstan`, so its lineage cannot be read from its manifest.

- Phive, which is published on Packagist but distributed for use as a
  standalone PHAR rather than as a Composer-installed dependency.

- The `phalcon` console, `Phalcon\Cli\Console`, which has no exit status
  contract to survey. Its `handle()` returns the task it ran, or `false`, and
  produces no status of its own; the values in the `cli.php` bootstrap that
  Phalcon's documentation shows, an implicit `0` and a literal `1` in each
  `catch`, belong to that script rather than to the framework.

- The `phpixie/console` package, which belongs to PHPixie 3. The `phpixie`
  project in README-RESEARCH.md is a PHPixie 2 sample application, and
  PHPixie 2 has no console.

- The consoles of `lightmvc`, `silex`, and `slim`, which add nothing to
  `symfony/console`. The `lightmvc` and `silex` skeletons each run Symfony's
  `Application` directly from a script in `bin/`, and Slim Console, at its
  only release, 0.1, extends that class without defining any values; the
  Slim skeleton does not include it.

- The `leafs/aloe` package, the console that `leafphp` used before Sprout.
  It builds on `symfony/console`, is marked abandoned on Packagist in favor
  of `leafs/sprout`, and is no longer required by the `leafphp` skeleton.

- Box, PHP_Depend, and Bref, which were not examined; no claim is made here
  about the values they use.
