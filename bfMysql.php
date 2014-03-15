<?php #coding: utf-8
/**
 * @file bfMysql.php
 */

class bfMysql extends bfApi
{
    protected $conId = null;
    protected $host;
    protected $user;
    protected $password;
    protected $database;
    protected $inTransaction;

    #################################################################
    ## __construct()

    # boolean
    public function __construct($host=null, $user=null, $password=null, $database=null) {
        $this->host = ($host !== null) ? bf::trim($host) : SQL_HOST;
        $this->user = ($user !== null) ? bf::trim($user) : SQL_USER;
        $this->password = ($password !== null) ? bf::trim($password) : SQL_PASSWORD;
        $this->database = ($database !== null) ? bf::trim($database) : SQL_DATABASE;
        $this->inTransaction = false;

        if ($this->connect() !== true)
        {
            //TODO: log error
            return false;
        }

        return true;
    }

    #################################################################
    ## connect()

    # boolean
    private function connect() {
        if ($this->conId !== null)
            return false;

        if (!($this->conId = mysql_connect($this->host, $this->user, $this->password)))
            return false;

        if (!mysql_select_db($this->database, $this->conId))
            return false;

        return true;
    }

    #################################################################
    ## disconnect()

    # null
    private function disconnect() {
        mysql_close($this->conId);
    }

    #################################################################
    ## setDatabase()

    # boolean
    public function setDatabase($database) {
        if ($database === null || bf::is_empty($database) === true)
            return false;

        if (!mysql_select_db($this->database, $this->conId))
            return false;

        return true;
    }

    #################################################################
    ## runQuery()

    # boolean
    public function runQuery(&$records, $query, $keyCol=null, $valueCol=null) {
        $records = null;
        if (is_resource($result = $this->_query($query)) && mysql_num_rows($result) > 0)
        {
            if ($keyCol === null)
            {
                if ($valueCol === null)
                {
                    while($record = mysql_fetch_assoc($result))
                        $records[] = $record;
                }
                elseif (!is_string($valueCol))
                    return false;
                else
                {
                    while($record = mysql_fetch_assoc($result))
                    {
                        if (!array_key_exists($valueCol, $record))
                            return false;

                        $records[] = $record[$valueCol];
                    }
                }
            }
            elseif (!is_string($keyCol))
                return false;
            else
            {
                if ($valueCol === null)
                {
                    while ($record = mysql_fetch_assoc($result))
                    {
                        if (!array_key_exists($keyCol, $record))
                            return false;

                        $records[$record[$keyCol]] = $record;
                    }
                }
                elseif (!is_string($valueCol))
                    return false;
                else
                {
                    while ($record = mysql_fetch_assoc($result))
                    {
                        if (!array_key_exists($keyCol, $record) || !array_key_exists($valueCol, $record))
                            return false;

                        $records[$record[$keyCol]] = $record[$valueCol];
                    }
                }
            }
        }
        return true;
    }

    #################################################################
    ## runSingleQuery()

    # boolean
    public function runSingleQuery(&$result, $query) {
        $res = $this->_query($query);

        if (is_resource($res))
        {
            $row = mysql_fetch_row($res);
            $result = $row[0];
            return true;
        }
        return false;
    }

    #################################################################
    ## runInsertQuery()

    # boolean
    public function runInsertQuery($table, $values, $ignore=false) {
        if (strlen(($set = $this->buildSet($values))) == 0)
            return false;

        return $this->_query('INSERT '.($ignore ? 'IGNORE ' : '').'INTO '.$this->backQuote($table).' '.$set);
    }

    #################################################################
    ## runReplaceQuery()

    # boolean
    public function runReplaceQuery($table, $values) {
        if (strlen(($set = $this->buildSet($values))) == 0)
            return false;

        return $this->_query('REPLACE INTO '.$this->backQuote($table).$set);
    }

    #################################################################
    ## runUpdateQuery()

    # boolean
    public function runUpdateQuery($table, $where, $values) {
        if (!is_string($where))
            return false;

        if (strlen(($set = $this->buildSet($values))) == 0)
            return false;

        return $this->_query('UPDATE FROM '.$this->backQuote($table).$set.(strlen($where) ? ' WHERE'.$where : ''));
    }

    #################################################################
    ## runDeleteQuery()

    # boolean
    public function runDeleteQuery($table, $where) {
        if (!is_string($where))
            return false;

        return $this->query('DELETE FROM '.$this->backQuote($table).(strlen($where) ? ' WHERE '.$where : ''));
    }

    #################################################################
    ## truncate()

    # boolean
    public function truncate($table) {
        return $this->_query('TRUNCATE TABLE '.$this->backQuote($table));
    }

    #################################################################
    ## escape()

    # string
    public function escape($string) {
        return mysql_real_escape_string($string, $this->conId);
    }

    #################################################################
    ## quote()

    # string
    public function quote($string) {
        return '\''.$this->escape($string).'\'';
    }

    #################################################################
    ## backQuote()

    # string
    public function backQuote($string) {
        return '`'.$string.'`';
    }

    #################################################################
    ## insertId()

    # int
    public function insertId() {
        return mysql_insert_id();
    }

    #################################################################
    ## buildSet()

    # string
    private function buildSet($data) {
        $set = '';
        if (is_array($data) && sizeof($data) > 0)
        {
            @reset($data);
            while (list($col, $value) = @each($data))
            {
                if (is_scalar($value))
                    $set.= (strlen($set) == 0 ? 'SET ' : ', ').'`'.$col.'`='.$this->quote($value);
                elseif (is_null($value))
                    $set.= (strlen($set) == 0 ? 'SET ' : ', ').'`'.$col.'`=NULL';
                elseif (is_object($value) && $value instanceof bfMysqlExpr)
                    $set.= (strlen($set) == 0 ? 'SET ' : ', ').'`'.$col.'`='.$value->get();
                else
                {
                    // TODO: Log error
                    continue;
                }
            }
        }
        return $set;
    }

    #################################################################
    ## _query()

    # resource
    # false
    private function _query($query) {
        if ($query === null || bf::is_empty($query))
            return false;
		
        return mysql_query($query, $this->conId);
    }

    #################################################################
    ## startTransaction()

    # boolean
    public function startTransaction() {
        if (!$this->inTransaction() && $this->_query('START TRANSACTION'))
        {
            $this->inTransaction = true;
            return true;
        }
        return false;
    }

    #################################################################
    # rollback()

    # boolean
    public function rollback() {
        $this->inTransaction = false;
        return $this->_query('ROLLBACK');
    }

    #################################################################
    ## commit()

    # boolean
    public function commit() {
        $this->inTransaction = false;
        return $this->_query('COMMIT');
    }

    #################################################################
    ## inTransaction()

    # boolean
    public function inTransaction() {
        return $this->inTransaction;
    }
}
?>
