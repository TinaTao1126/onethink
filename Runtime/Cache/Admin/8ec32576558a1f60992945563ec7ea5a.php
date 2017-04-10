<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|OneThink管理平台</title>
    <link href="/onethink/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/onethink/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/onethink/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/onethink/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/onethink/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/onethink/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
	<link rel="stylesheet" type="text/css" href="/onethink/Public/Admin/css/module_custom.css">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/onethink/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/onethink/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/onethink/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <span class="logo"></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
    <div class="main-title">
        <h2>开单</h2>
    </div>
    <form action="<?php echo U('Car/add');?>" method="post" class="form-column-2">
        <div class="form-item">
            <label class="item-label">门店</label>
            <div class="controls">
            	<div class="text input-large"> 爱一行（中关村店） <?php echo (time_format($vo["createtime"])); ?></div>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label"><span class="required">*</span>单号</label>
            <div class="controls">
                <input type="text" class="text input-large" name="" value="">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label "><span class="required">*</span>工位号</label>
            <div class="controls">
            	<div class="text input-large">
            	<select class="select" name="store_station_id" >
                	<option value="100">001</option>
                </select>
                </div>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label"><span class="required">*</span>车牌号</label>
            <div class="controls">
                <input type="text" class="text input-large" name="car_number" value="">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label"><span class="required">*</span>车主姓名</label>
            <div class="controls">
                <input type="text" class="text input-large" name="car_owner" value="">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label"><span class="required">*</span>车主电话</label>
            <div class="controls">
                <input type="text" class="text input-large" name="owner_phone" value="">
            </div>
            
        </div>
        <div class="form-item">
            <label class="item-label">行驶里程</label>
            <div class="controls">
                <input type="text" class="text input-large" name="driving_distance" value="">
            </div>
            
        </div>
        <div class="form-item">
            <label class="item-label">品牌</label>
            <div class="controls">
                <input type="text" class="text input-large" name="car_brand" value="">
            </div>
            
        </div>
        <div class="form-item">
            <label class="item-label">车型(型号？？？)</label>
            <div class="controls">
                <input type="text" class="text input-large" name="car_model" value="">
            </div>
            
        </div>
        <div class="form-item">
            <label class="item-label">颜色</label>
            <div class="controls">
                <input type="text" class="text input-large" name="car_color" value="">
            </div>
            
        </div>
        <div class="form-item">
            <label class="item-label">发动机号</label>
            <div class="controls">
                <input type="text" class="text input-large" name="car_engine_num" value="">
            </div>
            
        </div>
        <div class="form-item">
            <label class="item-label">VIN</label>
            <div class="controls">
                <input type="text" class="text input-large" name="car_vin" value="">
            </div>
            
        </div>
        <div class="row">
        	<input type="hidden" name="store_id" value="1"/><!-- FIXME -->
            <button class="btn submit-btn"  id="submit" type="submit" target-form="form-column-2">确 定</button>
            <button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
        </div>
    </form>
    <div class="cf">
		<div class="fl">
			<input type="hidden" name="car_id" id="car_id" value=""/>
            <a class="btn" href="<?php echo U('OrderItem/add');?>" id="order-item-add">新 增</a>
            <button class="btn ajax-post confirm" url="<?php echo U('OrderItem/delete',array('method'=>'del'));?>" target-form="ids">删 除</button>
        </div>

        
    </div>
    <div class="data-table table-striped">
	<table class="">
    <thead>
        <tr>
		<th class="row-selected row-selected"><input class="check-all" type="checkbox"/></th>
		<th class="">类别</th>
		<th class="">描述</th>
		<th class="">工时费</th>
		<th class="">零件</th>
		<th class="">数量</th>
		<th class="">合计</th>
		<th class="">操作</th>
		</tr>
    </thead>
    <tbody>
		<?php if(!empty($_list)): if(is_array($_list)): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
            <td><input class="ids" type="checkbox" name="id[]" value="<?php echo ($vo["id"]); ?>" /></td>
            <td><?php echo ($vo["item_type_name"]); ?> </td>
			<td><?php echo ($vo["item_name"]); ?> </td>
			<td><?php echo ($vo["hour_price"]); ?></td>
			<td><?php echo ($vo["item_price"]); ?></td>
			<td><?php echo ($vo["item_num"]); ?></td>
			<td><?php echo ($vo["hour_price+$vo"]["item_price*$vo"]["item_num"]); ?></td>
			<td>
				<a title="编辑" href="<?php echo U('OrderItem/edit?id='.$vo['id']);?>">编辑</a>
				<a class="confirm ajax-get" title="删除" href="<?php echo U('OrderItem/del?id='.$vo['id']);?>">删除</a>
            </td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		<?php else: ?>
		<td colspan="9" class="text-center"> 无记录！ </td><?php endif; ?>
	</tbody>
    </table>
	</div>
    <div class="page">
        <?php echo ($_page); ?>
    </div>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.onethink.cn" target="_blank">OneThink</a>管理平台</div>
                <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "/onethink", //当前网站地址
            "APP"    : "/onethink/index.php?s=", //当前项目地址
            "PUBLIC" : "/onethink/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/onethink/Public/static/think.js"></script>
    <script type="text/javascript" src="/onethink/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
    <script type="text/javascript">
  	//表单提交
	$(document)
    	.ajaxStart(function(){
    		$("button:submit").addClass("log-in").attr("disabled", true);
    	})
    	.ajaxStop(function(){
    		$("button:submit").removeClass("log-in").attr("disabled", false);
    	});
  	
	$("#order-item-add").click(function(){
		
	    $(this).attr("href",$(this).attr("href")+"/id/"+$("#car_id").val());
	});

	$("form").submit(function(){
		var self = $(this);
		$.post(self.attr("action"), self.serialize(), success, "json");
		return false;

		function success(data){
			console.log(data);
			if(data.code == 200){
				$("#car_id").val(data.data);
				//$("button:submit").attr("disabled", false);
			} else {
				alert(data.msg);
			}
		}
	});
        //导航高亮
        //highlight_subnav('<?php echo U('Order/index');?>');
    </script>

</body>
</html>