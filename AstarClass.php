<?php
/**
 * A*寻路算法的实现。
 *
 * @package  Astar
 * @author   JXY <jxy918@163.com>
 */

class Astar {
	/**
	 * 地图宽度
	 * @var    int
	 * @access public
	 */
	public $map_width  = 9;

	/**
	 * 地图高度
	 * @var    int
	 * @access public
	 */
	public $map_height = 9;

	/**
	 * 是否允许障碍物边界斜向通过
	 * @var    int
	 * @access public
	 */
	public $is_agree = 0;
    
    /**
	 * 上下左右权值，10表示是上下左右的权值，14表示的对角线权值，即为14^2 = 10^2+10^2；
	 * @var    int
	 * @access public
	 */
	public $cost_1 = 10;

	/**
	 * 斜对角权值即对角线权值，10表示是上下左右的权值，14表示的对角线权值，即为14^2 = 10^2+10^2；
	 * @var    int
	 * @access public
	 */
	public $cost_2 = 14;

	/*
	 * 起始x坐标
	 * @var    int
	 * @access public
	 */
	public $begin_x = 5;

	/*
	 * 起始y坐标
	 * @var    int
	 * @access public
	 */
	public $begin_y = 2;

	/*
	 * 终点x坐标
	 * @var    int
	 * @access public
	 */
	public $end_x = 0;

	/*
	 * 终点y坐标
	 * @var    int
	 * @access public
	 */
	public $end_y = 0;

	/**
	 * 障碍物数组
	 * @var    array
	 * @access public
	 */
	public $barrier = array();

	/**
	 * 生成图图数组
	 * @var    array
	 * @access public
	 */
	 public $area = array();

	/**
     * 类默认构造函数
     *
     * @param int $map_width  地图的宽度
     * @param int $map_height 地图的高度
	 * @param int $begin_x    起始X坐标
	 * @param int $begin_y    起始Y坐标
	 * @param int $end_x      终点X坐标
	 * @param int $end_y      终点Y坐标
	 * @param array $barrier  障碍物数组
     * @return none
     */

	function __construct($map_width=9,$map_height=9,$begin_x=5,$begin_y=2,$end_x=0,$end_y=0,$barrier=array()) {
		$this->map_width  = $map_width;
		$this->map_height = $map_height;
		$this->begin_x    = $begin_x;
		$this->begin_y    = $begin_y;
		$this->end_x      = $end_x;
		$this->end_y      = $end_y;

		// 设置障碍物坐标 ,默认初始化坐标
		if (!$barrier) {
			for ($i = 0;$i < 20;$i++) {
				$barrier[] = array(rand(0,$this->map_width),rand(0,$this->map_height));
			}
		}
		$this->barrier   = $barrier;
		$this->area = $this->createMap($map_width,$map_height,$begin_x,$begin_y,$end_x,$end_y,$barrier);
	}
    /** 
    * 创建地图 
    * 
    * @param int $width     地图的宽度
	* @param int $height    地图的高度
    * @param int $begin_x   起始X坐标
	* @param int $begin_y   起始Y坐标
	* @param int $end_x     终点X坐标
	* @param int $end_y     终点Y坐标
	* @param int $barrier   障碍物数组
	* @return array
    */ 
    function createMap($width, $height, $begin_x, $begin_y, $end_x, $end_y, $barrier) { 
		$map = array();
		for ($i = 0; $i < $height; $i++) {
			for ($j = 0; $j < $width; $j++) {
				$map[$j][$i]['x']      = $j;
				$map[$j][$i]['y']      = $i;
				$map[$j][$i]['status'] = 0;

				//设置障碍物
				if ($this->isInBarrier($barrier,$j,$i)) {
					$map[$j][$i]['status'] = -1;
				}

				//设置起点
				if ($j == $begin_x && $i == $begin_y) {
					$map[$j][$i]['status'] = 1;
				}

				//设置终点
				if ($j == $end_x && $i == $end_y) {
					$map[$j][$i]['status'] = 9;
				}
			}
		}
		return $map;
	}

