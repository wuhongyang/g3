<?php
class Global_api
{
    public function UinLogin(){
	    $loginuser = httpPOST(SSO_API_PATH,array('param'=>array('SessionKey'=>$this->param['SessionKey']),'extparam'=>array('Tag'=>'GetLogin')));
		return $loginuser;
    }
    
    public function UinInfo(){
	    $loginuser = httpPOST(SSO_API_PATH,array('param'=>$this->param,'extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$this->extparam['Uin'])));
        $result = array(
			'Flag'=>100,
			'FlagString'=>'ok',
			'Nick'=>$loginuser['baseInfo']['nick'],
			'Gender'=>$loginuser['baseInfo']['gender'],
			'Age'=>$loginuser['baseInfo']['age'],
		);
		return $result;
    }
    
    public function GetMoneyData(){
		$money = httpPOST(KMONEY_API_PATH,array('extparam'=>array('Tag'=>'GetKmoneyBalance','Uin'=>$this->param['Uin'],'GroupId'=>$this->extparam['GroupId'])));
    	return $money;
	}
    
    public function ChangeMoneyToGameFromKTV(){
		$extparam = array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$this->extparam['GroupId']);
		$request = array('param'=>$this->param,'extparam'=>$extparam);
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		$array = array('Flag'=>100,'Balance'=>$rst['LastBalance'],'MoneyWeight'=>$this->param['MoneyWeight'],'Desc'=>$this->param['Desc']);
		if($rst['fund_type'] == 'Kmoney'){
			return $array;
		}else{
			$array['VoucherBalance'] = $rst['LastBalance'];
			unset($array['Balance']);
			if(isset($rst['KmoneyBalance'])) $array['Balance'] = $rst['KmoneyBalance'];
		}
		return $array;
    }

    public function GetParentBalance(){
        $money = get_parent_money($this->param['BigCaseId'],$this->param['CaseId'],$this->param['ParentId'],$this->extparam['GroupId']);
        return array('Flag'=>100,'FlagString'=>'ok','ParentBalance'=>$money);
    }

}

$param = $_POST;
$g = new Global_api();
$g->param = $param['param'];
$g->extparam = $param['extparam'];
$tag = $param['extparam']['Tag'];
echo json_encode($g->$tag());
