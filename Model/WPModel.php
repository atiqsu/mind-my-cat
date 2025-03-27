<?php

namespace Mindmycat\Model;

abstract class WPModel
{

    protected string $primaryKey = 'id';

	protected string $table;

    protected $connection;

    protected static string $wpPluginPrefix = 'bpc_'; 

	protected int $limit = 0;
    protected int $offset = 0;

    protected $columnList = ['*'];
    protected $whereList = [];
	protected $orderList = [];

	public $lastQuery = '';


    public function __construct() {

        $this->connection = self::getConnection();
    }

    public static function getConnection() {

        global $wpdb;

        return $wpdb;
    }

	public static function all() {

        return (new static())->get();
    }

    protected function getTableName() {

		$tbl = $this->table ?? $this->makeNameFromCls(static::class);

        return $this->connection->prefix . static::$wpPluginPrefix . $tbl;
    }
    
	protected function getPrimaryKey() {

		return $this->primaryKey;
	}

    protected function find($id, array $columns = []) {
        
        $qry = $this->addColumns($columns)->where($this->primaryKey, intval($id))->toSelectSql();

		return $this->getRow($qry);
    }

	public function first() {

		$qry = $this->limit(1)->toSelectSql();

		return $this->getRow($qry);
    }

	protected function create(array $attributes): int {

		$qry = $this->toInsertSql($attributes);

		$row = $this->connection->query($qry);

		if ($this->connection->last_error) {

            throw new \Exception($this->connection->last_error);
        }

		$this->lastQuery = $qry;

		return $this->connection->insert_id;
    }

    protected function delete($id) {

        return $this->where($this->primaryKey, $id)->del();   
    }

	public function del() {

		if ( empty( $this->whereList )) {

            throw new \Exception('Deleting with empty where condition is prohibited, please use empty query to truncate the table');
		}

		$qry = $this->toDeleteSql();

		$row = $this->connection->query($qry);

		if ($this->connection->last_error) {

            throw new \Exception($this->connection->last_error);
        }

		$this->lastQuery = $qry;

		return $row;
	}

	protected function update(array $attributes, array $whr = []) {

		if (!empty($whr)) {

			foreach($whr as $fld => $val) {

				$this->where($fld, $val);
			}
		}


		$qry = $this->toUpdateSql($attributes);

		$row = $this->connection->query($qry);

		if ($this->connection->last_error) {

            throw new \Exception($this->connection->last_error);
        }

		$this->lastQuery = $qry;

		return $row; 
    }

    public function select($col, $as = null) {

		$col = $this->getColumnWrapped($col);
		$col = $as ? $col. ' AS ' . $as : $col;

		if($this->columnList[0] == '*') {

			$this->columnList[0] = $col;

		} else {

			$this->columnList[] = $col;
		}

		return $this;
	}


    public function get(array $columns = []) {

        if (! empty( $columns )) {

            foreach( $columns as $col) {
                $this->select($col);
            }
        }

        #query writing done....
        $qry = $this->toSelectSql();

        return $this->connection->get_results($qry);
	}

    protected function getRow($qry) {

		$row = $this->connection->get_row($qry);

		if ($this->connection->last_error) {

            throw new \Exception($this->connection->last_error);
        }

		$this->lastQuery = $qry;

		return $row;
	}


    public function pluckBy($val_field, $key_fld = null): array {

        $key = $key_fld ?? $this->primaryKey;

        $this->select($key)->select($val_field);
        
        
        $data = $this->get();

        $ret = [];

        foreach ($data as $datum) {
            $ret[$datum->{$key}] = $datum->{$val_field};
        }

        return $ret;
    }


    private function toSelectSql() {

        //$fields = empty($this->columnList) ? '*' : implode(', ', $this->columnList);
		$qry = 'SELECT ' . $this->getColumns() . ' FROM ' . $this->getTableName();

		$qry = $this->compileWhere($qry);

		#ordering ...

		if(!empty($this->orderList)) {

			$comma = ' ORDER BY ';

			foreach($this->orderList as $order) {

				$qry .= $comma . '' . $order['fld'] . ' ' . $order['ord'];

				$comma = ', ';
			}
		}

		if ($this->limit !== 0) {

			$qry .= ' LIMIT ' . $this->limit;

			if($this->offset !== 0) {
				
				$qry .= ' OFFSET ' . $this->offset;
			}
		}


		#query writing done....
		$qry .= ';';

		#logging here............
		$this->lastQuery = $qry;

		return $qry;
	}

