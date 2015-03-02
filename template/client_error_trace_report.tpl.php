<?php

/**
 * Template for a client error report.
 *
 * @var string $url
 *   The URL the report was executed against.
 * @var array $results
 *   An array of strings with the result for each test.
 */
?>
<h1><?php print t('Client error report for <a href="@url">@url</a>', array('@url' => $url)); ?></h1>

<?php foreach ($results as $result): ?>
<?php print $result; ?>
<?php endforeach; ?>
