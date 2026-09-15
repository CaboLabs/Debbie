<?php

namespace tests\suite4__Lorem_Ipsum_is_simply_dummy_text;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase42_simply_dummy_text extends DebbieTestCase {

   public function test_this_is_a_test()
   {
      echo "this is a test output";
      $this->assert(true, "This is happening");
   }

   public function test_this_is_another_test()
   {
      $this->assert(true, "This is happening");
   }
   public function test_this_is_yet_another_test()
   {
      print_r("this is a test output with print_r");
      $this->assert(true, "This is happening");
   }
   public function test_this_is_yet_another_test_2()
   {
      $this->assert(true, "This is happening");
   }
   public function test_this_is_yet_another_test_3()
   {
      $this->assert(true, "This is happening");
   }
}

?>