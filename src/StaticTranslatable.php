<?php

namespace Drupal\client_error_trace;

/**
 * Translation function that does not require a Drupal bootstrap.
 *
 * @class StaticTranslatable
 *
 * @package Drupal\client_error_trace
 */
trait StaticTranslatable {

  /**
   * Replace placeholders in a string according to Drupal's t() function.
   *
   * @param string $string
   *   The string to translate.
   * @param array $args
   *   An array of replacement arguments.
   *
   * @see t()
   * @see format_string()
   *
   * @return string
   *   A translated string.
   */
  public function t($string, array $args = array()) {
    return format_string($string, $args);
  }

}
