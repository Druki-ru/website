<?php

namespace Drupal\druki_content\Sync\ParsedContent\FrontMatter;

use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\Sync\ParsedContent\ParsedContentItemLoaderBase;

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
    $this->processSlug($data, $content);
    $this->processCategory($data, $content);
    $this->processCore($data, $content);
    $this->processSearchKeywords($data, $content);
    $this->processMetatags($data, $content);
  }

  /**
   * Process 'title' value.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processTitle(FrontMatterInterface $front_matter, DrukiContentInterface $content): void {
    $content->setTitle($front_matter->get('title')->getValue());
  }

  /**
   * Process 'title' value.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processSlug(FrontMatterInterface $front_matter, DrukiContentInterface $content): void {
    $content->set('slug', $front_matter->get('slug')->getValue());
  }

  /**
   * Process 'category' value.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processCategory(FrontMatterInterface $front_matter, DrukiContentInterface $content): void {
    if ($front_matter->has('category')) {
      $category = $front_matter->get('category')->getValue();
      $category_area = $category['area'];
      $category_order = $category['order'] ?? 0;
      $category_title = $category['title'] ?? NULL;

      $content->setCategory($category_area, $category_order, $category_title);
    }
    else {
      $content->set('category', NULL);
    }
  }

  /**
   * Process 'core' value.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processCore(FrontMatterInterface $front_matter, DrukiContentInterface $content): void {
    if ($front_matter->has('core')) {
      $content->setCore($front_matter->get('core')->getValue());
    }
  }

  /**
   * Process 'search-keywords' value.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processSearchKeywords(FrontMatterInterface $front_matter, DrukiContentInterface $content): void {
    // Reset value. Assumes that value was cleared.
    $content->set('search_keywords', NULL);
    if ($front_matter->has('search-keywords')) {
      $content->set('search_keywords', $front_matter->get('search-keywords')->getValue());
    }
  }

  /**
   * Process 'metatags' value.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $front_matter
   *   The parsed Front Matter.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  protected function processMetatags(FrontMatterInterface $front_matter, DrukiContentInterface $content): void {
    // Reset value. Assumes that value was cleared.
    $content->set('metatags', NULL);
    if ($front_matter->has('metatags')) {
      $metatags = $front_matter->get('metatags')->getValue();
      $allowed_values = ['title', 'description'];

      foreach ($metatags as $key => $value) {
        if (!\in_array($key, $allowed_values)) {
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

      $content->set('metatags', \serialize($metatags));
    }
  }

}
