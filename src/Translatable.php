<?php

namespace Drupal\client_error_trace;

/**
 * Translate strings with a bootstrapped Drupal database.
 *
 * @class Translatable
 *
 * @package Drupal\client_error_trace
 */
trait Translatable {

  /**
   * Fully translate a string.
   *
   * @param string $string
   *   The string to translate.
   * @param array $args
   *   (optional) Array of replacement arguments.
   * @param array $options
   *   (optional) Array of replacement options.
   *
   * @see t()
   *
   * @return string
   *   The translated string.
   */
  public function t($string, array $args = array(), array $options = array()) {
    return t($string, $args, $options);
  }

}
