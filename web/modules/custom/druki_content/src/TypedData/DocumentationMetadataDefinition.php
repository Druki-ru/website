<?php

declare(strict_types=1);

namespace Drupal\druki_content\TypedData;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\ListDataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;

/**
 * A typed data definition for content metadata data.
 *
 * Can contain this metadata:
 * - title: (required) The content title. This will be used in <h1> and <title>.
 * - slug: (required) The unique slug of the document. This will be used as URL
 *   suffix. E.g., if the current prefix is 'wiki/' and slug is 'drupal/about'
 *   that means that content will be available by
 *   https://example.com/wiki/drupal/about URL.
 *   The slug also used to find out previously created content for update
 *   instead of creating new one, this means, that value is also serves as ID
 *   and because of that should be unique across all content in single language.
 * - core: (optional) The Drupal core major version.
 * - category: (optional) The category allows grouping several contents into one
 *   group of content with navigation between them. The category definition is
 *   an array, which contains:
 *   - area: (required) The category area. The content with same category area
 *     set will be grouped.
 *   - order: (optional) The position of the current content in the group. By
 *     default all have order = 0. Sort is ascending — the lower order will be
 *     showed first.
 *   - title: (optional) The override for content title in the grouped list.
 * - search-keywords: (optional) An array with search keywords that can be used
 *   for search that content and not the part of the content or should be
 *   boosted is search rankings. E.g., content about Libraries API can contain
 *   such keywords: 'how to add javascript css', 'how to add script on the
 *   page'. These keywords have extra priority over content.
 * - metatags: (optional) An array with content metatags:
 *   - title: (optional) Allows overriding <title> value for the page as well as
 *     related metatags <meta name='title'>, <meta name='twitter:title'>,
 *     <meta property='og:title'>. This value does not change <h1> of the page.
 *   - description: (optional) Allows providing specific content description.
 *     This value will be used for <meta name='description'> and
 *     <meta property='og:description'>.
 * - authors: (optional) An array with author ID's from authors.json that should
 *   be listed as contributors/authors of the content. It's recommended to be
 *   set, because Git can't properly track all contributors if files are merged
 *   or copied.
 */
final class DocumentationMetadataDefinition extends MapDataDefinition {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(): array {
    $this->setPropertyDefinition('title', DataDefinition::create('string')->setRequired(TRUE));
    $this->setPropertyDefinition('slug', DataDefinition::create('string')->setRequired(TRUE));
    $this->setPropertyDefinition('core', $this->getCoreDefinition());
    $this->setPropertyDefinition('category', $this->getCategoryDefinition());
    $this->setPropertyDefinition('search-keywords', ListDataDefinition::create('string'));
    $this->setPropertyDefinition('metatags', $this->getMetatagsDefinition());
    $this->setPropertyDefinition('authors', ListDataDefinition::create('string'));
    return parent::getPropertyDefinitions();
  }

  /**
   * Gets definition for metatags.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   A metatags values definition.
   */
  protected function getMetatagsDefinition(): DataDefinitionInterface {
    return MapDataDefinition::create()
      ->setPropertyDefinition('title', DataDefinition::create('string'))
      ->setPropertyDefinition('description', DataDefinition::create('string'));
  }

  /**
   * Gets definition for Drupal core.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   A drupal core metadata definition.
   */
  protected function getCoreDefinition(): DataDefinitionInterface {
    return DataDefinition::create('integer')
      ->addConstraint('Range', [
        'min' => 8,
        'max' => 10,
      ]);
  }

  /**
   * Gets category metadata definition.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   A data definition for category.
   */
  protected function getCategoryDefinition(): DataDefinitionInterface {
    $order_definition = DataDefinition::create('integer')
      ->addConstraint('Range', [
        'min' => 0,
        'max' => 1000,
      ]);

    return MapDataDefinition::create()
      ->setPropertyDefinition('area', DataDefinition::create('string')->setRequired(TRUE))
      ->setPropertyDefinition('order', $order_definition)
      ->setPropertyDefinition('title', DataDefinition::create('string'));
  }

}
