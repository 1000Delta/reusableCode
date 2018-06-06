<?php
/**
 * Database Controller (DBC)
 *
 * 这是一个用来进行连接数据库操作的类，通过封装mysqli对象和PDO对象来通过两种不同的方式操作数据库
 * mysqli 模式下只能操作MySQL数据库
 * PDO模式下可以对多种类型的数据库进行操作
 * 所有代替完整SQL语句的方法参数都按照原SQL语句中的顺序出现
 */

 /**
  * Class DBCResult
  */

namespace MyClass\Database;

class DBCResult
{

    private
        $linkType = null,
        $main = null,
        $error = 0;

    public function __construct($obj) {

        if ($obj instanceof \mysqli_result) $this->linkType = 0;
        elseif ($obj instanceof \PDOStatement) $this->linkType = 1;
        $this->main = $obj;
    }

    public function fetch(int $attr = 11) {

        if ($this->linkType === 0) {

            switch ($attr) {

                case 11:
                    return $this->main->fetch_row();
                case 12:
                    return $this->main->fetch_assoc();
                case 13:
                    return $this->main->fetch_object();
                case 14:
                    return $this->main->fetch_array();
                default:
                    return $this->error = 101;
            }
        } elseif ($this->linkType === 1) {

            switch ($attr) {

                case 11:
                    return $this->main->fetch(\PDO::FETCH_NUM);
                case 12:
                    return $this->main->fetch(\PDO::FETCH_ASSOC);
                case 13:
                    return $this->main->fetch(\PDO::FETCH_OBJ);
                case 14:
                    return $this->main->fetch(\PDO::FETCH_BOTH);
                default:
                    return $this->error = 101;
            }
        } else return 0;
    }

    public function fetchAll(int $attr = 11) {

        if ($this->linkType === 0) {

            switch ($attr) {

                case 11:
                    return $this->main->fetch_all(MYSQLI_NUM);
                case 12:
                    return $this->main->fetch_all(MYSQLI_ASSOC);
                case 14:
                    return $this->main->fetch_all(MYSQLI_BOTH);
                default:
                    return $this->error = 101;
            }
        } elseif ($this->linkType === 1) {

            switch ($attr) {

                case 11:
                    return $this->main->fetchAll(\PDO::FETCH_NUM);
                case 12:
                    return $this->main->fetchAll(\PDO::FETCH_ASSOC);
                case 13:
                    return $this->main->fetchAll(\PDO::FETCH_OBJ);
                case 14:
                    return $this->main->fetchAll(\PDO::FETCH_BOTH);
                default:
                    return $this->error = 101;
            }
        } else return 0;
    }

    public function rowCount() {

        if ($this->linkType === 0) {

            if (isset($this->main->field_count)) {

                return $this->main->field_count;
            } else {

                return 0;
            }
        } elseif ($this->linkType === 1) {

            return $this->main->rowCount();
        } else return 0;
    }

}

 /**
  * Class DBCStatement
  */
class DBCStatement {

    private
        $linkType = null,
        $main,
//        $arg_num,
//        $row = array(),
        $error;

    /**
     * DBCStatement constructor.
     * @param object $obj
     */
    public function __construct($obj) {

        if ($obj instanceof \mysqli_stmt) $this->linkType = 0;
        elseif ($obj instanceof \PDOStatement) $this->linkType = 1;
        $this->main = $obj;
    }

    /**
     * @param $attr
     * @param $var1
     * @param null $_
     */
    public function bindParam($attr, &$var1, &$_ = null) {

        if ($this->linkType === 0) {

            $this->main->bind_param($attr, $var1, $_);
        } elseif ($this->linkType === 1) {

            $this->main->bindParam((int)$attr, $var1);
        }
    }


    /**
     * @todo 实现mysqli的bindValue功能
     */
    public function bindValue() {

        $a = func_get_args();
        $i = func_num_args();

        if ($this->linkType === 0) {

            return;
        } elseif ( $this->linkType === 1) {

            call_user_func_array(array($this->main, 'bindValue'), $a);
        }
    }

    public function execute() {

        if ($this->linkType === 0) {

            $this->main->execute();
        } elseif ($this->linkType === 1) {

            $this->main->execute();
        }

        return 0;
    }

