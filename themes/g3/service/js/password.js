//修改登录密码
(function(){
    var newpwd_status = 0;
    var post_status = 0;
    $(function(){
        $('input[name="password"]').focus();

        $('input[name="password"]').focus(function(){
            $('#J_pwd_tip').html('');
        });

        $('input[name="password"]').blur(function(){
            if($(this).val() == ''){
                $('#J_pwd_tip').html('请输入旧密码');
            }else{
                $('#J_pwd_tip').html('');
            }
            
        });

        $('input[name="new_password"]').focus(function(){
            $('#J_newpwd_tip').html('');
        });

        $('input[name="new_password"]').blur(function(){
            var pwd = $(this).val();
            if(pwd == ''){
                $('#J_newpwd_tip').html('密码不能为空');
            }else if(pwd.length < 6){
                $('#J_newpwd_tip').html('密码长度不能小于6个字符');
            }else{
                newpwd_status = 1;
            }
        });

        $('input[name="re_password"]').focus(function(){
            $('#J_renewpwd_tip').html('');
        })

        $('input[name="re_password"]').blur(function(){
            var pwd = $('input[name="new_password"]').val();
            var repwd = $(this).val();
            if(newpwd_status == 1){
                if(repwd=='' && pwd!=''){
                    $('#J_renewpwd_tip').html('请再次输入新密码');
                }else if(pwd != repwd){
                    $('#J_renewpwd_tip').html('两次输入的密码不一致');
                }else{
                    post_status = 1;
                }
            }
        });

        $('#J_submit').click(function(){
            var pwd = $('input[name="password"]').val();
			var newpwd = $('input[name="new_password"]').val();
			var repwd = $('input[name="re_password"]').val();
            if(pwd == ''){
                return false;
            }
			if(newpwd != repwd){
				$('#J_renewpwd_tip').html('两次输入的密码不一致');
				return false;
			}
            if(post_status != 1){
                $('input[name="new_password"]').blur();
                $('input[name="re_password"]').blur();
                return false;
            }
            $('#J_form').submit();
        });
    });   
})();