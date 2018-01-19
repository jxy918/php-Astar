<?php
include 'AstarClass.php';

//设置障碍数组
/*
$barrier[] = array(1,2); 
$barrier[] = array(1,3); 
$barrier[] = array(1,1); 
$barrier[] = array(1,0); 
$barrier[] = array(0,6); 
$barrier[] = array(3,1); 
$barrier[] = array(4,1); 
$barrier[] = array(5,1); 
$barrier[] = array(2,4); 
$barrier[] = array(3,4); 
$barrier[] = array(4,4); 
$barrier[] = array(1,4); 
$barrier[] = array(5,4); 
$barrier[] = array(6,4); 
$barrier[] = array(6,1); 
$barrier[] = array(6,2); 
$barrier[] = array(6,3); 
$barrier[] = array(5,8); 
$barrier[] = array(3,2); 
$barrier[] = array(3,5); 
$barrier[] = array(3,6); 
$barrier[] = array(3,7); 
$barrier[] = array(7,4); 
$barrier[] = array(6,5); 
$barrier[] = array(6,6); 
$barrier[] = array(1,6); 
$barrier[] = array(8,6); 
*/

//实例化
$astar = new Astar(20,12,18,10,1,1);

//定义开放列表
$open_arr  = array();

//定义关闭列表
$close_arr = array();

//初始化开放列表
$open_arr[0]['x'] = $astar->begin_x; 
$open_arr[0]['y'] = $astar->begin_y; 
$open_arr[0]['G'] = 0; 
$open_arr[0]['H'] = $astar->getH($astar->begin_x,$astar->begin_y,$astar->end_x,$astar->end_y); 
$open_arr[0]['F'] = $open_arr[0]['H']; 
$open_arr[0]['p_node'] = array($astar->begin_x, $astar->begin_y); 

//循环调用
while (1) {
	//取的最小F值的格子作为当前格	
	$curent_node = $astar->getLowersFNode($open_arr);
	//从开启列表中删除此格子
	$open_arr    = $astar->removeNode($open_arr,$curent_node['x'],$curent_node['y']);

	//将当前点加入到关闭列表中
	$close_arr[]   = $curent_node;
	$round_list = $astar->getRoundNode($curent_node['x'],$curent_node['y']);

	$round_num  = count($round_list);
    
	for($i = 0;$i < $round_num;$i++) {
		$pos_arr = array();
		$pos_arr = $round_list[$i];

		//跳过已在关闭列表中的格子，障碍格子和转角格
		if ($astar->isOutMap($pos_arr[0],$pos_arr[1],$astar->map_width,$astar->map_height)
			|| $astar->isNodeClose($close_arr,$pos_arr[0],$pos_arr[1])
			|| $astar->isBarrier($pos_arr[0],$pos_arr[1])
			|| $astar->isCorner($pos_arr[0],$pos_arr[1],$curent_node['x'],$curent_node['y'])) {
			continue;
		}

		$new_g   = $astar->getG($pos_arr[0],$pos_arr[1],$curent_node['x'],$curent_node['y']);
		$total_g = $new_g + $curent_node['G'];

		//如果节点已在开启列表中，重新计算一下G值


		$rs_open = $astar->isNodeOpen($open_arr,$pos_arr[0],$pos_arr[1]);
		if (!$rs_open) { 
			$arr[$i] = array(); 
			$arr[$i]['x'] = $pos_arr[0]; 
			$arr[$i]['y'] = $pos_arr[1]; 
			$arr[$i]['G'] = $total_g; 
			$arr[$i]['H'] = $astar->getH($pos_arr[0], $pos_arr[1], $astar->end_x, $astar->end_y); 
			$arr[$i]['F'] = $arr[$i]['G'] + $arr[$i]['H']; 
			$arr[$i]['p_node']['x'] = $curent_node['x']; 
			$arr[$i]['p_node']['y'] = $curent_node['y']; 
			$open_arr[] = $arr[$i];
		} else { 
			$k = $rs_open['index']; 
			if($total_g < $open_arr[$k]['G']) { 
				$open_arr[$k]['G'] = $open_arr[$k]['G']; 
				$open_arr[$k]['F'] = $total_g + $open_arr[$k]['H']; 
				$open_arr[$k]['p_node']['x'] = $curent_node['x']; 
				$open_arr[$k]['p_node']['y'] = $curent_node['y']; 
			} else { 
				$total_g = $open_arr[$k]['G']; 
			} 
		}
	}
	if ($curent_node['x'] == $astar->end_x && $curent_node['y'] == $astar->end_y) { 
		$path = $astar->getPath($close_arr); 
		if (!empty($path)) { 
			break; 
		} 
	}
	if (empty($open_arr)) { 
		break; 
	}
}
$map = '';
//输出地图
foreach($astar->area as $av) {
	foreach($av as $as) {
		$top  = $as['y']*50;
		$left = $as['x']*50;
		$tmp  = array('x'=>$as['x'],'y'=>$as['y']);
		$tmp1 = array($as['x'],$as['y']);
		//起点
		if(($as['x'] == $astar->begin_x  && $as['y'] == $astar->begin_y )) {
			$map .= '<div id="m_'.$as['x'].'_'.$as['y'].'" style="float:left;background-image:url(./images/begin.gif);width:50px;height:50px;border-right:#CCC 1px dotted;border-bottom:#CCC 1px dotted;position:absolute;top:'.$top.'px;left:'.$left.'px;">始'.'['.$as['x'].','.$as['y'].']</div>';            
		} elseif (($as['x'] == $astar->end_x  && $as['y'] == $astar->end_y )) {
			//终点
            $map .= '<div id="m_'.$as['x'].'_'.$as['y'].'" style="float:left;background-image:url(./images/end.gif);width:50px;height:50px;border-right:#CCC 1px dotted;border-bottom:#CCC 1px dotted;position:absolute;top:'.$top.'px;left:'.$left.'px;">末'.'['.$as['x'].','.$as['y'].']</div>'; 
		} elseif (in_array($tmp,$path)) {
			//寻得的路径
            $map .= '<div id="m_'.$as['x'].'_'.$as['y'].'" style="float:left;background-color:green;width:50px;height:50px;border-right:#CCC 1px dotted;border-bottom:#CCC 1px dotted;position:absolute;top:'.$top.'px;left:'.$left.'px;">'.'['.$as['x'].','.$as['y'].']</div>';            
		} elseif (in_array($tmp1,$astar->barrier)) {
			//障碍物
            $map .= '<div id="m_'.$as['x'].'_'.$as['y'].'" style="float:left;background-image:url(./images/guai.gif);width:50px;height:50px;border-right:#CCC 1px dotted;border-bottom:#CCC 1px dotted;position:absolute;top:'.$top.'px;left:'.$left.'px;"></div>';
		} else {
			//其他
			$map .= '<div id="m_'.$as['x'].'_'.$as['y'].'" style="float:left;background-image:url(./images/bg.jpg);width:50px;height:50px;border-right:#CCC 1px dotted;border-bottom:#CCC 1px dotted;position:absolute;top:'.$top.'px;left:'.$left.'px;"></div>';
		}
	}
}
echo $map;
?>