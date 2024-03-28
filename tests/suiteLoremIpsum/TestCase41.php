<?php

namespace tests\suiteLoremIpsum;

use \CaboLabs\Debbie\DebbieTestCase;

class TestCase41 extends DebbieTestCase {

   public function test_this_is_a_test()
   {
      echo "this is a test output";
      $this->assert(true, "This is happening");
   }

   public function test_this_is_another_test()
   {
      $this->assert(true, "This is happening");
   }
   public function test_this_is_another_test_failed()
   {
      echo "this is another test output";
      $this->assert(false, "This is happening", array(false));
   }
}

?>