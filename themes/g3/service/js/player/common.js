function thumb(obj){
	var lang = obj.lang;
	obj.lang = obj.src;
	obj.src = lang;
}

function initInput(obj){
	initSmileys('smileys_list','tbody');
	obj.style.height = '120px';
}

function initReInput(id){
	showid('relay-'+id);
	initSmileys('smileys-'+id,'tbody-'+id);
}

function showid(id){
	var id = document.getElementById(id);
	if(id.style.display == 'none' || id.style.display == ''){
		id.style.display = 'block';
	}else{
		id.style.display = 'none';
	}
}

//获取textarea的焦点并插入内容
function insertInFocus(myField, myValue) {
		var myField = document.getElementById(myField);
      if (document.selection) {
          // IE support 
          iRange.text = myValue;
      } else if (myField.selectionStart || myField.selectionStart == '0') {
          // MOZILLA/NETSCAPE support

          //起始位置
          var startPos = myField.selectionStart;

          //结束位置
          var endPos = myField.selectionEnd;

          //插入信息
          myField.value = myField.value.substring(0, startPos)
              + myValue
              + myField.value.substring(endPos, myField.value.length);
      } else {

          //没有焦点的话直接加在TEXTAREA的最后一位
          myField.value += myValue;
      }
}