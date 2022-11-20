<?php

namespace Drupal\sis_misc\Commands;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;

class SisMiscCommands extends DrushCommands {

  /**
   * Entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct();
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Drush command that migrates article images from one field to another.
   *
   * @command sis_misc:article_migrate_images
   * @aliases sis:ami
   */
  public function article_migrate_images() {
    $storage = $this->entityTypeManager->getStorage('node');
    /** @var \Drupal\node\Entity\Node[] $entities */
    $entities = $storage->loadByProperties(["type" => "article"]);

    foreach ($entities as $entity) {
      if ($entity->get("field_article_image")->isEmpty()) {
        continue;
      }

      try {
        $this->output()->writeln('Processing node: ' . $entity->id());
        $value = $entity->get("field_article_image")->getValue();
        $entity->get("field_article_image_migrated")->setValue($value);
        $entity->get("field_article_image")->setValue(NULL);
        $entity->save();
      }
      catch (Exception $ex) {
        $this->output()->writeln($ex);
      }
    }
  }
}
