var kkyoo = {
	$ : function(objName){if(document.getElementById){return eval('document.getElementById("'+objName+'")')}else{return eval('document.all.'+objName)}},
	isIE : navigator.appVersion.indexOf("MSIE")!=-1?true:false,
	addEvent : function(obj,eventType,func){if(obj.attachEvent){obj.attachEvent("on" + eventType,func);}else{obj.addEventListener(eventType,func,false)}},
	delEvent : function(obj,eventType,func){if(obj.detachEvent){obj.detachEvent("on" + eventType,func)}else{obj.removeEventListener(eventType,func,false)}},
	readCookie : function(l){var i="",I=l+"=";if(document.cookie.length>0){offset=document.cookie.indexOf(I);if(offset!=-1){offset+=I.length;end=document.cookie.indexOf(";",offset);if(end==-1)end=document.cookie.length;i=unescape(document.cookie.substring(offset,end))}};return i},
	writeCookie : function(O,o,l,I){var i="",c="";if(l!=null){i=new Date((new Date).getTime()+l*3600000);i="; expires="+i.toGMTString()};if(I!=null){c=";domain="+I};document.cookie=O+"="+escape(o)+i+c}
};

/**
* 
* URL encode / decode 
* 注意，使用方法 Url.encode(string)
**/ 
 
var Url = {
	// public method for url encoding
	encode : function (string) {
        return escape(this._utf8_encode(string));
	},
	// public method for url decoding
	decode : function (string) {
	    return this._utf8_decode(unescape(string));
	},
	// private method for UTF-8 encoding 
	_utf8_encode : function (string) {
	    string = string.replace(/\r\n/g,"\n");
	    var utftext = "";
	    for (var n = 0; n < string.length; n++) {
	        var c = string.charCodeAt(n);
	        if (c < 128) {
	            utftext += String.fromCharCode(c);
	        }
	        else if((c > 127) && (c < 2048)) {
	            utftext += String.fromCharCode((c >> 6) | 192);
	            utftext += String.fromCharCode((c & 63) | 128);
	        }
	        else {
	            utftext += String.fromCharCode((c >> 12) | 224);
	            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
	            utftext += String.fromCharCode((c & 63) | 128);
	        }
	    }
	    return utftext;
	},
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            } 
        } 
        return string;
	} 
};

function Ajax(){
    var _domId = null;
    var _xmlHttp = null;
    var _isAsynchronism = true;
    var _successFunction = null;
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
        if(_isAsynchronism){
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
        }
    };
    this.ajaxRequest = function(_id, _url, _parameter, _method, _asynchronism, _backFunction){
        this.createXMLHttpRequest();
        _domId = _id;
        _isAsynchronism = _asynchronism;
        _successFunction = _backFunction;
        _xmlHttp.onreadystatechange = this.backFunction;
        if(_method.toLowerCase() == "post"){
            this.doPost(_url, _parameter);
        }
        else if(_method.toLowerCase() == "get"){
            this.doGet(_url, _parameter);
        }
    };
}

function loads(page,request,container){
	var type = 'get';
	if(request != '') type = 'post';

	new Ajax().ajaxRequest(
	container,
	page,
	request,
	type,
	true,
	function callBack(result){
			var html = result.responseText.split('|^_^|');
			kkyoo.$(container).innerHTML = html[0];
			if(html[1]) attachStyle(html[1]);
			if(html[2]) include_js(html[2]);
		}
	);
}