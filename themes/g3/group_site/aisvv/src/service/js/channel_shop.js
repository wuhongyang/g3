(function () {
    $(function () {
        var btn_plus = $('#J_btn_plus'),
            btn_minus = $('#J_btn_minus'),
            num = $('#J_num_again'),
            price = $('#J_price').html() * 1,
            total_price = $('#J_total_price'),
            vbao = $('#J_vbao'),
			vdou = num.attr('money');
            myVbao = $('#J_myVbao').html() * 1,
            buy_num = $('#buy_num');
        num.keypress(function (e) {
            var keyCode = e.keyCode;
            if (keyCode == 46 || keyCode == 8 || (keyCode >= 48 && keyCode <= 57)) {
                setTimeout(calcNum, 0);
                return true;
            }
            return false;
        });
		calcNum();
        btn_plus.click(calcNum);
        btn_minus.click(calcNum);
        //修改数量产生的列表变动
        function calcNum(e) {
            var dom_id = this.id;
			var multiple = $('#J_multiple').val();
			multiple = multiple ? parseInt(multiple) : 1;
			
            switch (dom_id) {
                case 'J_btn_plus':
                    num.val(num.val() * 1 + multiple);
                    break;
                case 'J_btn_minus':
                    if (num.val() * 1 <= multiple) {
                        num.val(multiple);
                    } else {
                        num.val(num.val() * 1 - multiple);
                    }
                    break;

            }
            buy_num.val(num.val());
            total_price.html(price * num.val() * 1);
            $('#J_vdou').html(vdou * num.val() * 1);
            //判断是否超额，修改相应的充值金额
//                var need_vbao=parseFloat(myVbao)-parseFloat(total_price.html());
            var need_vbao = ((+myVbao) - (+total_price.html())).toFixed(2);
            if (need_vbao >= 0) {
				var module = $('#J_module').val();
				module = module || 'paymoney';
                $('#J_form_pay').attr('action', '?module=' + module).find('.type1').removeAttr('disabled');
                $('#J_form_pay').find('.type2').attr('disabled', 'disabled');
				$('#J_sorrytext').hide();
                $('#J_paybox').hide();
                $('#J_paybox2').show();
                $('#buy_num').val(num.val());
            } else {
				need_vbao = Math.abs(need_vbao);
				var recharge_vbao = need_vbao<10 ? 10 : parseInt(need_vbao);
                $('#J_form_pay').attr('action', 'agent_region.php?module=recharge').find('.type1').attr('disabled', 'disabled');
                $('#J_form_pay').find('.type2').removeAttr('disabled');
                $('#J_pay_expense').val(recharge_vbao);
				$('#J_sorrytext').show();
                $('#J_paybox').show();
                $('#J_paybox2').hide();
            }
        }
    })
})();