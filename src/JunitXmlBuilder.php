<?php

namespace CaboLabs\Debbie;

/**
 * Builder class for creating JUnit XML test result files
 */
class JUnitXMLBuilder
{
    private $testSuites = [];
    private $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

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
     * Generate the JUnit XML string
     * 
     * @return string The complete JUnit XML
     */
    public function toXML()
    {
        $totalTime = microtime(true) - $this->startTime;
        $totalTests = 0;
        $totalFailures = 0;
        $totalErrors = 0;
        $totalSkipped = 0;

        // Calculate totals
        foreach ($this->testSuites as $suite) {
            $stats = $suite->getStatistics();
            $totalTests += $stats['tests'];
            $totalFailures += $stats['failures'];
            $totalErrors += $stats['errors'];
            $totalSkipped += $stats['skipped'];
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $testsuites = $xml->createElement('testsuites');
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
    private $startTime;

    public function __construct($name)
    {
        $this->name = $name;
        $this->startTime = microtime(true);
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
        $element->setAttribute('time', number_format($stats['time'], 6, '.', ''));

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
    private $failure = null;
    private $error = null;
    private $skipped = false;
    private $startTime;
    private $systemOut = '';
    private $systemErr = '';

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
        $this->time = microtime(true) - $this->startTime;
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
        $this->failure = [
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
        $this->error = [
            'message' => $message,
            'type' => $type,
            'details' => $details
        ];
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
     * Add error output to this test case
     * 
     * @param string $output The error output text
     * @return self
     */
    public function addSystemErr($output)
    {
        $this->systemErr .= $output;
        return $this;
    }

    /**
     * Check if this test case has a failure
     * 
     * @return bool
     */
    public function hasFailure()
    {
        return $this->failure !== null;
    }

    /**
     * Check if this test case has an error
     * 
     * @return bool
     */
    public function hasError()
    {
        return $this->error !== null;
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
        return $this->time;
    }

    /**
     * Convert this test case to a DOMElement
     * 
     * @param \DOMDocument $doc The document to create elements in
     * @return \DOMElement
     */
    public function toXMLElement(\DOMDocument $doc)
    {
        $element = $doc->createElement('testcase');
        $element->setAttribute('name', $this->name);
        $element->setAttribute('classname', $this->classname);
        $element->setAttribute('time', number_format($this->time, 6, '.', ''));

        // Add failure element if present
        if ($this->failure !== null) {
            $failureElement = $doc->createElement('failure');
            $failureElement->setAttribute('message', $this->failure['message']);
            $failureElement->setAttribute('type', $this->failure['type']);
            if (!empty($this->failure['details'])) {
                $failureElement->appendChild($doc->createTextNode($this->failure['details']));
            }
            $element->appendChild($failureElement);
        }

        // Add error element if present
        if ($this->error !== null) {
            $errorElement = $doc->createElement('error');
            $errorElement->setAttribute('message', $this->error['message']);
            $errorElement->setAttribute('type', $this->error['type']);
            if (!empty($this->error['details'])) {
                $errorElement->appendChild($doc->createTextNode($this->error['details']));
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
            $systemOutElement->appendChild($doc->createCDATASection($this->systemOut));
            $element->appendChild($systemOutElement);
        }

        // Add system-err if present
        if (!empty($this->systemErr)) {
            $systemErrElement = $doc->createElement('system-err');
            $systemErrElement->appendChild($doc->createCDATASection($this->systemErr));
            $element->appendChild($systemErrElement);
        }

        return $element;
    }
}
