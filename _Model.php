<?php

//$config = include 'config.php';
//$m = new Model($config);
//$m->limit('0,5')->table('user')->field('age,name')->order('money desc')->where('id>1')->select();
//var_dump($m->sql);
//var_dump($m->field('name,id')->table('user')->where('id>0')->order('age desc')->select());
//var_dump($m->sql);

//$data=['age'=>30, 'name'=>'成龙', 'money'=>2000];
//$i=$m->table('user')->insert($data);
//var_dump($i);

//var_dump($m->table('user')->where('id=3')->delete());

//$data = ['name'=>'非法', 'money'=>3000];
//var_dump($m->table('user')->where('id=2')->update($data));

//var_dump($m->table('user')->avg('money'));

class Model
{
	//主机名
	protected $host;
	//用户名
	protected $user;
	//密码
	protected $pwd;
	//数据库名
	protected $dbname;
	//字符集  utf8
	protected $charset;
	//数据库前缀
	protected $prefix;

	//数据库连接资源
	protected $link;
	//数据表名  可以自己指定，也可以通过类名获取
	protected $tableName;
	//sql语句
	protected $sql;

	//操作数组  存放的是所有的查询条件
	protected $options;

	//构造方法
	function __construct($config){
		$this->host = $config['DB_HOST'];
		$this->user = $config['DB_USER'];
		$this->pwd = $config['DB_PWD'];
		$this->dbname = $config['DB_NAME'];
		$this->charset = $config['DB_CHARSET'];
		$this->prefix = $config['DB_PREFIX'];

		//连接数据库
		$this->link = $this->connect();

		//得到表名
		$this->tableName = $this->getTableName();

		//初始化options数组
		$this->initOptions();
	}

	protected function connect(){
		$link = mysqli_connect($this->host, $this->user, $this->pwd);
		if(!$link){
			die('数据库连接失败');
		}
		mysqli_select_db($link, $this->dbname);
		mysqli_set_charset($link, $this->charset);
		return $link;
	}

	protected function getTableName(){
		//看是否设置了表名
		if(!empty($this->tableName)){
			return $this->$prefix.$this->tableName;
		}
		$className = get_class($this);
		$table = strtolower(substr($className,0,-5));
		return $this->prefix.$table;
	}

	protected function initOptions(){
		$arr = ['where','table','field','order','group','having','limit'];
		foreach ($arr as $value) {
			$this->options[$value]='';
			if ($value=='table'){
				$this->options[$value] = $this->tableName;
			}
		}
		$this->options['field'] = '*';
	}

	//filed方法
	function field($field){
		if(!empty($field)){
			if(is_string($field)){
				$this->options['field'] = $field;
			}
			else if(is_array($field)){
				$this->options['field'] = join(',',$field);
			}
		}
		return $this;
	}

	//table方法
	function table($table){
		if(!empty($table)){
			if(is_string($table)){
				$this->options['table'] = $table;
			}
			else if(is_array($table)){
				$this->options['table'] = join(',',$table);
			}
		}
		return $this;
	}

	//where方法
	function where($where){
		if(!empty($where)){
			$this->options['where'] = 'where '.$where;
		}
		return $this;
	}

	//group方法
	function group($group){
		if(!empty($group)){
			$this->options['group'] = 'group by '.$group;
		}
		return $this;
	}

	//having方法
	function having($having){
		if(!empty(($having))){
			$this->options['having'] = 'having '.$having; 
		}
		return $this;
	}

	//order方法
	function order($order){
		if(!empty($order)){
			$this->options['order'] = 'order by '.$order;
		}
		return $this;
	}

	//limit方法
	function limit($limit){
		if(!empty($limit)){
			if(is_string($limit)){
				$this->options['limit'] = 'limit '.$limit;
			}else if(is_array($limit)){
				$this->options['limit'] = 'limit '.join(',',$limit);
			}
		}
		return $this;
	}

