<?php
/**
 * @file
 * Base class for all ClientError plugins.
 */

namespace Drupal\client_error_trace\Plugin\client_error;
use GuzzleHttp\Url;

/**
 * Base class for all ClientError plugins.
 *
 * @class ClientErrorBase
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
abstract class ClientErrorBase implements ClientErrorInterface {

  /**
   * {@inheritdoc}
   */
  public function execute(Url $url, \stdClass $account = NULL) {
    if (!$account) {
      $account = drupal_anonymous_user();
    }
  }

  /**
   * Return the internal system path for a given URL.
   *
   * @param \GuzzleHttp\Url $url
   *   The URL to return the normal path for.
   *
   * @return string
   *   The internal system path, or the path of $url if no path is found.
   */
  protected function getInternalUrl(Url $url) {
    $internal = drupal_get_normal_path($url->getPath());
    return $internal;
  }

  /**
   * Return if a URL corresponds to a node/<nid> system path.
   *
   * @param \GuzzleHttp\Url $url
   *   The URL to check.
   *
   * @return bool
   *   TRUE if the path of URL corresponds to a node/<nid> system path.
   */
  protected function urlIsNode(Url $url) {
    $internal = $this->getInternalUrl($url);
    $parts = explode('/', substr($internal, 1));

    return isset($parts[0]) && $parts[0] == 'node';
  }
}
