<?php

namespace Drupal\sis_misc\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\image\Entity\ImageStyle as IS;
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
   * @var \Drupal\Core\File\FileUrlGenerator;
   */
  protected $fileUrlGenerator;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $entity_type_manager, $file_url_generator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storage = $entity_type_manager->getStorage("file");
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get("entity_type.manager"),
      $container->get("file_url_generator")
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);

    if (stristr($text, 'data-entity-uuid') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);
      foreach ($xpath->query('//*[@data-entity-uuid]') as $node) {
        // Read the data-align attribute's value, then delete it.
        $style = $node->getAttribute('data-image-size');

        if (!$style) {
          $style = $node->parentNode->getAttribute('data-image-size');
          if (!$style && $node->parentNode->parentNode) {
            $style = $node->parentNode->parentNode->getAttribute('data-image-size');
          }
        }

        $uuid = $node->getAttribute('data-entity-uuid');

        $node->removeAttribute('data-image-size');
        $node->removeAttribute('data-entity-type');
        $node->removeAttribute('data-entity-uuid');

        $file = $this->storage->loadByProperties(['uuid' => $uuid]);
        if ($file) {
          $file = reset($file);
          $fileUri = $file->getFileUri();

          if (isset($file) && in_array($style, ['small', 'medium', 'large'])) {
            $size = $style;
            $style = IS::load($style);
            $uri = $style->buildUri($fileUri);
            $style->createDerivative($fileUri, $uri);
            $src = $this->fileUrlGenerator->generateString($uri);
            $node->setAttribute('src', $src);
            $node->setAttribute('class', 'img-size--' . $size);
          }
        }
      }
      $result->setProcessedText(Html::serialize($dom));
    }

    return $result;
  }

}
