<?php

/**
 *   站首页接口
 *   文件: group_site.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
 
class GroupSite{
	protected $rank_expire = 180;
	
	public function __construct(){
		$this->db = db::connect(config('database','default'));
		$this->cache = cache::connect(config('cache','memcache'));
		$this->group_mysql_db = domain::main()->GroupDBConn();
	}
	
	/**
	 *   查询站点风格信息
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的顶部导航信息
	 */
	public function getGroupNavigate($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".navigate WHERE group_id=$groupId AND status=1 ORDER BY `order` ASC";
		$list=$this->group_mysql_db->get_results($sql,'ASSOC');

		return array('Flag'=>100,'FlagString'=>'顶部导航','navigateList'=>$list);
	}
	
	/**
	 *   查询站点风格信息
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的风格信息
	 */
	public function getGroupStyle($group_id){
		$group_id = intval($group_id);
		if($group_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".style WHERE group_id={$group_id}";
		$row = $this->group_mysql_db->get_row($sql,ASSOC);
		$sql = "SELECT color_style FROM ".DB_NAME_GROUP.".style_setting WHERE id={$row['style_id']}";
		$color_style = $this->group_mysql_db->get_var($sql);
		$color_style = unserialize($color_style);
		$row = array_merge($row,$color_style);
		return array('Flag'=>100,'FlagString'=>'站点风格信息','StyleInfo'=>$row);
	}
	
	/**
	 *   查询站点图片
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的图片
	 */
	public function getGroupImg($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".image WHERE group_id=$groupId ORDER BY `order` ASC LIMIT 2";
		$list=$this->group_mysql_db->get_results($sql,'ASSOC');

		return array('Flag'=>100,'FlagString'=>'站点图片','imgList'=>$list);
	}
	
	/**
	 *   查询站点搜索配置
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回数据
	 */
	public function getGroupSearchConfig($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".search_config WHERE group_id=$groupId";
		$info=$this->group_mysql_db->get_row($sql,'ASSOC');

		return array('Flag'=>100,'FlagString'=>'搜索配置','info'=>$info);
	}
	
	
	/**
	 *   查询站点首页轮播图
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的轮播图信息
	 */
	public function getGroupCarousel($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".carousel WHERE group_id=$groupId ORDER BY `order` ASC";
		$list=$this->group_mysql_db->get_results($sql,'ASSOC');

		return array('Flag'=>100,'FlagString'=>'轮播图','carouselList'=>$list);
	}
	
	/**
	 *   查询站点下艺人
	 *   @param	int $groupId 站ID
	 *   @param	Boole $isHot 是否为热门
	 *   @return array $array 返回需要查找的艺人信息
	 */
	public function getGroupArtistList($data, $rule_id, $is_artist_detail){
		$groupId=intval($data['groupId']);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$where="c.type=15 AND c.room_id>0 AND c.up_uid=$groupId AND c.flag>0";
		if($data['keywords']){
			$where.=" AND (b.uin LIKE '".addslashes($data['keywords'])."%' OR b.nick LIKE '%".addslashes($data['keywords'])."%')";
		}
		
		if($data['limit']=='all'){
			$sql="SELECT c.room_id,b.uin,b.nick,b.is_online AS hasplay,r.curuser FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id WHERE $where ORDER BY b.is_online DESC,r.curuser DESC";
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');
            $list = $is_artist_detail ? $this->addArtistDetailToArr($groupId, $rule_id, $list) : $list;	
			return array('Flag'=>100,'FlagString'=>'艺人列表','artistList'=>$list,'total'=>count($list));
		}
		else{
			$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE $where";
			$count=$this->group_mysql_db->get_var($sql);
			if($count<=0){
				return array('Flag'=>100,'FlagString'=>'没有数据','artistList'=>array());
			}
			if($data['limit']>0){
				$limit=$data['limit'];
			}
			else{
				$limit=6;
			}
			$pageArr=$this->showPage($count,$limit,'get_group_artist');	
			$sql="SELECT c.room_id,b.uin,b.nick,b.is_online AS hasplay,r.curuser FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id WHERE $where ORDER BY b.is_online DESC,r.curuser DESC LIMIT ".$pageArr['limit'];
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');	
            $list = $is_artist_detail ? $this->addArtistDetailToArr($groupId, $rule_id, $list) : $list;		
			if($count/$limit<=1){
				$pageArr['page']='';
			}	
			return array('Flag'=>100,'FlagString'=>'艺人列表','artistList'=>$list,'page'=>$pageArr['page'],'total'=>$count);
		}
	}
	
	/**
	 *   查询站点下房间
	 *   @param	int $groupId 站ID
	 *   @param	Boole $isHot 是否为热门
	 *   @return array $array 返回需要查找的房间信息
	 */
	public function getGroupRoomList($data){
		$groupId=intval($data['groupId']);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$where="`group`=$groupId AND status>0";
		if($data['keywords']){
			$where.=" AND (id LIKE '".addslashes($data['keywords'])."%' OR name LIKE '%".addslashes($data['keywords'])."%')";
		}
		//分类
		if($data['catId']){
			$catId=intval($data['catId']);
			$sql="SELECT room_id FROM ".DB_NAME_GROUP.".rooms WHERE sort_id=$catId";
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');
			if(empty($list)){
				return array('Flag'=>100,'FlagString'=>'没有数据','roomList'=>array());
			}
			$roomIds='';
			foreach($list as $val){
				$roomIds.=$val['room_id'].',';
			}
			$roomIds=rtrim($roomIds,',');
			$where.=" AND id IN ($roomIds) AND status>0";
			$sql="SELECT id,name,curuser,hasplay,description FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE $where ORDER BY hasplay DESC,curuser DESC";
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');	
			foreach($list as $key=>$val){
				$list[$key]['description']=base64_encode($val['description']);
			}
			return array('Flag'=>100,'FlagString'=>'房间列表','roomList'=>$list,'page'=>'','total'=>count($list));
		}
		//所有
		if($data['limit']=='all'){
			$sql="SELECT id,name,curuser,hasplay,description FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE $where ORDER BY hasplay DESC,curuser DESC";
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');
			foreach($list as $key=>$val){
				$list[$key]['description']=base64_encode($val['description']);
			}
			return array('Flag'=>100,'FlagString'=>'房间列表','roomList'=>$list,'page'=>'','total'=>count($list));
		}
		//普通
		else{
			$sql="SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE $where";
			$count=$this->group_mysql_db->get_var($sql);
			if($count<=0){
				return array('Flag'=>100,'FlagString'=>'没有数据','roomList'=>array());
			}
			if($data['limit']>0){
				$limit=$data['limit'];
			}
			else{
				$limit=6;
			}
			$pageArr=$this->showPage($count,$limit,'get_group_room');
			$sql="SELECT id,name,curuser,hasplay,description FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE $where ORDER BY hasplay DESC,curuser DESC LIMIT ".$pageArr['limit'];
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');
			foreach($list as $key=>$val){
				$list[$key]['description']=base64_encode($val['description']);
			}
			if($count/$limit<=1){
				$pageArr['page']='';
			}
			$curPage = $_GET['page'] < 1 ? 1 : $_GET['page'];
			$firstPage = 1;
			$lastPage = ($count % $limit) == 0 ? floor($count / $limit) : floor($count / $limit) + 1;
			$prevPage = ($curPage -1) < $firstPage ? $firstPage : ($curPage -1);
			$nextPage = ($curPage +1) > $lastPage ? $lastPage : ($curPage +1);
			return array('Flag'=>100,'FlagString'=>'房间列表','roomList'=>$list,'page'=>$pageArr['page'],'total'=>$count,'curPage'=>$curPage,'prevPage'=>$prevPage,'nextPage'=>$nextPage,'lastPage'=>$lastPage,'showNum'=>$limit);
		}
	}
	
	
	/**
	 *   查询站点推荐位
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的站点推荐位信息
	 */
	public function getGroupRecommend($groupId, $rule_id, $is_artist_detail){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".recommend_cat WHERE group_id=$groupId AND status=1 ORDER BY `order` ASC";
		$recommendCat=$this->group_mysql_db->get_results($sql,'ASSOC');
		if(!empty($recommendCat)){
			foreach($recommendCat as $key=>$val){
				$sql="SELECT * FROM ".DB_NAME_GROUP.".recommend_sub_cat WHERE parent_id=".$val['id']." AND status=1 AND is_recommend=1 ORDER BY `order` ASC";
				$recommendSubCat=$this->group_mysql_db->get_results($sql,'ASSOC');
				if(!empty($recommendSubCat)){
					foreach($recommendSubCat as $key2=>$val2){
						//如果是房间推荐
						if($val['type']==1){
							//分页数
							if($val2['row']>0){
								$limit=$val2['row']*3;
							}
							else{
								$limit=6;
							}
							//如果是所有房间
							if($val2['mode']==1){
								$sql="SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId AND status>0";
								$count=$this->group_mysql_db->get_var($sql);
								if($count<=0){
									$recommendSubCat[$key2]['list']=array();
									continue;
								}
								$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
								$sql="SELECT id,name,curuser,hasplay,description FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId AND status>0 ORDER BY hasplay DESC,curuser DESC LIMIT ".$pageArr['limit'];
							}
							//如果是自定义房间
							elseif($val2['mode']==2){
								$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".recommend AS a LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS b ON a.code=b.id WHERE a.parent_id=".$val2['id']." AND b.status>0";
								$count=$this->group_mysql_db->get_var($sql);
								if($count<=0){
									$recommendSubCat[$key2]['list']=array();
									continue;
								}
								$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
								$sql="SELECT b.id,b.name,b.curuser,b.hasplay,b.description FROM ".DB_NAME_GROUP.".recommend AS a LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS b ON a.code=b.id WHERE a.parent_id=".$val2['id']." AND b.status>0 ORDER BY b.hasplay DESC,b.curuser DESC,a.`order` ASC LIMIT ".$pageArr['limit'];
							}
							$list=$this->group_mysql_db->get_results($sql,'ASSOC');
							foreach($list as $k=>$v){
								$list[$k]['description']=base64_encode($v['description']);
							}
							$recommendSubCat[$key2]['list']=$list;
							$recommendSubCat[$key2]['page']=str_replace('条记录','个房间',$pageArr['page']);
						}
						//如果是会员推荐
						elseif($val['type']==2){
							//分页数
							if($val2['pic']==1){
								if($val2['row']>0){
									$limit=$val2['row']*5;
								}
								else{
									$limit=10;
								}
							}
							else{
								if($val2['row']>0){
									$limit=$val2['row']*3;
								}
								else{
									$limit=6;
								}
							}
							//如果是所有会员
							if($val2['mode']==1){
								$sql="SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl WHERE group_id=$groupId";
								$count=$this->group_mysql_db->get_var($sql);
								if($count<=0){
									$recommendSubCat[$key2]['list']=array();
									continue;
								}
								$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
								$sql="SELECT uin,nick,group_id,is_online,room_id FROM ".DB_NAME_IM.".basic_tbl WHERE group_id=$groupId LIMIT ".$pageArr['limit'];
								$list=$this->group_mysql_db->get_results($sql,'ASSOC');
							}
							//如果是自定义会员
							elseif($val2['mode']==2){
								$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".recommend AS r LEFT JOIN ".DB_NAME_IM.".basic_tbl AS v ON r.code=v.uin WHERE r.parent_id=".$val2['id']." AND r.group_id=$groupId AND v.group_id=$groupId";
								$count=$this->group_mysql_db->get_var($sql);
								if($count<=0){
									$recommendSubCat[$key2]['list']=array();
									continue;
								}
								$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
								$sql="SELECT v.uin,v.nick,v.group_id,v.is_online,v.room_id FROM ".DB_NAME_GROUP.".recommend AS r LEFT JOIN ".DB_NAME_IM.".basic_tbl AS v ON r.code=v.uin WHERE r.parent_id=".$val2['id']." AND r.group_id=$groupId AND v.group_id=$groupId ORDER BY r.`order` ASC LIMIT ".$pageArr['limit'];
								$list=$this->group_mysql_db->get_results($sql,'ASSOC');
							}			
							if(!empty($list)){
								foreach($list as $key3=>$val3){
									$data=array(
										'extparam'=>array(
											'Tag'=>'GetUserBasicForUin',
											'Uin'=>$val3['uin']
										)
									);
									$userInfo=httpPOST(SSO_API_PATH,$data);
									$list[$key3]['nick']=$userInfo['baseInfo']['nick'];
								}
							}
							$recommendSubCat[$key2]['list']=$list;
							$recommendSubCat[$key2]['page']=str_replace('条记录','个会员',$pageArr['page']);
						}
						//如果是通用推荐
						elseif($val['type']==3){
							$sql="SELECT * FROM ".DB_NAME_GROUP.".recommend_common WHERE parent_id=".$val2['id']." ORDER BY `order` ASC";
							$list=$this->group_mysql_db->get_results($sql,'ASSOC');
							$recommendSubCat[$key2]['list']=$list;
						}
						//如果是艺人推荐
						elseif($val['type']==4){
							//分页数
							if($val2['row']>0){
								$limit=$val2['row']*3;
							}
							else{
								$limit=6;
							}
							//如果是所有艺人
							if($val2['mode']==1){
								$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user WHERE type=15 AND up_uid=$groupId AND flag=1";
								$count=$this->group_mysql_db->get_var($sql);
								if($count<=0){
									$recommendSubCat[$key2]['list']=array();
									continue;
								}
								$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
								$sql="SELECT r.id as room_id,r.curuser,b.uin,b.nick,b.is_online AS hasplay FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE c.type=15 AND c.up_uid=$groupId AND c.flag=1 ORDER BY b.is_online DESC,r.curuser DESC LIMIT ".$pageArr['limit'];
								$list=$this->group_mysql_db->get_results($sql,'ASSOC');
							}
							//如果是自定义艺人
							elseif($val2['mode']==2){
								$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".recommend AS r LEFT JOIN ".DB_NAME_PARTNER.".channel_user AS c ON r.code=c.uid WHERE r.parent_id=".$val2['id']." AND r.group_id=$groupId AND c.type=15 AND c.up_uid=$groupId AND c.flag=1";
								$count=$this->group_mysql_db->get_var($sql);
								if($count<=0){
									$recommendSubCat[$key2]['list']=array();
									continue;
								}
								$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
								$sql="SELECT r.id as room_id,r.curuser,b.uin,b.nick,b.is_online AS hasplay FROM ".DB_NAME_GROUP.".recommend AS re LEFT JOIN ".DB_NAME_PARTNER.".channel_user AS c ON re.code=c.uid LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE re.parent_id=".$val2['id']." AND re.group_id=$groupId AND c.type=15 AND c.up_uid=$groupId AND c.flag=1 ORDER BY  b.is_online DESC,r.curuser DESC,re.`order` ASC LIMIT ".$pageArr['limit'];
								$list=$this->group_mysql_db->get_results($sql,'ASSOC');
							}
							$recommendSubCat[$key2]['list']=$is_artist_detail ? $this->addArtistDetailToArr($groupId, $rule_id, $list) : $list;
							$recommendSubCat[$key2]['page']=str_replace('条记录','个艺人',$pageArr['page']);
						}
						if($count/$limit>1){
							$recommendSubCat[$key2]['page']=str_replace('page=','sub_id='.$val2['id'].'&page=',$recommendSubCat[$key2]['page']);
						}
						else{
							$recommendSubCat[$key2]['page']='';
						}
						if($limit){
							$curPage = $_GET['page'] < 1 ? 1 : $_GET['page'];
							$firstPage = 1;
							$lastPage = ($count % $limit) == 0 ? floor($count / $limit) : floor($count / $limit) + 1;
							$prevPage = ($curPage -1) < $firstPage ? $firstPage : ($curPage -1);
							$nextPage = ($curPage +1) > $lastPage ? $lastPage : ($curPage +1);
						}
						$recommendSubCat[$key2]['total']=$count;
						$recommendSubCat[$key2]['curPage']=$curPage;
						$recommendSubCat[$key2]['prevPage']=$prevPage;
						$recommendSubCat[$key2]['nextPage']=$nextPage;
						$recommendSubCat[$key2]['lastPage']=$lastPage;
						$recommendSubCat[$key2]['showNum']=$limit;;
					}
				}
				$recommendCat[$key]['child']=$recommendSubCat;
			}
		}
		return array('Flag'=>100,'FlagString'=>'推荐位列表','recommendCat'=>$recommendCat);
	}
	
    private function addArtistDetailToArr($group_id, $rule_id, $arr){
        $uin_arr = array();
        
        //从数组中获取sql条件
        foreach($arr as $one){
            $uin_arr[] = $one['uin'];
        }
        $uin_arr    = array_map("intval", $uin_arr);
        $in_uin_str = join(",", array_unique($uin_arr));
        
        //获取粉丝数和视频数据
		$artist_id_to_info = array();
        
        $sql              = "SELECT COUNT(*) AS c, following AS uin FROM ".DB_NAME_WEIBO.".follow WHERE following IN (".$in_uin_str.") GROUP BY following";
        $artist_fans_num  = $this->group_mysql_db->get_results($sql, "ASSOC");
        foreach($artist_fans_num as $one){
            $artist_id_to_info[$one['uin']]['fans_num'] = $one['c'];
        }
        
        $sql              = "SELECT COUNT(*) AS c, uin FROM ".DB_NAME_IM.".`video` WHERE uin IN (".$in_uin_str.") GROUP BY uin";
        $artist_video_num = $this->group_mysql_db->get_results($sql, "ASSOC");
        foreach($artist_video_num as $one){
            $artist_id_to_info[$one['uin']]['video_num'] = $one['c'];
        }
        
        //艺人等级图标和图标的名称
        $roleData = array(
			'extparam'=>array(
				'Tag'     => 'GetRole',
				'GroupId' => $group_id,
				'Uin'     => $uin_arr,
				'Ruleid'  => $rule_id
			)
		);
		$roleInfo = httpPOST(ROLE_API_PATH,$roleData);
        foreach($roleInfo['Roles'] as $row){
            $artist_id_to_info[$row['uin']]['role_small_icon']  = $row['role_small_icon'];
            $artist_id_to_info[$row['uin']]['icon_name']        = $row['name'];
        }
        
        //对应加入数组
        foreach($arr as $k=>$val){
            $fans_num        = $artist_id_to_info[$val['uin']]['fans_num'];
            $video_num       = $artist_id_to_info[$val['uin']]['video_num'];
            $role_small_icon = $artist_id_to_info[$val['uin']]['role_small_icon'];
            $icon_name       = $artist_id_to_info[$val['uin']]['icon_name'];
            
            $arr[$k]['fans_num']        = $fans_num  ? $fans_num : 0;
            $arr[$k]['video_num']       = $video_num ? $video_num : 0;
            $arr[$k]['role_small_icon'] = $role_small_icon ? $role_small_icon : '';
            $arr[$k]['icon_name']       = $icon_name ? $icon_name : '';
        }
        
        return $arr;
    }
    
	/**
	 *   查询站点推荐位
	 *   @param	int $subId recommend_sub_cat主键ID
	 *   @return array $array 返回需要查找的站点推荐位信息
	 */
	public function getRecommendSub($subId, $rule_id, $is_artist_detail){
		$subId=intval($subId);
		if($subId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="SELECT * FROM ".DB_NAME_GROUP.".recommend_sub_cat WHERE id=$subId";
		$subInfo=$this->group_mysql_db->get_row($sql,'ASSOC');
		if(empty($subInfo)){
			return array('Flag'=>110,'FlagString'=>'未知错误');
		}
		$sql="SELECT * FROM ".DB_NAME_GROUP.".recommend_cat WHERE id=".$subInfo['parent_id'];
		$subInfo['parentInfo']=$this->group_mysql_db->get_row($sql);
		if(empty($subInfo['parentInfo'])){
			return array('Flag'=>110,'FlagString'=>'未知错误');
		}
		$groupId=$subInfo['group_id'];
		$list=array();
		//如果是房间推荐
		if($subInfo['parentInfo']['type']==1){
			//分页数
			if($subInfo['row']>0){
				$limit=$subInfo['row']*3;
			}
			else{
				$limit=6;
			}
			//如果是所有房间
			if($subInfo['mode']==1){
				$sql="SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId AND status>0";
				$count=$this->group_mysql_db->get_var($sql);
				if($count>0){
					$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
					$sql="SELECT id,name,curuser,hasplay,description FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId AND status>0 ORDER BY hasplay DESC,curuser DESC LIMIT ".$pageArr['limit'];
				}
			}
			//如果是自定义房间
			elseif($subInfo['mode']==2){
				$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".recommend AS a LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS b ON a.code=b.id WHERE a.parent_id=".$subInfo['id']." AND b.status>0";
				$count=$this->group_mysql_db->get_var($sql);
				if($count>0){
					$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
					$sql="SELECT b.id,b.name,b.curuser,b.hasplay,b.description FROM ".DB_NAME_GROUP.".recommend AS a LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS b ON a.code=b.id WHERE a.parent_id=".$subInfo['id']." AND b.status>0 ORDER BY b.hasplay DESC,b.curuser DESC,a.`order` ASC LIMIT ".$pageArr['limit'];
				}
			}
			if($count>0){
				$list=$this->group_mysql_db->get_results($sql,'ASSOC');
				foreach($list as $k=>$v){
					$list[$k]['description']=base64_encode($v['description']);
				}
				$pageArr['page']=str_replace('条记录','个房间',$pageArr['page']);
			}
		}
		//如果是会员推荐
		elseif($subInfo['parentInfo']['type']==2){
			//分页数
			if($subInfo['pic']==1){
				if($subInfo['row']>0){
					$limit=$subInfo['row']*5;
				}
				else{
					$limit=10;
				}
			}
			else{
				if($subInfo['row']>0){
					$limit=$subInfo['row']*3;
				}
				else{
					$limit=6;
				}
			}
			//如果是所有会员
			if($subInfo['mode']==1){
				$sql="SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl WHERE group_id=$groupId";
				$count=$this->group_mysql_db->get_var($sql);
				if($count>0){
					$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
					$sql="SELECT uin,nick,group_id,is_online,room_id FROM ".DB_NAME_IM.".basic_tbl WHERE group_id=$groupId LIMIT ".$pageArr['limit'];
					$list=$this->group_mysql_db->get_results($sql,'ASSOC');
				}
			}
			//如果是自定义会员
			elseif($subInfo['mode']==2){
				$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".recommend AS r LEFT JOIN ".DB_NAME_IM.".basic_tbl AS v ON r.code=v.uin WHERE r.parent_id=".$subInfo['id']." AND r.group_id=$groupId AND v.group_id=$groupId";
				$count=$this->group_mysql_db->get_var($sql);
				if($count>0){
					$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
					$sql="SELECT v.uin,v.nick,v.group_id,v.is_online,v.room_id FROM ".DB_NAME_GROUP.".recommend AS r LEFT JOIN ".DB_NAME_IM.".basic_tbl AS v ON r.code=v.uin WHERE r.parent_id=".$subInfo['id']." AND r.group_id=$groupId AND v.group_id=$groupId ORDER BY r.`order` ASC LIMIT ".$pageArr['limit'];
					$list=$this->group_mysql_db->get_results($sql,'ASSOC');
				}
			}
			$pageArr['page']=str_replace('条记录','个会员',$pageArr['page']);
		}
		//如果是艺人推荐
		elseif($subInfo['parentInfo']['type']==4){
			//分页数
			if($subInfo['row']>0){
				$limit=$subInfo['row']*3;
			}
			else{
				$limit=6;
			}		
			//如果是所有艺人
			if($subInfo['mode']==1){
				$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user WHERE type=15 AND up_uid=$groupId AND flag=1";
				$count=$this->group_mysql_db->get_var($sql);
				if($count>0){
					$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
					$sql="SELECT r.id as room_id,r.curuser,b.uin,b.nick,b.is_online AS hasplay FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE c.type=15 AND c.up_uid=$groupId AND c.flag=1 ORDER BY b.is_online DESC,r.curuser DESC LIMIT ".$pageArr['limit'];
					$list=$this->group_mysql_db->get_results($sql,'ASSOC');
				}
			}
			//如果是自定义艺人
			elseif($subInfo['mode']==2){
				//$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".recommend AS r LEFT JOIN ".DB_NAME_PARTNER.".channel_user AS c ON r.code=c.uid WHERE r.parent_id=".$subInfo['id']." AND r.group_id=$groupId AND c.type=15 AND c.up_uid=$groupId AND c.flag=1";
				$sql = "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".recommend WHERE parent_id={$subInfo['id']} AND group_id={$groupId}";
				$count=$this->group_mysql_db->get_var($sql);
				if($count>0){
					$pageArr=$this->showPage($count,$limit,'get_recommend_sub');
					$sql="SELECT r.id as room_id,r.curuser,b.uin,b.nick,b.is_online AS hasplay FROM ".DB_NAME_GROUP.".recommend AS re LEFT JOIN ".DB_NAME_PARTNER.".channel_user AS c ON re.code=c.uid LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE re.parent_id=".$subInfo['id']." AND re.group_id=$groupId AND c.type=15 AND c.up_uid=$groupId AND c.flag=1 ORDER BY b.is_online DESC,r.curuser DESC,re.`order` ASC LIMIT ".$pageArr['limit'];
					$list=$this->group_mysql_db->get_results($sql,'ASSOC');
				}
			}
            if($is_artist_detail){
                $list = $this->addArtistDetailToArr($groupId, $rule_id, $list);
            }
			$pageArr['page']=str_replace('条记录','个艺人',$pageArr['page']);
		}
		if($limit){
			$curPage = $_GET['page'] < 1 ? 1 : $_GET['page'];
			$firstPage = 1;
			$lastPage = ($count % $limit) == 0 ? floor($count / $limit) : floor($count / $limit) + 1;
			$prevPage = ($curPage -1) < $firstPage ? $firstPage : ($curPage -1);
			$nextPage = ($curPage +1) > $lastPage ? $lastPage : ($curPage +1);
		}
		return array('Flag'=>100,'FlagString'=>'成功','type'=>$subInfo['parentInfo']['type'],'pic'=>$subInfo['pic'],'list'=>$list,'page'=>$pageArr['page'],'total'=>$count,'curPage'=>$curPage,'prevPage'=>$prevPage,'nextPage'=>$nextPage,'lastPage'=>$lastPage,'showNum'=>$limit);
	}
	
	/**
	 *   查询站点首页排行榜设置
	 *   @param	int $groupId 站ID
	 *   @param	int $type 类型
	 *   @param	int $type 条数
	 *   @return array $array 返回需要查找的排行榜信息
	 */
	public function getGroupRank($groupId,$type,$row,$roleimg){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".rank_config WHERE group_id=".$groupId;
		$info=$this->group_mysql_db->get_row($sql,'ASSOC');
		
		//排行类型，1首页，2排行榜页面
		if($type==1){
			$rankList=unserialize($info['index_rank']);
		}
		elseif($type==2){
			$rankList=unserialize($info['rank']);
		}
		
		$row=intval($row);
		foreach($rankList as $key=>$val){
			$sql="SELECT id,name,sort_type,sort_key FROM ".DB_NAME_TPL.".business_rule WHERE id=".$val['rule'].' AND `status` = "1"';
			$rank_set=$this->db->get_row($sql);
			if(empty($rank_set)){
				continue;
			}
			$ruleId=intval($val['rule']);
			$row = intval($val['Row']);
			$data=array(
				'ExtendUin'=>intval($groupId),
				'Period'=>array('week','month','total'),
				'Ruleid'=>$ruleId,
				'Rows'=>$row?$row:10
			);
			$cachename = "GROUPRANK_{$data['ExtendUin']}{$data['Ruleid']}{$data['Rows']}{$data['Time']}";
			$result = $this->cache->get($cachename);
			$this->mongodb=domain::main()->GroupDBConn('mongo');
			$rule=new rule($this->mongodb);
			if(empty($result)){
				$long_info = $this->cache->long_get($cachename);
				$this->cache->set($cachename,$long_info,$this->rank_expire);
				$result=$rule->getRuleRank($data['UinId'],$data['ChannelUin'],$data['ExtendUin'],0,$data['Ruleid'],$data['Rows'],$data['Period'],$data['Time']);
				foreach($result as $key2=>$val2){//重组数据 查询用户昵称 房间名称
					foreach($val2 as $key3=>$val3){
						$result[$key2][$key3]['SortType'] = $rank_set['sort_type'];
						if($rank_set['sort_type'] == 1 && $val3[$rank_set['sort_key']] > 0){//sort_type 1 用户 2房间  
							$sInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val3[$rank_set['sort_key']])));
							if($roleimg){
								$roleData=array(
									'extparam'=>array(
										'Tag'=>'GetRole',
										'GroupId'=>$val3['ExtendUin'],
										'Uin'=>$val3[$rank_set['sort_key']],
										'Ruleid'=>$val3['Ruleid']
									)
								);
								$roleInfo=httpPOST(ROLE_API_PATH,$roleData);
								$result[$key2][$key3]['RolesImg'] = $roleInfo['Roles'][0]['role_small_icon'];
								$result[$key2][$key3]['RolesName'] = $roleInfo['Roles'][0]['name'];
							}
							$result[$key2][$key3]['Nick'] = $sInfo['Flag']==100 ? $sInfo['Nick'] : $val3[$rank_set];
							$result[$key2][$key3]['Link'] = $val3[$rank_set['sort_key']];
						}else if($rank_set['sort_type'] == 2 && $val3[$rank_set['sort_key']] > 0){
							$result[$key2][$key3]['Nick'] = $this->getRoomName($val3[$rank_set['sort_key']]);
							$result[$key2][$key3]['Link'] = $val3[$rank_set['sort_key']];
						}else{
							$result[$key2][$key3]['Nick'] = $val3[$rank_set['sort_key']];
						}
					}
				}
				$this->cache->set($cachename,$result,$this->rank_expire);
			}
			
			$rankList[$key]['week']=$result['week'];
			$rankList[$key]['month']=$result['month'];
			$rankList[$key]['total']=$result['total'];
			
			//上周
			$rankLastWeek=intval(time()-3600*24*7);
			
			$cachename = "GROUPRANK_{$data['ExtendUin']}{$data['Ruleid']}{$data['Rows']}{$rankLastWeek}";
			$last_result = $this->cache->get($cachename);
			if(empty($last_result)){
				$long_info = $this->cache->long_get($cachename);
				$this->cache->set($cachename,$long_info,$this->rank_expire);
				$last_result=$rule->getRuleRank($data['UinId'],$data['ChannelUin'],$data['ExtendUin'],0,$data['Ruleid'],$data['Rows'],array('week'),$rankLastWeek);
				foreach($last_result as $key2=>$val2){//重组数据 查询用户昵称 房间名称
					foreach($val2 as $key3=>$val3){
						$last_result[$key2][$key3]['SortType'] = $rank_set['sort_type'];
						if($rank_set['sort_type'] == 1 && $val3[$rank_set['sort_key']] > 0){//sort_type 1 用户 2房间  
							$sInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val3[$rank_set['sort_key']])));
							if($roleimg){
								$roleData=array(
									'extparam'=>array(
										'Tag'=>'GetRole',
										'GroupId'=>$val3['ExtendUin'],
										'Uin'=>$val3[$rank_set['sort_key']],
										'Ruleid'=>$val3['Ruleid']
									)
								);
								$roleInfo=httpPOST(ROLE_API_PATH,$roleData);
								$last_result[$key2][$key3]['RolesImg'] = $roleInfo['Roles'][0]['role_small_icon'];
								$last_result[$key2][$key3]['RolesName'] = $roleInfo['Roles'][0]['name'];
							}
							$last_result[$key2][$key3]['Nick'] = $sInfo['Flag']==100 ? $sInfo['Nick'] : $val3[$rank_set];
							$last_result[$key2][$key3]['Link'] = $val3[$rank_set['sort_key']];
						}else if($rank_set['sort_type'] == 2 && $val3[$rank_set['sort_key']] > 0){
							$last_result[$key2][$key3]['Nick'] = $this->getRoomName($val3[$rank_set['sort_key']]);
							$last_result[$key2][$key3]['Link'] = $val3[$rank_set['sort_key']];
						}else{
							$last_result[$key2][$key3]['Nick'] = $val3[$rank_set['sort_key']];
						}
					}
				}
				$this->cache->set($cachename,$result,$this->rank_expire);
			}
			$rankList[$key]['last_week']=$last_result['week'];
		}
		return array('Flag'=>100,'FlagString'=>'排行榜','rankList'=>$rankList);
	}
	
	/**
	 *   查询站点首页左部导航
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的导航信息
	 */
	public function getGroupMenu($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".menu WHERE group_id=$groupId AND super_id=0 AND status=1 ORDER BY `order` ASC";
		$menuList=$this->group_mysql_db->get_results($sql,'ASSOC');
		foreach($menuList as $key=>$val){
			$sql="SELECT * FROM ".DB_NAME_GROUP.".menu WHERE super_id={$val['id']} AND status=1 ORDER BY `order` ASC";
			$menuChildList=$this->group_mysql_db->get_results($sql,'ASSOC');
			foreach($menuChildList as $key2=>$val2){
				$menuChildList[$key2]['other']=json_decode($val2['other']);
			}
			$menuList[$key]['child']=$menuChildList;
		}

		return array('Flag'=>100,'FlagString'=>'导航','menuList'=>$menuList);
	}
	
	/**
	 *   查询站点动态信息
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的动态信息
	 */
	public function getGroupMessage($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT title,status FROM ".DB_NAME_GROUP.".custom_title WHERE group_id=$groupId AND type=1";
		$customTitle=$this->group_mysql_db->get_row($sql);
		if($customTitle['status']!=1){
			return array('Flag'=>102,'FlagString'=>'未开启滚动消息');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".message WHERE group_id=$groupId ORDER BY `order` ASC";
		$list=$this->group_mysql_db->get_results($sql,'ASSOC');

		return array('Flag'=>100,'FlagString'=>'滚动消息','messageList'=>$list,'title'=>$customTitle['title']);
	}
	
	/**
	 *   查询站内在麦会员
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的会员信息
	 */
	public function getGroupVipOnline($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".mic_setting WHERE group_id=$groupId";
		$micSetting=$this->group_mysql_db->get_row($sql);
		if($micSetting['status']!=1){
			return array('Flag'=>102,'FlagString'=>'未开启在麦会员显示');
		}
		
		$sql="SELECT uin,room_id FROM ".DB_NAME_IM.".basic_tbl WHERE group_id=$groupId AND is_online=1 LIMIT 20";
		$list=$this->group_mysql_db->get_results($sql,'ASSOC');
		foreach($list as $key=>$val){
			$data=array(
				'extparam'=>array(
					'Tag'=>'GetUserBasicForUin',
					'Uin'=>$val['uin']
				)
			);
			$userInfo=httpPOST(SSO_API_PATH,$data);
			$list[$key]['nick']=$userInfo['baseInfo']['nick'];
			$list[$key]['name']=$this->getRoomName($val['room_id']);
		}

		return array('Flag'=>100,'FlagString'=>'在麦会员','vipList'=>$list,'title'=>$micSetting['title']);
	}
	
	/**
	 *   查询站点在线人数
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回人数
	 */
	public function getGroupOnlineNum($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT SUM(curuser) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId AND status>0";
		$total=$this->group_mysql_db->get_var($sql);

		return array('Flag'=>100,'FlagString'=>'成功','total'=>$total);
	}
	
	/**
	 *   查询站点配置
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回配置单
	 */
	public function getGroupSetting($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".group_setting WHERE group_id=$groupId";
		$result=$this->group_mysql_db->get_results($sql);
		$setting=array();
		foreach($result as $val){
			$setting[$val['key']]=unserialize($val['value']);
		}

		return array('Flag'=>100,'FlagString'=>'成功','setting'=>$setting);
	}
	
	/**
	 *   查询站点艺人直播墙
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回艺人列表
	 */
	public function getGroupLivePhoto($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".recommend_sub_cat WHERE group_id=$groupId AND is_live=1 AND status=1";
		$info=$this->group_mysql_db->get_row($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>100,'FlagString'=>'成功','liveList'=>array());
		}
		//如果是所有艺人
		if($info['mode']==1){
			$sql="SELECT c.room_id,b.uin,b.nick,b.is_online AS hasplay,r.curuser FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id WHERE c.type=15 AND c.up_uid=$groupId AND c.flag=1 ORDER BY b.is_online DESC,r.curuser DESC";
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');
		}
		//如果是自定义艺人
		elseif($info['mode']==2){
			$sql="SELECT c.room_id,b.uin,b.nick,b.is_online AS hasplay,r.curuser FROM ".DB_NAME_GROUP.".recommend AS re LEFT JOIN ".DB_NAME_PARTNER.".channel_user AS c ON re.code=c.uid LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON c.room_id=r.id LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE re.parent_id=".$info['id']." AND re.group_id=$groupId AND c.type=15 AND c.up_uid=$groupId AND c.flag=1 ORDER BY r.hasplay DESC,r.curuser DESC,re.`order` ASC";
			$list=$this->group_mysql_db->get_results($sql,'ASSOC');
		}

		return array('Flag'=>100,'FlagString'=>'成功','liveList'=>$list);
	}
	
	function practiceUserLogin($user_name, $telephone, $group_id){
		
		$sql  = "INSERT INTO ".DB_NAME_GROUP.".`user_intention` (`user_name`, `telephone`, `group_id`, `uptime`) VALUES ('".$user_name."', '".$telephone."', '".$group_id."', '".time()."');";
		$done = $this->group_mysql_db->query($sql);
		if(!$done){
			return array('Flag'=>101,'FlagString'=>'数据库错误，请重新登陆');
		}
		
		$sql = "SELECT role_name,account_details FROM ".DB_NAME_GROUP.".practice_account WHERE group_id = ".$group_id;
		$account_list = $this->group_mysql_db->get_results($sql, "ASSOC");
		if(!$account_list){
			return array("Flag"=>101, "FlagString"=>"该站不存在体验账号");
		}
		
		$test_account_list = array();
		foreach($account_list as $row){
			$account_details 		= json_decode($row['account_details'], true);
			$row['account_detail']	= $account_details[array_rand($account_details)];
			unset($row['account_details']);
			$test_account_list[] = $row;
		}
		
		return array("Flag"=>100, "FlagString"=>"登陆成功", "Data"=>$test_account_list);
	}
	
	function getPractice($group_id){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`practice_account` WHERE group_id = ".$group_id." ORDER BY `id` ASC";
		
		$account_list = $this->group_mysql_db->get_results($sql, "ASSOC");
		
		$account_list_with_uin = array();
		foreach($account_list as $row){
			$row['account_details']  = json_decode($row['account_details'], true);
			$uin_str				 = "";
			foreach($row['account_details'] as $account){
				$uin_str .= $account['login'].",";
			}
			$row['uin_str'] 		 = substr($uin_str, 0, -1);
			
			$account_list_with_uin[] = $row; 
		}
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$account_list_with_uin);
	}
    
    function artistDetail($group_id, $rule_id, $uin_arr){
        //获取艺人的昵称、视频数、粉丝数、观看人数、是否直播、所在房间id、艺人等级图标和图标的名称
        if(!is_array($uin_arr) || !$group_id || !$rule_id){
            return array("Flag"=>101, "FlagString"=>"参数错误");
        }

        $artist_detail = array();
        $uin_arr    = array_map("intval", $uin_arr);
        $in_uin_str = join(",", $uin_arr);
        
        //艺人昵称,是否直播
        $sql = "SELECT nick,uin,is_online FROM ".DB_NAME_IM.".`basic_tbl` WHERE uin IN (".$in_uin_str.")";
        $artist_nick_isonline = $this->group_mysql_db->get_results($sql, "ASSOC");
        
        foreach($artist_nick_isonline as $row){
			$artist_detail[$row['uin']]['uin']       = $row['uin'];
            $artist_detail[$row['uin']]['nick']      = $row['nick'];
            $artist_detail[$row['uin']]['hasplay']   = $row['is_online'];
        }
        
        //艺人观看人数、所在房间id
        $sql = "SELECT cu.`room_id`,cu.`uid` as uin,r.`curuser` FROM `kkyoo_new_rooms`.`rooms` AS r LEFT JOIN `g3_partner`.`channel_user` AS cu ON cu.`room_id` = r.`id` WHERE cu.`uid` IN (".$in_uin_str.") AND type=15";
        $artist_users_roomid = $this->group_mysql_db->get_results($sql, "ASSOC");
        
        foreach($artist_users_roomid as $row){
            $artist_detail[$row['uin']]['room_id'] = $row['room_id'];
            $artist_detail[$row['uin']]['curuser'] = $row['curuser'];
        }
        
        //读取缓存中的数据
        $has_cache_uin_arr   = array();
        foreach($uin_arr as $uin){
            $cache_data = $this->cache->get("artist_detail_uin_".$uin);
            if(!$cache_data){
                continue;
            }
            
            $has_cache_uin_arr[] = $uin;
            $cache_data_arr      = unserialize($cache_data);
            
            foreach($cache_data_arr as $key=>$value){
                $artist_detail[$uin][$key] = $value;
            }
        }
        $not_chace_uin_arr = array_diff($uin_arr, $has_cache_uin_arr);
        $in_uin_str        = join(",", $not_chace_uin_arr);
        
        if(!$not_chace_uin_arr){
            return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$artist_detail);
        }
        
        //艺人视频数
        $sql              = "SELECT COUNT(*) AS video_num, uin FROM ".DB_NAME_IM.".`video` WHERE uin IN (".$in_uin_str.") GROUP BY uin";
        $artist_video_num = $this->group_mysql_db->get_results($sql, "ASSOC");
        
        foreach($artist_video_num as $row){
            $artist_detail[$row['uin']]['video_num'] = $row['video_num'];
        }
        
        //艺人粉丝数
        $sql              = "SELECT COUNT(*) AS fans_num, following AS uin FROM ".DB_NAME_WEIBO.".follow WHERE following IN (".$in_uin_str.") GROUP BY following";
        $artist_fans_num  = $this->group_mysql_db->get_results($sql, "ASSOC");
        
        foreach($artist_fans_num as $row){
            $artist_detail[$row['uin']]['fans_num'] = $row['fans_num'];
        }
        
        //艺人等级图标和图标的名称
        $roleData = array(
			'extparam'=>array(
				'Tag'     => 'GetRole',
				'GroupId' => $group_id,
				'Uin'     => $not_chace_uin_arr,
				'Ruleid'  => $rule_id
			)
		);
		$roleInfo = httpPOST(ROLE_API_PATH,$roleData);
        
        foreach((array)$roleInfo['Roles'] as $row){
            $artist_detail[$row['uin']]['icon_name']        = $row['name'];
            $artist_detail[$row['uin']]['role_small_icon']  = $row['role_small_icon'];
        }
        
        //填补可能缺少数据
        $default_detail = array("video_num"=>0, 
                                "fans_num"=>0);
        foreach($artist_detail as $k=>$one){
            $artist_detail[$k] = array_merge($default_detail, $artist_detail[$k]);
        }
        
        //将数据缓存
        foreach($not_chace_uin_arr as $uin){
            $need_cache_data = array("video_num"        => $artist_detail[$uin]['video_num'],
                                     "fans_num"         => $artist_detail[$uin]['fans_num'],
                                     "icon_name"        => $artist_detail[$uin]['icon_name'],
                                     "role_small_icon"  => $artist_detail[$uin]['role_small_icon']
                                    );
            $this->cache->set("artist_detail_uin_".$uin, serialize($need_cache_data), $this->rank_expire);
        }
        
        return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$artist_detail);
    }
	
	//房间名称
	private function getRoomName($roomId){
		$roomId=intval($roomId);
		if($roomId<=0){
			return '';
		}
		$sql="SELECT name FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$roomId;
		$info=$this->group_mysql_db->get_row($sql,'ASSOC');
		return $info['name'];
	}
	
	//分页
	private function showPage($total,$perpage=20,$ajax=''){
		if($total>0){
			$page=new extpage(array (
				'total'=>$total,
				'perpage'=>$perpage,
				'ajax'=>$ajax
			));
			$page_arr['page']=$page->show();
			$page_arr['limit']=$page->limit();
			unset($page);
		}
		return $page_arr;
	}
	
}