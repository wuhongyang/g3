//倒计时回调
function rest(box,callback){
	var restTime = 60;
	var id = document.getElementById(box);
	if(id == undefined) return false;
	var num = parseInt(id.innerHTML);
	num = ( ! num)? restTime-1 : num-1;
	id.innerHTML = num;
	if(num == 0){
		callback();
		return true;
	}
	setTimeout(function(){rest(box,callback)},1000);
}

//显示隐藏ID
function showid(id){
	var show = document.getElementById(id);
	if( ! show) return false;
	show.style.display = (show.style.display=='none')? 'block' : 'none';
}

function showone(showid,hide){
	var show = document.getElementById(showid);
	var hide = document.getElementById(hide);
	show.style.display = 'block';
	hide.style.display = 'none';
}

//表单验证类
function checkInput(){
	this.inputPhone = false; //手机号码
	this.mailBox = document.getElementById('mails'); //邮箱容器对象
	this.mailInput = document.getElementById('email'); //输入邮箱
	this.mails = new Array('qq.com','163.com','126.com','gmail.com','sohu.com','yahoo.cn','hotmail.com','139.com','189.cn','wo.com.cn');
	this.mailsHtml = '';
	this.pregPhone = /^(13|15|18)\d{9}$/;
	this.pregMail = /^\w+@(\w+([._-][a-zA-Z]+))+$/;
	this.keySelectedStatus = 0; //键盘默认选择状态

//------------------------------手机验证----------------------------------------
	//验证手机号
	this.checkPhone = function(obj){
		if(this.pregPhone.exec(obj.value)){
			this.showError(obj,'正确',1);
			return this.inputPhone = obj.value;
		}else{
			this.showError(obj,'不正确',0);
			return this.inputPhone = false;
		}
	}

	//注册验证码
	this.sendRegCode = function(obj){
		if( ! this.inputPhone){
			return false;
		}
		new Ajax().ajaxRequest('index.php?user_phone&sendcode','nouser='+this.inputPhone,'post',
			function(rst){
				rst = (typeof JSON == 'object') ? JSON.parse(rst.responseText) : eval('('+rst.responseText+')');
				if(rst.Flag == 100){
					obj.disabled = true;
					obj.value = '校验码已发送';
					//把短信uniqueId记录下来
					if(document.getElementById('uniqueid')){
						var o = document.getElementById('uniqueid');
						document.getElementById('phone_reg').removeChild(o);
					}
					var input = document.createElement("input");
					input.type='hidden';
					input.name='uniqueid';
					input.id = 'uniqueid';
					input.value=rst.UniqueId;
					document.getElementById('phone_reg').appendChild(input);
				}else{
					alert(rst.FlagString);
				}
			},'',true);
		return true;
	}

	//改密码验证码
	this.sendPwdCode = function(obj){
		if( ! this.inputPhone){
			return false;
		}
		new Ajax().ajaxRequest('index.php?user_phone&sendcode','user='+this.inputPhone+'&module=找回密码','post',
			function(rst){
				rst = (typeof JSON == 'object') ? JSON.parse(rst.responseText) : eval('('+rst.responseText+')');
				if(rst.Flag == 100){
					obj.disabled = true;
					obj.value = '校验码已发送';
					//把短信uniqueId记录下来
					if(document.getElementById('uniqueid')){
						var o = document.getElementById('uniqueid');
						document.getElementById('gain_pwd').removeChild(o);
					}
					var input = document.createElement("input");
					input.type='hidden';
					input.name='uniqueid';
					input.id = 'uniqueid';
					input.value=rst.UniqueId;
					document.getElementById('gain_pwd').appendChild(input);
				}else{
					alert(rst.FlagString);
				}
			},'',true);
		return true;
	}

//------------------------------邮箱验证------------------------------------------------

	//验证邮箱
	this.checkMail = function(obj){
		var rst = this.pregMail.exec(obj.value);
		if( ! rst){
			this.showError(obj,'邮箱不正确',0);
			return false;
		}
		new Ajax().ajaxRequest('index.php?user_email&countmail='+obj.value,'','post',
		function(rst){
			if(rst.responseText == 0){
				reg.showError(obj,'正确',1);
				return true;
			}else if(rst.responseText == 1){
				reg.showError(obj,'用户已存在',0);
				return false;
			}else{
				reg.showError(obj,'检查异常',0);
				return false;
			}
		},'',true);
		return true;
	}

	//验证邮箱
	this.pwdCheckMail = function(obj){
		if( ! this.pregMail.exec(obj.value)){
			this.showError(obj,'邮箱不正确',0);
			return false;
		}
		new Ajax().ajaxRequest('index.php?user_email&countmail='+obj.value,'','post',
		function(rst){
			if(rst.responseText == 0){
				reg.showError(obj,'用户不存在',0);
				return true;
			}else if(rst.responseText == 1){
				reg.showError(obj,'正确',1);
				return false;
			}else{
				reg.showError(obj,'检查异常',0);
				return false;
			}
		},'',true);
		return true;
	}
	
	this.resendRegMail = function (obj){
		new Ajax().postForm(document.getElementById('resend'),
		function(rst){
			if(rst.responseText == 1){
				obj.onclick = '';
				showid('send-rest');
			}else{
				alert('发送失败，请重试或更换邮箱！');
			}
		});
		rest('send-num',function(){obj.onclick=function(){resend(obj)};showid('send-rest')});
	}

	//提示邮箱类型
	this.showMails = function (obj){
		this.hideMails();
		var user = obj.value;
		var user_arr = user.split('@');
		if( ! user ||  this.checkMail(obj)) return;
		this.mailBox.style.display = 'block';
		if(user.indexOf('@') == -1){
			for(var i = 0; i < this.mails.length; i++){
				this.mailBox.childNodes[i].innerHTML = user_arr[0]+'@'+this.mails[i];
			}
		}
		var bodys = document.getElementsByTagName('body');
		bodys[0].onclick =  function(){
			reg.hideMails(document.getElementById('email'));
		}
		bodys[0].onkeydown = function(event){
			var evt = event ? event : (window.event ? window.event : null);
			var selectedColor = '#E8F8FF';
			reg.keySelectedMails(evt,selectedColor);
		}
	}
	
	//初始化提示邮箱结点
	this.createMailBoxChilds = function (){
		for(var i = 0; i < this.mails.length; i++){
			this.mailsHtml += '<a onclick="reg.selectMail(this)"></a>';
		}
		this.mailBox.innerHTML = this.mailsHtml;
	}
	
	//键盘上下选择邮箱
	this.keySelectedMails = function (evt,selectedColor){
		var mailBox = this.mailBox;
		//键盘向上键选择
		if(evt.keyCode == 38){
			if(this.keySelectedStatus == 1){
				for(var i=0; i<mailBox.childNodes.length; i++){
					if(mailBox.childNodes[i].style.backgroundColor == this.colorConvert(selectedColor)){
						var selected = parseInt((parseInt(i)-1)%mailBox.childNodes.length);
						if(i == 0){
							selected = parseInt(mailBox.childNodes.length-1);
						}
						mailBox.childNodes[i].style.backgroundColor = '';
						mailBox.childNodes[selected].style.backgroundColor = selectedColor;
                        break;
					}
				}
			}else{
				mailBox.childNodes[0].style.backgroundColor = selectedColor;
                this.keySelectedStatus = 1;
			}
		}
		//键盘向下键选择
		if(evt.keyCode == 40){
			if(this.keySelectedStatus == 1){
				for(var i=0; i<mailBox.childNodes.length; i++){
					if(mailBox.childNodes[i].style.backgroundColor == this.colorConvert(selectedColor)){
						var selected = parseInt((parseInt(i)+1)%mailBox.childNodes.length);
						mailBox.childNodes[i].style.backgroundColor = '';
						mailBox.childNodes[selected].style.backgroundColor = selectedColor;
						break;
					}
				}
			}else{
				mailBox.childNodes[0].style.backgroundColor = selectedColor;
                this.keySelectedStatus = 1;
			}
		}
		//回车选中
		if(evt.keyCode == 13){
			for(var i=0; i<mailBox.childNodes.length; i++){
				if(mailBox.childNodes[i].style.backgroundColor == this.colorConvert(selectedColor)){
					reg.selectMail(mailBox.childNodes[i]);
					break;
				}
			}
		}
	}
	
	//颜色值转换
	this.colorConvert = function (hex){
		var ie = !+"\v1";
		if(ie){
			return hex.toLowerCase();
		}else{
			return this.hexToRGB(hex.toUpperCase());
		}
	}
	
	//RGB到16进制
	this.rgbToHex = function (r,g,b){
		var hexch = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F"];
		var rh,gh,bh,rl,gl,bl,rhex,ghex,bhex;
		r = Math.round(r);
		rl = r % 16;
		rh = Math.floor((r / 16)) % 16;
		g = Math.round(g);
		gl = r % 16;
		gh = Math.floor((g / 16)) % 16;
		b = Math.round(b);
		bl = r % 16;
		bh = Math.floor((b / 16)) % 16;
		rhex = hexch[rh] + hexch[rl];
		ghex = hexch[gh] + hexch[gl];
		bhex = hexch[bh] + hexch[bl];
		return ('#' + rhex + ghex + bhex);
	}
	
	//16进制到RGB
	this.hexToRGB = function (hex){
		var hexStr ="0123456789ABCDEF";
		var r = hexStr.indexOf(hex.charAt(1))*16 + hexStr.indexOf(hex.charAt(2));
		var g = hexStr.indexOf(hex.charAt(3))*16 + hexStr.indexOf(hex.charAt(4));
		var b = hexStr.indexOf(hex.charAt(5))*16 + hexStr.indexOf(hex.charAt(6));
		return ("rgb(" + r + ", " + g + ", " + b + ")");
	}

	//隐藏邮箱提示
	this.hideMails = function (){
		//this.mailBox.innerHTML = '';
		this.mailBox.style.display = 'none';
	}

	//选择邮箱类型
	this.selectMail = function (obj){
		this.mailInput.value = obj.innerHTML;
		this.hideMails();
		this.checkMail(this.mailInput);
	}
	
	this.goLoginMail = function(email){
		var url = email.replace(/\w+@/,'mail.');
		location.href='http://'+url;
	}
	
//-----------------------------其他表单验证-------------------------------------------
	this.noempty = function(obj){
		if(obj.value == ''){
			this.showError(obj,'不能为空',0);
			return false;
		}else{
			this.showError(obj,'正确',1);
			return true;
		}
	}
	
	this.password = function(obj){
		if(obj.value.length >= 6){
			this.showError(obj,'正确',1);
		}else{
			this.showError(obj,'不能少于6位',0);
		}
	}

	this.repassword = function(obj){
		var pwd = document.getElementById('password');
		if(pwd.value !== obj.value){
			this.showError(obj,'密码不一致',0);
			return false;
		}else if(obj.value.length < 6){
			this.showError(obj,'不能少于6位',0);
			return false;
		}else{
			this.showError(obj,'正确',1);
			return true;
		}
	}

	this.showError = function(input,msg,code){
		code = code? 'ok' : 'error';
		var childs = input.parentNode.childNodes;
		for(i=0; i<childs.length; i++){
			if(childs[i].nodeName=='SPAN'){
				childs[i].setAttribute('class',code);
				childs[i].innerHTML = ' '+msg;
			}
		}
		input.title = code;
	}

	this.onsubmit = function(obj){
		for(i=0; i < obj.length; i++){
			if(obj[i].title == 'error'){
				 return false;
			}
			if(obj[i].type=='submit') var btn = obj[i];
		}
		md5id = new Array('oldpwd','password');
		for(i=0;i<md5id.length;i++){
			var pwd = document.getElementById(md5id[i]);
			if(pwd && pwd.value != '') pwd.value = hex_md5(pwd.value);
		}
		btn.disabled=false;
		return true;
	}

	this.getCheckCode = function(n){
		var ids = document.getElementById('checkcodes');
		ids.innerHTML = '<img src="checkcode.php?'+n+'" border="0" onclick="reg.getCheckCode('+parseInt(Math.random()*100+1)+')" title="看不清? 请点击" />';
	}
	
	this.checkCode = function(obj){
		if(obj.value==''){
			reg.showError(obj,'不能为空',0);
			return false;
		}
		new Ajax().ajaxRequest('index.php?user_email&checkcode','checkcode='+obj.value,'post',
			function(rst){
				if(rst.responseText == 0){
					reg.showError(obj,'验证码错误',0);
					return true;
				}else if(rst.responseText == 1){
					reg.showError(obj,'正确',1);
					return false;
				}
			},'',true);
		return true;
	}
	
}

