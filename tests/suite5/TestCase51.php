<?php

namespace tests\suite5;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase51 extends DebbieTestCase {

   public function test_this_is_a_test_5()
   {
      $a = 1;

      $this->assert($a == 1, "This is happening");
   }

   public function test_this_is_another_test_5()
   {
      $a = 5;

      $this->assert($a == 5, "This is happening");
   }
}

?>