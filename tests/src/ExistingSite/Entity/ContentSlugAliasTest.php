<?php

namespace Druki\Tests\ExistingSite\Entity;

use Drupal\Tests\druki_content\Trait\DrukiContentCreationTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for [druki_content:computed-alias] token.
 */
final class ContentSlugAliasTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Tests behavior for common content entity.
   */
  public function testAlias(): void {
    $entity = $this->createDrukiContent([
      'type' => 'documentation',
      'slug' => 'wiki/Test.dot',
    ]);
    $expected = '/' . $entity->getSlug();
    $this->assertEquals($expected, $entity->toUrl()->toString());
  }

}
