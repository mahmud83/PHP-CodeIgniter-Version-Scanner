<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package     CodeIgniter
 * @author      Rick Ellis
 * @copyright   Copyright (c) 2006, pMachine, Inc.
 * @license     http://www.codeignitor.com/user_guide/license.html 
 * @link        http://www.codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * oci8 Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package     CodeIgniter
 * @subpackage  Drivers
 * @category    Database
 * @author      Rick Ellis
 * @link        http://www.codeigniter.com/user_guide/libraries/database/
 */

/**
 * oci8 Database Adapter Class
 *
 * This is a modification of the DB_driver class to 
 * permit access to oracle databases
 *
 * NOTE: this uses the PHP 4 oci methods
 *
 * @author      Kelly McArdle
 *
 */

class CI_DB_oci8 extends CI_DB {

    // need to track statement id and cursor id
    var $stmt_id;
    var $curs_id;

    // if we use a limit, we will add a field that will
    // throw off num_fields later
    var $limit_used;

    /**
     * Non-persistent database connection
     *
     * @access  private called by the base class
     * @return  resource
     */
    function db_connect()
    {
        return ocilogon($this->username, $this->password, $this->hostname);
    }

    // --------------------------------------------------------------------

    /**
     * Persistent database connection
     *
     * @access  private called by the base class
     * @return  resource
     */
    function db_pconnect()
    {
        return ociplogon($this->username, $this->password, $this->hostname);
    }

    // --------------------------------------------------------------------

