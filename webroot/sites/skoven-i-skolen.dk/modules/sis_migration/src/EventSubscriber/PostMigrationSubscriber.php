<?php

namespace Drupal\YOUR_MODULE\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\user\Entity\User;

/**
 * Class PostMigrationSubscriber.
 *
 * Run our user flagging after the last node migration is run.
 *
 * @package Drupal\YOUR_MODULE
 */
class PostMigrationSubscriber implements EventSubscriberInterface {

  /**
   * Get subscribed events.
   *
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::POST_IMPORT][] = ['onMigratePostImport'];
    return $events;
  }

  /**
   * Check for our specified last node migration and run our flagging mechanisms.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The import event object.
   */
  public function onMigratePostImport(MigrateImportEvent $event) {
    if ($event->getMigration()->getBaseId() == 'YOUR_MIGRATION_ID') {
      $this->createRedirects();
    }
  }

  /**
   * Updates all default flags on our users.
   */
  private function createRedirects() {
   

  }
}
