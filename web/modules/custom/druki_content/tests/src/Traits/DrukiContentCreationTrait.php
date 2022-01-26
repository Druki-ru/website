<?php

namespace Drupal\Tests\druki_content\Traits;

use Drupal\druki_content\Entity\Content;
use Drupal\druki_content\Entity\ContentInterface;
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
   * @return \Drupal\druki_content\Entity\ContentInterface
   *   The created entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createDrukiContent(array $values = []): ContentInterface {
    $values += [
      'title' => $this->randomMachineName(32),
      'slug' => $this->randomMachineName(32),
      'relative_pathname' => 'docs/' . $this->randomMachineName() . '.md',
      'search_keywords' => [
        $this->randomMachineName(),
        $this->randomMachineName(),
      ],
    ];

    $content = Content::create($values);
    $content->save();

    $this->markEntityForCleanup($content);

    return $content;
  }

}
