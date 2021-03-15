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
  public function testAlias(): void {
    $entity = $this->createDrukiContent();
    $expected = '/wiki/' . $entity->getSlug();
    $this->assertEquals($expected, $entity->toUrl()->toString());
  }

}
