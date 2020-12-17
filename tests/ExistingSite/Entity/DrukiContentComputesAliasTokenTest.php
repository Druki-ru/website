<?php

namespace Druki\Tests\ExistingSite\Entity;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for [druki_content:computed-alias] token.
 */
final class DrukiContentComputesAliasTokenTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Tests behavior for common content entity.
   */
  public function testDefaultAlias(): void {
    $entity = $this->createDrukiContent();
    $expected = '/wiki/' . $entity->getExternalId();
    $this->assertEquals($expected, $entity->toUrl()->toString());
  }

  /**
   * Tests alias for content with 'core' set.
   */
  public function testWithCoreValue(): void {
    $entity = $this->createDrukiContent(['core' => 9]);
    $expected = '/wiki/9/' . $entity->getExternalId();
    $this->assertEquals($expected, $entity->toUrl()->toString());
  }

  /**
   * Tests alias for content with 'path' set.
   */
  public function testForcedPath(): void {
    $entity = $this->createDrukiContent(['forced_path' => '/foo-bar/test']);
    $this->assertEquals('/wiki/foo-bar/test', $entity->toUrl()->toString());
  }

}
