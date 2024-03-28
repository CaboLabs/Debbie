<?php

namespace tests\suite3;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase31 extends DebbieTestCase {

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