  /** 
    * 设置障碍，判断断是否为障碍坐标 
    * 
    * @param array   $arr  障碍数组
	* @param int     $x    X坐标
	* @param int     $y    Y坐标
	* @return bool
    */ 
	function isInBarrier($arr, $x, $y) {
		foreach ( $arr as $key=>$val) {
			if ($val[0] == $x && $val[1] == $y) {
				return true;
			}
		}
		return false;
	}

   /** 
	* 回溯路径,获得寻路路径 
	* 
	* @param int $close_arr   关闭列表数组
	* @return array
	*/ 
	function getPath($close_arr) { 
		$path = array(); 
		$p = $close_arr[count($close_arr)-1]['p_node']; 
		$path[] = $p; 
		while(1) { 
			for($i=0; $i<count($close_arr); $i++) { 
				if($close_arr[$i]['x']==$p['x'] && $close_arr[$i]['y']==$p['y']) { 
					$p=$close_arr[$i]['p_node']; 
					$path[] = $p; 
				} 
			} 

			if($p['x'] == $this->begin_x && $p['y'] == $this->begin_y) { 
				break; 
			} 
		} 
		return $path; 
   }

   /** 
	* 判断结点是否是障碍物 
	* 
	* @param int $node_x   节点X
    * @param int $node_Y   节点Y
	* @return bool
	*/
	function isBarrier($node_x,$node_y) {
		if (isset($this->area[$node_x][$node_y]['status']) && $this->area[$node_x][$node_y]['status'] == -1) {
			return true;
		}
		return false;
	}
   /** 
	* 判断是否超出地图 
	* 
	* @param int  $x          X坐标
	* @param int  $y          Y坐标
	* @param int  $map_width  地图宽度
	* @param int  $map_height 地图高度
	* @return bool
	*/ 
	function isOutMap($x,$y,$map_width,$map_height) {
		if ($x < 0 || $y < 0 || $x > ($map_width - 1) || $y > ($map_height - 1)) {
			return true;
		}
		return false;
	} 

	/** 
	* 取周边节点 ,周边的8个节点
	* 
	* @param int $x X坐标
	* @param int $y Y坐标
	* @return array
	*/
	function getRoundNode($x,$y) {
		$round_arr = array();
		$round_arr[0] = array($x - 1,$y - 1);
		$round_arr[1] = array($x - 1,$y);
		$round_arr[2] = array($x - 1,$y + 1);
		$round_arr[3] = array($x,$y - 1);
		$round_arr[4] = array($x,$y + 1);
		$round_arr[5] = array($x + 1,$y - 1);
		$round_arr[6] = array($x + 1,$y);
		$round_arr[7] = array($x + 1,$y + 1);

		return $round_arr;
	}
	
   /** 
	* 判断是否是转角点 
	* 
	* @param int  $x       X坐标
	* @param int  $y       Y坐标
	* @param int  $cur_x   X坐标
	* @param int  $cur_y   Y坐标
	* @return bool
	*/ 
	function isCorner($x,$y,$cur_x,$cur_y) {
		if ($x > $cur_x) {
			if ($y > $cur_y) {
				if ($this->isBarrier($x - 1,$y) || $this->isBarrier($x,$y - 1)) {
					return true;
				}
			} elseif ($y < $cur_y) {
				if ($this->isBarrier($x - 1,$y) || $this->isBarrier($x,$y + 1)) {
					return true;
				}
			}
		}

		if ($x < $cur_x) {
			if ($y < $cur_y) {
				if ($this->isBarrier($x + 1,$y) || $this->isBarrier($x,$y + 1)) {
					return true;
				}
			} elseif ($y > $cur_y) {
				if ($this->isBarrier($x + 1,$y) || $this->isBarrier($x,$y - 1)) {
					return true;
				}
			}
		}
		return false;
	}

