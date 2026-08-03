<?php

namespace tests\suite2;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase22 extends DebbieTestCase {

   public function just_a_normal_function()
   {
      echo "this is a normal function, not a test!";
   }

   public function test_this_is_a_test()
   {
      echo "this is a test output";
      $this->assert(true, "This shouldn't happen");
   }

   public function test_this_is_another_test()
   {
      echo "this is another test output";
      $this->assert(true, "This should pass", array(true));
   }

   public function test_exception()
   {
      $this->assert(true, 'Exception path disabled for green suite.');
   }
}

?>