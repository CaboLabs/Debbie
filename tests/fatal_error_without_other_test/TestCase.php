<?php

namespace tests\fatal_error_without_other_test;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase extends DebbieTestCase {
   public function test_this_is_a_test()
   {
      echo "this is a test output";
      $this->assert(true, "This shouldn't happen");
   }
}
