<?php

/**
 * Database schema upgrade task.
 *
 * Upgrades the Gleez database schema without requiring an administrator login. Required when upgrading from Gleez 1.2.0
 * or older, because signing in is impossible until the schema has been upgraded:
 *
 * - Every user lookup fails with an SQL error, since the soft-delete `deleted` column (introduced in 1.3.0) is missing
 *   in the old schema.
 * - Legacy password hashes (sha1) no longer match the default hash method (sha256). This is detected and reported
 *   below.
 *
 * @package    Gleez\Minion
 * @author     Loong <loong2460@gmail.com>
 * @copyright  (c) 2026 Muffet Lab
 */
class Task_Db_Upgrade extends Minion_Task
{
    /**
     * Applies the upgrade SQL script to the configured database.
     *
     * @param array $params CLI parameters (unused)
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
    protected function _execute(array $params)
    {
        $db = Database::instance();
        $prefix = $db->table_prefix();

        $dbVersion = Kohana::$config->load('site')->get('version');
        $targetVersion = Gleez::VERSION;

        Minion_CLI::write('Gleez Database Upgrade');
        Minion_CLI::write('======================');
        Minion_CLI::write('Current version: ' . ($dbVersion ?: 'Unknown (pre-1.3.0 installation)'));
        Minion_CLI::write('Target version:  ' . $targetVersion);
        Minion_CLI::write();

        if ($dbVersion && version_compare($dbVersion, $targetVersion, '>=')) {
            Minion_CLI::write(Minion_CLI::color('Database schema is already up to date.', 'green'));

            return;
        }

        $warning = $this->hashMethodWarning($prefix);

        if ($warning !== null) {
            Minion_CLI::write(Minion_CLI::color('WARNING: ' . $warning, 'brown'));
            Minion_CLI::write();
        }

        $answer = Minion_CLI::read('This will modify your database schema. Back up your database first. Continue?', ['y', 'n']);

        if (strtolower($answer) !== 'y') {
            Minion_CLI::write('Upgrade aborted.');

            return;
        }

        Minion_CLI::write();

        $sqlFile = MODPATH . 'gleez/views/install/upgrade.sql';

        if (!is_file($sqlFile)) {
            Minion_CLI::write(Minion_CLI::color('Unable to find the upgrade SQL file: ' . $sqlFile, 'red'));

            return;
        }

        $buf = '';
        $errors = [];
        $statements = 0;
        $version = serialize($targetVersion);

        foreach (file($sqlFile) as $line) {
            $line = trim($line);

            if ($line === '' || strncmp($line, '--', 2) === 0) {
                continue;
            }

            $buf .= ' ' . $line;

            if (preg_match('/;$/', $buf)) {
                // Replace the version token before table prefix expansion
                $sql = str_replace('{version}', $version, trim($buf));
                $sql = preg_replace('/{(\w+)}/', $prefix . '$1', $sql);

                try {
                    $db->query(Database::UPDATE, $sql);
                    $statements++;
                } catch (Database_Exception $e) {
                    $errors[] = $e->getMessage();
                }

                $buf = '';
            }
        }

        if (empty($errors)) {
            Cache::instance()->delete_all();

            Minion_CLI::write(Minion_CLI::color('Upgrade completed successfully! ' . $statements . ' SQL statements executed.', 'green'));
            Minion_CLI::write('Database is now at version ' . $targetVersion . '.');

            if ($warning !== null) {
                Minion_CLI::write();
                Minion_CLI::write(Minion_CLI::color('Reminder: ' . $warning, 'brown'));
            } else {
                Minion_CLI::write('You can now sign in to the admin panel.');
            }
        } else {
            Minion_CLI::write(Minion_CLI::color('The upgrade finished with errors (' . $statements . ' SQL statements executed):', 'red'));

            foreach ($errors as $error) {
                Minion_CLI::write(' - ' . $error);
            }

            Minion_CLI::write();
            Minion_CLI::write('Review the errors above and fix them manually, then run this task again.');
        }
    }

    /**
     * Detects a mismatch between the stored password hashes and the configured hash method.
     *
     * Password hashes are one-way: hashes stored with the pre-1.3.0 default algorithm (sha1, 40 hex characters) cannot
     * be converted to sha256 (64 hex characters). Until the configured hash method matches the stored hashes, no
     * existing user can sign in.
     *
     * @param string $prefix Table prefix
     * @return string|null Warning message, null when no mismatch was detected
     * @throws Kohana_Exception
     */
    protected function hashMethodWarning(string $prefix): ?string
    {
        try {
            $pass = DB::query(Database::SELECT, "SELECT `pass` FROM `{$prefix}users` WHERE `id` = 2")
                ->execute()
                ->get('pass');
        } catch (Database_Exception $e) {
            return null;
        }

        $configured = Kohana::$config->load('auth')->get('hash_method', '');
        $length = strlen((string) $pass);

        if ($length === 40 && $configured !== 'sha1') {
            return "The stored passwords use 'sha1' hashes, but the configured hash method is '$configured'. "
                . "Set 'hash_method' => 'sha1' in application/config/auth.php, otherwise no existing user can sign in.";
        }

        if ($length === 64 && $configured !== 'sha256') {
            return "The stored passwords use 'sha256' hashes, but the configured hash method is '$configured'. "
                . "Set 'hash_method' => 'sha256' in application/config/auth.php, otherwise no existing user can sign in.";
        }

        return null;
    }
}
