<?php

namespace Drupal\client_error_trace\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Describes the annotation fields on a ClientError plugin.
 *
 * @Annotation
 *
 * @class ClientError
 * @package Drupal\client_error_trace\Annotation
 *
 * @see AccessContent An example ClientError plugin.
 */
class ClientError extends Plugin {

  /**
   * The plugin identifier string.
   *
   * @var string
   */
  public $id;

  /**
   * The HTTP client error that this plugin validates for.
   *
   * @link https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#4xx_Client_Error
   *
   * @var int
   */
  public $statusCode;

  /**
   * A human-readable description of this plugin.
   *
   * @var string
   */
  public $description;
}
