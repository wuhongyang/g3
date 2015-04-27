//验证昵称
function checkNick()
{
	var result1 = 1;
	var nickLength = $('#nick').val().length;
	if( nickLength > 0 )
	{
		if( nickLength >= 2 && nickLength <= 16 )

			$('#cnick').html('');
		else 
		{
			$('#cnick').html('*昵称长度在2-16之间,请符合要求填写');
			result1 = 0;
		}
	}
	else 
	{
		$('#cnick').html('*昵称长度在2-16之间,请符合要求填写');
		result1 = 0;
	}
	return result1;

}

//验证姓名
function checkName()
{
	var result2 = 1;
	var nameValue = $('#name').val();
	var nameLength = $('#name').val().length;
		if( nameLength >= 2 && nameLength <= 8 )
		{
			//判断输出的是否为中文
			var reg = /[^\u4E00-\u9FA5]/g;
			var pregResult = nameValue.match(reg);
			if( pregResult )
			{
				$('#cname').html('*姓名只能输入中文');
				$('#cname').attr('class','red');
				result2 = 0;
			}
			else
			{
				$('#cname').html('');
			}
		}
		else 
		{
			$('#cname').html(' *姓名长度在2-8之间,请符合要求填写');
			$('#cname').attr('class','red');
			result2 = 0;
		}
	return result2;
}

//验证身份证号码
function checkCard()
{
	var result3 = 1;
	var cardLength = $('#idcard').val().length;
		if( cardLength == 15 || cardLength == 18 )
			$('#cidcard').html('');
		else
		{
			$('#cidcard').html(' *身份证号码15位或18位,请符合要求填写');
			$('#cidcard').attr('class','red');
			result3 = 0;
		}
	return result3;
}
//提交前验证一次
function checkAll()
{
	var c1 = checkNick();
	var c2 = checkName();
	var c3 = checkCard();
	if( c1 == 1 && c2 == 1 && c3 == 1 )
	{
		return true;
	}
	else 
	{
		return false;
	}
}
//验证用户兴趣爱好
function checkHobby()
{
	var hobby = $('#hobby').val();
	var hobbyLength = hobby.length;
	/*
	alert(hobbyLength);
	return false;
	*/
	var reg = /\d+/;
	if( hobbyLength < 1 )
	{
		alert('输入不能为空');
		return false;
	}
	if( hobbyLength > 10 )
	{
		alert('输入小于10个字');
		$('#hobby').val('');
		return false;
	}
}

//城市添加到隐藏域
function  addCity(cityid)
{
	if( cityid != '-1' )
	{
		var orival = $('#cityname').val();
		var a = ('#province').find(' > option').text();
		alert(a);
	}
}
function getCityName(cityid,state){

	if( state == '1')
	{
		if( cityid != '-1' )
		{
			var val = $("#province > option:selected").html();
			v = $('#city_name').val(val);
		} else 
		{
			$('#city_name').val('');
		}
	} 
	else if ( state == '2' )
	{
		if( cityid != '-1' )
		{
			var val = $("#city > option:selected").html();
			val =$("#province > option:selected").html() + ' ' + val ;
		} else 
		{
			var val = $("#province > option:selected").html();
		}
		$('#city_name').val(val);
	}
	else 
	{
		if( cityid != '-1' )
		{
			var val = $("#area > option:selected").html();
			val = $("#province > option:selected").html() + ' ' + $("#city > option:selected").html() + ' ' + val;
		} else 
		{
			val = $("#province > option:selected").html() + ' ' + $("#city > option:selected").html()
		}
		$('#city_name').val(val);
	}
}
//修改资料提交
function formsubmit(){
	var uid = $('#userid').val();
	var nick = $('#nick').val();
	var province = $('#province').val();
	var city = $('#city').val();
	var area = $('#area').val();
	var cityname = $('#city_name').val();
	var gender = $('#gender').val();
	var birthday = $('#birthday').val();
	var usersign = $('#usersign').val();
	var send = {uin:uid,nick:nick,province:province,city:city,area:area,cityname:cityname,gender:gender,birthday:birthday,usersign:usersign};
	var url = "personal.php?module=edituser";
	//验证城市
	if( province < 1 || city < 1 || area < 1 )
	{
		$('#ccity').html('*请选择好区域');
		return 0;
	}
	else
	{
		$('#ccity').html('');
	}
	//验证昵称
	if( !checkNick() )
		return 0;
	//验证个人简介长度
	if( usersign.length > 200 )
	{
		$('#cusersign').html(' *简介长度小于200个字');
		return 0;
	}
	$.post(url,send,function(d){
		if( d == '100' )
		{
			sAlert('<img src=../themes/g3/service/images/caozuochenggong.png />');
		}
		else
		{
			alert('更新失败');
		}
	});
}
//修改联系方式
function submitdeltail(){
	var uin = $('#uin').val();
	var userqq = $('#userqq').val();
	var usermsn = $('#usermsn').val();
	var detailadd = $('#detailadd').val();
	var userweibo = $('#userweibo').val();
	var userboke = $('#userboke').val();
	//验证QQ
	if( !checkqq(userqq) && userqq.length > 0)
	{
		$('#cuserqq').html(' *QQ号码填写不正确,长度在4-12位');
		return false;
	}
	else
		$('#cuserqq').html('');
	//验证msn
	if( !checkmsn(usermsn) && usermsn.length > 0)
	{
		$('#cusermsn').html(' *MSN填写地址不正确');
		return false;
	}
	else
		$('#cusermsn').html('');

	//验证微博地址和博客地址
	if( !checkwww(userweibo) && userweibo.length > 0 )
	{
		$('#cuserweibo').html(' *填写的微博网址不正确');
		return false;
	}
	else
		$('#cuserweibo').html('');

	if( !checkwww(userboke) && userboke.length > 0 )
	{
		$('#cuserboke').html(' *填写的博客网址不正确');
		return false;
	}
	else
		$('#cuserboke').html('');

	var send = {uin:uin,userqq:userqq,usermsn:usermsn,detailadd:detailadd,userweibo:userweibo,userboke:userboke,connect:'1'};
	var url = "personal.php?module=edituser";
	$.post(url,send,function(d){
		if( d == '100' )
		{
			sAlert('<img src=../themes/g3/service/images/caozuochenggong.png />');
			$('#cuserqq').html('');
		}
		else
		{
			alert('修改失败');
		}
	});
}
//QQ验证
function checkqq(qq)
{
	return qq.match(/^[1-9]\d{4,14}$/);
}
//验证msn
function checkmsn(msn)
{
	return msn.match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/);
}
//验证网址
function checkwww(www)
{
	return www.match(/^((http(s)?|ftp|telnet|news|rtsp|mms):\/\/)?(((\w(\-*\w)*\.)+[a-zA-Z]{2,4})|(((1\d\d|2([0-4]\d|5[0-5])|[1-9]\d|\d).){3}(1\d\d|2([0-4]\d|5[0-5])|[1-9]\d|\d).?))(:\d{0,5})?(\/+.*)*$/);
}
