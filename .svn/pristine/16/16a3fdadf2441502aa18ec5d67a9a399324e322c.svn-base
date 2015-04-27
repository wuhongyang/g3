
//图片分类添加验证
function checkCat()
{
	var cat_name = $('#cat_name').val();
	var cat_length = cat_name.length;
	if( cat_length > 0 )
		return true;
	else 
	{
		alert('请输入分类名称');
		return false;
	}
}

//图片添加验证
function checkPic()
{
	var cat_id = $('#cat_id').val();
	if( cat_id.length < 1 )
	{
		alert('请选择图片类别');
		return false;
	}
	var is_id = $('#is_id').val();
	var pic_name = $('#pic_name').val();
	if( pic_name.length < 1 ) 
	{
		alert('请输入图片名');
		return false;
	}

	if( is_id.length < 1 ) 
	{
		var img_path = $('#img_path').val();
		if(img_path.length < 1)
		{
			alert('请上传图片');
			return false;
		}
	}
	return true;
}

//图片联动
function linkPic()
{
	var cat_id = $('#cat_id').val();
	if( cat_id > 0 ) 
	{
		var url = URL+'&cat_id='+cat_id;
		$.get(url,function(d){
			var lists;
			lists = eval("("+d+")");
			var listsNum = lists.length;  //数据长度
			if( listsNum > 0 )
			{
				var select = " 图片列表:<select name='id' id = 'pic_id' onchange = 'subpic();'><option value = '0'>请选择</option></select>";
				$('#pic_name').html(select);

				//显示图片列表
				var i;
				for(i=0; i<listsNum; i++)
				{
					var ch = "";
					if(lists[i]['id'] == pic_id) {
						var ch = "selected";
					} 
					var option = " <option  value = '"+lists[i]['id']+"' "+ch+" >"+lists[i]['pic_name']+"</option>";
					$('#pic_id').append(option);
				}
			}
			else
			{
				var select = "此分类下无图片";
				$('#pic_name').html(select);
			}
		});
	}
}
function subpic()
{
	$('#pic_form').submit();
}
