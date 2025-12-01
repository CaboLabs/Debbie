---
layout: default
title: Debbie - Minimal PHP Test Library
---

# Debbie

The minimal PHP test library with beautiful test reports.

[![GitHub Actions](https://github.com/CaboLabs/Debbie/workflows/Run%20Debbie%20Tests/badge.svg)](https://github.com/CaboLabs/Debbie/actions)
[![CircleCI](https://circleci.com/gh/CaboLabs/Debbie.svg?style=svg)](https://circleci.com/gh/CaboLabs/Debbie)

## Download

**Latest Release:** [Download Debbie](https://github.com/CaboLabs/Debbie/releases/latest)

Or install via Composer:
```bash
composer require --dev cabolabs/debbie
```

## Quick Start

### Installation

Add to your `composer.json`:
```json
{
  "require-dev": {
    "cabolabs/debbie": "^0.8"
  }
}
```

Then run:
```bash
composer install
```

### Running Tests
```bash
# Run all test suites
php cli.php tests

# Run specific suite
php cli.php tests suite1

# Generate JUnit XML report
php cli.php tests -report=junit
```

## Writing Tests

Create a test class:
```php
<?php
namespace mytests\suite1;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase1 extends DebbieTestCase {
    public function test_this_is_a_test()
    {
        echo "this is a test output";
        $this->assert(true, "This message only appears when the assert evaluates to false");
    }

    public function test_this_is_another_test()
    {
        // add extra parameters to show on the test report
        $debug_parameters = ['var1' => 'val1'];
        $this->assert(false, "This is a fail", $debug_parameters);
    }

    public function test_no_output()
    {
        $this->assert('cabolabs' == strtolower('CaboLabs'));
    }
}
```

## Features

- ✅ Minimal setup - just PHP, no dependencies
- ✅ Beautiful HTML test reports
- ✅ JUnit XML output for CI/CD integration
- ✅ Support for test suites and test cases
- ✅ Simple assertion syntax
- ✅ Debug parameters for failed tests
- ✅ Works with GitHub Actions, CircleCI, and more

## CI/CD Integration

Debbie generates JUnit XML reports that work with all major CI platforms:

### GitHub Actions
```yaml
- name: Run Debbie tests
  run: php cli.php tests -report=junit --file=test-results.xml

- name: Publish Test Results
  uses: EnricoMi/publish-unit-test-result-action@v2
  if: always()
  with:
    files: test-results.xml
```

### CircleCI
```yaml
- run:
    name: Run tests
    command: php cli.php tests -report=junit --file=test-results.xml

- store_test_results:
    path: test-results.xml
```

## Requirements

- PHP 7.0+ or 8.0+
- Composer (for installation)

## Links

- [GitHub Repository](https://github.com/CaboLabs/Debbie)
- [Latest Release](https://github.com/CaboLabs/Debbie/releases/latest)
- [Report Issues](https://github.com/CaboLabs/Debbie/issues)
- [Packagist Package](https://packagist.org/packages/cabolabs/debbie)

## License

[View License](https://github.com/CaboLabs/Debbie/blob/master/LICENSE)