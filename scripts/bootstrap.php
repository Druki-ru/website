<?php
/**
 * @file
 * A bootstrap file for `phpunit` test runner.
 *
 * This bootstrap file from DTT is fast and customizable.
 *
 * If you get 'class not found' errors while running tests, you should copy this
 * file to a location inside your code-base --such as `/scripts`. Then add the
 * missing namespaces to the bottom of the copied field. Specify your custom
 * `bootstrap-fast.php` file as the bootstrap in `phpunit.xml`.
 *
 * Alternatively, use the bootstrap.php file, in this same directory, which is
 * slower but registers all the namespaces that Drupal tests expect.
 */

use Drupal\TestTools\PhpUnitCompatibility\PhpUnit8\ClassWriter;
use weitzman\DrupalTestTraits\AddPsr4;

[$finder, $class_loader] = AddPsr4::add();
$root = $finder->getDrupalRoot();

// So that test cases may be simultaneously compatible with multiple major
// versions of PHPUnit.
$class_loader->addPsr4('Drupal\TestTools\\', "$root/core/tests");
if (class_exists('Drupal\TestTools\PhpUnitCompatibility\PhpUnit8\ClassWriter')) {
  ClassWriter::mutateTestBase($class_loader);
}

// Register custom modules namespaces.
$class_loader->addPsr4('Drupal\druki\\', "$root/modules/custom/druki/src");
$class_loader->addPsr4('Drupal\druki_author\\', "$root/modules/custom/druki_author/src");
$class_loader->addPsr4('Drupal\druki_content\\', "$root/modules/custom/druki_content/src");
$class_loader->addPsr4('Drupal\druki_redirect\\', "$root/modules/custom/druki_redirect/src");
// Register custom tests namespaces.
$class_loader->addPsr4('Drupal\Tests\druki\\', "$root/modules/custom/druki/tests/src");
$class_loader->addPsr4('Drupal\Tests\druki_author\\', "$root/modules/custom/druki_author/tests/src");
$class_loader->addPsr4('Drupal\Tests\druki_content\\', "$root/modules/custom/druki_content/tests/src");
$class_loader->addPsr4('Drupal\Tests\druki_redirect\\', "$root/modules/custom/druki_redirect/tests/src");
