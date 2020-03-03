<?php

namespace Drupal\druki_content\ParsedContent\FrontMatter;

use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\ParsedContent\ParsedContentItemLoaderBase;

/**
 * Provides loader for Front Matter data.
 */
final class FrontMatterLoader extends ParsedContentItemLoaderBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = FrontMatter::class;

  /**
   * {@inheritdoc}
   */
  public function process($data, DrukiContentInterface $content): void {
    $this->processTitle($data, $content);
    $this->processCategory($data, $content);
    $this->processCore($data, $content);
    $this->processPath($data, $content);
    $this->processDifficulty($data, $content);
    $this->processLabels($data, $content);
    $this->processSearchKeywords($data, $content);
    $this->processMetatags($data, $content);
  }

  /**
   * Process 'title' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processTitle(FrontMatter $front_matter, DrukiContentInterface $content): void {
    $content->setTitle($front_matter->get('title')->getValue());
  }

  /**
   * Process 'category' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processCategory(FrontMatter $front_matter, DrukiContentInterface $content): void {
    if ($front_matter->has('category')) {
      $category = $front_matter->get('category')->getValue();
      $category_area = $category['area'];
      $category_order = (isset($category['order'])) ? $category['order'] : 0;
      $category_title = (isset($category['title'])) ? $category['title'] : NULL;

      $content->setCategory($category_area, $category_order, $category_title);
    }
  }

  /**
   * Process 'core' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processCore(FrontMatter $front_matter, DrukiContentInterface $content): void {
    if ($front_matter->has('core')) {
      $content->setCore($front_matter->get('core')->getValue());
    }
  }

  /**
   * Process 'path' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   *
   * @see druki_content_tokens()
   */
  protected function processPath(FrontMatter $front_matter, DrukiContentInterface $content): void {
    if ($front_matter->has('path')) {
      $forced_alias = $front_matter->get('path')->getValue();
      $content->set('forced_path', $forced_alias);
    }
  }

  /**
   * Process 'difficulty' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   *
   * @todo Consider remove this field or use it.
   */
  protected function processDifficulty(FrontMatter $front_matter, DrukiContentInterface $content): void {
    // Reset value. Assumes that value was cleared.
    $content->set('difficulty', NULL);
    if ($front_matter->has('difficulty')) {
      // Get available values directly from field.
      $field_definitions = $content->getFieldDefinitions();

      if (isset($field_definitions['difficulty'])) {
        $difficulty = $field_definitions['difficulty'];
        $settings = $difficulty->getSetting('allowed_values');
        $allowed_values = array_keys($settings);

        if (in_array($front_matter->get('difficulty')->getValue(), $allowed_values)) {
          $content->set('difficulty', $front_matter->get('difficulty')->getValue());
        }
      }
    }
  }

  /**
   * Process 'labels' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   *
   * @todo Consider remove this field or use it.
   */
  protected function processLabels(FrontMatter $front_matter, DrukiContentInterface $content): void {
    // Reset value. Assumes that value was cleared.
    $content->set('labels', NULL);
    if ($front_matter->has('labels')) {
      $content->set('labels', $front_matter->get('labels')->getValue());
    }
  }

  /**
   * Process 'search-keywords' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processSearchKeywords(FrontMatter $front_matter, DrukiContentInterface $content): void {
    // Reset value. Assumes that value was cleared.
    $content->set('search_keywords', NULL);
    if ($front_matter->has('search-keywords')) {
      $content->set('search_keywords', $front_matter->get('search-keywords')->getValue());
    }
  }

  /**
   * Process 'metatags' value.
   *
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processMetatags(FrontMatter $front_matter, DrukiContentInterface $content): void {
    // Reset value. Assumes that value was cleared.
    $content->set('metatags', NULL);
    if ($front_matter->has('metatags')) {
      $metatags = $front_matter->get('metatags')->getValue();
      $allowed_values = ['title', 'description'];

      foreach ($metatags as $key => $value) {
        if (!in_array($key, $allowed_values)) {
          unset($metatags[$key]);
        }
      }

      if (isset($metatags['title'])) {
        $metatags['og_title'] = $metatags['title'];
        $metatags['twitter_cards_title'] = $metatags['title'];
      }

      if (isset($metatags['description'])) {
        $metatags['og_description'] = $metatags['description'];
      }

      $content->set('metatags', serialize($metatags));
    }
  }

}
