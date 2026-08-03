<?php

namespace tests\fatal_error_without_other_test;

use \CaboLabs\Debbie\DebbieTestCase;

class TestFatalErr3 extends DebbieTestCase {

   public function test_with_fatal_error_3()
   {
      $n = (object) ['name' => 'safe'];
      $this->assert(isset($n->name), "test_this_is_a_test_fatal_error");
   }
}
?>