(function(){
	$(function(){
		$('input[type="radio"][name="set_default"]').click(function(){
			if($(this).attr('mode') == 1){
				return false;
			}
			var that = $(this);
			var parent_id = $(this).val();
			$.ajax({
				url: '?module=defaultProps',
				type: 'POST',
				data: {parent_id: parent_id},
				dataType: 'JSON',
				success: function(data){
					if(data.Flag != 100){
						art.dialog({
							content: data.FlagString,
							ok: false,
							cancel: false,
							lock: true,
							icon: 'error',
							time: 2,
							esc: false
						});
						that.attr("checked",false);
						var obj = $('input[type="radio"][name="set_default"]');
						for(var i=0; i<obj.length; i++){
							if($(obj[i]).attr('mode') == 1){
								$(obj[i]).attr('checked',true);
							}
						}
					}else{
						art.dialog({
							content: data.FlagString,
							ok: false,
							cancel: false,
							lock: true,
							icon: 'succeed',
							time: 2,
							esc: false
						});
						$('input[type="radio"][name="set_default"]').attr('mode',0);
						that.attr('mode',1);
					}
				}
			});
		});
		//UIN切换
		$('select[name="uin"]').change(function(){
			var uin = $(this).val();
			var href = location.href;
			if(!/^.*module.*$/.test(href)){
				href += '?module=basic';
			}
			if(!/^.*uin.*$/.test(href)){
				href += '&uin='+uin;
			}else{
				href = href.replace(/uin=(\d+)/,'uin='+uin);
			}
			location.href = href;
		});
	});
})();