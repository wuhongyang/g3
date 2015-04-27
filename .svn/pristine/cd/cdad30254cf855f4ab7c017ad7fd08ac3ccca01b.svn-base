$(function(){
		var string = $("#string");
		$("#MoneyWeight").keyup(function(){
			var reg = /^\d+$/;
			var num = $(this).attr("value");
			
			if($(this).attr("rate")){
				num = num*parseInt($(this).attr("rate"));
			}
			var pre = "";
			
			if(isNaN(num) || (num+"").indexOf(".") != -1){
				$(this).attr("value", $(this).attr("value").replace(/\D/g,''));	
				return false;
			}
			if(!num){
				string.html("零");
			}else{
				string.html(numberConvert(num));
			}
		})
		var d = "<OPTION value='-1'>请选择</OPTION>";
		var m = "<OPTION value='-1'>请选择</OPTION><OPTION value='107'>成本中心-V豆平衡调节净存入</OPTION><OPTION value='108'>成本中心-V豆平衡调节净支出</OPTION>";
		var k = "<OPTION value='-1'>请选择</OPTION><OPTION value='105'>成本中心-V宝平衡调节净存入</OPTION><OPTION value='106'>成本中心-V宝平衡调节净支出</OPTION>";	
		$("#type").change(function(){
			var select = $("#ChildId");
			switch($(this).attr("value")){
				case "-1":
					select.html(d);
					break;
				case "0":
					select.html(k);
					break;
				case "1":
					select.html(m);
					break;
			}
		})
	})