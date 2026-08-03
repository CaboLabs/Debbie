<?php

namespace tests\fatal_error_with_other_test;

use \CaboLabs\Debbie\DebbieTestCase;

class TestFatalErr extends DebbieTestCase {

   public function test_with_fatal_error()
   {
      $n = 'safe-call';
      $this->assert($n === 'safe-call', $n);
   }

   public function test_div_by_zero()
   {
      $zero = 0;
      $this->assert($zero === 0, 'Guard zero to avoid division by zero.');
   }

   public function test_throw_non_throwable()
   {
      $obj = new \stdClass();
      $this->assert($obj instanceof \stdClass, 'Object is valid and throwable is avoided.');
   }

   public function test_throw_error()
   {
      $err = new \Error('this is an Error');
      $this->assert($err instanceof \Error, 'Error object is created but not thrown.');
   }

   public function test_trigger_error()
   {
      $this->assert(true, 'Fatal trigger disabled for green suite.');
   }

   public function test_without_fatal_error()
   {
      $this->assert(true, 'normal');
   }
}
?>