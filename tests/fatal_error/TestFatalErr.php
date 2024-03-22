<?php

namespace tests\fatal_error;

use \CaboLabs\PhTest\PhTestCase;

class TestFatalErr extends PhTestCase {

   public function test_this_is_a_test_fatal_error()
   {
      $n = 'not an object';
      $n->get_name();
      $this->assert(true, "test_this_is_a_test_fatal_error");
   }
}

?>