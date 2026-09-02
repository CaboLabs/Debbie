<?php

namespace CaboLabs\Debbie;

/**
 * Builder class for creating JUnit XML test result files
 */
class JunitXmlBuilder
{
    const ROOT_REPORT_NAME = 'Amplify - Tests Report';

    private $testSuites = [];

    /**
     * Create a new test suite
     *
     * @param string $name The name of the test suite (typically the test class name)
     * @return TestSuite
     */
    public function addTestSuite($name)
    {
        $suite = new TestSuite($name);
        $this->testSuites[] = $suite;
        return $suite;
    }

    /**
     * *
     * Historical context:
     *  before this method existed, DebbieRun transformed
     *  the reports manually before passing them to the builder. Now this
     *  function centralizes that conversion in JunitXmlBuilder, allowing
     *  DebbieRun to provide its original reports without losing information.
     *  Previously, DebbieRun::generate_junit_xml() only used part of the
     *  available information: it kept the first failure or error, ignored OK
     *  assertions, could discard additional failures, and did not always pass
     *  all error details and traces. This function now preserves all received
     *  assertions, failures, errors, exceptions, parameters, traces, and test
     *  output when converting the reports.
     *
     * Translate DebbieRun reports into the builder's internal format.
     *
     * This method converts the complete reports returned by DebbieRun into
     * TestSuite and TestCase objects, without changing or duplicating report
     * transformation logic in DebbieRun itself.
     *
     * @param array $reports Reports returned by DebbieRun::get_reports()
     * @return self
     */
    public function addReports(array $reports)
    {
        foreach ($reports as $suiteReports) {
            foreach ($suiteReports as $className => $tests) {
                $suite = $this->addTestSuite($className);

                foreach ($tests as $testName => $report) {
                    $testCase = $suite->addTestCase($testName);

                    foreach (isset($report['asserts']) ? $report['asserts'] : [] as $assert) {
                        $type = isset($assert['type']) ? $assert['type'] : '';
                        $message = $this->buildAssertionMessage(
                            $testName,
                            isset($assert['msg']) ? $assert['msg'] : ''
                        );

                        if ($type === 'OK') {
                            $testCase->addAssertion();
                        } elseif ($type === 'FAIL') {
                            $testCase->addFailure($message, 'AssertionFailedError', $assert);
                        } elseif ($type === 'ERROR' || $type === 'EXCEPTION') {
                            $errorType = $type === 'EXCEPTION' ? 'Exception' : 'RuntimeError';
                            $testCase->addError($message, $errorType, $assert);
                        } elseif ($type === 'SKIPPED') {
                            $testCase->setSkipped($message ?: 'Test skipped');
                        }
                    }

                    if (!empty($report['output'])) {
                        $testCase->addSystemOut($report['output']);
                    }

                    $testCase->finish();
                }
            }
        }

        return $this;
    }

    /**
     * Prefix an assertion message with its test function name.
     *
     * @param string $testName Test function name
     * @param string $message Assertion message
     * @return string
     */
    private function buildAssertionMessage($testName, $message)
    {
        return '[' . $testName . '] ' . $message;
    }

    /**
     * Clear suites previously added to the builder.
     *
     * @return self
     */
    public function reset()
    {
        $this->testSuites = [];
        return $this;
    }

    /**
     * Convert DebbieRun reports and save the resulting JUnit XML.
     *
     * @param array $reports Reports returned by DebbieRun::get_reports()
     * @param string $filepath Path where to save the XML file
     * @return bool True on success, false on failure
     */
    public function generateFromReports(array $reports, $filepath)
    {
        $this->reset();
        $this->addReports($reports);
        return $this->saveToFile($filepath);
    }

