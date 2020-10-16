<?php

$page = new Page(5, 60);
var_dump($page->allUrl());
class Page
{
	//每页显示多少条数据
	protected $number;
	//一共有多少数据
	protected $totalCount;
	//当前页
	protected $page;
	//总页数
	protected $totalPage;
	//url
	protected $url;

	public function __construct($number,$totalCount){
		$this->number = $number;
		$this->totalCount = $totalCount;
		//得到总页数
		$this->totalPage = $this->getTotalPage();
		//得到当前页数
		$this->page = $this->getPage();
		//得到url
		$this->url = $this->getUrl();
	}

	protected function getUrl(){
		//得到协议名 http https
		$scheme = $_SERVER['REQUEST_SCHEME'];
		//得到主机名
		$host = $_SERVER['SERVER_NAME'];
		//得到端口号 http:80  https:443  ftp:21
		$port = $_SERVER['SERVER_PORT'];
		//得到路径和请求字符串
		$uri = $_SERVER['REQUEST_URI'];

		//中间做处理，若原来的url中有page参数，要清除掉，并拼接上新的
		$uriArray = parse_url($uri);
		$path = $uriArray['path'];

		if (!empty($uriArray['query'])){
			//将字符串变成关联数组
			parse_str($uriArray['query'], $array);
			//清除掉原有的page
			unset($array['page']);
			//将剩余的参数拼接
			$query = http_build_query($array);
			if($query != ''){
				$path = $path.'?'.$query;
			}
		}
		return $scheme.'://'.$host.':'.$port.'/'.$path;
	}

	protected function getPage(){
		if (empty($_GET['page'])){
			$page = 1;
		}
		elseif ($_GET['page'] > $this->totalPage) {
			$page = $this->totalPage;
		}
		elseif ($_GET['page'] < 1) {
			$page = 1;
		}else{
			$page = $_GET['page'];
		}
		return $page;
	}

	protected function getTotalPage(){
		return ceil($this->totalCount / $this->number);
	}

	protected function setUrl($str){
		if(strstr($this->url,'?')){
			$url = $this->url.'&'.$str;
		}else{
			$url = $this->url.'?'.$str;
		}
		return $str;
	}

	public function allUrl(){
		return [
			'first'=>$this->first(),
			'prev'=>$this->prev(),
			'next'=>$this->next(),
			'end'=>$this->end(),
		];
	}

	public function first(){
		return $this->setUrl('page=1');
	}

	public function next(){
		if ($this->page + 1 > $this->totalPage){
			$page = $this->totalPage;
		}else{
			$page = $this->page + 1;
		}
		return $this->setUrl('page='.$page);
	}

	public function prev(){
		if ($this->page - 1 < 1){
			$page = 1;
		}else{
			$page = $this->page - 1;
		}
		return $this->setUrl('page='.$page);
	}

	public function end(){
		return $this->setUrl('page='.$this->totalPage);
	}

	public function limit(){
		// limit 0,5  5,5
		$offset = ($this->page - 1) * $this->number;  
		return $offset.','.$this->number;
	}

}