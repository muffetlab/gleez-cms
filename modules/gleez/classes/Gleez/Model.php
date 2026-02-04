<?php

/**
 * Extended ORM class.
 *
 * This class extends Kohana ORM implementation to provide additional functionality including jQuery DataTables
 * integration.
 *
 * @author     Gleez Team
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class Gleez_Model extends ORM
{
    /**
     * @var Datatables
     */
    protected $_datatables;

    /**
     * Setter/Getter for jquery DataTables support.
     *
     * @param array|null $columns Columns for setting
     * @return Datatables
     * @throws Kohana_Exception
     */
    public function dataTables(array $columns = NULL): Datatables
    {
        if (!empty($columns)) {
            $this->_datatables = Datatables::factory($this)->columns($columns)->execute();
        }

        return $this->_datatables;
    }
}
