<?php

/**
 * Extended ORM class.
 *
 * This class extends Kohana ORM implementation to provide additional functionality including jQuery DataTables
 * integration.
 *
 * @author     Gleez Team
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
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
     * @var array|null
     */
    protected $_deleted_column = null;

    /**
     * @var Datatables
     */
    protected $_datatables;

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
