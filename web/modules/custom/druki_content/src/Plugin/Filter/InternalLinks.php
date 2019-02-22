<?php

namespace Drupal\druki_content\Plugin\Filter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a 'InternalLinks' filter.
 *
 * @Filter(
 *   id = "druki_content_internal_links",
 *   title = @Translation("Internal Links"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 *   weight = -10
 * )
 */
class InternalLinks extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The dom crawler.
   *
   * @var \Symfony\Component\DomCrawler\Crawler
   */
  protected $crawler;

  /**
   * An array with cache tags for lazy re-render.
   *
   * @var array
   */
  protected $lazyCacheTags = [];

  /**
   * The druki content sotrage.
   *
   * @var \Drupal\druki_content\DrukiContentStorage
   */
  protected $drukiContentStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->crawler = new Crawler();
    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $this->crawler->addContent($text);

    $replace = function ($node) use ($langcode) {
      $original_href = $node->getNode(0)->getAttribute('href');
      $href = $this->replaceHref($original_href, $langcode);
      $node->getNode(0)->setAttribute('href', $href);
    };

    // Dom Crawler not intended to change DOM, but little changes as ours is
    // possible.
    $this->crawler->filter('[href^="@druki_content:"]')
      ->each($replace);

    $text = $this->crawler->html();

    $result = new FilterProcessResult($text);
    // The main reason for this, is to handle links, which can be created at
    // particular moment, but can be later.
    // F.e. we have two content "Page 1" and "Page 2", they're imported
    // consecutive, but "Page 1" have internal link to "Page 2" content, but
    // this content can be new, and doesn't have actual entity on site. So, we
    // can't generate path for it. We add it cache tag to current result, and
    // when content will be created, it's invalidate tag, and this result will
    // be generated again, but at this time, "Page 2" will exist.
    // Maybe it's good place to use placeholder, but I think this technique is
    // will be more less CPU intended in long run. But who knows, we can rework
    // it if you prove it.
    $result->setCacheTags($this->lazyCacheTags);

    return $result;
  }

  /**
   * Replace internal link syntax with real path.
   *
   * @param string $href
   *   The internal link href.
   * @param string $langcode
   *   The langcode.
   *
   * @return string
   *   The internal path.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  protected function replaceHref($href, $langcode) {
    $parts = explode(':', $href);
    if ($parts[0] != '@druki_content') {
      return $href;
    }

    $external_id = $parts[1];
    $core_version = isset($parts[2]) ? $parts[2] : NULL;

    $druki_content = $this->drukiContentStorage->loadByMeta($external_id, $langcode, $core_version);
    if ($druki_content instanceof DrukiContentInterface) {
      $href = $druki_content->toUrl()->toString();
    }
    else {
      $cache_tag = $this->drukiContentStorage->getEntityTypeId() . ':' . $langcode . ':' . $external_id;
      $this->addLazyCacheTag($cache_tag);
    }

    return $href;
  }

  /**
   * Adds lazy cache tag.
   *
   * @param string $cache_tag
   *   The cache tag.
   */
  protected function addLazyCacheTag($cache_tag) {
    $this->lazyCacheTags[] = $cache_tag;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('Convert internal links by content ID to actual links.');
  }


}
