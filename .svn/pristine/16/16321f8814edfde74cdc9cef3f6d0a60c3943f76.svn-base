/**
 * Created with JetBrains WebStorm.
 * User: Administrator
 * Date: 12-8-16
 * Time: 下午7:04
 * To change this template use File | Settings | File Templates.
 */
$(function () {
    var lbl_person = $('.cont1').find('.J_lbl_person'),
        lbl_money = $('.cont1').find('.J_lbl_money'),
        friend = $('#J_friend').find('.txt1'),
        check_friend = $('#J_checkuser'),
        getVbao = $('#J_getVbao'),
        custom_money = $('#J_custom_money'),
        buy_submit = $('#J_buy');
    lbl_person.each(function () {
        $(this).click(function () {
            lbl_person.addClass('radioOff').removeClass('radioOn');
            $(this).removeClass('radioOff').addClass('radioOn');
            if ($(this).hasClass('other')) {
                $('#J_friend').show().find('.txt1').val('').focus();
                check_friend.removeClass('friend-wrong friend-right').html('');
            } else {
                $('#J_friend').hide();
            }
        });
    });
    lbl_money.each(function () {
        $(this).click(function () {
            lbl_money.addClass('radioOff').removeClass('radioOn red');
            $(this).removeClass('radioOff').addClass('radioOn red');
            getVbao.html($(this).html().slice(0, -1));
            custom_money.val($(this).html().slice(0, -1));
            //getVbao.html($(this).html().split('元')[0].toString());
            //custom_money.val($(this).html().split('元')[0].toString());
        });
    });
    friend.blur(checkFriendID);
    custom_money.keyup(function (e) {
        var keyCode = e.keyCode,
            $self = $(this),
			val;
        if (keyCode == 46 || keyCode == 8 || (keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) {
            setTimeout(function () {
                getVbao.html($self.val()?$self.val():0);
            }, 0);
            return true;
        }else{
			val = parseInt($self.val(),10);
			$self.val(val);
			getVbao.html(val);
		}
        return false;
    });
    custom_money.blur(function () {
        if ($(this).val() < 10) {
            $('#tip').addClass('red');
            $(this).focus();
        } else {
            $('#tip').removeClass('red');
        }
    })
    function checkFriendID() {
        var friend_id = friend.val();
        if (friend_id == "") {
            return false;
        }
        $.ajax({
            type:'GET',
            url:'',
            dataType:'text',
            async:true,
            data:{
                'module':'getUserInfo',
                'uin':friend_id
            },
            success:function (data) {
                handleData(data, friend_id);
            }
        });
    }

    //处理ajax返回数据
    function handleData(data, friend_id) {
        var uid = data.split('|')[0];
        var unick = data.split('|')[1];
        if (uid == friend_id) {
            check_friend.removeClass('friend-wrong').addClass('friend-right').html('');
			$('#J_nick').html(unick);
            $("#payment-form").append('<input type="hidden" name="other_nick" id="other_nick" value="' + unick + '" />');
            handleData.flag = true;
            return true;
        } else {
            check_friend.removeClass('friend-right').addClass('friend-wrong').html('账号不存在，请重新输入');
            friend.focus();
            handleData.flag = false
        }
    }

    buy_submit.click(function (e) {
        var tip = pay_check();
        if (tip) {
            $("#payment-form").submit();
        } else {
            return false;
        }
//        if (!$('#other').is(':checked')) {
//            $("#payment-form").submit();
//        } else {
//
//            //        console.log(tip, handleData.flag);
//            if (tip && tip2) {
//                $("#payment-form").submit();
//            } else {
//                return false;
//            }
//        }

    });

    function pay_check() {
        if ($('#other').is(':checked')) {
            var otherUin = $('#other_uin').val();
            checkFriendID();
            var tip2 = handleData.flag;
            if ((isNaN(otherUin) || otherUin < 1) || !tip2) {
                return false;
            }
        }
        var money = custom_money.val();
        if (isNaN(money) || money < 10) {
            return false;
        }
        return true;
    }
});
