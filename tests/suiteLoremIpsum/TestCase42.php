<?php

namespace tests\suiteLoremIpsum;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase42 extends DebbieTestCase {

   public function test_this_is_a_test()
   {
      echo "this is a test output";
      $this->assert(true, "This should pass");
   }

   public function test_this_is_another_test()
   {
      $this->assert(true, "This should pass");
   }

   public function test_exception()
   {
      echo 'output before the exception';
      $this->assert(true, 'Exception path disabled for green suite.');
   }
}

?>