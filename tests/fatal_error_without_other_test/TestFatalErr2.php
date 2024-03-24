<?php

namespace tests\fatal_error_without_other_test;

use \CaboLabs\PhTest\PhTestCase;

class TestFatalErr2 extends PhTestCase {
   public function test_with_fatal_error()
   {
      $n = 'not an object';
      $n->get_name();
      $this->assert(true, "test_this_is_a_test_fatal_error");
   }
}
?>