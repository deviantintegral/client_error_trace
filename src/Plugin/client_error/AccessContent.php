<?php
/**
 * @file
 * Contains the access_content plugin.
 */

namespace Drupal\client_error_trace\Plugin\client_error;

use Drupal\client_error_trace\Annotation\ClientError;
use GuzzleHttp\Url;

/**
 * Contains the access_content plugin.
 *
 * @class AccessContent
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 *
 * @ClientError(
 *   id="access_content",
 *   description="Validate that a user has permission to access content.",
 *   status_code=403
 * )
 */
class AccessContent extends ClientErrorBase {

  /**
   * {@inheritdoc}
   */
  public function execute(Url $url, \stdClass $account = NULL) {
    parent::execute($url, $account);

    // Find if $url is a node, and if it is check 'access content'.
    if (!$this->urlIsNode($url)) {
      return new AccessContentReport($url, AccessContentReport::SKIPPED);
    }

    if (user_access('access content', drupal_anonymous_user())) {
      return new AccessContentReport($url, AccessContentReport::SUCCESS);
    }

    return new AccessContentReport($url, AccessContentReport::FAILED);
  }
}
