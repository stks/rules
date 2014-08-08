<?php

/**
 * @file
 * Contains \Drupal\rules\Tests\Action\FetchEntityByIdTest.
 */

namespace Drupal\rules\Tests\Action;

use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\rules\Plugin\Action\FetchEntityById;
use Drupal\rules\Tests\RulesTestBase;
use Drupal\Core\TypedData\TypedDataManager;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Action\FetchEntityById
 * @group rules_action
 */
class FetchEntityByIdTest extends RulesTestBase {

  /**
   * The action to be tested.
   *
   * @var \Drupal\rules\Engine\RulesActionInterface
   */
  protected $action;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->entityManager = $this->getMock('Drupal\Core\Entity\EntityManagerInterface');

    $this->action = new FetchEntityById([], '', [
      'context' => [
        'entity_type' => new ContextDefinition('text'),
        'entity_id' => new ContextDefinition('integer'),
      ],
      'provides' => [
        'entity' => new ContextDefinition('entity'),
      ]
    ], $this->entityManager);

    $this->action->setStringTranslation($this->getMockStringTranslation());
    $this->action->setTypedDataManager($this->getMockTypedDataManager());
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary()
   */
  public function testSummary() {
    $this->assertEquals('Fetch entity by id', $this->action->summary());
  }

  /**
   * Tests the action execution.
   *
   * @covers ::execute()
   */
  public function testActionExecution() {

    $entity = $this->getMock('Drupal\Core\Entity\EntityInterface');
    $entityStorage = $this->getMock('Drupal\Core\Entity\EntityStorageInterface');
    $entityStorage->expects($this->once())
      ->method('load')
      ->with(1)
      ->will($this->returnValue($entity));

    $this->entityManager->expects($this->once())
      ->method('getStorage')
      ->with('node')
      ->will($this->returnValue($entityStorage));

    $this->action->setContextValue('entity_type', $this->getMockTypedData('node'))
      ->setContextValue('entity_id', $this->getMockTypedData(1));

    $this->action->execute();

    $this->assertEuaqls($entity, $this->action->getContextValue('entity'), 'Action returns the loaded entity for fetching entity by id.');
  }
}