	//select
	function select(){
		$sql = 'select %FIELD% from %TABLE% %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT%';
		//将options中的值替换上面值
		$sql = str_replace(['%FIELD%','%TABLE%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%'], 
			[$this->options['field'],$this->options['table'],$this->options['where'],$this->options['group'],$this->options['having'],$this->options['order'],$this->options['limit']],
			$sql);
		//保存一份sql语句
		$this->sql = $sql;
		//执行sql语句
		return $this->query($sql);
	}

	//query 返回结果集 查
	function query($sql){
		//清空options数组
		$this->initOptions();
		//执行sql
		$result = mysqli_query($this->link, $sql);
		//提取结果集存放到数组中
		if($result && mysqli_affected_rows($this->link)){
			while($data = mysqli_fetch_assoc($result)){
				$newData[] = $data;
			}
			return $newData;
		}
	}

	//exec 不返回结果集 增删改
	//删除和修改返回受影响的行数，插入返回id值
	function exec($sql, $isInsert = false){
		$this->initOptions();

		$result = mysqli_query($this->link, $sql);
		if($result && mysqli_affected_rows($this->link)){
			//判断是插入还是修改
			if($isInsert){
				return mysqli_insert_id($this->link);
			}else{
				return mysqli_affected_rows($this->link);
			}
		}
		return false;
	}

	//insert into table() values()
	function insert($data){
		//$data是关联数组，键是字段名，值是字段值

		//处理字符串问题，两边需要加单或双引号
		$data = $this->parseValue($data);
		//提取所有的键，就是字段
		$keys = array_keys($data);
		//提取所有的值
		$values = array_values($data);

		$sql = 'insert into %TABLE%(%FIELD%) values(%VALUES%)';
		$sql = str_replace(['%TABLE%','%FIELD%','%VALUES%'],
		 [$this->options['table'],join(',',$keys),join(',',$values)], $sql);
		$this->sql = $sql;
		return $this->exec($sql, true);
	}

	function delete(){
		$sql = 'delete from %TABLE% %WHERE%';
		$sql = str_replace(['%TABLE%','%WHERE%'], [$this->options['table'],$this->options['where']], $sql);
		$this->sql = $sql;
		return $this->exec($sql);
	}

	//update 表名 set 字段名=字段值 where 。。。
	function update($data){
		//处理字符串加引号问题
		$data = $this->parseValue($data);
		$value = $this->parseUpdate($data);

		$sql = 'update %TABLE% set %VALUE% %WHERE%';
		$sql = str_replace(['%TABLE%','%VALUE%','%WHERE%'], 
			[$this->options['table'],$value,$this->options['where']], $sql);
		$this->sql = $sql;
		return $this->exec($sql);
	}

	protected function parseUpdate($data){
		foreach ($data as $key => $value) {
			$newData[] = $key.'='.$value;
		}
		return join(',',$newData);
	}

	//将数组中的字符串的两边加上引号
	protected function parseValue($data){
		foreach ($data as $key => $value) {
			if(is_string($value)){
				$value = '"'.$value.'"';
			}
			$newData[$key] = $value;
		}
		return $newData;
	}

	//魔术方法 访问sql语句
	function __get($name){
		if ($name == 'sql'){
			return $this->sql;
		}
		return false;
	}

	function max($field){
		$result = $this->field('max('.$field.') as max')->select();
		return $result[0]['max'];
	}

	function min($field){
		$result = $this->field('min('.$field.') as min')->select();
		return $result[0]['min'];
	}

	function sum($field){
		$result = $this->field('sum('.$field.') as sum')->select();
		return $result[0]['sum'];
	}

	function avg($field){
		$result = $this->field('avg('.$field.') as avg')->select();
		return $result[0]['avg'];
	}

	//析构方法 当对象被销毁时执行
	function __destruct(){
		mysqli_close($this->link);
	}

	//调用不存在的方法时执行 getByName() getByAge()
	function __call($name, $args){
		//获取前五个字符
		$str = substr($name, 0, 5);
		//获取后面的字符串
		$field = strtolower(substr($name, 5));
		//判断前五个字符是否正确
		if( $str == 'getBy'){
			return $this->where($field.'="'.$args[0].'"')->select();
		}
		return false;
	}

}