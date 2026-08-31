<?php

namespace CaboLabs\Debbie;

use DateTime;
use DateTimeZone;
use DOMDocument;

/**
 * Generate JUnit XML reports from DebbieRun test reports
 *
 * Groups tests by suite (folder) and test (function), ignoring test case classes
 */
class JUnitReport
{
  const ROOT_REPORT_NAME = 'Amplify - Tests Report';

  private $reports;
  private $execution_time;
  private $doc;
  private $root_suite;

  public function __construct($reports, $execution_time = null)
  {
    $this->reports = $reports;
    // If execution_time is not provided, calculate it as a rough estimate
    $this->execution_time = $execution_time !== null ? $execution_time : $this->estimate_execution_time($reports);
  }

  /**
   * Estimate total execution time based on number of tests
   * Rough estimate: 0.1 seconds per test
   */
  private function estimate_execution_time($reports)
  {
    $test_count = 0;
    foreach ($reports as $suite_reports) {
      foreach ($suite_reports as $test_case_data) {
        $test_count += count($test_case_data);
      }
    }
    return $test_count * 0.1;
  }

  /**
   * Generate JUnit XML from reports
   *
   * @param string $output_path Path where to save the XML file
   * @return string Path to generated file
   */
  public function generate($output_path)
  {
    // Create XML structure
    $this->doc = new DOMDocument('1.0', 'UTF-8');
    $this->doc->formatOutput = true;

    // Group reports by suite
    $suites_data = $this->group_by_suite();

    // Create root element
    $total_tests = 0;
    $total_failures = 0;
    $total_errors = 0;
    $total_skipped = 0;

    // Calculate totals
    foreach ($suites_data as $suite_tests)
    {
      foreach ($suite_tests as $test_data)
      {
        $total_tests++;
        $total_failures += $test_data['failures'];
        $total_errors += $test_data['errors'];
        $total_skipped += $test_data['skipped'];
      }
    }

    // Create root testsuites element
    $this->root_suite = $this->doc->createElement('testsuites');
    $this->root_suite->setAttribute('name', self::ROOT_REPORT_NAME);
    $this->root_suite->setAttribute('tests', $total_tests);
    $this->root_suite->setAttribute('failures', $total_failures);
    $this->root_suite->setAttribute('errors', $total_errors);
    $this->root_suite->setAttribute('skipped', $total_skipped);
    $this->root_suite->setAttribute('time', number_format($this->execution_time, 3));

    $this->doc->appendChild($this->root_suite);

    // Create testsuite elements for each suite folder
    foreach ($suites_data as $suite_name => $suite_tests)
    {
      $this->create_testsuite($suite_name, $suite_tests);
    }

    // Create output directory if it doesn't exist
    $dir = dirname($output_path);
    if (!is_dir($dir))
    {
      mkdir($dir, 0755, true);
    }

    // Save XML
    $this->doc->save($output_path);

    return $output_path;
  }

  /**
   * Group reports by suite folder name
   * Structure: suite_folder => [test_function => test_data, ...]
   */
  private function group_by_suite()
  {
    $grouped = [];

    // Handle both array and potentially keyed arrays
    // $this->reports is an array where each element is a suite's test results
    // Each suite result is: [test_case_class => [test_function => [asserts => [...], output => '...'], ...], ...]

    foreach ($this->reports as $suite_reports)
    {
      // Skip empty suites
      if (empty($suite_reports))
      {
        continue;
      }

      // Determine suite name from first test case class in this suite
      $suite_name = null;

      // Each suite contains test cases
      foreach ($suite_reports as $test_case_class => $test_case_data)
      {
        // Extract suite name from class namespace (only once per suite)
        if ($suite_name === null)
        {
          $suite_name = $this->extract_suite_name($test_case_class);
          $grouped[$suite_name] = [];
        }

        // Each test case class contains test functions
        foreach ($test_case_data as $test_function => $test_data)
        {
          $test_key = $this->extract_class_name($test_case_class) . '::' . $test_function;

          $test_info = [
            'function' => $test_function,
            'class' => $test_case_class,
            'suite' => $suite_name,
            'data' => $test_data,
            'failures' => 0,
            'errors' => 0,
            'skipped' => 0,
            'assertions' => 0,
            'time' => 0.1  // Default time
          ];

          // Analyze test results
          if (isset($test_data['asserts']))
          {
            $test_info['assertions'] = count($test_data['asserts']);

            foreach ($test_data['asserts'] as $assert)
            {
              if (isset($assert['type']))
              {
                if ($assert['type'] === 'FAIL')
                {
                  $test_info['failures']++;
                }
                elseif ($assert['type'] === 'ERROR' || $assert['type'] === 'EXCEPTION')
                {
                  $test_info['errors']++;
                }
              }
            }
          }

          $grouped[$suite_name][$test_key] = $test_info;
        }
      }
    }

    ksort($grouped);
    return $grouped;
  }

  /**
   * Extract suite name from fully qualified class name
   */
  private function extract_suite_name($class_name)
  {
    // E.g., "tests\report_claim_per_org\TestReportClaimPerOrg" => "report_claim_per_org"
    $parts = explode('\\', $class_name);

    // Remove 'tests' namespace and class name, keep the folder
    if (count($parts) >= 2)
    {
      // Return the folder part (usually at index 1)
      return $parts[1];
    }

    return 'default';
  }

