<?php

/**
 * @file
 * Contains \Drupal\rules\Tests\AutoSaveTest.
 */

namespace Drupal\rules\Tests;

use Drupal\rules\Plugin\RulesExpression\RulesAction;

/**
 * Test auto saving of variables after Rules execution.
 *
 * @group rules
 */
class AutoSaveTest extends RulesTestBase {

  /**
   * Tests auto saving after an action execution.
   */
  public function testActionAutoSave() {
    $processor_manager = $this->getMockBuilder('Drupal\rules\Plugin\RulesDataProcessorManager')
      ->disableOriginalConstructor()
      ->getMock();

    $action_manager = $this->getMockBuilder('Drupal\Core\Action\ActionManager')
      ->disableOriginalConstructor()
      ->getMock();

    $action_manager->expects($this->once())
      ->method('getDefinition')
      ->willReturn([
        'context' => [
          'entity' => $this->getMock('Drupal\Core\Plugin\Context\ContextDefinitionInterface')
        ],
      ]);

    $action_manager->expects($this->once())
      ->method('createInstance')
      ->willReturn($this->testAction);

    $this->testAction->expects($this->once())
      ->method('getContextDefinitions')
      ->willReturn(['entity' => $this->getMock('Drupal\Core\Plugin\Context\ContextDefinitionInterface')]);

    $this->testAction->expects($this->once())
      ->method('getProvidedDefinitions')
      ->willReturn([]);

    $this->testAction->expects($this->once())
      ->method('autoSaveContext')
      ->willReturn(['entity']);

    $action = new RulesAction([
      'action_id' => 'test',
    ], 'test', [], $action_manager, $processor_manager);

    $entity = $this->getMock('Drupal\Core\Entity\EntityInterface');
    $entity->expects($this->once())
      ->method('save');

    $context = $this->getMock('Drupal\Core\Plugin\Context\ContextInterface');
    $context->expects($this->once())
      ->method('getContextValue')
      ->willReturn($entity);
    $context->expects($this->once())
      ->method('getContextData')
      ->willReturn($entity);

    $action->setContext('entity', $context);

    $action->execute();
  }

}
