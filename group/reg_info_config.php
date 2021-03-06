<?php
$reg_info_config=array(
	'vvai'=>array(
		'marry'=>array(
			'is_marry'=>array(
				'name'=>'婚恋状况',
				'type'=>'radio',
				'is_required'=>true,
				'value'=>array(
					'0'=>'未婚',
					'1'=>'离异',
					'2'=>'丧偶'
				)
			),
			'education'=>array(
				'name'=>'学历',
				'type'=>'radio',
				'is_required'=>true,
				'value'=>array(
					'0'=>'高中或中专',
					'1'=>'大专',
					'2'=>'本科',
					'3'=>'双学士',
					'4'=>'硕士',
					'5'=>'博士'
				)
			),
			'salary'=>array(
				'name'=>'月收入',
				'type'=>'select',
				'value'=>array(
					'0'=>'2000以下',
					'1'=>'2000~5000',
					'2'=>'5000~8000',
					'3'=>'8000~10000',
					'4'=>'10000~20000',
					'5'=>'20000~50000',
					'6'=>'50000以上'
				)
			),
			'house_status'=>array(
				'name'=>'住房情况',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后再告诉你',
					'1'=>'与父母同住',
					'2'=>'租房',
					'3'=>'已购房（有贷款）',
					'4'=>'已购房（无贷款）',
					'5'=>'住单位房',
					'6'=>'住亲朋家',
					'7'=>'需要时购置'
				)
			),
			'has_car'=>array(
				'name'=>'购车情况',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后再告诉你',
					'1'=>'未购车',
					'2'=>'已购车',
					'3'=>'单位用车',
					'4'=>'需要时购置'
				)
			)
		),
		'work_study'=>array(
			'industry'=>array(
				'name'=>'公司行业',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'计算机（软件、硬件、服务）',
					'2'=>'通信、电信',
					'3'=>'互联网',
					'4'=>'电子（半导体、仪器、自动化）',
					'5'=>'金融/投资/证券',
					'6'=>'贸易（进出口、批发、零售）',
					'7'=>'快速消费品（食品、饮料、化妆品）',
					'8'=>'服装/纺织/皮革',
					'9'=>'家具/家电/工艺品/玩具',
					'10'=>'办公用品及设备',
					'11'=>'医疗，医院',
					'12'=>'广告/公关/市场推广/会展',
					'13'=>'影视/媒体/出版/印刷/包装',
					'14'=>'房地产相关',
					'15'=>'家具/室内设计/装潢',
					'16'=>'服务（咨询、人力资源）',
					'17'=>'法律相关',
					'18'=>'教育/培训',
					'19'=>'保密'
				)
			),
			'company_type'=>array(
				'name'=>'公司类型',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'政府机关',
					'2'=>'事业单位',
					'3'=>'外企企业',
					'4'=>'世界500强',
					'5'=>'上市公司',
					'6'=>'国有企业',
					'7'=>'私营企业',
					'8'=>'自有公司',
					'9'=>'保密'
				)
			),
			'work_status'=>array(
				'name'=>'工作状态',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'轻松稳定',
					'2'=>'朝九晚五',
					'3'=>'偶尔加班',
					'4'=>'经常加班',
					'5'=>'偶尔出差',
					'6'=>'经常出差',
					'7'=>'经常有应酬',
					'8'=>'工作时间自由',
					'9'=>'保密'
				)
			),
			'salary_status'=>array(
				'name'=>'收入描述',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'福利优越',
					'2'=>'奖金丰厚',
					'3'=>'事业刚起步',
					'4'=>'事业稳定上升',
					'5'=>'投资高回报',
					'6'=>'保密'
				)
			),
			'school_tag'=>array(
				'name'=>'毕业学校',
				'type'=>'text',
				'max_length'=>20
			),
			'entrance_school_date'=>array(
				'name'=>'入学年份',
				'type'=>'select',
				'value'=>array(
					'0'=>1996,
					'1'=>1997,
					'2'=>1998,
					'3'=>1999,
					'4'=>2000,
					'5'=>2001,
					'6'=>2002,
					'7'=>2003,
					'8'=>2004,
					'9'=>2005,
					'10'=>2006,
					'11'=>2007,
					'12'=>2008,
					'13'=>2009,
					'14'=>2010,
					'15'=>2011,
					'16'=>2012,
					'17'=>2013
				)
			),
			'profession_type'=>array(
				'name'=>'专业类型',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'计算机类',
					'2'=>'电子信息类',
					'3'=>'中文类',
					'4'=>'外文类',
					'5'=>'经济学类',
					'6'=>'金融学类',
					'7'=>'管理类',
					'8'=>'市场营销类',
					'9'=>'法学类',
					'10'=>'教育类',
					'11'=>'社会学类',
					'12'=>'历史类',
					'13'=>'哲学类',
					'14'=>'艺术类',
					'15'=>'图书馆类',
					'16'=>'情报档案类',
					'17'=>'政治类',
					'18'=>'数学类',
					'19'=>'统计类',
					'20'=>'物理类',
					'21'=>'化学类',
					'22'=>'生物类',
					'23'=>'食品类',
					'24'=>'医学类',
					'25'=>'环境类',
					'26'=>'地理类',
					'27'=>'建筑类',
					'28'=>'测绘类',
					'29'=>'电气类',
					'30'=>'机械类'
				)
			),
			'linguistic_competence'=>array(
				'name'=>'语言能力',
				'type'=>'checkbox',
				'value'=>array(
					'ZWPTH'=>'中文（普通话）',
					'ZWGDH'=>'中文（广东话）',
					'YY'=>'英语',
					'FY'=>'法语',
					'RY'=>'日语',
					'HY'=>'韩语',
					'EY'=>'俄语',
					'FLY'=>'芬兰语',
					'HLY'=>'荷兰语',
					'PTYY'=>'葡萄牙语',
					'XBYY'=>'西班牙语',
					'YNY'=>'越南语',
					'ALBY'=>'阿拉伯语',
					'TGY'=>'泰国语',
					'YDNXYY'=>'印度尼西亚语',
					'YDY'=>'印度语',
					'DMY'=>'丹麦语',
					'XLY'=>'希腊语',
					'YLY'=>'伊朗语',
					'XYLY'=>'匈牙利语',
					'TEQY'=>'土耳其语',
					'RDY'=>'瑞典语',
					'MDY'=>'缅甸语',
					'LMNYY'=>'罗马尼亚语',
					'QT'=>'其他'
				)
			)
		),
		'live'=>array(
			'nationality'=>array(
				'name'=>'国籍',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'999'=>'中国大陆',
					'1'=>'中国台湾',
					'2'=>'中国香港',
					'3'=>'中国澳门',
					'4'=>'韩国',
					'5'=>'朝鲜',
					'6'=>'印度',
					'7'=>'印度尼西亚',
					'8'=>'美国',
					'9'=>'加拿大',
					'10'=>'日本',
					'11'=>'俄罗斯',
					'12'=>'英国',
					'13'=>'德国',
					'14'=>'意大利',
					'15'=>'法国',
					'16'=>'芬兰',
					'17'=>'瑞典',
					'18'=>'瑞士',
					'19'=>'南非',
					'20'=>'蒙古',
					'21'=>'越南',
					'22'=>'缅甸',
					'23'=>'泰国',
					'24'=>'老挝',
					'25'=>'菲律宾',
					'26'=>'西班牙',
					'27'=>'葡萄牙',
					'28'=>'阿尔巴尼亚',
					'29'=>'阿尔及利亚',
					'30'=>'阿富汗',
					'31'=>'阿根廷',
					'32'=>'阿拉伯联合酋长国',
					'33'=>'阿拉伯叙利亚共和国',
					'34'=>'阿鲁巴',
					'35'=>'阿曼',
					'36'=>'阿塞拜疆',
					'37'=>'埃及',
					'38'=>'埃塞俄比亚',
					'39'=>'爱尔兰',
					'40'=>'爱沙尼亚',
					'41'=>'安道尔',
					'42'=>'安哥拉',
					'43'=>'安圭拉',
					'44'=>'安提瓜和巴布达',
					'45'=>'奥地利',
					'46'=>'澳大利亚',
					'47'=>'巴巴多斯',
					'48'=>'巴布亚新几内亚',
					'49'=>'巴哈马',
					'50'=>'巴基斯坦',
					'51'=>'巴勒斯坦',
					'52'=>'巴拉圭',
					'53'=>'巴林',
					'54'=>'巴拿马',
					'55'=>'巴西',
					'56'=>'白俄罗斯',
					'57'=>'百慕大',
					'58'=>'保加利亚',
					'59'=>'北马里亚纳',
					'60'=>'贝宁',
					'61'=>'比利时',
					'62'=>'冰岛',
					'63'=>'波多黎各',
					'64'=>'波兰',
					'65'=>'波斯尼亚和黑塞哥维那',
					'66'=>'玻利维亚',
					'67'=>'伯利兹',
					'68'=>'博茨瓦纳',
					'69'=>'不丹',
					'70'=>'布基纳法索',
					'71'=>'布隆迪',
					'72'=>'布维岛',
					'73'=>'赤道几内亚',
					'74'=>'大阿拉伯利比亚民众国',
					'75'=>'丹麦',
					'76'=>'东帝汶',
					'77'=>'多哥',
					'78'=>'多米尼加共和国',
					'79'=>'多米尼克',
					'80'=>'厄瓜多尔',
					'81'=>'厄立特里亚',
					'82'=>'法罗群岛',
					'83'=>'梵蒂冈城国',
					'84'=>'斐济',
					'85'=>'佛得角',
					'86'=>'冈比亚',
					'87'=>'刚果',
					'88'=>'哥伦比亚',
					'89'=>'哥斯达黎加',
					'90'=>'格林纳达',
					'91'=>'格陵兰',
					'92'=>'格鲁吉亚',
					'93'=>'古巴',
					'94'=>'关岛',
					'95'=>'圭亚那',
					'96'=>'哈萨克斯坦',
					'97'=>'海地',
					'98'=>'荷兰',
					'99'=>'荷属安的列斯',
					'100'=>'赫德和麦克唐纳群岛',
					'101'=>'洪都拉斯',
					'102'=>'基里巴斯',
					'103'=>'吉布提',
					'104'=>'吉尔吉斯斯坦',
					'105'=>'几内亚',
					'106'=>'几内亚比绍',
					'107'=>'加纳',
					'108'=>'加蓬',
					'109'=>'柬埔寨',
					'110'=>'捷克共和国',
					'111'=>'津巴布韦',
					'112'=>'喀麦隆',
					'113'=>'卡塔尔',
					'114'=>'开曼群岛',
					'115'=>'科摩罗',
					'116'=>'科特迪瓦',
					'117'=>'科威特',
					'118'=>'克罗地亚',
					'119'=>'肯尼亚',
					'120'=>'库克群岛',
					'121'=>'拉脱维亚',
					'122'=>'莱索托',
					'123'=>'黎巴嫩',
					'124'=>'立陶宛',
					'125'=>'利比里亚',
					'126'=>'列支敦士登',
					'127'=>'卢森堡',
					'128'=>'卢旺达',
					'129'=>'罗马尼亚',
					'130'=>'马达加斯加',
					'131'=>'马尔代夫',
					'132'=>'马耳他',
					'133'=>'马拉维',
					'134'=>'马来西亚',
					'135'=>'马里',
					'136'=>'马绍尔群岛',
					'137'=>'毛里求斯',
					'138'=>'毛利塔尼亚',
					'139'=>'蒙特塞拉特',
					'140'=>'孟加拉国',
					'141'=>'秘鲁',
					'142'=>'密克罗尼西亚联邦',
					'143'=>'摩尔多瓦',
					'144'=>'摩洛哥',
					'145'=>'摩纳哥',
					'146'=>'莫桑比克',
					'147'=>'墨西哥',
					'148'=>'纳米比亚',
					'149'=>'南极洲',
					'150'=>'南斯拉夫',
					'151'=>'瑙鲁',
					'152'=>'尼泊尔',
					'153'=>'尼加拉瓜',
					'154'=>'尼日尔',
					'155'=>'尼日利亚',
					'156'=>'纽埃',
					'157'=>'挪威',
					'158'=>'诺福克岛',
					'159'=>'帕劳群岛',
					'160'=>'皮特凯恩',
					'161'=>'前南斯拉夫马其顿共和国',
					'162'=>'萨尔瓦多',
					'163'=>'萨摩亚',
					'164'=>'塞拉利昂',
					'165'=>'塞内加尔',
					'166'=>'塞浦路斯',
					'167'=>'塞舌尔',
					'168'=>'沙特阿拉伯',
					'169'=>'圣诞岛',
					'170'=>'圣多美和普林西比',
					'171'=>'圣赫勒拿',
					'172'=>'圣基茨和尼维斯',
					'173'=>'圣卢西亚',
					'174'=>'圣马力诺',
					'175'=>'斯里兰卡',
					'176'=>'斯洛伐克共和国',
					'177'=>'斯洛文尼亚',
					'178'=>'斯瓦尔巴岛和扬马延岛',
					'179'=>'斯威士兰',
					'180'=>'苏丹',
					'181'=>'苏里南',
					'182'=>'所罗门群岛',
					'183'=>'索马里',
					'184'=>'塔吉克斯坦',
					'185'=>'坦桑尼亚',
					'186'=>'汤加',
					'187'=>'特克斯和凯科斯群岛',
					'188'=>'特立尼达和多巴哥',
					'189'=>'突尼斯',
					'190'=>'图瓦卢',
					'191'=>'土耳其',
					'192'=>'土库曼斯坦',
					'193'=>'托克劳',
					'194'=>'瓦努阿图',
					'195'=>'危地马拉',
					'196'=>'委内瑞拉',
					'197'=>'文莱',
					'198'=>'乌干达',
					'199'=>'乌克兰',
					'200'=>'乌拉圭',
					'201'=>'乌兹别克斯坦',
					'202'=>'西撒哈拉',
					'203'=>'希腊',
					'204'=>'新加坡',
					'205'=>'新西兰',
					'206'=>'匈牙利',
					'207'=>'牙买加',
					'208'=>'亚美尼亚',
					'209'=>'也门',
					'210'=>'伊拉克',
					'211'=>'伊朗伊斯兰共和国',
					'212'=>'以色列',
					'213'=>'约旦',
					'214'=>'赞比亚',
					'215'=>'扎伊尔',
					'216'=>'乍得',
					'217'=>'直布罗陀',
					'218'=>'智利',
					'219'=>'中非共和国'
				)
			),
			'is_smoke'=>array(
				'name'=>'吸烟',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'不吸，很反感吸烟',
					'2'=>'不吸，但不反感',
					'3'=>'社交时偶尔吸',
					'4'=>'每周吸几次',
					'5'=>'每天都吸'
				)
			),
			'is_drink'=>array(
				'name'=>'饮酒',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'不喝',
					'2'=>'社交有需要时喝',
					'3'=>'有兴致时喝',
					'4'=>'每天都离不开酒'
				)
			),
			'routine'=>array(
				'name'=>'作息习惯',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'早睡早起很规律',
					'2'=>'总是早起',
					'3'=>'偶尔懒散一下',
					'4'=>'经常夜猫子',
					'5'=>'没有规律'
				)
			),
			'is_exercise'=>array(
				'name'=>'锻炼习惯',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'每天锻炼',
					'2'=>'每周至少三次',
					'3'=>'每周至少一次',
					'4'=>'每月几次',
					'5'=>'没时间锻炼',
					'6'=>'不喜欢锻炼'
				)
			),
			'faith'=>array(
				'name'=>'宗教信仰',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'无宗教信仰',
					'2'=>'大乘佛教显宗',
					'3'=>'大乘佛教密宗',
					'4'=>'大乘佛教净宗',
					'5'=>'小乘佛教',
					'6'=>'道教',
					'7'=>'儒教',
					'8'=>'基督教天主教派',
					'9'=>'基督教东正教派',
					'10'=>'基督教新教派',
					'11'=>'犹太教',
					'12'=>'伊斯兰教什叶派',
					'13'=>'伊斯兰教逊尼派',
					'14'=>'印度教',
					'15'=>'神道教',
					'16'=>'萨满教',
					'17'=>'其他宗教信仰'
				)
			),
			'birth_order'=>array(
				'name'=>'家中排行',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'独生子女',
					'2'=>'老大',
					'3'=>'老二',
					'4'=>'老三',
					'5'=>'老四',
					'6'=>'老五及更小',
					'7'=>'老么'
				)
			),
			'want_baby'=>array(
				'name'=>'是否要孩子',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'愿意',
					'2'=>'不愿意',
					'3'=>'视情况而定'
				)
			),
			'living_parents'=>array(
				'name'=>'是否愿与父母同住',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'愿意',
					'2'=>'不愿意',
					'3'=>'视情况而定'
				)
			),
			'pet'=>array(
				'name'=>'宠物',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'猫',
					'2'=>'狗',
					'3'=>'鸟',
					'4'=>'鱼',
					'5'=>'仓鼠/荷兰猪',
					'6'=>'马',
					'7'=>'爬行动物',
					'8'=>'保密',
					'9'=>'正准备养',
					'10'=>'不喜欢养'
				)
			),
			'personality'=>array(
				'name'=>'个性',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'浪漫迷人',
					'2'=>'成熟稳重',
					'3'=>'风趣幽默',
					'4'=>'乐天达观',
					'5'=>'活泼可爱',
					'6'=>'忠厚老实',
					'7'=>'淳朴害羞',
					'8'=>'温柔体贴',
					'9'=>'多愁善感',
					'10'=>'新潮时尚',
					'11'=>'热辣动感',
					'12'=>'豪放不羁'
				)
			),
			'max_consumption'=>array(
				'name'=>'最大消费',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'美食',
					'2'=>'服装',
					'3'=>'娱乐',
					'4'=>'出行',
					'5'=>'交友',
					'6'=>'文化',
					'7'=>'教育',
					'8'=>'其他'
				)
			),
			'is_romantic'=>array(
				'name'=>'喜欢制造浪漫',
				'type'=>'select',
				'value'=>array(
					'0'=>'以后告诉你',
					'1'=>'经常',
					'2'=>'偶尔',
					'3'=>'视情况而定',
					'4'=>'从不',
					'5'=>'不喜欢'
				)
			),
			'living_habit'=>array(
				'name'=>'擅长生活技能',
				'type'=>'checkbox',
				'value'=>array(
					'SXGRMB'=>'实现个人目标',
					'WHPYQGX'=>'维护朋友圈关系',
					'XZFW'=>'装修房屋',
					'ZYZ'=>'志愿者/义工',
					'WXDN'=>'维修电脑',
					'BCSHTL'=>'保持生活条理',
					'LC'=>'理财',
					'ZJKDPY'=>'在家款待朋友',
					'FYHZGHZ'=>'抚养或照顾孩子',
					'ZZLM'=>'制造浪漫',
					'SJHD'=>'社交活动',
					'TSY'=>'谈生意',
					'BCJK'=>'保持健康',
					'JJCT'=>'解决冲突',
					'BWQZ'=>'博闻强识',
					'DZWCQGH'=>'对自我长期规划',
					'ZJDZXQMZ'=>'在简单中寻求满足',
					'WHHYS'=>'文化和艺术',
					'JJXPY'=>'结交新朋友',
					'PR'=>'烹饪',
					'ZQYJ'=>'赚钱养家',
					'BYXLQC'=>'保养、修理汽车',
					'LSYY'=>'良师益友',
					'QT'=>'其他'
				)
			)
		),
		'hobby'=>array(
			'favorite_sports'=>array(
				'name'=>'喜欢的体育运动',
				'type'=>'checkbox',
				'value'=>array(
					'ZQ'=>'足球',
					'LQ'=>'篮球',
					'PQ'=>'排球',
					'WQ'=>'网球',
					'YMQ'=>'羽毛球',
					'BQ'=>'壁球',
					'BLQ'=>'保龄球',
					'SQ'=>'手球',
					'GLQ'=>'橄榄球',
					'BQ'=>'棒球',
					'JS'=>'健身',
					'PB'=>'跑步',
					'ZXC'=>'自行车',
					'MTC'=>'摩托车',
					'QC'=>'汽车',
					'TC'=>'体操',
					'TQD'=>'跆拳道',
					'RD'=>'柔道',
					'KSD'=>'空手道',
					'YY'=>'游泳',
					'SSYD'=>'水上运动',
					'HH'=>'航海',
					'HXHB'=>'滑雪/滑冰',
					'QJ'=>'拳击',
					'DY'=>'钓鱼',
					'BPQ'=>'兵乓球',
					'GEF'=>'高尔夫',
					'WD'=>'舞蹈',
					'QS'=>'潜水',
					'YJ'=>'瑜伽',
					'WS'=>'武术',
					'QT'=>'其他',
					'BXH'=>'不喜欢运动'
				)
			),
			'favorite_recreation'=>array(
				'name'=>'喜欢的娱乐',
				'type'=>'checkbox',
				'value'=>array(
					'FD'=>'饭店',
					'SC'=>'商场',
					'JY'=>'剧院',
					'JB'=>'酒吧',
					'KTV'=>'KTV',
					'DYY'=>'电影院',
					'YYH'=>'音乐会',
					'DB'=>'迪吧',
					'WB'=>'网吧',
					'WQ'=>'温泉',
					'TSG'=>'图书馆/书店',
					'KFT'=>'咖啡厅',
					'YLC'=>'游乐场',
					'TYG'=>'体育馆',
					'GJ'=>'逛街',
					'ZZJL'=>'宅在家里',
					'QT'=>'其他'
				)
			),
			'favorite_food'=>array(
				'name'=>'喜欢的食物',
				'type'=>'checkbox',
				'value'=>array(
					'ZGC'=>'中国菜',
					'YDC'=>'印度菜',
					'TGC'=>'泰国菜',
					'FGC'=>'法国菜',
					'YDLC'=>'意大利菜',
					'ELSC'=>'俄罗斯菜',
					'RBC'=>'日本菜',
					'SK'=>'烧烤',
					'JKSP'=>'健康食品',
					'SS'=>'素食',
					'KC'=>'快餐',
					'QKL'=>'巧克力和甜点',
					'QT'=>'其他',
					'WU'=>'无特别爱好'
				)
			),
			'favorite_book'=>array(
				'name'=>'喜欢的书籍',
				'type'=>'checkbox',
				'value'=>array(
					'XYQC'=>'校园青春',
					'WX'=>'文学',
					'YSSY'=>'艺术与摄影',
					'LZCG'=>'励志与成功',
					'DMYM'=>'动漫与幽默',
					'ZZJS'=>'政治与军事',
					'ZXZJ'=>'哲学与宗教',
					'LSZJ'=>'历史传纪',
					'YDJS'=>'运动健身',
					'JKYS'=>'健康与养生',
					'PRYS'=>'烹饪与饮食',
					'LY'=>'旅游',
					'TZLC'=>'投资理财',
					'HLJT'=>'婚恋与家庭',
					'QKZZ'=>'期刊杂志',
					'YLSS'=>'娱乐时尚',
					'RWSK'=>'人文社科',
					'ZRKX'=>'自然科学',
					'SCJS'=>'收藏与鉴赏',
					'QT'=>'其他'
				)
			),
			'favorite_movie'=>array(
				'name'=>'喜欢的电影',
				'type'=>'checkbox',
				'value'=>array(
					'AQ'=>'爱情',
					'XJ'=>'喜剧',
					'DZMX'=>'动作冒险',
					'GZWX'=>'古装武侠',
					'KHMH'=>'科幻魔幻',
					'XXTL'=>'悬疑推理',
					'JSKB'=>'惊悚恐怖',
					'DH'=>'动画',
					'ZZ'=>'战争',
					'YYGW'=>'音乐歌舞',
					'JLP'=>'纪录片',
					'JQ'=>'剧情',
					'XB'=>'西部',
					'LSZJ'=>'历史传纪',
					'QT'=>'其他'
				)
			),
			'favorite_program'=>array(
				'name'=>'喜欢的节目',
				'type'=>'checkbox',
				'value'=>array(
					'ZZSJ'=>'政治事件',
					'YLBG'=>'娱乐八卦',
					'TYSS'=>'体育赛事',
					'LCTZ'=>'理财投资',
					'XSQY'=>'相声曲艺',
					'HXHD'=>'海选活动',
					'CXS'=>'畅销书',
					'YSRP'=>'影视热片',
					'XXSH'=>'休闲生活',
					'HYFZ'=>'行业发展',
					'QT'=>'其他',
					'ZW'=>'暂无'
				)
			),
			'hobby'=>array(
				'name'=>'业余爱好',
				'type'=>'checkbox',
				'value'=>array(
					'WL'=>'网络',
					'QC'=>'汽车',
					'DW'=>'动物',
					'SY'=>'摄影',
					'YS'=>'影视',
					'YYU'=>'音乐',
					'XZ'=>'写作',
					'GW'=>'购物',
					'SGY'=>'手工艺',
					'YYI'=>'园艺',
					'WD'=>'舞蹈',
					'ZL'=>'展览',
					'PR'=>'烹饪',
					'DS'=>'读书',
					'HH'=>'绘画',
					'JSJ'=>'计算机',
					'TYYD'=>'体育运动',
					'LY'=>'旅游',
					'DZYX'=>'电子游戏',
					'QT'=>'其他'
				)
			),
			'favorite_travel'=>array(
				'name'=>'喜欢的旅游去处',
				'type'=>'checkbox',
				'value'=>array(
					'QYJD'=>'惬意海岛',
					'MSGC'=>'名山古刹',
					'FHDS'=>'繁华都市',
					'FQMC'=>'风情名城',
					'GMSL'=>'广袤森林',
					'GYXY'=>'高原雪域',
					'XMSS'=>'秀美山水',
					'LSYJ'=>'历史遗迹',
					'JHDC'=>'江河大川',
					'JXXG'=>'俊秀峡谷',
					'XQLS'=>'小桥流水人家',
					'QT'=>'其他',
					'ZW'=>'暂无'
				)
			)
		)
	)
);