  /**
   * Create a testsuite XML element
   */
  private function create_testsuite($suite_name, $tests)
  {
    $total_tests = count($tests);
    $total_failures = 0;
    $total_errors = 0;
    $total_skipped = 0;
    $total_time = 0;
    $total_assertions = 0;

    // Calculate totals for this suite
    foreach ($tests as $test_data)
    {
      $total_failures += $test_data['failures'];
      $total_errors += $test_data['errors'];
      $total_skipped += $test_data['skipped'];
      $total_time += $test_data['time'];
      $total_assertions += $test_data['assertions'];
    }

    $testsuite = $this->doc->createElement('testsuite');
    $testsuite->setAttribute('name', 'suite: '. $this->format_suite_name($suite_name));
    $testsuite->setAttribute('tests', $total_tests);
    $testsuite->setAttribute('failures', $total_failures);
    $testsuite->setAttribute('errors', $total_errors);
    $testsuite->setAttribute('skipped', $total_skipped);
    $testsuite->setAttribute('assertions', $total_assertions);
    $testsuite->setAttribute('time', number_format($total_time, 3));

    // Add timestamp
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $testsuite->setAttribute('timestamp', $now->format('c'));

    // Add properties
    $this->add_properties($testsuite);

    // Add test cases
    foreach ($tests as $test_data)
    {
      $this->create_testcase($testsuite, $test_data);
    }

    $this->root_suite->appendChild($testsuite);
  }

  /**
   * Format suite folder names to readable titles.
   */
  private function format_suite_name($suite_name)
  {
    return str_replace('_', ' ', $suite_name);
  }

  /**
   * Add system properties to testsuite
   */
  private function add_properties($testsuite_element)
  {
    $properties = $this->doc->createElement('properties');

    $php_version = phpversion();
    $property_php = $this->doc->createElement('property');
    $property_php->setAttribute('name', 'php.version');
    $property_php->setAttribute('value', $php_version);
    $properties->appendChild($property_php);

    $property_os = $this->doc->createElement('property');
    $property_os->setAttribute('name', 'os');
    $property_os->setAttribute('value', php_uname('s'));
    $properties->appendChild($property_os);

    $testsuite_element->appendChild($properties);
  }

  /**
   * Create a testcase XML element
   */
  private function create_testcase($testsuite, $test_data)
  {
    $testcase = $this->doc->createElement('testcase');

    // GitHub renders titles as "name (classname)".
    $testcase->setAttribute('name', $test_data['suite']);
    $testcase->setAttribute('classname', $this->extract_class_name($test_data['class']));
    $testcase->setAttribute('assertions', $test_data['assertions']);
    $testcase->setAttribute('time', number_format($test_data['time'], 3));

    // Add failure or error elements
    if (isset($test_data['data']['asserts']))
    {
      foreach ($test_data['data']['asserts'] as $assert)
      {
        if ($assert['type'] === 'FAIL')
        {
          $failure = $this->doc->createElement('failure');
          $failure->setAttribute('type', 'AssertionFailedError');
          $failure->setAttribute('message', $this->build_assert_message($test_data['function'], $assert['msg'] ?? 'Assertion failed'));

          $failure_text = $this->doc->createCDATASection($this->format_assert_details($assert));
          $failure->appendChild($failure_text);

          $testcase->appendChild($failure);
        }
        elseif ($assert['type'] === 'ERROR' || $assert['type'] === 'EXCEPTION')
        {
          $error = $this->doc->createElement('error');
          $error->setAttribute('type', $assert['type']);
          $error->setAttribute('message', $this->build_assert_message($test_data['function'], $assert['msg'] ?? 'Error occurred'));

          $error_text = $this->doc->createCDATASection($this->format_exception_details($assert));
          $error->appendChild($error_text);

          $testcase->appendChild($error);
        }
      }
    }

    // Add output if exists
    if (isset($test_data['data']['output']) && !empty($test_data['data']['output']))
    {
      $system_out = $this->doc->createElement('system-out');
      $system_out->appendChild($this->doc->createCDATASection($test_data['data']['output']));
      $testcase->appendChild($system_out);
    }

    $testsuite->appendChild($testcase);
  }

  /**
   * Format assertion details for output
   */
  private function format_assert_details($assert)
  {
    $details = "Assertion: " . ($assert['msg'] ?? 'Failed') . "\n";

    if (!empty($assert['params']))
    {
      $details .= "\nParameters:\n" . $assert['params'];
    }

    if (!empty($assert['trace']))
    {
      $details .= "\nStack Trace:\n";
      foreach ($assert['trace'] as $frame)
      {
        $file = $frame['file'] ?? 'unknown';
        $line = $frame['line'] ?? '?';
        $function = $frame['function'] ?? 'unknown';
        $details .= "  at $function() in $file:$line\n";
      }
    }

    return $details;
  }

  /**
   * Format exception details for output
   */
  private function format_exception_details($exception)
  {
    $details = "Exception: " . ($exception['msg'] ?? 'Unknown error') . "\n";

    if (!empty($exception['trace']))
    {
      $details .= "\nStack Trace:\n";
      foreach ($exception['trace'] as $frame)
      {
        $file = $frame['file'] ?? 'unknown';
        $line = $frame['line'] ?? '?';
        $function = $frame['function'] ?? 'unknown';
        $class = $frame['class'] ?? '';

        if ($class)
        {
          $details .= "  #0 $file($line): $class::$function()\n";
        }
        else
        {
          $details .= "  #0 $file($line): $function()\n";
        }
      }
    }

    return $details;
  }

  /**
   * Extract class name from fully qualified name
   */
  private function extract_class_name($class_name)
  {
    $parts = explode('\\', $class_name);
    return end($parts);
  }

  /**
   * Prefix assert messages with test function name to preserve detail in annotations.
   */
  private function build_assert_message($function_name, $message)
  {
    return '['. $function_name .'] '. $message;
  }
}