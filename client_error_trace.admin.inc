<?php
/**
 * @file
 * Administration page callbacks.
 */

use Drupal\client_error_trace\ClientErrorPluginManager;
use Drupal\client_error_trace\Plugin\client_error\ClientErrorInterface;
use GuzzleHttp\Url;

/**
 * Form callback to run tests against a URL.
 */
function client_error_trace_form($form, &$form_state) {
  $form = array(
    'url' => array(
      '#type' => 'textfield',
      '#title' => t('The public URL to trace.'),
      '#description' => t('Enter the URL to test as used by the public.'),
      '#required' => TRUE,
    ),
  );

  $manager = ClientErrorPluginManager::create();
  $options = array();
  foreach ($manager->getDefinitions() as $plugin) {
    $options[$plugin['id']] = $plugin['description'];
  }

  $form['plugins'] = array(
    '#type' => 'checkboxes',
    '#options' => $options,
    '#title' => t('Validations'),
    '#default_value' => array_keys($options),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Trace errors'),
  );

  return $form;
}

/**
 * Form callback to run tests against a URL.
 */
function client_error_trace_form_submit(&$form, &$form_state) {
  $url = Url::fromString($form_state['values']['url']);
  $manager = ClientErrorPluginManager::create();
  foreach ($form_state['values']['plugins'] as $plugin) {
    /** @var ClientErrorInterface $instance */
    $instance = $manager->createInstance($plugin);
    $report = $instance->execute($url);
    drupal_set_message($report->resultMessage());
    if ($report->hasSuggestions()) {
      drupal_set_message(implode(', ', $report->suggestions()));
    }
  }
}
