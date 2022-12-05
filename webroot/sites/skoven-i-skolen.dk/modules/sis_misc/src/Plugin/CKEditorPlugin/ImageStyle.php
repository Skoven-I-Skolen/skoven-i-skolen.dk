<?php

namespace Drupal\sis_misc\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginContextualInterface;
use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "link" plugin.
 *
 * @CKEditorPlugin(
 *   id = "sis_image_style",
 *   label = @Translation("SIS: image style"),
 *   module = "sis_misc"
 * )
 */
class ImageStyle extends PluginBase implements CKEditorPluginInterface, CKEditorPluginContextualInterface, ContainerFactoryPluginInterface {

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleList;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
    $instance->moduleList = $container->get('extension.list.module');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->moduleList->getPath('sis_misc') . '/js/plugins/imagestyle/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled(Editor $editor) {
    // Check if a DrupalImage has been placed in the CKeditor.
    $settings = $editor->getSettings();
    if ($this->checkImageEnable($settings['toolbar']['rows'][0])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if a DrupalImage exists in the given toolbar row.
   *
   * @param array $toolbar
   *   A CKeditor toolbar row containing Ckeditor plugin items.
   *
   * @return bool
   *   Does the DrupalImage has been placed in the CKeditor.
   */
  public function checkImageEnable($toolbar) {
    foreach ($toolbar as $items) {
      foreach ($items['items'] as $item) {
        if ('DrupalImage' === $item) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

}
