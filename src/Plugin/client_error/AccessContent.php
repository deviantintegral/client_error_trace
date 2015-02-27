<?php

namespace Drupal\client_error_trace\Plugin\client_error;

use Drupal\client_error_trace\Annotation\ClientError;
use GuzzleHttp\Url;

/**
 * @class AccessContent
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
  public function execute(Url $url) {
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