    /**
     * Check whether a failure or error was recorded.
     *
     * @return bool
     */
    public function hasFailures()
    {
        foreach ($this->testSuites as $suite) {
            $stats = $suite->getStatistics();
            if ($stats['failures'] > 0 || $stats['errors'] > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the JUnit XML string
     *
     * @return string The complete JUnit XML
     */
    public function toXML()
    {
        $totalTests = 0;
        $totalFailures = 0;
        $totalErrors = 0;
        $totalSkipped = 0;
        $totalTime = 0;

        // Calculate totals
        foreach ($this->testSuites as $suite) {
            $stats = $suite->getStatistics();
            $totalTests += $stats['tests'];
            $totalFailures += $stats['failures'];
            $totalErrors += $stats['errors'];
            $totalSkipped += $stats['skipped'];
            $totalTime += $stats['time'];
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $testsuites = $xml->createElement('testsuites');
        $testsuites->setAttribute('name', self::ROOT_REPORT_NAME);
        $testsuites->setAttribute('tests', $totalTests);
        $testsuites->setAttribute('failures', $totalFailures);
        $testsuites->setAttribute('errors', $totalErrors);
        $testsuites->setAttribute('skipped', $totalSkipped);
        $testsuites->setAttribute('time', number_format($totalTime, 6, '.', ''));

        foreach ($this->testSuites as $suite) {
            $suiteElement = $suite->toXMLElement($xml);
            $testsuites->appendChild($suiteElement);
        }

        $xml->appendChild($testsuites);

        return $xml->saveXML();
    }

    /**
     * Save the JUnit XML to a file
     *
     * @param string $filepath Path to save the XML file
     * @return bool True on success, false on failure
     */
    public function saveToFile($filepath)
    {
        $xml = $this->toXML();
        $directory = dirname($filepath);
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
            return false;
        }
        return file_put_contents($filepath, $xml) !== false;
    }
}

/**
 * Represents a test suite (typically a test class)
 */
class TestSuite
{
    private $name;
    private $testCases = [];

    /**
     * Create a test suite.
     *
     * @param string $name Test suite name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Add a test case to this suite
     *
     * @param string $name The test case name (typically the test method name)
     * @return TestCase
     */
    public function addTestCase($name)
    {
        $testCase = new TestCase($name, $this->name);
        $this->testCases[] = $testCase;
        return $testCase;
    }

    /**
     * Get statistics for this test suite
     *
     * @return array Array with keys: tests, failures, errors, skipped
     */
    public function getStatistics()
    {
        $stats = [
            'tests' => count($this->testCases),
            'failures' => 0,
            'errors' => 0,
            'skipped' => 0,
            'assertions' => 0,
            'time' => 0
        ];

        foreach ($this->testCases as $testCase) {
            if ($testCase->hasFailure()) {
                $stats['failures']++;
            }
            if ($testCase->hasError()) {
                $stats['errors']++;
            }
            if ($testCase->isSkipped()) {
                $stats['skipped']++;
            }
            $stats['assertions'] += $testCase->getAssertionCount();
            $stats['time'] += $testCase->getTime();
        }

        return $stats;
    }

    /**
     * Convert this test suite to a DOMElement
     *
     * @param \DOMDocument $doc The document to create elements in
     * @return \DOMElement
     */
    public function toXMLElement(\DOMDocument $doc)
    {
        $stats = $this->getStatistics();

        $element = $doc->createElement('testsuite');
        $element->setAttribute('name', $this->name);
        $element->setAttribute('tests', $stats['tests']);
        $element->setAttribute('failures', $stats['failures']);
        $element->setAttribute('errors', $stats['errors']);
        $element->setAttribute('skipped', $stats['skipped']);
        $element->setAttribute('assertions', $stats['assertions']);
        $element->setAttribute('time', number_format($stats['time'], 6, '.', ''));
        $element->setAttribute('timestamp', gmdate('c'));

        $properties = $doc->createElement('properties');
        $phpProperty = $doc->createElement('property');
        $phpProperty->setAttribute('name', 'php.version');
        $phpProperty->setAttribute('value', phpversion());
        $properties->appendChild($phpProperty);
        $osProperty = $doc->createElement('property');
        $osProperty->setAttribute('name', 'os');
        $osProperty->setAttribute('value', php_uname('s'));
        $properties->appendChild($osProperty);
        $element->appendChild($properties);

        foreach ($this->testCases as $testCase) {
            $testCaseElement = $testCase->toXMLElement($doc);
            $element->appendChild($testCaseElement);
        }

        return $element;
    }
}

/**
 * Represents a single test case (test method)
 */
class TestCase
{
    private $name;
    private $classname;
    private $time;
    private $failures = [];
    private $errors = [];
    private $skipped = false;
    private $startTime;
    private $systemOut = '';
    private $assertions = 0;

    /**
     * Create a test case.
     *
     * @param string $name Test case name
     * @param string $classname Test class name
     */
    public function __construct($name, $classname)
    {
        $this->name = $name;
        $this->classname = $classname;
        $this->startTime = microtime(true);
        $this->time = 0;
    }

    /**
     * Mark the test case as finished and record execution time
     */
    public function finish()
    {
        if ($this->time === 0) {
            $this->time = microtime(true) - $this->startTime;
        }
        return $this;
    }

    /**
     * Add a failure to this test case
     *
     * @param string $message The failure message
     * @param string $type The failure type (default: 'AssertionFailure')
     * @param string $details Additional details/stack trace
     * @return self
     */
    public function addFailure($message, $type = 'AssertionFailure', $details = '')
    {
        $this->assertions++;
        $this->failures[] = [
            'message' => $message,
            'type' => $type,
            'details' => $details
        ];
        return $this;
    }

    /**
     * Add an error to this test case
     *
     * @param string $message The error message
     * @param string $type The error type (default: 'RuntimeError')
     * @param string $details Additional details/stack trace
     * @return self
     */
    public function addError($message, $type = 'RuntimeError', $details = '')
    {
        $this->assertions++;
        $this->errors[] = [
            'message' => $message,
            'type' => $type,
            'details' => $details
        ];
        return $this;
    }

    /**
     * Record a successful assertion.
     *
     * @return self
     */
    public function addAssertion()
    {
        $this->assertions++;
        return $this;
    }

    /**
     * Mark this test case as skipped
     *
     * @param string $message Optional skip message
     * @return self
     */
    public function setSkipped($message = 'Test skipped')
    {
        $this->skipped = $message;
        return $this;
    }

    /**
     * Add standard output to this test case
     *
     * @param string $output The output text
     * @return self
     */
    public function addSystemOut($output)
    {
        $this->systemOut .= $output;
        return $this;
    }

    /**
     * Check if this test case has a failure
     *
     * @return bool
     */
    public function hasFailure()
    {
        return !empty($this->failures);
    }

    /**
     * Check if this test case has an error
     *
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->errors);
    }

    /**
     * Check if this test case is skipped
     *
     * @return bool
     */
    public function isSkipped()
    {
        return $this->skipped !== false;
    }

    /**
     * Get the execution time
     *
     * @return float
     */
    public function getTime()
    {
        $this->finish();
        return $this->time;
    }

    /**
     * Get the number of recorded assertion results.
     *
     * @return int
     */
    public function getAssertionCount()
    {
        return $this->assertions;
    }

    /**
     * Convert this test case to a DOMElement
     *
     * @param \DOMDocument $doc The document to create elements in
     * @return \DOMElement
     */
    public function toXMLElement(\DOMDocument $doc)
    {
        $this->finish();

        $element = $doc->createElement('testcase');
        $element->setAttribute('name', $this->name);
        $element->setAttribute('classname', $this->classname);
        $element->setAttribute('assertions', $this->getAssertionCount());
        $element->setAttribute('time', number_format($this->time, 6, '.', ''));

        // Add every failure recorded for this test case.
        foreach ($this->failures as $failure) {
            $failureElement = $doc->createElement('failure');
            $failureElement->setAttribute('message', $failure['message']);
            $failureElement->setAttribute('type', $failure['type']);
            if (!empty($failure['details'])) {
                $failureElement->appendChild($this->createSafeCDATA($doc, $this->formatDetails($failure['details'])));
            }
            $element->appendChild($failureElement);
        }

        // Add every error recorded for this test case.
        foreach ($this->errors as $error) {
            $errorElement = $doc->createElement('error');
            $errorElement->setAttribute('message', $error['message']);
            $errorElement->setAttribute('type', $error['type']);
            if (!empty($error['details'])) {
                $errorElement->appendChild($this->createSafeCDATA($doc, $this->formatDetails($error['details'])));
            }
            $element->appendChild($errorElement);
        }

        // Add skipped element if present
        if ($this->skipped !== false) {
            $skippedElement = $doc->createElement('skipped');
            $skippedElement->setAttribute('message', $this->skipped);
            $element->appendChild($skippedElement);
        }

        // Add system-out if present
        if (!empty($this->systemOut)) {
            $systemOutElement = $doc->createElement('system-out');
            $systemOutElement->appendChild($this->createSafeCDATA($doc, $this->systemOut));
            $element->appendChild($systemOutElement);
        }

        return $element;
    }

    /**
     * Create a CDATA fragment that remains valid when content contains ]]>.
     *
     * @param \DOMDocument $doc XML document
     * @param string $content Content to wrap in CDATA
     * @return \DOMDocumentFragment
     */
    private function createSafeCDATA(\DOMDocument $doc, $content)
    {
        $parts = explode(']]>', (string) $content);
        $fragment = $doc->createDocumentFragment();
        $lastPart = count($parts) - 1;

        foreach ($parts as $index => $part) {
            if ($index === 0) {
                $part .= $index < $lastPart ? ']]' : '';
            } else {
                $part = '>' . $part . ($index < $lastPart ? ']]' : '');
            }

            $fragment->appendChild($doc->createCDATASection($part));
        }

        return $fragment;
    }

    /**
     * Convert assertion details or stack frames to readable text.
     *
     * @param mixed $details Assertion details
     * @return string
     */
    private function formatDetails($details)
    {
        if (is_array($details) && (isset($details['trace']) || isset($details['params']) || isset($details['msg']))) {
            $formatted = isset($details['msg']) ? 'Assertion: ' . $details['msg'] : '';
            if (!empty($details['params'])) {
                $formatted .= "\n\nParameters:\n" . $details['params'];
            }
            if (!empty($details['trace'])) {
                $formatted .= "\n\nStack Trace:\n" . $this->formatDetails($details['trace']);
            }
            return $formatted;
        }

        if (is_array($details)) {
            $lines = [];
            foreach ($details as $frame) {
                if (is_array($frame)) {
                    $file = isset($frame['file']) ? $frame['file'] : 'unknown';
                    $line = isset($frame['line']) ? $frame['line'] : '?';
                    $class = isset($frame['class']) ? $frame['class'] : '';
                    $function = isset($frame['function']) ? $frame['function'] : 'unknown';
                    $call = $class ? $class . '::' . $function : $function;
                    $lines[] = 'at ' . $call . '() in ' . $file . ':' . $line;
                } else {
                    $lines[] = $frame;
                }
            }
            return implode("\n", $lines);
        }

        return $details;
    }
}
