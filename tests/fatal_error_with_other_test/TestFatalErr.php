<?php

namespace tests\fatal_error_with_other_test;

use \CaboLabs\PhTest\PhTestCase;

class TestFatalErr extends PhTestCase {
   public function test_with_fatal_error()
   {
      $n = undefinedFunctionCall();
      $this->assert(true, $n);
   }
   public function test_without_fatal_error()
   {
      $this->assert(true, 'normal');
   }
}
?>