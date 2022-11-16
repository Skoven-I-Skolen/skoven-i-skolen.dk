<?php

namespace Drupal\sis_misc\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to wrap each table into DIV with class name "table-responsive".
 *
 * @Filter(
 *   id = "filter_image_style",
 *   title = @Translation("Use image style"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class ImageStyle extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface;
   */
  protected $storage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storage = $entity_type_manager->getStorage("file");
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get("entity_type.manager"));
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);

    if (stristr($text, 'data-image-style') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);
      foreach ($xpath->query('//*[@data-image-style]') as $node) {
        // Read the data-align attribute's value, then delete it.
        $style = $node->getAttribute('data-image-style');
        $uuid = $node->getAttribute('data-entity-uuid');
        $node->removeAttribute('data-image-style');

        $file = $this->storage->loadByProperties(['uuid' => $uuid]);
        $file = reset($file);

        if (isset($file) && in_array($style, ['small', 'medium', 'large'])) {
          $style = ImageStyle::load($style);
          $src = $style->buildUrl($file->getFileUri());
          $node->setAttribute('src', $src);
        }
      }
      $result->setProcessedText(Html::serialize($dom));
    }

    return $result;
  }

}
