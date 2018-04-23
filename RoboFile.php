<?php

use Robo\Result;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks {
    const BASE = __DIR__.\DIRECTORY_SEPARATOR;
    const BASE_TEST = self::BASE."tests".\DIRECTORY_SEPARATOR;

    /**
     * Runs the full test suite
     *
     * Arguments passed to the task are passed on to PHPUnit. Thus one may, for
     * example, run the following command and get the expected results:
     *
     * ./robo test --testsuite TTRSS --exclude-group slow --testdox
     *
     * Please see the PHPUnit documentation for available options.
    */
    public function test(array $args): Result {
        $execpath = realpath(self::BASE."vendor-bin/phpunit/vendor/phpunit/phpunit/phpunit");
        $confpath = realpath(self::BASE_TEST."phpunit.xml");
        return $this->taskExec("php")->arg($execpath)->option("-c", $confpath)->args($args)->run();
    }

    /**
     * Runs the full test suite
     *
     * This is an alias of the "test" task.
    */
    public function testFull(array $args): Result {
        return $this->test($args);
    }

    /**
     * Runs a quick subset of the test suite
     *
     * See help for the "test" task for more details.
    */
    public function testQuick(array $args): Result {
        return $this->test(array_merge(["--exclude-group", "slow,optional"], $args));
    }

    /** Produces a code coverage report
     *
     * By default this task produces an HTML-format coverage report in
     * arsse/tests/coverage/. Additional reports may be produced by passing
     * arguments to this task as one would to PHPUnit.
     *
     * Robo first tries to use phpdbg and will fall back to Xdebug if available.
     * Because Xdebug slows down non-coverage tasks, however, phpdbg is highly
     * recommanded is debugging facilities are not otherwise needed.
    */
    public function coverage(array $args): Result {
        // run tests with code coverage reporting enabled
        $exec = $this->findCoverageEngine();
        $execpath = realpath(self::BASE."vendor-bin/phpunit/vendor/phpunit/phpunit/phpunit");
        $confpath = realpath(self::BASE_TEST."phpunit.xml");
        return $this->taskExec($exec)->arg($execpath)->option("-c", $confpath)->option("--coverage-html", self::BASE_TEST."coverage")->args($args)->run();
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
}
