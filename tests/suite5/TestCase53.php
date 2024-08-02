<?php

namespace tests\suite5;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase53 extends DebbieTestCase {

   public function test_this_is_a_test_53()
   {
      $this->assert(true, "This is happening");
   }

   public function test_this_is_another_test_53()
   {
      $this->assert(false, "This is happening");
   }
}

?>