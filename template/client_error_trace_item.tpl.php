<?php

use Drupal\client_error_trace\Plugin\client_error\ReportInterface;

/**
 * Template for a single client error report item.
 *
 * @var int $result
 *   The result of the report, as a constant from ReportInterface.
 * @var string $description
 *   The description of the test that was run.
 * @var $message
 *   The result message of the test.
 * @var $suggestions
 *   An array of suggestions with possible fixes for any failures.
 */
?>
<h2>
  <?php if ($result == ReportInterface::SUCCESS): ?>
    <strong><?php print t('Passed'); ?>:</strong>
  <?php elseif ($result == ReportInterface::SKIPPED): ?>
    <strong><?php print t('Skipped'); ?>:</strong>
  <?php else: ?>
    <strong><?php print t('Failed'); ?>:</strong>
  <?php endif; ?>

  <?php print $description; ?>
</h2>

<p><?php print $message; ?></p>

<?php if (!empty($suggestions)): ?>
  <h3><?php print t('Fix suggestions'); ?></h3>
  <ul>
  <?php foreach ($suggestions as $suggestion): ?>
    <li><?php print $suggestion; ?></li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
