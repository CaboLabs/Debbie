<?php

namespace tests\suite_database;

use \CaboLabs\Debbie\DebbieTestCase;

/**
 * Verifies the test runner can reach a MySQL database using credentials
 * provided via environment variables (DB_TEST_HOST, DB_TEST_NAME, DB_TEST_USER, DB_TEST_PASS).
 *
 * On GitHub Actions, these are injected from the repository secrets
 */
class TestCaseDatabaseConnection extends DebbieTestCase {

   public function test_can_connect_to_database()
   {
      $host = getenv('DB_TEST_HOST');
      $name = getenv('DB_TEST_NAME');
      $user = getenv('DB_TEST_USER');
      $pass = getenv('DB_TEST_PASS');

      $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

      try
      {
         $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => 5,
         ]);

         $this->assert($pdo instanceof \PDO, 'Could not create a PDO connection to the database');
      }
      catch (\PDOException $e)
      {
         $this->assert(false, 'Database connection failed: '. $e->getMessage());
      }
   }
}

?>