    /**
     * @param $var1
     * @param array ...$_
     * @todo 待实现fetch拉取mysqli结果集后废弃
     */
    public function bindResult(&$var1, &...$_) {

        $a = func_get_args();

        if ($this->linkType === 0) {

            call_user_func_array(array($this->main, 'bindResult'), $a);
        } else return;
    }

    /**
     * @param int $attr
     * @return mixed
     * @todo 实现mysqli->PDO两种方式用相同参数方法拉取数据
     */
    public function fetch(int $attr = 11) {

        if ($this->linkType === 0) {

            return $this->main->fetch();
        } elseif ($this->linkType === 1) {

            switch ($attr) {

                case 11:
                    return $this->main->fetch(\PDO::FETCH_COLUMN);
                case 12:
                    return $this->main->fetch(\PDO::FETCH_ASSOC);
                case 13:
                    return $this->main->fetch(\PDO::FETCH_OBJ);
                case 14:
                    return $this->main->fetch(\PDO::FETCH_BOTH);
                default:
                    return $this->error = 101;
            }
        } else return 0;
    }

    /**
     * @param int $attr
     * @return mixed
     * @todo 实现mysqli_stmt的fetchAll()
     */
    public function fetchAll(int $attr = 11) {

        if ($this->linkType === 0) {

            return -1;
        } elseif ($this->linkType === 1) {

            switch ($attr) {

                case 11:
                    return $this->main->fetchAll(\PDO::FETCH_NUM);
                case 12:
                    return $this->main->fetchAll(\PDO::FETCH_ASSOC);
                case 13:
                    return $this->main->fetchAll(\PDO::FETCH_OBJ);
                case 14:
                    return $this->main->fetchAll(\PDO::FETCH_BOTH);
                default:
                    return $this->error = 101;
            }
        }
    }
}

 /**
  * Class DBC
  * @todo add 存储过程
  * @todo add 事务处理
  */
class DBC {

    private
        $linkType,
        $dbType,
        $host,
        $username,
        $passwd,
        $DBName,
        $link,
        $error = 0;

    const
        LINK_MYSQLI_DBC = 0,
        LINK_PDO_DBC = 1,
        FETCH_NUM_DBC = 11,
        FETCH_ASSOC_DBC = 12,
        FETCH_OBJ_DBC = 13,
        FETCH_ARRAY_DBC = 14, // Both num and associate.
        FETCH_BOTH_DBC = 15,
        CREATE_DB_DBC = 101,
        CREATE_TABLE_DBC = 102,
        ERROR_LINKTYPE_MATCH_DBC = 23301,
        ERROR_FETCH_ATTR_DBC = 23302;

    /**
     * DBC constructor.
     * @param int $linkType
     * @param string $host
     * @param string $username
     * @param string $passwd
     * @param string $DBName
     */
    function __construct(int $linkType = 0, string $host = '127.0.0.1', string $username = 'root', string $passwd = '', string $DBName = '')  {

        if ($linkType === 0)
            $this->linkType = 0;
        elseif ($linkType === 1)
            $this->linkType = 1;
        $this->dbType = 'MySQL';
        $this->host = $host;
        $this->username = $username;
        $this->passwd = $passwd;
        $this->DBName = $DBName;
    }

    public function changeDB(string $DBName) {}

    public function changeUser(string $username, string $password) {}

    /**
     * @param string|null $DBType
     * @return int
     */
    public function connect(string $DBType = 'mysql') {

        $a = func_get_args();
        $i = func_num_args();

        if(method_exists($this, $f = 'connect'.$i))
            call_user_func_array(array($this, $f), $a);

        return 0;
    }

    private function connect0() {

        if($this->linkType === 0)
            $this->link = new \mysqli($this->host, $this->username, $this->passwd, $this->DBName);
        else
            $this->link = new \PDO('mysql:host='.$this->host.';dbname='.$this->DBName, $this->username, $this->passwd);

        return 0;
    }
    
    private function connect1(string $DBType) {
        
        if($this->linkType === 0)
            return $this->error = 23301;
        else
            $this->link = new \PDO($DBType.':host='.$this->host.';dbname='.$this->dbname, $this->username, $this->passwd);

        return 0;
    }

    /**
     * @return int
     */
    public function errorCode() {

        return $this->error;
    }

