<?php

namespace tests\suite5;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase51 extends DebbieTestCase {

   public function test_this_is_a_test_5()
   {
      $this->assert(true, "This is happening");
   }

   public function test_this_is_another_test_5()
   {
      $this->assert(true, "This is happening");
   }
}

?>