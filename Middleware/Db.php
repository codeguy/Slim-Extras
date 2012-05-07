<?
/**
 * Simple sql database helper class	
 * @author Jorge Garrido Oval (aka: FireZenk) <firezenk@gmail.com>
 * @version 1.0
 * @copyright 2012 Jorge Garrido Oval
 *
 * USAGE
 *
 * Require the Database class:
 *  	require_once 'Db.php';
 *
 * Create database object:
 * 		$db = new Db();
 * or:
 *		$db = new Db('theHost','theUser','thePassword');
 * or:
 *		$db = new Db('theHost','theUser','thePassword','theDatabase');
 *
 * Workflow:
 * 		$db -> connect();
 *		$valid  = $db -> database(); or $db -> database('theDatabase');
 *		$db -> query('theQuery');
 *		$result = $db -> execute();
 *		$state  = $db -> disconnect();
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class Db {

	/**
	 *	Data base user data
	 */
	private $sql_host = "db_host:port";
	private $sql_user = "db_user";
	private $sql_pass = "db_password";
	private $sql_data = "db_database";

	/**
	 *	@var resource
	 */
	private $connection;

	/**
	 *	@var string
	 */
	private $query;

	/**
	 * This method emulates the behavior of the overloaded methods in Java
	 * @param string method name
	 * @param string method arguments
	 * @return void
	 */
	function __call($method_name, $arguments) {
    	$accepted_methods = array("Db", "database");

	    if(!in_array($method_name, $accepted_methods)) {
	      trigger_error("Method <strong>$method_name</strong> not exists", E_USER_ERROR);
	    }

	    if($method_name == "Db") {
	    	switch ( count($arguments) ) {
	    	case 3:
				$this-> Db1($arguments[0], $arguments[1], $arguments[2]);
	    		break;
	    	case 4:
	    		$this-> Db2($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
	    		break;
	    	default:
	    		$this-> Db0();
	    		break;
	    	}
	    } else {
	    	switch ( count($arguments) ) {
	    	case 1:
				$this-> database1($arguments[0]);
	    		break;
	    	default:
	    		$this-> database0();
	    		break;
	    	}
	    } 
      
	    
    }

	/**
	 *	Constructor
	 *	@return void
	 */
	public function Db0() {}

	/**
	 *	Constructor
	 *	@param string $sql_host The database host url w/o server port
	 *	@param string $sql_user The database user name
	 *	@param string $sql_pass The database user password
	 *	@return void
	 */
	public function Db1($sql_host, $sql_user, $sql_pass) {
		$this->sql_host = $sql_host;
		$this->sql_user = $sql_user;
		$this->sql_pass = $sql_pass;
	}

	/**
	 *	Constructor
	 *	@param string $sql_host The database host url w/o server port
	 *	@param string $sql_user The database user name
	 *	@param string $sql_pass The database user password
	 *	@param string $sql_data The selected database from host
	 *	@return void
	 */
	public function Db2($sql_host, $sql_user, $sql_pass, $sql_data) {
		$this->sql_host = $sql_host;
		$this->sql_user = $sql_user;
		$this->sql_pass = $sql_pass;
		$this->sql_data = $sql_data;
	}

	/** 
	 *	Open server connection
	 *	@return void
	 */
	public function connect() {
		$this->connection = mysql_connect($this->sql_host, $this->sql_user, $this->sql_pass) or die('Could not connect to the server!');
	}

	/** 
	 *	Select a database
	 *	@return bool valid database
	 */
	public function database0() {
		return mysql_select_db($this->sql_data) or die('Could not select a database.');
	}

	/** 
	 *	Select a database
	 *	@param string $database The selected database from host
	 *	@return bool valid database
	 */
	public function database1($database) {
		return mysql_select_db($database) or die('Could not select a database.');
	}

	/** 
	 *	Fill query
	 *	@param string $query The query to execute
	 *	@return void
	 */
	public function query($query) {
		$this->query = $query;
	}

	/**
	 *	Execute query
	 *	@return resource data result
	 */
	public function execute() {
		$resource = mysql_query($this->query) or die('A error occured: ' . mysql_error());
		return $resource;
	}

	/**
	 *	Close server connection
	 *	@return bool discconect result
	 */
	public function disconnect() {
		return mysql_close($this->connection);
	}

}
?>