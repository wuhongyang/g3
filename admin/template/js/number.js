
//数字转换成"中文金额"
function numberConvert(currencyDigits) {
	//基本设置
	var MAXIMUM_NUMBER = 999999999999.99;
	var CN_ZERO = "零";
	digits = new Array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
	radices = new Array("", '十', '百', '千');
	bigRadices = new Array("", '万', '亿');
	// 后缀单位: 人民币、元、角、分、 整
	var CN_SYMBOL = "";
	var CN_DOLLAR = "";
	var CN_TEN_CENT = "";
	var CN_CENT = "";
	var CN_INTEGER = "";
	decimals = new Array(CN_TEN_CENT, CN_CENT);
	 
	// Variables:
	var integral; // Represent integral part of digit number. 
	var decimal; // Represent decimal part of digit number.
	var outputCharacters; // The output result.
	var parts;
	var digits, radices, bigRadices, decimals;
	var zeroCount;
	var i, p, d;
	var quotient, modulus;
 
	//验证输入值判断
	currencyDigits = currencyDigits.toString();
	if (currencyDigits == "") { //alert("Empty input!"); 
		return "";
	}
	if (currencyDigits.match(/[^,.\d]/) != null) { //alert("Invalid characters in the input string!");
		return "";
 	}
 	if ((currencyDigits).match(/^((\d{1,3}(,\d{3})*(.((\d{3},)*\d{1,3}))?)|(\d+(.\d+)?))$/) == null) { //alert("Illegal format of digit number!");
		return "";
	}
 
	// 输入值转换为数字
	currencyDigits = currencyDigits.replace(/,/g, ""); // Remove comma delimiters.
	currencyDigits = currencyDigits.replace(/^0+/, ""); // Trim zeros at the beginning. 
	// Assert the number is not greater than the maximum number.
	if (Number(currencyDigits) > MAXIMUM_NUMBER) { 
		alert("A large a number!");
		return "";
	}
	
	// 分隔小数和整数
	parts = currencyDigits.split(".");
	if (parts.length > 1) {
		integral = parts[0];
		decimal = parts[1];
  		// Cut down redundant decimal digits that are after the second.
  		decimal = decimal.substr(0, 2);
 	}else {
	  integral = parts[0];
	  decimal = "";
 	}
 	
	// Start processing:
	outputCharacters = "";
	//正整数部分
	if (Number(integral) > 0) {
		zeroCount = 0;
		for (i = 0; i < integral.length; i++) {
		   p = integral.length - i - 1;
		   d = integral.substr(i, 1);
		   quotient = p / 4;
		   modulus = p % 4;
		   if (d == "0") {
		     zeroCount++;
		   }else {
			   if (zeroCount > 0) {
				   outputCharacters += digits[0];
			    }
			    zeroCount = 0;
			    outputCharacters += digits[Number(d)] + radices[modulus];
			   }
			   if (modulus == 0 && zeroCount < 4) { 
				   outputCharacters += bigRadices[quotient];
			   }
			}
		outputCharacters += CN_DOLLAR;
	}
	//小数部分
	if (decimal != "") {
		for (i = 0; i < decimal.length; i++) {
			d = decimal.substr(i, 1);
			if (d != "0") {
				outputCharacters += digits[Number(d)] + decimals[i];
   			}
   		}
   	}
 	// 联合输出
 	if (outputCharacters == "") {
 	 	outputCharacters = CN_ZERO + CN_DOLLAR;
 	 }
	 if (decimal == "") {
		 outputCharacters += CN_INTEGER;
	 }
	 outputCharacters = CN_SYMBOL + outputCharacters;
	 return outputCharacters;
}//END func convertCurrency