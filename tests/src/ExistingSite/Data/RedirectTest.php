<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Data;

use Drupal\druki_redirect\Data\Redirect;
use Drupal\Tests\druki\Trait\EntityCleanupTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect value object.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\Redirect
 */
final class RedirectTest extends ExistingSiteBase {

  use EntityCleanupTrait;

  /**
   * {@inheritdoc}
   */
  public function tearDown() {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that objects works as expected.
   */
  public function testObject(): void {
    $redirect = Redirect::createFromUserInput('/foo-bar', '/');
    $this->assertEquals('/foo-bar', $redirect->getSource()->getPath());
    $this->assertEquals('/', $redirect->getRedirect()->getPath());

    $redirect = Redirect::createFromUserInput('/foo-bar?with=query', '/');
    $this->assertEquals('/foo-bar', $redirect->getSource()->getPath());
    $this->assertEquals(['with' => 'query'], $redirect->getSource()->getQuery());
    $this->assertEquals('/', $redirect->getRedirect()->getPath());

    $redirect = Redirect::createFromUserInput('/foo-baz', '/#fragment');
    $this->assertEquals('/', $redirect->getRedirect()->getPath());
    $this->assertEquals('fragment', $redirect->getRedirect()->getFragment());

    $redirect = Redirect::createFromUserInput('/foo-baz', '/test#fragment');
    $this->assertEquals('/test', $redirect->getRedirect()->getPath());
    $this->assertEquals('fragment', $redirect->getRedirect()->getFragment());

    $redirect = Redirect::createFromUserInput('/foo-bar', 'https://example.com/foo-baz');
    $this->assertEquals('https://example.com/foo-baz', $redirect->getRedirect()->getPath());

    $this->assertIsString($redirect->checksum());
    $this->assertEquals($redirect->checksum(), $redirect->checksum());

    $redirect_repository = $this->container->get('druki_redirect.repository.redirect');
    $redirect = Redirect::createFromUserInput('/foo-baz?with=query', '/test?another=query#fragment');
    $redirect_entity = $redirect_repository->createRedirect($redirect);
    $redirect_2 = Redirect::createFromRedirectEntity($redirect_entity);
    $this->assertEquals($redirect->checksum(), $redirect_2->checksum());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storeEntityIds(['redirect']);
  }

}
