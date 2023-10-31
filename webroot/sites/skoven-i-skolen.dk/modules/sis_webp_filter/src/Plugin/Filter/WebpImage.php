<?php

namespace Drupal\sis_webp_filter\Plugin\Filter;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\imageapi_optimize\ImageAPIOptimizeProcessorManager;
use Drupal\webp\Webp;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\FilterProcessResult;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webp Image filter.
 *
 * @Filter(
 * id = "sis_webp_filter",
 * title = @Translation("SiS WebP Filter for CKEditor"),
 * description = @Translation("Converts images to WebP images."),
 * type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 * weight = 101,
 * )
 */
class WebpImage extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The webp service.
   *
   * @var \Drupal\webp\Webp
   */
  protected $webp;

  /**
   * Constructs WebpImage.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param array $plugin_definition
   * @param ModuleHandlerInterface $module_handler
   * @param Webp $webp
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ModuleHandlerInterface $module_handler, Webp $webp) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $module_handler;
    $this->webp = $webp;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('webp.webp')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function tips($long = FALSE) {
    $message = $this->t('Converts images to WebP images. You can set the filter to be after the "Embed Media" filter.');
    return $message;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $doc = new \DOMDocument();
    $doc->encoding = 'UTF-8';
    libxml_use_internal_errors(TRUE);
    @$doc->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));

    $query = new \DOMXPath($doc);
    $results = $query->query("//img");
    if ($results->length > 0) {
      foreach ($results as $result) {
        // If the src is already webp, ignore.
        /** @var \DOMElement $result */
        $src = str_replace('/sites/default/', '/sites/skoven-i-skolen.dk/', $result->getAttribute('src'));
        $rel_path = str_replace('/sites/skoven-i-skolen.dk/files/', '', $src);

        $uri = \Drupal::config('system.file')->get('default_scheme') . '://' . $rel_path;
        /** @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $stream_wrapper_manager */
        $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager');
        $file_uri = $stream_wrapper_manager->normalizeUri($uri);
        // Remove query arguments.
        $uri = preg_match('/^.*(?:\.)[a-zA-Z]+/m', $file_uri, $matches) ? $matches[0] : $file_uri;
        $uri = urldecode($uri);
        // If the src is already webp, ignore.
        if (preg_match('/\.webp$/', $uri)) {
          continue;
        }
        if (!$uri) {
          continue;
        }
        $webp_file_path = $this->webp->createWebpCopy($uri);
        $webp_file = File::create([
          'filename' => basename($webp_file_path),
          'uri' => $webp_file_path,
          'status' => 1,
          'uid' => 0,
        ]);
        $result->setAttribute('src', $webp_file->createFileUrl());
      }
      $text = $doc->saveHTML();
    }
    return new FilterProcessResult($text);
  }

}
