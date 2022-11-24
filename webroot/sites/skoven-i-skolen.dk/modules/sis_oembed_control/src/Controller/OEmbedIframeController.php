<?php

namespace Drupal\sis_oembed_control\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use Drupal\media\Controller\OEmbedIframeController as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller which renders an oEmbed resource in a bare page (without blocks)
 * along with additional video settings.
 *
 */
class OEmbedIframeController extends BaseController {

  /**
   * Renders an oEmbed resource.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *   Will be thrown if the 'hash' parameter does not match the expected hash
   *   of the 'url' parameter.
   */
  public function render(Request $request) {
    $url = $request->query->get('url');
    $max_width = $request->query->getInt('max_width');
    $max_height = $request->query->getInt('max_height');

    $parsed_url = UrlHelper::parse($url);
    $media_oembed_control = $request->query->get('sis_oembed_control', NULL);
    if (!empty($media_oembed_control)) {
      if (in_array($media_oembed_control['provider_name'], ['YouTube', 'Vimeo', 'TwentyThree'])) {
        if (isset($media_oembed_control['settings']['video_autoplay']) && $media_oembed_control['settings']['video_autoplay']) {
          $parsed_url['query']['autoplay'] = TRUE;
        }
        $url = $parsed_url['path'] . '?' . rawurldecode(UrlHelper::buildQuery($parsed_url['query']));
      }
    }

    $request->query->set('url', $url);
    $hash = $this->iFrameUrlHelper->getHash($url, $max_width, $max_height);
    $request->query->set("hash", $hash);

    return parent::render($request);
  }

}
