<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Access;

use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultNeutral;
use Drupal\Core\Entity\EntityAccessControlHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\druki_content\Entity\ContentInterface;
use Drupal\Tests\druki_content\Traits\DrukiContentCreationTrait;
use weitzman\DrupalTestTraits\Entity\UserCreationTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for testing access handler for druki_content entity.
 *
 * @coversDefaultClass \Drupal\druki_content\Access\ContentAccessControlHandler
 */
final class ContentAccessControlHandlerTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;
  use UserCreationTrait;

  /**
   * An entity access control handler.
   */
  protected EntityAccessControlHandlerInterface $accessControlHandler;

  /**
   * A content entity.
   */
  protected ContentInterface $contentEntity;

  /**
   * An anonymous user.
   */
  protected AccountInterface $anonymousUser;

  /**
   * An admin user.
   */
  protected AccountInterface $adminUser;

  /**
   * Test access checking.
   *
   * @covers ::checkAccess
   * @dataProvider operations
   */
  public function testCheckAccess(string $operation, string $expected_anonymous_result, string $expected_admin_result): void {
    $anonymous_result = $this->accessControlHandler->access($this->contentEntity, $operation, $this->anonymousUser, TRUE);
    $admin_result = $this->accessControlHandler->access($this->contentEntity, $operation, $this->adminUser, TRUE);

    $this->assertInstanceOf($expected_anonymous_result, $anonymous_result);
    $this->assertInstanceOf($expected_admin_result, $admin_result);
  }

  /**
   * Tests 'create' operation access check.
   *
   * @covers ::checkCreateAccess
   */
  public function testCheckCreateAccess(): void {
    $anonymous_result = $this->accessControlHandler->createAccess(account: $this->anonymousUser, return_as_object: TRUE);
    $admin_result = $this->accessControlHandler->createAccess(account: $this->adminUser, return_as_object: TRUE);

    $this->assertInstanceOf(AccessResultNeutral::class, $anonymous_result);
    $this->assertInstanceOf(AccessResultAllowed::class, $admin_result);
  }

  /**
   * An operations provider.
   *
   * @return array
   *   An array with operations and expected results.
   */
  public function operations(): array {
    $operations = [];

    $operations['view'] = [
      'view',
      AccessResultAllowed::class,
      AccessResultAllowed::class,
    ];

    $operations['invalidate'] = [
      'invalidate',
      AccessResultNeutral::class,
      AccessResultAllowed::class,
    ];

    $operations['delete'] = [
      'delete',
      AccessResultNeutral::class,
      AccessResultAllowed::class,
    ];

    $operations['not existed'] = [
      'not_exists',
      AccessResultNeutral::class,
      AccessResultNeutral::class,
    ];

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->accessControlHandler = $this->container->get('entity_type.manager')
      ->getAccessControlHandler('druki_content');

    $this->contentEntity = $this->createDrukiContent(['type' => 'documentation']);
    $this->markEntityForCleanup($this->contentEntity);

    $this->anonymousUser = new AnonymousUserSession();
    $this->adminUser = $this->createUser(admin: TRUE);
  }

}
