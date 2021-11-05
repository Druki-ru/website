<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\druki\Data\TableOfContents;
use Drupal\druki\Data\TableOfContentsHeading;
use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentHeadingElement;

/**
 * Provides builder for Table of Contents (ToC) from the Content.
 */
final class ContentTableOfContentsBuilder {

  /**
   * Builds Table of Contents for provided content.
   *
   * @param \Drupal\druki_content\Data\Content $content
   *   The content.
   *
   * @return \Drupal\druki\Data\TableOfContents
   *   The Table of Contents.
   */
  public static function build(Content $content): TableOfContents {
    $toc = new TableOfContents();
    /** @var \Drupal\druki_content\Data\ContentElementInterface $element */
    foreach ($content->getElements() as $element) {
      if (!$element instanceof ContentHeadingElement) {
        continue;
      }
      $heading = new TableOfContentsHeading($element->getContent(), $element->getLevel());
      $toc->addHeading($heading);
    }
    return $toc;
  }

}
