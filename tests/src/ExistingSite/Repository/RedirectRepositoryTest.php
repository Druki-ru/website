<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Repository;

use Drupal\druki_redirect\Data\Redirect;
use Drupal\druki_redirect\Repository\RedirectRepository;
use Drupal\redirect\Entity\Redirect as RedirectEntity;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use Symfony\Component\HttpFoundation\Request;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect repository extension.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Repository\RedirectRepository
 */
final class RedirectRepositoryTest extends ExistingSiteBase {

  use EntityCleanupTrait;

  /**
   * The redirect repository.
   */
  private RedirectRepository $redirectRepository;

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that we can crate new redirect entity from value object.
   */
  public function testCreateRedirect(): void {
    $redirect = Redirect::createFromUserInput('/foo-bar?with=query', '/#fragment');
    $this->redirectRepository->createRedirect($redirect);

    $request = Request::create('/foo-bar?with=query', server: ['SCRIPT_NAME' => 'index.php']);
    $response = $this->container->get('http_kernel')->handle($request);
    $this->assertEquals(301, $response->getStatusCode());
  }

  /**
   * Tests that redirect search working as expected.
   */
  public function testFindRedirect(): void {
    $redirect = Redirect::createFromUserInput('/foo-baz?with=query', '/#fragment');
    $this->assertNull($this->redirectRepository->findRedirect($redirect));
    $redirect_entity = $this->redirectRepository->createRedirect($redirect);
    $this->assertEquals($redirect_entity->id(), $this->redirectRepository->findRedirect($redirect));
  }

  /**
   * Tests that loading works as expected.
   */
  public function testLoadRedirect(): void {
    $redirect = Redirect::createFromUserInput('/foo-baz?with=query', '/#fragment');
    $this->assertNull($this->redirectRepository->loadRedirect($redirect));
    $redirect_entity = $this->redirectRepository->createRedirect($redirect);
    $loaded_entity = $this->redirectRepository->loadRedirect($redirect);
    $this->assertEquals($redirect_entity->id(), $loaded_entity->id());
    $this->assertInstanceOf(RedirectEntity::class, $loaded_entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->redirectRepository = $this->container->get('druki_redirect.repository.redirect');
    $this->storeEntityIds(['redirect']);
  }

}
