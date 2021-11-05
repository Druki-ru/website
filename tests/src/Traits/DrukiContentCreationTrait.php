<?php

namespace Druki\Tests\Traits;

use Drupal\druki_content\Entity\DrukiContent;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\Tests\RandomGeneratorTrait;
use weitzman\DrupalTestTraits\DrupalTrait;

/**
 * Provides methods to create druki content.
 */
trait DrukiContentCreationTrait {

  use RandomGeneratorTrait;
  use DrupalTrait;

  /**
   * Creates a druki content.
   *
   * @param array $values
   *   An associative array with values for entity.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The created entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createDrukiContent(array $values = []): DrukiContentInterface {
    $values += [
      'title' => $this->randomMachineName(32),
      'slug' => $this->randomMachineName(32),
      'relative_pathname' => 'docs/' . $this->randomMachineName() . '.md',
      'search_keywords' => [
        $this->randomMachineName(),
        $this->randomMachineName(),
      ],
    ];

    $content = DrukiContent::create($values);
    $content->save();

    $this->markEntityForCleanup($content);

    return $content;
  }

}
