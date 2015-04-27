//json包括json.Flag和json.FlagString
//scallback为json.Flag=100的时候的回调
//_show_msg在source/js/common.js中,所以要先引入该文件
function callback(json,scallback){
    if(json.Flag == 100){
        art.dialog({
            content: json.FlagString,
            ok: scallback,
            lock: true,
            cancel: false,
            esc: false,
            icon: 'succeed'
        });
    }else{
         _show_msg(json.FlagString);
    }
}

function oper(module,data,scallback){
	$.ajax({
        url: '?module='+module,
        type: 'POST',
        data: data,
        dataType: 'JSON',
        success: function(data){
            callback(data,scallback);
        }
    });
}