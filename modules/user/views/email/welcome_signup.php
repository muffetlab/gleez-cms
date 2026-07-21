<?php echo __('Hello :name!', [':name' => ($nick ?: $name)]) . PHP_EOL ?>
<?php echo __('Your account at :site (:url) has been activated.', [
        ':site' => Kohana::$config->load('site')->get('site_name', 'Gleez CMS'),
        ':url' => $url
    ]) . PHP_EOL ?>

<?php echo __('You may now log in to !uri_brief clicking on this link or copying and pasting it in your browser.', [
        '!uri_brief' => $uri_brief
    ]) . PHP_EOL ?>

<?php echo __('Your registration data:') . PHP_EOL ?>
<?php echo __('username: :name', [':name' => $name]) . PHP_EOL ?>
<?php echo __('email: :mail', [':mail' => $mail]) . PHP_EOL ?>

--

<?php echo __('Best regards, :site team', [
        ':site' => Kohana::$config->load('site')->get('site_name', 'Gleez CMS')
    ]) . PHP_EOL ?>
<?php echo URL::site('', TRUE) ?>
