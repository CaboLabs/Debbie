<?php

namespace CaboLabs\Debbie\PHPUnitTests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Integration test: runs Debbie's own CLI against its fixture test suites
 * (tests/) and verifies the generated JUnit XML report matches the totals
 * expected from those fixtures.
 */
class CliJunitReportTest extends PHPUnitTestCase
{
    private static $projectRoot;
    private static $resultsFile;
    private static \DOMDocument $xml;
    private static int $cliExitCode;

    public static function setUpBeforeClass(): void
    {
        self::$projectRoot = dirname(__DIR__);
        self::$resultsFile = self::$projectRoot . DIRECTORY_SEPARATOR . 'test-results.xml';

        if (file_exists(self::$resultsFile)) {
            unlink(self::$resultsFile);
        }

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            'php cli.php tests -report=junit',
            $descriptorSpec,
            $pipes,
            self::$projectRoot
        );

        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        self::$cliExitCode = proc_close($process);

        self::assertFileExists(self::$resultsFile, 'The CLI did not generate test-results.xml');

        $xml = new \DOMDocument();
        $xml->load(self::$resultsFile);
        self::$xml = $xml;
    }

    private function testSuiteAttribute(string $suiteName, string $attribute): string
    {
        foreach (self::$xml->getElementsByTagName('testsuite') as $testsuite) {
            if ($testsuite->getAttribute('name') === $suiteName) {
                return $testsuite->getAttribute($attribute);
            }
        }

        $this->fail("Test suite \"$suiteName\" not found in the generated JUnit XML");
    }

    public function testCliExitsWithFailureBecauseSomeFixturesAreDesignedToFail()
    {
        // Debbie's own fixtures include failures and errors on purpose,
        // so the CLI is expected to report a non-zero exit code.
        $this->assertSame(1, self::$cliExitCode);
    }

    public function testOverallTotalsMatchTheKnownFixtures()
    {
        $testsuites = self::$xml->getElementsByTagName('testsuites')->item(0);

        $this->assertSame('41', $testsuites->getAttribute('tests'));
        $this->assertSame('8', $testsuites->getAttribute('failures'));
        $this->assertSame('11', $testsuites->getAttribute('errors'));
        $this->assertSame('0', $testsuites->getAttribute('skipped'));
    }

    public function testFatalErrorWithOtherTestSuiteReportsFiveErrorsOutOfSixTests()
    {
        $suiteName = 'tests\\fatal_error_with_other_test\\TestFatalErr';

        $this->assertSame('6', $this->testSuiteAttribute($suiteName, 'tests'));
        $this->assertSame('0', $this->testSuiteAttribute($suiteName, 'failures'));
        $this->assertSame('5', $this->testSuiteAttribute($suiteName, 'errors'));
    }

    public function testFatalErrorWithoutOtherTestDoesNotStopSiblingCasesInTheSuite()
    {
        // The suite fatal_error_without_other_test has 3 separate test case files,
        // one of them (TestCase) has no fatal error and must still be reported.
        $this->assertSame('0', $this->testSuiteAttribute('tests\\fatal_error_without_other_test\\TestCase', 'errors'));
        $this->assertSame('1', $this->testSuiteAttribute('tests\\fatal_error_without_other_test\\TestFatalErr2', 'errors'));
        $this->assertSame('1', $this->testSuiteAttribute('tests\\fatal_error_without_other_test\\TestFatalErr3', 'errors'));
    }

    public function testSuite1ReportsOneFailurePerCase()
    {
        $this->assertSame('1', $this->testSuiteAttribute('tests\\suite1\\TestCase11', 'failures'));
        $this->assertSame('1', $this->testSuiteAttribute('tests\\suite1\\TestCase12', 'failures'));
    }

    public function testSuite3HasNoFailuresOrErrors()
    {
        $this->assertSame('0', $this->testSuiteAttribute('tests\\suite3\\TestCase31', 'failures'));
        $this->assertSame('0', $this->testSuiteAttribute('tests\\suite3\\TestCase31', 'errors'));
        $this->assertSame('0', $this->testSuiteAttribute('tests\\suite3\\TestCase32', 'failures'));
        $this->assertSame('0', $this->testSuiteAttribute('tests\\suite3\\TestCase32', 'errors'));
    }

    public function testSuiteLoremIpsumTestCase42HasFailuresAndAnError()
    {
        $suiteName = 'tests\\suiteLoremIpsum\\TestCase42';

        $this->assertSame('3', $this->testSuiteAttribute($suiteName, 'tests'));
        $this->assertSame('2', $this->testSuiteAttribute($suiteName, 'failures'));
        $this->assertSame('1', $this->testSuiteAttribute($suiteName, 'errors'));
    }

    public function testSuite5ReportsExpectedErrorsAndFailures()
    {
        $this->assertSame('2', $this->testSuiteAttribute('tests\\suite5\\TestCase51', 'errors'));
        $this->assertSame('0', $this->testSuiteAttribute('tests\\suite5\\TestCase52', 'failures'));
        $this->assertSame('0', $this->testSuiteAttribute('tests\\suite5\\TestCase52', 'errors'));
        $this->assertSame('1', $this->testSuiteAttribute('tests\\suite5\\TestCase53', 'failures'));
    }
}
