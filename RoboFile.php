<?php
declare(strict_types=1);

use Robo\Result;

class RoboFile extends \Robo\Tasks {
    const BASE = __DIR__.\DIRECTORY_SEPARATOR;
    const BASE_TEST = self::BASE."tests".\DIRECTORY_SEPARATOR;

    /**
     * Runs the typical test suite
     *
     * Arguments passed to the task are passed on to PHPUnit. Thus one may, for
     * example, run the following command and get the expected results:
     *
     * ./robo test --testsuite TTRSS --exclude-group slow --testdox
     *
     * Please see the PHPUnit documentation for available options.
    */
    public function test(array $args): Result {
        return $this->runTests("php", "typical", $args);
    }

    /**
     * Runs the full test suite
     *
     * This includes pedantic tests which may help to identify problems.
     * See help for the "test" task for more details.
    */
    public function testFull(array $args): Result {
        return $this->runTests("php", "full", $args);
    }

    /**
     * Runs a quick subset of the test suite
     *
     * See help for the "test" task for more details.
    */
    public function testQuick(array $args): Result {
        return $this->runTests("php", "quick", $args);
    }

    /** Produces a code coverage report
     *
     * By default this task produces an HTML-format coverage report in
     * tests/coverage/. Additional reports may be produced by passing
     * arguments to this task as one would to PHPUnit.
     *
     * Robo first tries to use phpdbg and will fall back to Xdebug if available.
     * Because Xdebug slows down non-coverage tasks, however, phpdbg is highly
     * recommended if debugging facilities are not otherwise needed.
    */
    public function coverage(array $args): Result {
        // run tests with code coverage reporting enabled
        $exec = $this->findCoverageEngine();
        return $this->runTests($exec, "typical", array_merge(["--coverage-html", self::BASE_TEST."coverage"], $args));
    }

    /** Runs a performance evaluation.
     *
     * The performance of the library's basic functionality is tested against
     * the IntlCodePointBreakIterator class
    */
    public function perf(array $args): Result {
        $execpath = realpath(self::BASE."perf/perf.php");
        return $this->taskExec("php")->arg($execpath)->args($args)->run();
    }

    /** Runs the coding standards fixer */
    public function clean($opts = ['demo|d' => false]): Result {
        $t = $this->taskExec(realpath(self::BASE."vendor/bin/php-cs-fixer"));
        $t->arg("fix")->arg("--allow-risky=yes");
        if ($opts['demo']) {
            $t->args("--dry-run", "--diff")->option("--diff-format", "udiff");
        }
        return $t->run();
    }

    protected function findCoverageEngine(): string {
        $null = null;
        $code = 0;
        exec("phpdbg --version", $null, $code);
        if (!$code) {
            return "phpdbg -qrr";
        } else {
            return "php";
        }
    }

    protected function runTests(string $executor, string $set, array $args) : Result {
        switch ($set) {
            case "typical":
                $set = ["--exclude-group", "optional"];
                break;
            case "quick":
                $set = ["--exclude-group", "optional,slow"];
                break;
            case "full":
                $set = [];
                break;
            default:
                throw new \Exception;
        }
        $execpath = realpath(self::BASE."vendor-bin/phpunit/vendor/phpunit/phpunit/phpunit");
        $confpath = realpath(self::BASE_TEST."phpunit.xml");
        return $this->taskExec($executor)->arg($execpath)->option("-c", $confpath)->args(array_merge($set, $args))->run();
    }
}
