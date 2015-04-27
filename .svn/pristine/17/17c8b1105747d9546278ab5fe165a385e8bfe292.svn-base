<script charset="utf-8" src="template/js/kindeditor/kindeditor1.js"></script>
<script charset="utf-8" src="template/js/kindeditor/lang/zh_CN.js"></script>
<script>
$(function(){
	$('textarea').each(function(){
		KindEditor.create(this,{
			width:'500px',
			height:'200px',
			minHeight:'88px',
			newlineTag : 'br',
			themesPath : 'template/js/KindEditor/themes/',
			pluginsPath: 'template/js/KindEditor/plugins/',
			afterBlur:function(){
				if(!this.isEmpty()){
					this.sync();
				}else{
					this.html('');
					this.sync();
				}
			},
			afterChange:function(){
				var autoheight = KindEditor.IE?this.edit.doc.body.scrollHeight:this.edit.doc.body.offsetHeight;
				if(autoheight<88){
					autoheight = '88px';
				}
				this.edit.setHeight(autoheight);
			}
		})
	});
})
function textarea(){
	$('body').find("textarea").each(function(){
		var obj = $(this);
		KindEditor.create(obj,{
					width:'500px',
					height:'200px',
					newlineTag : 'br',
					themesPath : 'template/js/KindEditor/themes/',
					pluginsPath: 'template/js/KindEditor/plugins/',
					afterBlur:function(){
						if(!this.isEmpty()){
							this.sync();
						}else{
							this.html('');
							this.sync();
						}
					},
					afterChange:function(){
						var autoheight = KindEditor.IE?this.edit.doc.body.scrollHeight:this.edit.doc.body.offsetHeight;
						if(autoheight<88){
							autoheight = '88px';
						}
						this.edit.setHeight(autoheight);
					}
				})
	})
}
</script>
