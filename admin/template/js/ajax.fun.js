/* @package Ajax类
 * @author 阿德
 * @version 0.1
 * @dependence 09.12
 * @link http://www.zzlabs.com.cn
 *  
 * domId       = 'result';   
 * url            = 'result.php';   
 * parameter  = 'id='+id;   
 * method      = 'get';    //传输方式   
 * asynchronism = true      //是否异步   
 * new Ajax().ajaxRequest(domId,url,parameter,method,asynchronism,function callBack(result){$('result').innerHTML = result.responseText;});   
 */

if ($ == undefined){
    var $ = function(id){return document.getElementById(id);}
}

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
        //if(_isAsynchronism){
            if(_domId){
                switch(_xmlHttp.readyState){
                    case 1:
                        $(_domId).innerHTML = '<samp>请稍候，正在建立连接...</smap>';
                        break;
                    case 2:
                        $(_domId).innerHTML = '<samp>请稍候，正在发送数据...</smap>';
                        break;
                    case 3:
                        $(_domId).innerHTML = '<samp>请稍候，正在接收数据...</smap>';
                        break;
                }
            }
            if((_xmlHttp.readyState == 4) && (_xmlHttp.status == 200)){
                _successFunction(_xmlHttp);
                return;
            }
        //}
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