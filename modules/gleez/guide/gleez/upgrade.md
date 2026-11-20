# Upgrading Gleez

When you update the Gleez CMS files to a newer version, the database schema may also need to be updated. Gleez automatically detects when a database upgrade is required and guides you through the process.

How you run the upgrade depends on the version you are upgrading from:

- **From Gleez 1.3.0 or later** — use the web interface: [Admin → Tools → Database Upgrade](#running-the-upgrade-from-130-or-later).
- **From Gleez 1.2.0 or older** — use the CLI task: [db:upgrade](#running-the-upgrade-from-12-or-older), because signing in is impossible until the schema has been upgraded.

## How Gleez Detects Available Upgrades

Gleez stores the current database schema version in the `config` table (`group_name = 'site'`, `config_key = 'version'`). The admin dashboard compares this stored version against the `Gleez::VERSION` constant and displays a warning when an upgrade is needed.

Versions prior to 1.3.0 do not have a version record in the `config` table, so the current version is reported as "Unknown" and the upgrade is always considered necessary.

## Running the Upgrade from 1.3.0 or Later

When an upgrade is needed:

1. The admin dashboard displays a warning with a link to the upgrade page.
2. Navigate to **Admin → Tools → Database Upgrade** at your convenience.
3. The upgrade page shows your current version and the target version.

To run the upgrade:

1. **Back up your database** before proceeding.
2. Navigate to **Admin → Tools → Database Upgrade**.
3. Click the **Run Upgrade** button.
4. Do not refresh the page during the process.

If the upgrade completes without errors, you will see a success message and be redirected back to the tools page. If errors occur, they will be displayed on the same page so you can review and fix them manually.

## Running the Upgrade from 1.2.0 or Older

After replacing the files of a pre-1.3.0 installation with 1.3.0, no administrator can sign in, so the web interface above is unreachable:

1. The soft-delete column `deleted` (introduced in 1.3.0) is missing from the old schema, so every login attempt fails with an SQL error: `Unknown column 'users.deleted' in 'WHERE clause'`.
2. Legacy `sha1` password hashes are not verified by the new default hash method `sha256` (see [Password Hash Method](#password-hash-method)).

Instead, run the upgrade from the command line using the Minion task `db:upgrade`:

    php public/index.php --task=db:upgrade

or via the minion wrapper:

    ./modules/minion/minion db:upgrade

The task connects to the database configured in `application/config/database.php` and:

1. Shows the current and the target version.
2. Detects legacy `sha1` password hashes and warns you to keep the hash method configured accordingly.
3. Asks for confirmation — back up your database first.
4. Applies `modules/gleez/views/install/upgrade.sql` to the database.
5. Clears the cache after a successful upgrade.

If errors occur, every failed SQL statement is listed so you can review and fix it manually, then run the task again.

[!!] If you are running a version older than 1.2.0, do not run the upgrade without first verifying your database schema matches the expected 1.2.0 structure. Running the wrong upgrade can cause irreversible damage to your database.

## What the Upgrade Does

The upgrade is executed via `modules/gleez/views/install/upgrade.sql`. This SQL script:

- Adds new columns (e.g., soft-delete columns)
- Removes obsolete columns
- Changes column types (e.g., timestamps to `INT UNSIGNED`)
- Converts tables to `utf8mb4` character set
- Optimizes column types (e.g., `BIGINT` to `INT` where appropriate)
- Updates the schema version in the `config` table

Foreign key checks are temporarily disabled during the upgrade. The few foreign keys whose columns must be altered
(charset conversion, `BIGINT` to `INT` optimization) are dropped beforehand and re-created afterwards, because MySQL
rejects such changes even when foreign key checks are off.

## Password Hash Method

Starting with Gleez 1.3.0, the default `hash_method` in `modules/user/config/auth.php` was changed from `sha1` to `sha256`. Because password hashes are one-way, existing passwords hashed with `sha1` cannot be converted to `sha256` without the original plaintext.

If you are upgrading from a pre-1.3.0 installation, you **must** override the default in your application config to keep `sha1`, otherwise all existing users will be locked out:

1. Create or edit `application/config/auth.php`.
2. If the file does not exist yet, copy it from `modules/user/config/auth.php` and change `'hash_method'` to `'sha1'`.
3. If the file already exists, ensure `'hash_method'` is set to `'sha1'`.

This ensures that existing password hashes continue to be verified correctly. New installations of 1.3.0+ will use `sha256` by default.