    /**
     * @param string $sql
     * @param string|NULL $index
     * @return DBCResult
     */
    public function query(string $sql, string $index = '') {

        if ($this->linkType === 0) {

            if ($index === '')
                return new DBCResult($this->link->query($sql));
            return new DBCResult($this->link->query($sql));
        } else {

            if ($index === '')
                return new DBCResult($this->link->query($sql));
            return new DBCResult($this->link->query($sql));
        }
    }


    /**
     * @param string $tableName
     * @param array $columns
     * @param string|NULL $param
     * @param string|NULL $index
     * @return DBCResult
     */
    public function select(string $tableName, array $columns, string $param = '', string $index = '') {

        $column = '';
        foreach ($columns as $i) {

            $column .= ($columns[0] != $i) ? ','.$i : $i;
        }
        if ($param === '') {

            $sql = 'SELECT '.$column.' FROM '.$tableName.';';
        } else {

            $sql = 'SELECT '.$column.' FROM '.$tableName.' '.$param.';';
        }
        return $this->query($sql, $index);
    }

    /**
     * @param string $tableName
     * @param array $values
     * @param string $param
     * @return null
     */
    public function update(string $tableName, array $values, string $param) {

         $column = '';
         $key0 = key($values);
        foreach ($values as $k => $v) {

            $column .= ($values[$key0] === $v) ? $k.'='.$v : ','.$k.'='.$v;
        }
        if ($param === NULL) {

            return NULL;
        } else {

            $sql = 'UPDATE '.$tableName.' SET '.$column.' WHERE '.$param;
            $this->query($sql);
        }
    }

    /**
     * @param string $tableName
     * @param array $values
     */
    public function insert(string $tableName, array $values) {

        $col = '';
        $val = '';
        $key0 = key($values);
        foreach ($values as $k => $v) {

            $col .= ($values[$key0] === $v) ? '('.$k : ','.$k;
            $val .= ($values[$key0] === $v) ? '('.$v : ','.$v;
        }
        $col .= ')';
        $val .= ')';
        $sql = "INSERT INTO $tableName $col VALUES $val;";
        $this->query($sql);
    }

    /**
     * @param string $tableName
     * @param string $param
     * @return int
     */
    public function delete(string $tableName, string $param) {

        if ($param === NULL) {

            return 0;
        } else {

            $sql = "DELETE FROM $tableName WHERE $param";
        }
        $this->query($sql);
    }

    /**
     * @param string $sql
     * @return DBCStatement
     */
    public function prepare(string $sql) {

        return new DBCStatement($this->link->prepare($sql));
    }

    /**
     * @param array $columns
     * @param string $table1
     * @param string $table2
     * @param int $joinType
     * @param string $param
     * @return DBCResult
     */
    public function join(array $columns, string $table1, string $table2, int $joinType, string $param) {

        $column = '';
        foreach ($columns as $col) {

            $column .= ($col === $column[0]) ? $col : ','.$col;
        }
        $sql = 'SELECT '.$column.' FROM '.$table1.' '.$joinType.' JOIN '.$table2.' WHERE '.$param.';';
        return $this->query($sql);
    }

    /**
     * @param string $DBName
     * @return DBCResult
     */
    public function createDB(string $DBName) {

        $sql = 'CREATE DATABASE '.$DBName.';';
        return $this->query($sql);
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @return DBCResult
     */
    public function createTable(string $tableName, array $columns) {

        $sql = 'CREATE TABLE '.$tableName;
        foreach ($columns as $column) {

            $sql .= ($column === $columns[0]) ? '('.$column : ','.$column;
        }
        $sql .= ');';

        return $this->query($sql);
    }
}

define('LINK_MYSQLI_DBC', 0);
define('LINK_PDO_DBC', 1);
define('FETCH_NUM_DBC', 11);
define('FETCH_ASSOC_DBC', 12);
define('FETCH_OBJ_DBC', 13);
define('FETCH_ARRAY_DBC', 14); // Both num and associate.
define('FETCH_BOTH_DBC', 15);
define('CREATE_DB_DBC', 101);
define('CREATE_TABLE_DBC', 102);
define('ERROR_LINKTYPE_MATCH_DBC', 23301);
define('ERROR_FETCH_ATTR_DBC', 23302);