    private function toDeleteSql() {

		$qry = 'DELETE FROM ' . $this->getTableName();

		$qry = $this->compileWhere($qry);

		return $this->finishQry($qry);
    }

	private function toInsertSql(array $valArr) {

		$sep = '';
        $cols = '';
		$val = '';

		foreach($valArr as $col => $value) {

			$cols .= $sep . $this->getColumnWrapped($col);

			$val .= $sep . $this->getValueWrapped($value);

			$sep = ', ';
		}

		return 'INSERT INTO ' . $this->getTableName() . ' (' . $cols . ') VALUES (' . $val . ');';
    }

	private function toUpdateSql(array $valArr) {

		$sep = '';
        $part = '';

		foreach($valArr as $col => $value) {

			$part .= $sep . $this->getColumnWrapped($col) . ' = ' . $this->getValueWrapped($value);

			$sep = ', ';
		}

		$qry = 'UPDATE ' . $this->getTableName() . '  SET ' . $part ;

		$qry = $this->compileWhere($qry);

		return $this->finishQry($qry);
    }

	private function finishQry($qry) {
        
		return $qry . ';';
	}

    private function getColumns() {

		if(empty($this->columnList) || $this->columnList[0] == '*') {

			return '*';
		}

		$str = '';
		$comma = '';

		foreach($this->columnList as $col) {

			$str .= $comma . $this->getColumnClean($col) ;
		}


		return $str;
	}

    private function compileWhere(string $qry): string {


		if(!empty($this->whereList)) {

			$part = '';

			$this->whereList[0]['lo'] = ' WHERE';

			foreach($this->whereList as $where) {

				if (is_null($where['cond'])) {

					$part .= $where['lo'] . ' ' . $this->getColumnWrapped($where['fld']) . ' IS NULL ';

					continue;
				}

				$part .= $where['lo'] . ' ' . $this->getColumnWrapped($where['fld']) . ' ' . $where['op'] . ' ' . $this->getConditionWrapped($where['cond']);
			}

			$qry .= $part;
		}

        return $qry;
    }

    public function where($fld, $condition, $op = '=', $lo = 'and') {

		$wh['fld']  = $fld;
		$wh['cond'] = $condition;
		$wh['op']   = empty($op) ? '=' : str_replace("'", '', $op);
		$wh['lo']   = $lo === 'or' ? 'OR' : 'AND';  # lo - logic operator?!

		$this->whereList[] = $wh;

		return $this;
	}

    public function orderBy($fld, $ord = 'ASC') {

        $fld = str_replace("'", '', $fld);
        $ord = ($ord === 'DESC' || $ord === 'desc') ? 'DESC' : 'ASC';

		$this->orderList[] = ['fld' => $fld, 'ord' => $ord];

		return $this;
	}

	public function latest($fld = 'id') {

		return $this->orderBy($fld, 'DESC');
	}

	public function oldest($fld = 'id') {

		return $this->orderBy($fld, 'ASC');
	}

	public function limit(int $limit, int $offset = 0): static {
        
		$this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function offset(int $offset): static {
        
		$this->offset = $offset;

        return $this;
    }

	private function makeNameFromCls($cls) {

        $name = basename(str_replace('\\', '/', $cls));

        return strtolower($name);
    }

    private function addColumns(array $cols) {

		if (!empty($cols)) {

			foreach($cols as $col) {

				$this->select($col);
			}
		}

		return $this;
	}

    private function getColumnWrapped($col) {

        return '`' . str_replace('`', '', $col) . '`';
    }
    
    private function getColumnClean($col) {

		return str_replace('`', '', $col);
	}

    private function getConditionWrapped($str) : mixed {

        return is_numeric($str) ? $str . ' ' : "'". str_replace("'", '', $str) . "'";
    }

	private function getValueWrapped($str) : mixed {

        return is_numeric($str) ? $str : "'". str_replace("'", '&apos;', $str) . "'";
    }

    public static function __callStatic($method, $parameters) {

        return (new static)->$method(...$parameters);
    }
}