  /** 
	* 计算G值 
	* 
	* @param int  $begin_x   起始X坐标
	* @param int  $begin_y   起始Y坐标
	* @param int  $parent_x  父亲X坐标
	* @param int  $parent_y  父亲X坐标
	* @return int
	*/
	function getG($begin_x,$begin_y,$parent_x,$parent_y) {
		if (($begin_x - $parent_x) * ($begin_y - $parent_y) != 0) {
			return $this->cost_2;
		} else {
			return $this->cost_1;
		}
	}

  /** 
	* 计算H值 
	* 
	* @param int  $begin_x   起始X坐标
	* @param int  $begin_y   起始Y坐标
	* @param int  $end_x     终点X坐标
	* @param int  $end_y     终点Y坐标
	* @param int  $cost      权值
	* @return int
	*/
	function getH($begin_x,$begin_y,$end_x,$end_y,$cost = 10) {
		$h_cost = abs(($end_x - $begin_x) * $cost);
		$v_cost = abs(($end_y - $begin_y) * $cost);
		return $h_cost + $v_cost; 
	} 

  /** 
	* 对开启列表排序 
	* 
	* @param array  $a 开启列表 
	* @param array  $b 新开启列表 
	* @return float
	*/ 
	static function sortOpenList($a,$b) {
		if ($a['F'] == $b['F']) return 0;
		return ($a['F'] > $b['F']) ? -1 : +1;
	}

  /** 
	* 取得最小F值的点 
	* 
	* @param array  $open_arr  开放列表,外部从小到大排好序 
	* @return array
	*/
	function getLowersFNode($open_arr) { 
		usort($open_arr, array($this,"sortOpenList"));
		$node = array();
		$i    = 0;
		foreach ($open_arr as $key=>$val) {
			if ($i == 0) {
				$node = $val;
			} else {
				if ($val['F'] <= $node['F']) {
					$node = $val;
				}
			}
			$i++;
		}
		return $node;
	}

  /** 
	* 删除节点 
	* 
	* @param  array $arr     数组 
	* @param  int   $x       X坐标 
	* @param  int   $y       Y坐标
	* @param  int   $status  状态值 
	* @return array
	*/
	
	function removeNode($arr,$x,$y,$status='') {
		foreach ($arr as $key=>$val) {
			if (isset($val['x']) && $val['x'] == $x && isset($val['y']) && $val['y'] == $y) {
				unset($arr[$key]);
			}

		}
		return $arr;
	}

  /** 
	* 判断节点是否已关闭 
	* 
	* @param int  $node_x   节点x
	* @param int  $node_y   节点y
	* @return bool
	*/ 
    function isNodeClose($close_arr,$node_x,$node_y) {
		foreach ($close_arr as $key=>$val) {
			if (isset($val['x']) && $val['x'] == $node_x && isset($val['y']) && $val['y'] == $node_y) {
				return true;
			}
		}
		return false;
	}

  /** 
	* 判断节点是否已在开启列表中 
	* 
	* @param int  $node_x   节点x
	* @param int  $node_Y   节点Y
	* @return bool
	*/
	function isNodeOpen($open_arr,$node_x,$node_y) {
		foreach ($open_arr as $key=>$val) {
			if (isset($val['x']) && $val['x'] == $node_x && isset($val['y']) && $val['y'] == $node_y) {
				$rs['index'] = $key;
				return $rs;
			}
		}
		return false;
	}

  /** 
	* 检查某结点是否在寻路路径中 
	* 
	* @param array  $parent_arr  父亲数组
    * @param int    $x			 X坐标
	* @param int    $y			 Y坐标
	* @return bool
	*/
	function isInPath($parent_arr,$x,$y) {
		foreach ($parent_arr as $key=>$val) {
			if(isset($val['x']) && $val['x'] == $x && isset($val['y']) && $val['y'] == $y) {
				return false;
			}
		}
		return false;
	}
}
?>