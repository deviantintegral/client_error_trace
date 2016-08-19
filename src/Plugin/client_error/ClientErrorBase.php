<?php
/**
 * @file
 * Base class for all ClientError plugins.
 */

namespace Drupal\client_error_trace\Plugin\client_error;
use Guzzle\Http\Url;

/**
 * Base class for all ClientError plugins.
 *
 * @class ClientErrorBase
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
abstract class ClientErrorBase implements ClientErrorInterface {

  /**
   * Return the internal system path for a given URL.
   *
   * @param \Guzzle\Http\Url $url
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
   * @param \Guzzle\Http\Url $url
   *   The URL to check.
   *
   * @return bool
   *   TRUE if the path of URL corresponds to a node/<nid> system path.
   */
  protected function urlIsNode(Url $url) {
    $parts = $this->nodeUrlParts($url);
    return isset($parts[0]) && $parts[0] == 'node' && is_numeric($parts[1]);
  }

  /**
   * Return the node ID from a URL.
   *
   * @param \Guzzle\Http\Url $url
   *   The URL to return the node ID from.
   *
   * @return int
   *   The node ID.
   */
  protected function urlNodeId(Url $url) {
    if ($this->urlIsNode($url)) {
      $parts = $this->nodeUrlParts($url);
      return (int) $parts[1];
    }

    throw new \InvalidArgumentException(format_string('!url is not a node view URL.', array('!url' => $url->__toString())));
  }

  /**
   * Return the default account.
   *
   * @param mixed $account
   *   The account to check.
   *
   * @return mixed
   *   If $account is NULL, return the anonymous user account. Otherwise, return
   *   $account.
   */
  protected function defaultAccount($account = NULL) {
    if (!$account) {
      return drupal_anonymous_user();
    }

    return $account;
  }

  /**
   * Return the parts for a node URL.
   *
   * @param \Guzzle\Http\Url $url
   *   The URL to parse.
   *
   * @return array
   *   An array of URL parts based on slashes in the URL.
   */
  private function nodeUrlParts(Url $url) {
    $internal = $this->getInternalUrl($url);
    $parts = explode('/', substr($internal, 1));
    return $parts;
  }
}
