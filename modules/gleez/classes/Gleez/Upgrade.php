<?php

/**
 * Database upgrade helper.
 *
 * Selects versioned schema migration scripts from
 * `modules/gleez/sql/upgrade/<version>.sql` so that only the
 * scripts newer than the installed schema version are executed.
 *
 * @package    Gleez
 * @author     Loong <loong2460@gmail.com>
 * @copyright  (c) 2026 Muffet Lab
 */
class Gleez_Upgrade
{
    /**
     * Lists the migration scripts required to bring the database from
     * $dbVersion up to $targetVersion, oldest first.
     *
     * @param string|null $dbVersion Current schema version stored in the database, null/empty for pre-1.3.0 installs
     * @param string $targetVersion Target Gleez version
     * @return array Absolute paths of the pending .sql scripts
     */
    public static function scripts(?string $dbVersion, string $targetVersion): array
    {
        $scripts = glob(MODPATH . 'gleez/sql/upgrade/*.sql') ?: [];

        usort($scripts, static function (string $a, string $b): int {
            return version_compare(basename($a, '.sql'), basename($b, '.sql'));
        });

        return array_values(array_filter($scripts, static function (string $file) use ($dbVersion, $targetVersion): bool {
            $version = basename($file, '.sql');

            return ($dbVersion === null || $dbVersion === '' || version_compare($dbVersion, $version, '<'))
                && version_compare($version, $targetVersion, '<=');
        }));
    }

    /**
     * Stamps the schema version in the config table with the given release.
     *
     * @param string $prefix Table prefix
     * @param string $version Version to stamp
     * @return void
     * @throws Kohana_Exception
     */
    public static function stamp(string $prefix, string $version): void
    {
        DB::query(Database::UPDATE, "REPLACE INTO `{$prefix}config` (`group_name`, `config_key`, `config_value`) VALUES ('site', 'version', '" . serialize($version) . "')")->execute();
    }
}
