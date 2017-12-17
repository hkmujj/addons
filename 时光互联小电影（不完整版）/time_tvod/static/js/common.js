// 滚动加载
function loaddata(url, obj, tpl ,sc,page) {
	var nownum = $(page).find("." + obj ).attr("page-num");
	if(nownum == undefined || nownum == 0){
		nownum = 1;
	}
    // 如果正在加载，则退出
    if (loading == true){
    	return;
    } 
    // 设置状态
    var loading = true;
    //分页替换
	var link = url.replace('p=0000', 'p=' + nownum);
	$.post(link, function (response) {
		response = JSON.parse(response);
		
		console.log(response);
		
		//当没有数据
		if(response.total == 0){
			$.detachInfiniteScroll($(page).find('.infinite-scroll'));
			$('.infinite-scroll-preloader').remove();
			var html = '<div class="infinite-scroll-nodata">没有更多内容</div>';
			$(page).find("." + obj ).parent().append(html);
			return;
		}
		
		var gettpl = $("#" + tpl).html();
		$.each(response.result,function(key,data){
			var html = $.Template7(gettpl,data);
			$(page).find("." + obj ).append(html).show();	
		});

		nownum++;
		$(page).find("." + obj ).attr("page-num",nownum);	
		last = $(page).find("." + obj ).children().length;
        if(last == response.total){
            // 加载完毕，则注销无限加载事件，以防不必要的加载
            $.detachInfiniteScroll($('.infinite-scroll'));
            // 删除加载提示符
            $(page).find('.infinite-scroll-preloader').remove();
            return;
        }
		
		loading = false;
	});
	//确定为可加载
	if(sc == true){
	    // 注册'infinite'事件处理函数
	    $(document).on('infinite', '.infinite-scroll-bottom',function() {
	        // 如果正在加载，则退出
	        if (loading == true){
	        	return;
	        } 
	        // 设置状态
	        loading = true;
	        //分页替换
			link = url.replace('0000', nownum);
			$.post(link, function (response) {
				response = JSON.parse(response);
				var gettpl = $("#" + tpl).html();
				$.each(response.result,function(key,data){
					var html = $.Template7(gettpl,data);
					$(page).find("." + obj ).append(html);	
				});
				
				loading = false;
				nownum++;
				$(page).find("." + obj ).attr("page-num",nownum);
				
		        last = $(page).find("." + obj ).children().length;
		        if(last == response.total){
					var html = '<div class="infinite-scroll-nodata">没有更多内容</div>';
					$(page).find("." + obj ).parent().append(html);
	                // 加载完毕，则注销无限加载事件，以防不必要的加载
	                $.detachInfiniteScroll($('.infinite-scroll'));
	                // 删除加载提示符
	                $(page).find('.infinite-scroll-preloader').remove();
	                return;
		        }
			});
   		});
	}
}


//监听页面滚动
 $(".content").scroll(function() {
 	var h = $(this).scrollTop();
 	var view = '<div class="upscroller">↑ 回到顶部</div>' ;
    if(h > 300){
    	if($(this).parent().find(".upscroller").length == 0){
    		$(this).parent().append(view);
    	}else{
    		$(this).parent().find(".upscroller").addClass('show');
    	}
    }else{
    	$(".upscroller").removeClass('show');
    }
 
});

//点击到顶部
$(document).on("click", ".upscroller", function() {
	$(this).parent().find('.content').animate({scrollTop:0}, 'slow');
});

//搜索
$(document).on("submit",".searchbar",function(e){
  	var page = "#page-search";
	e.preventDefault();
  	var keyword = $("#search").val();
  	
  	var uniacid = $("#uniacid").val();
  
  	if(keyword == ''){
  		$.toast('请输入关键字',2000,2);
  		return;
  	}
  	var url = 'index.php?i='+ uniacid +'&c=entry&keyword='+keyword+'&p=0000&do=load&m=time_tvod';

  	$(page).find(".data-box").empty();
  	$(page).find(".data-box").parent().append('<div class="infinite-scroll-preloader"><div class="preloader"></div></div>');
  	loaddata(url,"data-box",'vodTepmlate',true,page);
});

$(document).on("pageReinit", "#page-search", function(e, id, page) {
	var page = "#page-search";
  	var keyword = $("#search").val();
  	if(keyword == ''){
  		return;
  	}
  	var url = 'index.php?i=1&c=entry&keyword='+keyword+'&p=0000&do=load&m=time_tvod';
  	$(page).find(".data-box").empty();
  	$(page).find(".data-box").parent().append('<div class="infinite-scroll-preloader"><div class="preloader"></div></div>');
  	loaddata(url,"data-box",'vodTepmlate',true,page);
});