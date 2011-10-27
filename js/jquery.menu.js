//$(function(){
//	$("ul.topnav a").hover(function(){
//		$(this).siblings('span').addClass("subhover");
//	}, function() {
//		$(this).siblings('span').removeClass("subhover");
//	});
//
//	$("ul.subnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled (Adds empty span tag after ul.subnav*)
//	$("ul.topnav li").hover(function() { //When trigger is clicked...
//		
//		//Following events are applied to the subnav itself (moving subnav up and down)
//		$(this).find("ul.subnav").stop().slideDown('fast').show('fast', function(){
//			$(this).height("auto");
//		}); //Drop down the subnav on click
//
//
//		$(this).hover(function() {
//		}, function(){	
//			$(this).find("ul.subnav").stop().slideUp('fast'); //When the mouse hovers out of the subnav, move it back up
//		});
//
//		//Following events are applied to the trigger (Hover events for the trigger)
//		}).hover(function() { 
//			$(this).addClass("subhover"); //On hover over, add class "subhover"
//			$(this).siblings('a').addClass("subhover");
//		}, function(){	//On Hover Out
//			$(this).removeClass("subhover"); //On hover out, remove class "subhover"
//			$(this).siblings('a').removeClass("subhover");
//	});
//	
//	$("ul.topnav li span").hover(function() { //When trigger is clicked...
//
//		//Following events are applied to the subnav itself (moving subnav up and down)
//		$(this).parent().find("ul.subnav").slideDown('fast').show(); //Drop down the subnav on click
//		
//		$(this).parent().hover(function() {
//		}, function(){
//			$(this).parent().find("ul.subnav").slideUp('fast'); //When the mouse hovers out of the subnav, move it back up
//		});
//
//		//Following events are applied to the trigger (Hover events for the trigger)
//		}).hover(function() {
//			$(this).addClass("subhover"); //On hover over, add class "subhover"
//			$(this).siblings('a').addClass("subhover");
//		}, function(){	//On Hover Out
//			$(this).removeClass("subhover"); //On hover out, remove class "subhover"
//			$(this).siblings('a').removeClass("subhover");
//	});	
//});


$(function(){
	$("ul.topnav a").hover(function(){
		$(this).siblings('span').addClass("subhover");
	}, function() {
		$(this).siblings('span').removeClass("subhover");
	});

	$("ul.subnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled - Adds empty span tag after ul.subnav
	
	$("ul.topnav li span").click(function() { //When trigger is clicked...
		
		//Following events are applied to the subnav itself (moving subnav up and down)
		$(this).parent().find("ul.subnav").slideDown('fast').show(); //Drop down the subnav on click

		$(this).parent().hover(function() {
		}, function(){	
			$(this).parent().find("ul.subnav").slideUp('fast'); //When the mouse hovers out of the subnav, move it back up
		});

		//Following events are applied to the trigger (Hover events for the trigger)
		}).hover(function() { 
			$(this).addClass("subhover"); //On hover over, add class "subhover"
			$(this).siblings('a').addClass("subhover");
		}, function(){	//On Hover Out
			$(this).removeClass("subhover"); //On hover out, remove class "subhover"
			$(this).siblings('a').removeClass("subhover");
	});
});