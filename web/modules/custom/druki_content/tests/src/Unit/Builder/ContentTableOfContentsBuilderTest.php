<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Builder;

use Drupal\druki_content\Builder\ContentTableOfContentsBuilder;
use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentHeadingElement;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for Table of Contents builder for Content objects.
 *
 * @coversDefaultClass \Drupal\druki_content\Builder\ContentTableOfContentsBuilder
 */
final class ContentTableOfContentsBuilderTest extends UnitTestCase {

  /**
   * Tests that builder works as expected.
   */
  public function testBuilder(): void {
    $content = new Content();
    $heading_1 = new ContentHeadingElement(2, 'Heading 1');
    $content->addElement($heading_1);
    $heading_2 = new ContentHeadingElement(3, 'Heading 2');
    $content->addElement($heading_2);
    $heading_3 = new ContentHeadingElement(2, 'Heading 3');
    $content->addElement($heading_3);

    $toc = ContentTableOfContentsBuilder::build($content);
    $this->assertEquals(3, $toc->getIterator()->count());
    $heading_iterator = $toc->getIterator();
    $this->assertEquals($heading_1->getContent(), $heading_iterator->current()->getText());
    $heading_iterator->next();
    $this->assertEquals($heading_2->getContent(), $heading_iterator->current()->getText());
    $heading_iterator->next();
    $this->assertEquals($heading_3->getContent(), $heading_iterator->current()->getText());
    $heading_iterator->next();
    $this->assertFalse($heading_iterator->valid());
  }

}
