<?php

namespace tests\suite3;

use \CaboLabs\PhTest\PhTestCase;

class TestCase32 extends PhTestCase {

   public function test_this_is_a_test()
   {
      echo "this is a test output";
      $this->assert(true, "This is happening");
   }

   public function test_this_is_another_test()
   {
      $this->assert(true, "This is happening");
   }
}

?>