<?php

/**
 * Extended ORM class.
 *
 * This class extends Kohana ORM implementation to provide additional functionality including jQuery DataTables
 * integration.
 *
 * @author     Gleez Team
 * @copyright  (c) 2011-2015 Gleez Technologies
 */
class Gleez_Model extends ORM
{
    /**
     * Soft-delete column configuration.
     *
     * When set to an array with 'column' and 'format' keys, calling `delete(true)` will UPDATE the column with a
     * timestamp instead of performing a hard DELETE. Format mirrors `$_updated_column`:
     *   - `true` => Unix timestamp via `time()`
     *   - string => `date($format)` result
     *
     * Soft-deleted rows (`deleted` != 0) are excluded from `find()`, `find_all()`, and `count_all()` unless
     * `with_deleted()` is used.
     *
     * @var array|null
     */
    protected $_deleted_column = null;

    /**
     * When true, soft-deleted rows are included in retrieval queries.
     *
     * @var bool
     */
    protected $withDeleted = false;

    /**
     * @var Datatables
     */
    protected $_datatables;

    /**
     * Include soft-deleted rows in subsequent find/find_all/count_all queries.
     *
     * @param bool $withDeleted Include soft-deleted rows
     * @return $this
     */
    public function withDeleted(bool $withDeleted = true): self
    {
        $this->withDeleted = $withDeleted;

        return $this;
    }

    /**
     * Apply soft-delete exclusion when `$_deleted_column` is configured.
     *
     * Active records store `0` in the deleted column; soft delete writes a timestamp.
     *
     * @return $this
     */
    protected function excludeDeleted(): self
    {
        if (is_array($this->_deleted_column) && !$this->withDeleted) {
            $column = $this->_object_name . '.' . $this->_deleted_column['column'];
            $this->where($column, '=', 0);
        }

        return $this;
    }

    /**
     * Finds and loads a single database row into the object.
     *
     * Soft-deleted rows are excluded when soft delete is configured.
     *
     * @return Database_Result|ORM
     * @throws Kohana_Exception
     */
    public function find()
    {
        $this->excludeDeleted();

        return parent::find();
    }

    /**
     * Finds multiple database rows and returns an iterator of the rows found.
     *
     * Soft-deleted rows are excluded when soft delete is configured.
     *
     * @return Database_Result|Database_Result_Cached|Kohana_ORM|object
     * @throws Kohana_Exception
     */
    public function find_all()
    {
        $this->excludeDeleted();

        return parent::find_all();
    }

    /**
     * Count the number of records in the table.
     *
     * Soft-deleted rows are excluded when soft delete is configured.
     *
     * @return int
     * @throws Kohana_Exception
     */
    public function count_all(): int
    {
        $this->excludeDeleted();

        return parent::count_all();
    }

    /**
     * Clears query builder state and soft-delete include flag when resetting.
     *
     * @param bool $next Reset immediately and clear for next query
     * @return Kohana_ORM
     */
    public function reset(bool $next = true): Kohana_ORM
    {
        if ($next && $this->_db_reset) {
            $this->withDeleted = false;
        }

        return parent::reset($next);
    }

    /**
     * Delete a single record, optionally performing a soft delete.
     *
     * When `$soft` is `true` and `$_deleted_column` is configured, the record is marked as deleted via an UPDATE
     * instead of being removed from the database.
     *
     * @param bool $soft Perform soft delete when possible
     * @return Kohana_ORM
     * @throws Kohana_Exception
     */
    public function delete(bool $soft = false): Kohana_ORM
    {
        if (is_array($this->_deleted_column) && $soft) {
            if (!$this->_loaded) {
                throw new Kohana_Exception('Cannot delete :model model because it is not loaded.', [
                    ':model' => $this->_object_name
                ]);
            }

            $column = $this->_deleted_column['column'];
            $format = $this->_deleted_column['format'];
            $value = $format === true ? time() : date($format);

            $this->_object[$column] = $value;

            DB::update($this->_table_name)
                ->value($column, $value)
                ->where($this->_primary_key, '=', $this->pk())
                ->execute($this->_db);

            return $this;
        }

        return parent::delete();
    }

    /**
     * Setter/Getter for jquery DataTables support.
     *
     * @param array|null $columns Columns for setting
     * @return Datatables
     * @throws Kohana_Exception
     */
    public function dataTables(array $columns = null): Datatables
    {
        if (!empty($columns)) {
            $this->_datatables = Datatables::factory($this)->columns($columns)->execute();
        }

        return $this->_datatables;
    }
}
