<?php

namespace Druki\Tests\ExistingSite\Sync\ParsedContent\FrontMatter;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatter;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for front matter loader.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterLoader
 */
final class FrontMatterLoaderTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  /**
   * Testing saving content into paragraph.
   */
  public function testProcess(): void {
    $front_matter = new FrontMatter();
    $front_matter->add(new FrontMatterValue('title', 'Foo bar!'));
    $front_matter->add(new FrontMatterValue('category', [
      'area' => 'foo',
      'order' => 10,
      'title' => 'bar',
    ]));
    $front_matter->add(new FrontMatterValue('core', '9'));
    $front_matter->add(new FrontMatterValue('path', '/foo-bar'));
    $front_matter->add(new FrontMatterValue('difficulty', 'medium'));
    $front_matter->add(new FrontMatterValue('labels', ['foo', 'bar']));
    $front_matter->add(new FrontMatterValue('search-keywords', ['foo', 'bar']));
    $front_matter->add(new FrontMatterValue('metatags', [
      'title' => 'Foo bar!',
      'description' => 'This is foo bar content!',
    ]));

    $druki_content = $this->createDrukiContent();
    $content_loader = $this->container->get('druki_content.parsed_content_loader');
    $content_loader->process($front_matter, $druki_content);

    $this->assertEquals($front_matter->get('title')->getValue(), $druki_content->label());
    $this->assertEquals($front_matter->get('category')->getValue(), $druki_content->get('category')->first()->getValue());
    $this->assertEquals($front_matter->get('core')->getValue(), $druki_content->getCore());
    $this->assertEquals($front_matter->get('path')->getValue(), $druki_content->get('forced_path')->value);
    $this->assertEquals($front_matter->get('difficulty')->getValue(), $druki_content->get('difficulty')->value);
    $this->assertEquals($front_matter->get('labels')->getValue(), [
      $druki_content->get('labels')->offsetGet(0)->value,
      $druki_content->get('labels')->offsetGet(1)->value,
    ]);
    $this->assertEquals($front_matter->get('search-keywords')->getValue(), [
      $druki_content->get('search_keywords')->offsetGet(0)->value,
      $druki_content->get('search_keywords')->offsetGet(1)->value,
    ]);

    // The loader also provides additional metatags values bases on these two.
    $metatags_expected = $front_matter->get('metatags')->getValue();
    $metatags_expected['og_title'] = $metatags_expected['title'];
    $metatags_expected['twitter_cards_title'] = $metatags_expected['title'];
    $metatags_expected['og_description'] = $metatags_expected['description'];
    $this->assertEquals(\serialize($metatags_expected), $druki_content->get('metatags')->value);
  }

}
