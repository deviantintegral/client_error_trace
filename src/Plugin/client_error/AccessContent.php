<?php
/**
 * @file
 * Contains the access_content plugin.
 */

namespace Drupal\client_error_trace\Plugin\client_error;

use Drupal\client_error_trace\Annotation\ClientError;
use Guzzle\Http\Url;

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
  public function execute(Url $url, $account = NULL) {
    $account = $this->defaultAccount($account);

    // Find if $url is a node, and if it is check 'access content'.
    if (!$this->urlIsNode($url)) {
      $result = AccessContentReport::SKIPPED;
    }
    elseif (user_access('access content', $account)) {
      $result = AccessContentReport::SUCCESS;
    }
    else {
      $result = AccessContentReport::FAILED;
    }

    return new AccessContentReport($url, $account, $result);
  }

}