    /**
     * Select the database
     *
     * @access  private called by the base class
     * @return  resource
     */
    function db_select()
    {
        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Execute the query
     *
     * @access  private called by the base class
     * @param   string  an SQL query
     * @return  resource
     */
    function execute($sql)
    {
        // oracle must parse the query before it
        // is run, all of the actions with
        // the query are based off the statement id
        // returned by ociparse        
        $this->_set_stmt_id($sql);
        ocisetprefetch($this->stmt_id, 1000);
        return @ociexecute($this->stmt_id);
    }
    
    /**
     * Generate a statement ID
     *
     * @access  private
     * @param   string  an SQL query
     * @return  none
     */
    function _set_stmt_id($sql)
    {
        if ( ! is_resource($this->stmt_id))
        {
			$this->stmt_id = ociparse($this->conn_id, $this->_prep_query($sql));
        }
    }

    // --------------------------------------------------------------------

    /**
     * Prep the query
     *
     * If needed, each database adapter can prep the query string
     *
     * @access  private called by execute()
     * @param   string  an SQL query
     * @return  string
     */
    function _prep_query($sql)
    {
        return $sql;
    }

    // --------------------------------------------------------------------

    /**
     * getCursor.  Returns a cursor from the datbase
     *
     * @access  public
     * @return  cursor id
     */
    function get_cursor()
    {
		return $this->curs_id = ocinewcursor($this->conn_id);
    }

    // --------------------------------------------------------------------

    /**
     * Stored Procedure.  Executes a stored procedure
     *
     * @access  public
     * @param   package     package stored procedure is in
     * @param   procedure   stored procedure to execute
     * @param   params      array of parameters
     * @return  array
     *
     * params array keys
     *
     * KEY      OPTIONAL    NOTES
     * name     no          the name of the parameter should be in :<param_name> format
     * value    no          the value of the parameter.  If this is an OUT or IN OUT parameter,
     *                      this should be a reference to a variable
     * type     yes         the type of the parameter
     * length   yes         the max size of the parameter
     */
    function stored_procedure($package, $procedure, $params)
    {
        if ($package == '' OR $procedure == '' OR ! is_array($params))
        {
            if ($this->db_debug)
            {
                log_message('error', 'Invalid query: '.$package.'.'.$procedure);
                return $this->display_error('db_invalid_query');
            }
            return FALSE;
        }
		
		// build the query string
		$sql = "begin $package.$procedure(";

		$have_cursor = FALSE;
		foreach($params as $param)
		{
			$sql .= $param['name'] . ",";
			
			if (array_key_exists('type', $param) && ($param['type'] == OCI_B_CURSOR))
			{
				$have_cursor = TRUE;
			}
		}
		$sql = trim($sql, ",") . "); end;";
				
		$this->stmt_id = FALSE;
		$this->_set_stmt_id($sql);
		$this->_bind_params($params); 
		$this->query($sql, FALSE, $have_cursor);
	}
	
    // --------------------------------------------------------------------

    /**
     * Bind parameters
     *
     * @access  private
     * @return  none
     */
	function _bind_params($params)
	{
		if ( ! is_array($params) OR ! is_resource($this->stmt_id))
		{
			return;
		}
		
        foreach ($params as $param)
        {
 			foreach (array('name', 'value', 'type', 'length') as $val)
        	{
        		if ( ! isset($param[$val]))
        		{
        			$param[$val] = '';
        		}
        	}
 
			ocibindbyname($this->stmt_id, $param['name'], $param['value'], $param['length'], $param['type']);
        }
	}

    // --------------------------------------------------------------------

    /**
     * Escape String
     *
     * @access  public
     * @param   string
     * @return  string
     */
    function escape_str($str)
    {
        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * Close DB Connection
     *
     * @access  public
     * @param   resource
     * @return  void
     */
    function destroy($conn_id)
    {
        ocilogoff($conn_id);
    }

    // --------------------------------------------------------------------

    /**
     * Affected Rows
     *
     * @access  public
     * @return  integer
     */
    function affected_rows()
    {
        return @ocirowcount($this->stmt_id);
    }

    // --------------------------------------------------------------------

    /**
     * Insert ID
     *
     * @access  public
     * @return  integer
     */
    function insert_id()
    {
        // not supported in oracle
        return 0;
    }

    // --------------------------------------------------------------------

    /**
     * "Count All" query
     *
     * Generates a platform-specific query string that counts all records in
     * the specified database
     *
     * @access  public
     * @param   string
     * @return  string
     */
    function count_all($table = '')
    {
        if ($table == '')
            return '0';

        $query = $this->query("SELECT COUNT(1) AS numrows FROM ".$table);

        if ($query == FALSE)
            {
            return 0;
            }

        $row = $query->row();
        return $row->NUMROWS;
    }

    // --------------------------------------------------------------------

    /**
     * The error message string
     *
     * @access  public
     * @return  string
     */
    function error_message()
    {
        $error = ocierror($this->conn_id);
        return $error['message'];
    }

    // --------------------------------------------------------------------

    /**
     * The error message number
     *
     * @access  public
     * @return  integer
     */
    function error_number()
    {
        $error = ocierror($this->conn_id);
        return $error['code'];
    }

    // --------------------------------------------------------------------

    /**
     * Escape Table Name
     *
     * This function adds backticks if the table name has a period
     * in it. Some DBs will get cranky unless periods are escaped
     *
     * @access  public
     * @param   string  the table name
     * @return  string
     */
    function escape_table($table)
    {
        if (stristr($table, '.'))
        {
            $table = preg_replace("/\./", "`.`", $table);
        }

        return $table;
    }

    // --------------------------------------------------------------------

    /**
     * Field data query
     *
     * Generates a platform-specific query so that the column data can be retrieved
     *
     * @access  public
     * @param   string  the table name
     * @return  object
     */
    function _field_data($table)
    {
        $sql = "SELECT * FROM ".$this->escape_table($table)." where rownum = 1";
        $query = $this->query($sql);
        return $query->field_data();
    }

    // --------------------------------------------------------------------

    /**
     * Insert statement
     *
     * Generates a platform-specific insert string from the supplied data
     *
     * @access  public
     * @param   string  the table name
     * @param   array   the insert keys
     * @param   array   the insert values
     * @return  string
     */
    function _insert($table, $keys, $values)
    {
    return "INSERT INTO ".$this->escape_table($table)." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
    }

    // --------------------------------------------------------------------

    /**
     * Update statement
     *
     * Generates a platform-specific update string from the supplied data
     *
     * @access  public
     * @param   string  the table name
     * @param   array   the update data
     * @param   array   the where clause
     * @return  string
     */
    function _update($table, $values, $where)
    {
        foreach($values as $key => $val)
        {
            $valstr[] = $key." = ".$val;
        }

        return "UPDATE ".$this->escape_table($table)." SET ".implode(', ', $valstr)." WHERE ".implode(" ", $where);
    }

    // --------------------------------------------------------------------

    /**
     * Delete statement
     *
     * Generates a platform-specific delete string from the supplied data
     *
     * @access  public
     * @param   string  the table name
     * @param   array   the where clause
     * @return  string
     */
    function _delete($table, $where)
    {
        return "DELETE FROM ".$this->escape_table($table)." WHERE ".implode(" ", $where);
    }

    // --------------------------------------------------------------------

    /**
     * Version number query string
     *
     * @access  public
     * @return  string
     */
    function _version()
    {
        $ver = ociserverversion($this->conn_id);
        return $ver;
    }

    // --------------------------------------------------------------------

    /**
     * Show table query
     *
     * Generates a platform-specific query string so that the table names can be fetched
     *
     * @access  public
     * @return  string
     */
    function _show_tables()
    {
        return "select TABLE_NAME FROM ALL_TABLES";
    }

    // --------------------------------------------------------------------

    /**
     * Show columnn query
     *
     * Generates a platform-specific query string so that the column names can be fetched
     *
     * @access  public
     * @param   string  the table name
     * @return  string
     */
    function _show_columns($table = '')
    {
        return "SELECT COLUMN_NAME FROM all_tab_columns WHERE table_name = '$table'";
    }

    // --------------------------------------------------------------------

    /**
     * Limit string
     *
     * Generates a platform-specific LIMIT clause
     *
     * @access  public
     * @param   string  the sql query string
     * @param   integer the number of rows to limit the query to
     * @param   integer the offset value
     * @return  string
     */
    function _limit($sql, $limit, $offset)
    {
        $limit = $offset + $limit;
        $newsql = "SELECT * FROM (select inner_query.*, rownum rnum FROM ($sql) inner_query WHERE rownum < $limit)";

        if ($offset != 0)
        {
            $newsql .= " WHERE rnum >= $offset";
        }

        // remember that we used limits
        $this->limit_used = TRUE;

        return $newsql;
    }	

}


/**
 * oci8 Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category    Database
 * @author      Rick Ellis
 * @link        http://www.codeigniter.com/user_guide/libraries/database/
 */
class CI_DB_oci8_result extends CI_DB_result {

    var $stmt_id;
    var $curs_id;
    var $limit_used;

    /**
     * Number of rows in the result set
     *
     * @access  public
     * @return  integer
     */
    function num_rows()
    {
        // get the results, count them,
        // rerun query - otherwise we
        // won't have data after calling 
        // num_rows()
        $this->result_array();
        $rowcount = count($this->result_array);
        @ociexecute($this->stmt_id);
        if ($this->curs_id)
            {
            @ociexecute($this->curs_id);
            }
        return $rowcount;
    }

    // --------------------------------------------------------------------

    /**
     * Number of fields in the result set
     *
     * @access  public
     * @return  integer
     */
    function num_fields()
    {
        $count = @ocinumcols($this->stmt_id);

        // if we used a limit, we added a field,
        // subtract it out
        if ($this->limit_used)
        {
            $count = $count - 1;
        }

        return $count;
    }

    // --------------------------------------------------------------------

    /**
     * Field data
     *
     * Generates an array of objects containing field meta-data
     *
     * @access  public
     * @return  array
     */
    function field_data()
    {
        $retval = array();
        $fieldCount = $this->num_fields();
        for ($c = 1; $c <= $fieldCount; $c++)
        {
            $F              = new stdClass();
            $F->name        = ocicolumnname($this->stmt_id, $c);
            $F->type        = ocicolumntype($this->stmt_id, $c);
            $F->max_length  = ocicolumnsize($this->stmt_id, $c);

            $retval[] = $F;
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * Result - associative array
     *
     * Returns the result set as an array
     *
     * @access  private
     * @return  array
     */
    function _fetch_assoc(&$row)
    {
        // if pulling from a cursor, use curs_id
        if ($this->curs_id)
		{
			return ocifetchinto($this->curs_id, $row, OCI_ASSOC + OCI_RETURN_NULLS);
		}
		else
		{
			return ocifetchinto($this->stmt_id, $row, OCI_ASSOC + OCI_RETURN_NULLS);
		}
    }

    // --------------------------------------------------------------------

    /**
     * Result - object
     *
     * Returns the result set as an object
     *
     * @access  private
     * @return  object
     */
    function _fetch_object()
    {
        // the PHP 4 version of the oracle functions do not
        // have a fetch method so we call the array version
        // and build an object from that

        $row = array();
        $res = $this->_fetch_assoc($row);
        if ($res != FALSE)
		{
			$obj = new stdClass();
			foreach ($row as $key => $value)
			{
				$obj->{$key} = $value;
			}
			
			$res = $obj;
		}
        return $res;
    }

    /**
     * Query result.  "array" version.
     *
     * @access  public
     * @return  array
     */
    function result_array()
    {
        if (count($this->result_array) > 0)
        {
            return $this->result_array;
        }

        // oracle's fetch functions do not
        // return arrays, the information
        // is returned in reference parameters
        //
        $row = NULL;
        while ($this->_fetch_assoc($row))
        {
            $this->result_array[] = $row;
        }

        if (count($this->result_array) == 0)
        {
            return FALSE;
        }

        return $this->result_array;
    }

}

?>