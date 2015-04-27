/**
 * Created with JetBrains WebStorm.
 * User: chenwei
 * Date: 12-8-15
 * Time: 下午5:40
 * 购买道具----填写购买信息脚本
 */
    (function () {
        $(function () {
			var nickShow = 1;
			
			//初始化
			if($('input[name="who"]:checked').val() == 2){
				_get_user();
			}
			
        	//显示好友输入框
            $('input[name="who"]').click(function(){
            	if($(this).attr('isChecked') != 1){
            		$('input[name="who"]').attr('isChecked',0);
            		$(this).attr('isChecked',1);
	            	if($(this).val() == 1){
	            		$('#J_other').hide();
	            	}else{
	            		$('#J_other').show();
	            	}
            	}
            });
			
			//重置信号
			$('#other_uin').focus(function(){
				nickShow = 1;
			});
			
            //得到好友昵称
            $('#other_uin').blur(function(){
            	_get_user();
            });
			
			function _get_user(){
				var uin = $('#other_uin').val();
            	if(uin!='' && !isNaN(uin)){
            		getUserNick(uin,_hander_uins);
            	}
			}

            function _hander_uins(data){
            	if(data != '|'){
					nickShow = 3;
					$('#J_nick').html(data.split('|')[1]);
            	}else{
					nickShow = 2;
				}
            }


            //提交表单
            $('#J_submit').click(function(){				
            	var payMoney = parseInt($('#J_pay_money').html(),10);
            	var myWealth = parseInt($('#J_my_wealth').html(),10);
            	var diff = payMoney - myWealth;

            	var who = $('input[name="who"]:checked').val();
            	if(who == 2){
					var other_uin = $('#other_uin').val();
            		if(other_uin=='' || !/^[-]?\d+$/.test(other_uin)){
            			if(typeof art=='function' && typeof art.dialog=='function'){
            				art.dialog({
            					content: '好友ID必须为数字',
            					lock: true,
            					icon: 'warning',
            					cancel: false,
            					esc: false,
            					time: 2
            				});
            			}else{
            				alert('好友ID必须为数字');
            			}
            			return false;
            		}
					if(nickShow == 1){
						if(typeof art=='function' && typeof art.dialog=='function'){
							art.dialog({
								content: '正在为您检测数据，请稍后……',
								lock: true,
								time: 3
							});
						}else{
							alert('正在为您检测数据，请稍后……');
						}
						return false;
					}else if(nickShow == 2){
						if(typeof art=='function' && typeof art.dialog=='function'){
							art.dialog({
								content: '好友ID有误，请重新输入',
								lock: true,
								icon: 'warning',
								esc: false,
								time: 3
							});
						}else{
							alert('好友ID有误，请重新输入');
						}
						return false;
					}
            	}
				
				var num = $('#J_num_again').val();
				if(num=='' || !/^[-]?\d+$/.test(num)){
					if(typeof art=='function' && typeof art.dialog=='function'){
						art.dialog({
							content: '购买数量只能在1到9999之间',
							icon: 'warning',
							lock: true,
							ok: true,
							cancel: false
						});
					}else{
						alert('购买数量只能在1到9999之间');
					}
					return false;
				}
            	
            	if(diff > 0){ //钱不够
					//把信息写入cookie
					var currentUrl = window.location.href + '&who=' + who + '&target_uin=' + other_uin;
					document.cookie = "currentUrl=" + currentUrl + ";path=/";
					document.cookie = "buynum=" + $('#J_num_again').val() + ";path=/";
					
            		var desc = '对不起，您的V宝余额不足，还差 ' + diff + 'V宝 才能购买，请先充值V宝！';
            		if(typeof art=='function' && typeof art.dialog=='function'){
	            		art.dialog({
	            			content: desc,
	            			icon: 'warning',
	            			lock: true,
	            			width: 220,
	            			height: 120,
	            			ok: function(){
	            				location.href = '/shop/index.php?v='+diff;
	            			},
	            			cancel: false,
	            			esc: false
	            		});
            		}else{
            			alert(desc);
            			location.href = '/shop/index.php?v='+diff;
            		}
					return false;
            	}
				$('#J_form_buyInfo').submit();
            });

            //改变数量的值
            $('#J_num_again').keypress(function (e) {
	            var keyCode = e.keyCode;
	            if (keyCode == 8 || (keyCode >= 48 && keyCode <= 57)) {
	                setTimeout(calcNum, 0);
	                return true;
	            }
	            return false;
	        });

            //keypress don't support the backspace,when enter the backspace,use this func to gain and handler 
	        $('#J_num_again').keyup(function(e){
	        	//8 is backspace's keycode
	        	if(e.keyCode == 8){
	        		var num = $(this).val().length;

	        		if(num <= 0){
	        			this.value = 1;
	        		} 
					calcNum();
					return;
	        	}
	        	if($(this).val() < 1){
	        		$(this).val(1);
					calcNum();
	        	}
	        });

	        //增加数量
	        $('#J_btn_plus').click(function(){
	        	var num = parseInt($('#J_num_again').val(),10);
	        	num++;
	        	num = num>9999 ? 9999 : num;
	        	$('#J_num_again').val(num);
	        	calcNum();
	        });

	        //减少数量
	         $('#J_btn_minus').click(function(){
	        	var num = parseInt($('#J_num_again').val(),10);
	        	num--;
	        	num = num<1 ? 1 : num;
	        	$('#J_num_again').val(num);
	        	calcNum();
	        });

	         //计算
	        function calcNum(){
	        	//变化总价  数量*单价
	        	var num = parseInt($('#J_num_again').val(),10);
	        	var price = parseInt($('#J_num_again').attr('price'),10);
	        	var total = num * price;
	        	$('#J_total_price').html(total + ' V宝');
	        	//应付总额
	        	$('#J_pay_money').html(total);
	        	//送币
	        	var sendMoney = $('#J_num_again').attr('send_money');
	        	sendMoney = parseInt(sendMoney,10);
	        	if(sendMoney > 0){
	        		$('#J_send_money').html(sendMoney * num);
	        	}
				//使用期限
				if($('#J_expire').length > 0){
					var expire = $('#J_num_again').attr('expire');
					expire = parseInt(expire,10);
					$('#J_expire').html(expire * num + '天');
				}
	        }
        });
    })();