var reg = new checkInput();

//AJAX
function Ajax(){
    var _domId = null;
    var _xmlHttp = null;
    var _isAsynchronism = true;
    var _successFunction = null;
	
	this.postForm = function (frm,callback){
		var e;
		var arr=[];
		for(var i=0;i<frm.length;++i){
			e=frm[i];
			if(!e.name || e.disabled){
				continue;
			}
			if(e.type=='select-one'){
				arr.push(encodeURIComponent(e.name)+'='+encodeURIComponent(e.options[e.selectedIndex].value));
			}else if(e.type=='select-multiple'){
				for(var j=0;j<e.length;++j){
					if(e.options[j].selected){
						arr.push(encodeURIComponent(e.name)+'='+encodeURIComponent(e.options[j].value));
					}
				}
			}else if(e.type=='checkbox' || e.type=='radio'){
				if(e.checked){
					arr.push(encodeURIComponent(e.name)+'='+encodeURIComponent(e.value));
				}
			}else if(typeof(e.value)!='undefined'){
				arr.push(encodeURIComponent(e.name)+'='+encodeURIComponent(e.value));
			}
		}
		this.ajaxRequest(frm.action,arr.join('&'),'post',callback,'',true);
	}
	
    this.doPost = function(_url, _parameter){
        try{
            _xmlHttp.open("POST", _url, _isAsynchronism);
            _xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            _xmlHttp.send(_parameter);
        } catch(e){
            alert(e.name + " : " + e.message);
        }
    };

    this.doGet = function(_url, _parameter){
        try{
            var _random = Math.round(Math.random() * 10000);
            var _getParameter = (_url + "?random=" + _random + "&" + _parameter);
            if(_getParameter.length > 255) {
                this.doPost(_url, _parameter);
            } else{
                _xmlHttp.open("GET", _getParameter,_isAsynchronism);
                _xmlHttp.send(null);
            }
        } catch(e){
            alert(e.name + " : " + e.message);
        }
    };

    this.backFunction = function(){
        if(_successFunction == null){
            return;
        }
		if(_domId){
			switch(_xmlHttp.readyState){
				case 1:
					kkyoo.$(_domId).innerHTML = '<samp>请稍候，正在建立连接...</smap>';
					break;
				case 2:
					kkyoo.$(_domId).innerHTML = '<samp>请稍候，正在发送数据...</smap>';
					break;
				case 3:
					kkyoo.$(_domId).innerHTML = '<samp>请稍候，正在接收数据...</smap>';
					break;
			}
		}
		if((_xmlHttp.readyState == 4) && (_xmlHttp.status == 200)){
			_successFunction(_xmlHttp);
			return;
		}
    };

    this.ajaxRequest = function( _url,_parameter,_method,_backFunction,_id,_asynchronism){
        this.createXMLHttpRequest();
        _domId = _id;
        _isAsynchronism = _asynchronism;
        _successFunction = _backFunction;
        _xmlHttp.onreadystatechange = this.backFunction;
        if(_method.toLowerCase() == "get"){
        	this.doGet(_url, _parameter);
        }else{
        	this.doPost(_url, _parameter);
        }
    };
	
    this.createXMLHttpRequest = function(){
        try{
            if(window.ActiveXObject){
                _xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } else {
                if(window.XMLHttpRequest){
                    _xmlHttp = new XMLHttpRequest();
                } else{
                    _xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                }
            }
        } catch(e){
            alert(e.name + " : " + e.message);
        }
    };
}

