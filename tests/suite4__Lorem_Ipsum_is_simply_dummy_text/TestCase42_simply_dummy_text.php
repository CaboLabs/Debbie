<?php

namespace tests\suite4__Lorem_Ipsum_is_simply_dummy_text;

use \CaboLabs\PhTest\PhTestCase;

class TestCase42_simply_dummy_text extends PhTestCase {

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