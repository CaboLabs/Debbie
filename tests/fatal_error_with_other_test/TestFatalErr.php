<?php

namespace tests\fatal_error_with_other_test;

use \CaboLabs\PhTest\PhTestCase;

class TestFatalErr extends PhTestCase {

   public function test_with_fatal_error()
   {
      $n = undefinedFunctionCall();
      $this->assert(true, $n);
   }

   public function test_div_by_zero()
   {
      $zero = 0;
      $calc = 3.14159 / $zero;
      echo $calc;
   }

   public function test_throw_non_throwable()
   {
      throw new TestFatalErr('a', 'b');
   }

   public function test_throw_error()
   {
      throw new \Error('this is an Error');
   }

   public function test_trigger_error()
   {
      trigger_error("Trigger fatal error", E_USER_ERROR);
   }

   public function test_without_fatal_error()
   {
      $this->assert(true, 'normal');
   }
}
?>