//md5
var hexcase=0;function hex_md5(a){return rstr2hex(rstr_md5(str2rstr_utf8(a)))}function hex_hmac_md5(a,b){return rstr2hex(rstr_hmac_md5(str2rstr_utf8(a),str2rstr_utf8(b)))}function md5_vm_test(){return hex_md5("abc").toLowerCase()=="900150983cd24fb0d6963f7d28e17f72"}function rstr_md5(a){return binl2rstr(binl_md5(rstr2binl(a),a.length*8))}function rstr_hmac_md5(c,f){var e=rstr2binl(c);if(e.length>16){e=binl_md5(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binl_md5(a.concat(rstr2binl(f)),512+f.length*8);return binl2rstr(binl_md5(d.concat(g),512+128))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binl(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(c%32)}return a}function binl2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(c%32))&255)}return a}function binl_md5(p,k){p[k>>5]|=128<<((k)%32);p[(((k+64)>>>9)<<4)+14]=k;var o=1732584193;var n=-271733879;var m=-1732584194;var l=271733878;for(var g=0;g<p.length;g+=16){var j=o;var h=n;var f=m;var e=l;o=md5_ff(o,n,m,l,p[g+0],7,-680876936);l=md5_ff(l,o,n,m,p[g+1],12,-389564586);m=md5_ff(m,l,o,n,p[g+2],17,606105819);n=md5_ff(n,m,l,o,p[g+3],22,-1044525330);o=md5_ff(o,n,m,l,p[g+4],7,-176418897);l=md5_ff(l,o,n,m,p[g+5],12,1200080426);m=md5_ff(m,l,o,n,p[g+6],17,-1473231341);n=md5_ff(n,m,l,o,p[g+7],22,-45705983);o=md5_ff(o,n,m,l,p[g+8],7,1770035416);l=md5_ff(l,o,n,m,p[g+9],12,-1958414417);m=md5_ff(m,l,o,n,p[g+10],17,-42063);n=md5_ff(n,m,l,o,p[g+11],22,-1990404162);o=md5_ff(o,n,m,l,p[g+12],7,1804603682);l=md5_ff(l,o,n,m,p[g+13],12,-40341101);m=md5_ff(m,l,o,n,p[g+14],17,-1502002290);n=md5_ff(n,m,l,o,p[g+15],22,1236535329);o=md5_gg(o,n,m,l,p[g+1],5,-165796510);l=md5_gg(l,o,n,m,p[g+6],9,-1069501632);m=md5_gg(m,l,o,n,p[g+11],14,643717713);n=md5_gg(n,m,l,o,p[g+0],20,-373897302);o=md5_gg(o,n,m,l,p[g+5],5,-701558691);l=md5_gg(l,o,n,m,p[g+10],9,38016083);m=md5_gg(m,l,o,n,p[g+15],14,-660478335);n=md5_gg(n,m,l,o,p[g+4],20,-405537848);o=md5_gg(o,n,m,l,p[g+9],5,568446438);l=md5_gg(l,o,n,m,p[g+14],9,-1019803690);m=md5_gg(m,l,o,n,p[g+3],14,-187363961);n=md5_gg(n,m,l,o,p[g+8],20,1163531501);o=md5_gg(o,n,m,l,p[g+13],5,-1444681467);l=md5_gg(l,o,n,m,p[g+2],9,-51403784);m=md5_gg(m,l,o,n,p[g+7],14,1735328473);n=md5_gg(n,m,l,o,p[g+12],20,-1926607734);o=md5_hh(o,n,m,l,p[g+5],4,-378558);l=md5_hh(l,o,n,m,p[g+8],11,-2022574463);m=md5_hh(m,l,o,n,p[g+11],16,1839030562);n=md5_hh(n,m,l,o,p[g+14],23,-35309556);o=md5_hh(o,n,m,l,p[g+1],4,-1530992060);l=md5_hh(l,o,n,m,p[g+4],11,1272893353);m=md5_hh(m,l,o,n,p[g+7],16,-155497632);n=md5_hh(n,m,l,o,p[g+10],23,-1094730640);o=md5_hh(o,n,m,l,p[g+13],4,681279174);l=md5_hh(l,o,n,m,p[g+0],11,-358537222);m=md5_hh(m,l,o,n,p[g+3],16,-722521979);n=md5_hh(n,m,l,o,p[g+6],23,76029189);o=md5_hh(o,n,m,l,p[g+9],4,-640364487);l=md5_hh(l,o,n,m,p[g+12],11,-421815835);m=md5_hh(m,l,o,n,p[g+15],16,530742520);n=md5_hh(n,m,l,o,p[g+2],23,-995338651);o=md5_ii(o,n,m,l,p[g+0],6,-198630844);l=md5_ii(l,o,n,m,p[g+7],10,1126891415);m=md5_ii(m,l,o,n,p[g+14],15,-1416354905);n=md5_ii(n,m,l,o,p[g+5],21,-57434055);o=md5_ii(o,n,m,l,p[g+12],6,1700485571);l=md5_ii(l,o,n,m,p[g+3],10,-1894986606);m=md5_ii(m,l,o,n,p[g+10],15,-1051523);n=md5_ii(n,m,l,o,p[g+1],21,-2054922799);o=md5_ii(o,n,m,l,p[g+8],6,1873313359);l=md5_ii(l,o,n,m,p[g+15],10,-30611744);m=md5_ii(m,l,o,n,p[g+6],15,-1560198380);n=md5_ii(n,m,l,o,p[g+13],21,1309151649);o=md5_ii(o,n,m,l,p[g+4],6,-145523070);l=md5_ii(l,o,n,m,p[g+11],10,-1120210379);m=md5_ii(m,l,o,n,p[g+2],15,718787259);n=md5_ii(n,m,l,o,p[g+9],21,-343485551);o=safe_add(o,j);n=safe_add(n,h);m=safe_add(m,f);l=safe_add(l,e)}return Array(o,n,m,l)}function md5_cmn(h,e,d,c,g,f){return safe_add(bit_rol(safe_add(safe_add(e,h),safe_add(c,f)),g),d)}function md5_ff(g,f,k,j,e,i,h){return md5_cmn((f&k)|((~f)&j),g,f,e,i,h)}function md5_gg(g,f,k,j,e,i,h){return md5_cmn((f&j)|(k&(~j)),g,f,e,i,h)}function md5_hh(g,f,k,j,e,i,h){return md5_cmn(f^k^j,g,f,e,i,h)}function md5_ii(g,f,k,j,e,i,h){return md5_cmn(k^(f|(~j)),g,f,e,i,h)}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};