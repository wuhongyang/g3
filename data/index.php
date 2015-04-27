<?php
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set("Asia/Shanghai");
$starttime = microtime(true);
require_once dirname(__FILE__).'/library/global.fun.php';

//检查参数
if(isset($_POST['parameter'])){
    $parameter = $_POST['parameter'];
}elseif(isset($_GET['parameter'])){
    $parameter = $_GET['parameter'];
}else{
    $parameter = file_get_contents('php://input');
}
$request = json_decode($parameter,true);
if(!is_array($request)){
    $data = array('Flag'=>1002,'FlagString'=>'参数错误');
}else{
    //初始化参数
    $extparam  = $request['extparam'];
    $param = $request['param'];

    $results = httpPOST(CCS_API_PATH,array('param'=>$param,'extparam'=>array('Tag' => 'GetBusinessConfig'))); //取得业务科目配置

    //检查业务科目
    if($results['Flag'] != 100){
        $data = array('Flag'=>1001,'FlagString'=>$results['FlagString']);
    }else{
        //合并业务配置
        $business_config = $results['Result'];
        $request['param'] = array_merge($request['param'],$business_config);
		
        //登录授权
        if($business_config['is_auth']){
			$login_param = array('param'=>array('SessionKey'=>$param['SessionKey']),'extparam'=>array('Tag'=>'GetLogin'));
			if($business_config['bigcase_id'] == 10002){
				$login_param['param']['GroupId'] = 10000;
			}else{
				$login_param['param']['GroupId'] = $request['param']['GroupId'];
			}
            $user_login = httpPOST(SSO_API_PATH,$login_param);
            if($user_login['Flag'] != 100){
                $data = $user_login;
            }else{
                //合并用户登录信息
                $request['param'] = array_merge($request['param'],$user_login);

                //如果是后台接口
                if($business_config['bigcase_id'] == 10002){
                    $request['BusinessConfig'] = $business_config;
                    $data = httpPOST('api/admin/admin_api.php',$request);
                }else{
                    //权限控制
					domain::main()->SetGroupId($param);
					if($business_config['is_power']){
                        $data = httpPOST(CCS_API_PATH,array('param'=>$param,'extparam'=>array('Tag'=>'CheckUserPower')));
                        if($data['Flag'] == 100){
                            $power_status = true;
                        }
                    }else{
                        $power_status = true;
                    }
                    if($power_status && !empty($business_config['api'])){
                        $data = httpPOST($business_config['api'],$request);
                    }
                }
            }
        }else{
            //如果是后台接口
            if($business_config['bigcase_id'] == 10002){
                $request['BusinessConfig'] = $business_config;
                $data = httpPOST('api/admin/admin_api.php',$request);
            }else{
                //权限控制
				domain::main()->SetGroupId($param);
                if($business_config['is_power']){
                    $data = httpPOST(CCS_API_PATH,array('param'=>$param,'extparam'=>array('Tag'=>'CheckUserPower')));
                    if($data['Flag'] == 100){
                        $power_status = true;
                    }
                }else{
                    $power_status = true;
                }
                if($power_status && !empty($business_config['api'])){
                    $data = httpPOST($business_config['api'],$request);
                }
            }
        }
        //当未绑定接口或者非权限控制或接口返回空时，手动给data赋值Flag
        if(empty($data) && $business_config['is_log']){
            $data['Flag'] = 100;
			$data['LogData'][] = getLogData($param,$extparam);
        }
        
        /* 行为日志记录 */
		if(!empty($data['LogData']) && is_array($data['LogData'])){//业务接口里有返回LogData时
			$business_config['is_log'] = 1;
			$logs = $data['LogData'];
			$ext_log = getExtLogData($logs[0]['param'],$logs[0]['extparam']);
			unset($data['LogData']);
		}else{//业务接口里无返回LogData时
			if($data['Uin'])
				$request['param']['Uin'] = $data['Uin'];

			if(empty($request['param']['TargetUin']))
				$request['param']['TargetUin'] = $request['param']['Uin'];

			if(empty($request['param']['ChannelId']))
				$request['param']['ChannelId'] = 0;

			if(empty($request['param']['MoneyWeight']))
				$request['param']['MoneyWeight'] = 0;

			if(empty($request['param']['DoingWeight']))
				$request['param']['DoingWeight'] = 0;

			if(empty($request['param']['Desc']))
				$request['param']['Desc'] = $request['param']['child_name'];

			$logs[] = array(
				'param'=>array(
					'Uin'=>(int)$request['param']['Uin'],
					'TargetUin'=>(int)$request['param']['TargetUin'],
					'ChannelId'=>(int)$request['param']['ChannelId'],
					'BigCaseId'=>(int)$request['param']['BigCaseId'],
					'CaseId'=>(int)$request['param']['CaseId'],
					'ParentId'=>(int)$request['param']['ParentId'],
					'ChildId'=>(int)$request['param']['ChildId'],
					'MoneyWeight'=>(int)$request['param']['MoneyWeight'],
					'DoingWeight'=>(int)$request['param']['DoingWeight'],
					'Desc'=>(string)$request['param']['Desc'],
					'GroupId'=>(int)$request['param']['GroupId']
				),
				'extparam'=>$extparam
			);
			$ext_log = getExtLogData($request['param'],$request['extparam']);
		}
		
		
		//日志数据整理
		foreach((array)$logs as $key => $log){
			$log_y['param']['uptime'] = time();
			$log_y['param']['Uin'] = (int)$log['param']['Uin'];
			$log_y['param']['TargetUin'] = (int)$log['param']['TargetUin'];
			$log_y['param']['ChannelId'] = (int)$log['param']['ChannelId'];
			$log_y['param']['BigCaseId'] = (int)$log['param']['BigCaseId'];
			$log_y['param']['CaseId'] = (int)$log['param']['CaseId'];
			$log_y['param']['ParentId'] = (int)$log['param']['ParentId'];
			$log_y['param']['ChildId'] = (int)$log['param']['ChildId'];
			$log_y['param']['MoneyWeight'] = (int)$log['param']['MoneyWeight'];
			$log_y['param']['DoingWeight'] = (int)$log['param']['DoingWeight'];
			$log_y['param']['GroupId'] = (int)$log['param']['GroupId'];
			$log_y['param']['Desc'] = (string)$log['param']['Desc'];
			$log_data[$key]['param'] = array_merge($log_y['param'],(array)$ext_log['param']);
			$log_data[$key]['extparam'] = $log['extparam'];
		}
		/* 行为日志记录 */
		$log_data['LogStatus'] = array('Flag'=>$data['Flag'],'is_log'=>$business_config['is_log']);
		/* 质量数据 */
		$runtime = round((microtime(true) - $starttime) * 1000, 3);
		$log_data['QualityData'] = array('platform'=>$_SERVER['HTTP_HOST'],'id'=>'webApi','state'=>false,'data'=>array('url'=>$_SERVER['HTTP_HOST'],'mypid'=>getmypid(),'bigcase_id'=>$param['BigCaseId'],'case_id'=>$param['CaseId'],'parent_id'=>$param['ParentId'],'child_id'=>$param['ChildId'],'callTime'=>$runtime));
		/* 质量数据 */
		
		$redis = cache::connect(config('cache','redis'),'redis');
		$redis->rpush('service',json_encode($log_data));
		//socket_request(REDIS_API_PATH.'/?cmd='.urlencode(json_encode($log_data)));
    }
}
echo json_encode($data);