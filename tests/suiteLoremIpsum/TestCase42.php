<?php

namespace tests\suiteLoremIpsum;

use \CaboLabs\PhTest\PhTestCase;

class TestCase42 extends PhTestCase {

   public function test_this_is_a_test()
   {
      echo "this is a test output";
      $this->assert(false, "This is happening");
   }

   public function test_this_is_another_test()
   {
      $this->assert(false, "This is happening");
   }
}

?>