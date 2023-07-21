( function( $ ) {
	"use strict";
    var loopTime , updatedtime = '';
    $('.tp-countdown').each(function() {

        var $this = $(this),
        countData = $this.data('countdata');
        
        if( countData && countData.type && countData.type == 'normal' ){
            if( countData.style && countData.style == 'style-1') {
                WidgetCountDownHandler(countData.blockId);
            }
            if( countData.style && countData.style == 'style-2') {
                styleFlipdown(countData.blockId);
            }
            if( countData.style && countData.style == 'style-3') {
                styleProgressbar(countData.blockId);
            }
        }else if( countData && countData.type && countData.type == 'scarcity' ){

            if( countData.style && countData.style == 'style-1') {
                tpgb_scarcity_1(countData.blockId);
            }else if(countData.style && countData.style == 'style-2'){
                tpgb_scarcity_2(countData.blockId);
            }else if(countData.style && countData.style == 'style-3'){
                tpgb_scarcity_3(countData.blockId)
            }
        }else if(countData && countData.type && countData.type == 'numbers'){
            tpgb_fakenumber_countdown(countData.blockId)
        }
	});
	
	/*Style 1*/
	function WidgetCountDownHandler(blockId) {
		var selector = $('.'+blockId+'.tp-countdown');
		if(selector.length > 0 ) {
			theplus_countdown();
			selector.change(function() {
				theplus_countdown();
			});
			
			function theplus_countdown() {
				selector.each(function () {
					var attrthis = $(this),
					timer1 = attrthis.find('.tpgb-countdown-counter').data("time"),
					offset_timer = attrthis.data("offset"),
					text_days = attrthis.data("day"),
					text_hours = attrthis.data("hour"),
					text_minutes = attrthis.data("min"),
					text_seconds = attrthis.data("sec");
					if(timer1 && timer1 != '') {
						attrthis.downCount({
							date: timer1,
							offset: offset_timer,
							text_day:text_days,
							text_hour:text_hours,
							text_minute:text_minutes,
							text_second:text_seconds,
						}, function () {
							countdownExpiry(blockId);
						});
					}
				});
			}
		}
	};
	/*Style 1*/
    
    /*Style 2*/
	function styleFlipdown(blockId) {
		var future_unixtime_ms = $('.'+blockId+' .flipdown').data('time'),
		blockData = $('.'+blockId),
		offset  = blockData.data('offset'),
		day     = blockData.data('day'),
		hour    = blockData.data('hour'),
		minute  = blockData.data('min'),
		second  = blockData.data('sec'),
        theme  = blockData.data('filptheme');

		day = (day != null) ? day : '';
		hour = (hour != null) ? hour : '';
		minute = (minute != null) ? minute : '';
		second = (second != null) ? second : '';

		if(future_unixtime_ms != null) {
			future_unixtime_ms = future_unixtime_ms - 3600*offset;
			new FlipDown(future_unixtime_ms, blockId, {
                theme: theme,
				headings: [day, hour, minute, second],
			  }).start().ifEnded(() => {
				countdownExpiry(blockId);
			});
		}
	}
	/*Style 2*/
	
	/*Style 3*/
	function styleProgressbar(blockId) {
		var elements = document.getElementById('s'+blockId),
		elementm = document.getElementById('m'+blockId),
		elementh = document.getElementById('h'+blockId),
		elementd = document.getElementById('d'+blockId),
		param = { duration: 200, color: "#000000", trailColor: "#ddd", strokeWidth: 5, trailWidth: 3 };

		if(elements != null ) {

			var seconds = new ProgressBar.Circle(elements, param),
			minutes = new ProgressBar.Circle(elementm, param),
			hours = new ProgressBar.Circle(elementh, param),
			days = new ProgressBar.Circle(elementd, param);
			
			var blockData = $('.'+blockId),
			shortcode_date  = blockData.find('.tpgb-countdown-counter').data('time'),
			textDay         = blockData.data('day'),
			textHour        = blockData.data('hour'),
			textMin         = blockData.data('min'),
			textSec         = blockData.data('sec');

			textDay = (textDay != null) ? textDay : '';
			textHour = (textHour != null) ? textHour : '';
			textMin = (textMin != null) ? textMin : '';
			textSec = (textSec != null) ? textSec : '';

			var countInterval = setInterval(function() {
				var now = new Date();
				var countTo = new Date(shortcode_date);
				var difference = (countTo - now);
				
				var day = Math.floor(difference / (60 * 60 * 1000 * 24) * 1);
				days.animate(day / (day + 5), function() {
					days.setText("<span class = \"number\">" + day + "</span>" + "<span class=\"label\">"+ textDay +"</span>");
				});
				
				var hour = Math.floor((difference % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
				hours.animate(hour / 24, function() {
					hours.setText("<span class=\"number\">" + hour + "</span>" + "<span class=\"label\">"+ textHour +"</span>");
				});
				
				var minute = Math.floor(((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
				minutes.animate(minute / 60, function() {
					minutes.setText("<span class = \"number\">" + minute + "</span>" + "<span class = \"label\">"+ textMin +"</span>");
				});
				
				var second = Math.floor((((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
				seconds.animate(second / 60, function() {
					seconds.setText("<span class = \"number\">" + second + "</span>" + "<span class = \"label\">"+ textSec +"</span>");
				});
				
				if(day + hour + minute + second == 0) {
					countdownExpiry(blockId);
					clearInterval(countInterval);
				}

			}, 1000);
			
		}
	}
	/*Style 3*/
    
    /* scarcity Style 1*/
    function tpgb_scarcity_1(blockId){
        let $this = $('.'+blockId+'.tp-countdown'),
        scardata = $this.data('countdata'),
        sc_countdown = $this[0].querySelectorAll('.tpgb-countdown-counter'),
        addtime = new Date().getTime() + scardata.sctimeli * 60000;

        if( scardata && scardata.storetype  && scardata.storetype  == "cookie"){
            var uid = `tpgb-${scardata.type}-${scardata.style}-${blockId}-${scardata.sctimeli}`
        }

        if( sc_countdown.length > 0 ){

            if( scardata.storetype == "cookie"){
                if( !localStorage.getItem(uid) ){
                    localStorage.setItem(uid, + new Date(addtime) );
                }
                updatedtime = + localStorage.getItem(uid);
                loopTime = updatedtime + scardata.delayminit * 60000;
                let x = $this.s1Interval = setInterval(function() {
                    let minit = Math.floor( (updatedtime - new Date()) / 60000);
                
                    if((minit+1) > 0){
                        let now = new Date(),
                            distance = updatedtime - now,
                            days = Math.floor(distance / (1000 * 60 * 60 * 24)),
                            hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                            minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
                            seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            if( $this[0].querySelectorAll(".days").length > 0 ){
                                $this[0].querySelector(".days").innerHTML = days;
                            }
                            if( $this[0].querySelectorAll(".hours").length > 0 ){
                                $this[0].querySelector(".hours").innerHTML = hours;
                            }
                            if( $this[0].querySelectorAll(".minutes").length > 0 ){
                                $this[0].querySelector(".minutes").innerHTML = minutes;
                            }
                            if( $this[0].querySelectorAll(".seconds").length > 0 ){
                                $this[0].querySelector(".seconds").innerHTML = seconds;
                            }
                    }else{
                        countdownExpiry(blockId,uid);
                        if( scardata.fakeloop == 'no' ){
                            clearInterval(x);
                        }
                    }
                }, 1000);
               
            }else if( scardata.storetype == "normal" ){
                 
                let x = setInterval(function() {
                    let updatedtime = + new Date(addtime),
                        now = new Date(),
                        distance = updatedtime - now;

                    let days = Math.floor(distance / (1000 * 60 * 60 * 24)),
                        hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                        minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
                        seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        if(days + hours + minutes + seconds >= 0){
                            if( $this[0].querySelectorAll(".days").length > 0 ){
                                $this[0].querySelector(".days").innerHTML = days;
                            }
                            if( $this[0].querySelectorAll(".hours").length > 0 ){
                                $this[0].querySelector(".hours").innerHTML = hours;
                            }
                            if( $this[0].querySelectorAll(".minutes").length > 0 ){
                                $this[0].querySelector(".minutes").innerHTML = minutes;
                            }
                            if( $this[0].querySelectorAll(".seconds").length > 0 ){
                                $this[0].querySelector(".seconds").innerHTML = seconds;
                            }
                        }else{
                            clearInterval(x);
                        }
                }, 1000);
            }
        }
    }
    /* scarcity Style 1*/

    /* scarcity Style 2*/
    function tpgb_scarcity_2(blockId){
        let parent = $('.'+blockId),
        scardata = parent.data('countdata'),
        $this = $('.'+blockId+' .flipdown'),
		day     = parent.data('day'),
		hour    = parent.data('hour'),
		minute  = parent.data('min'),
		second  = parent.data('sec'),
        theme  = parent.data('filptheme'),
        $uid = `tpgb-${scardata.type}-${scardata.style}-${blockId}-${scardata.sctimeli}`;
        
		day = (day != null) ? day : '';
		hour = (hour != null) ? hour : '';
		minute = (minute != null) ? minute : '';
		second = (second != null) ? second : '';

        if(parent[0].classList.contains('countdown-style-2') ){
            parent[0].insertAdjacentHTML("afterbegin", `<div id ="${blockId}" class="flipdown tpgb-countdown-counter"></div>`);

            if( scardata.storetype == "cookie"){
                let addtime = new Date().getTime() + scardata.sctimeli * 60000;
                if( !localStorage.getItem($uid) ){
                    localStorage.setItem($uid, + new Date(addtime) );
                }

                let updatedtime = + localStorage.getItem($uid),
                    CounterDate = new Date(updatedtime).getTime() / 1000,
                    minit = Math.floor( (updatedtime - new Date()) / 60000);

                    loopTime = updatedtime + scardata.delayminit * Number(60000);
                
                    if(minit >= 0){
                        new FlipDown( CounterDate, blockId , {
                            theme: theme,
                            headings: [
                                day, 
                                hour, 
                                minute, 
                                second
                            ],
                        })
                        .start()
                        .ifEnded(() => {
                            countdownExpiry(blockId,$uid);
                        });
                    }else{
                        countdownExpiry(blockId,$uid);
                    }
            }
            else if( scardata.storetype == "normal"){
                let addtime = new Date().getTime() + scardata.sctimeli * 60000,
                    updatedtime = + new Date(addtime),
                    CounterDate = new Date(updatedtime).getTime() /1000;
            
                    new FlipDown( CounterDate, blockId , {
                        theme: theme,
                        headings: [
                            day, 
                            hour, 
                            minute, 
                            second
                        ],
                    })
                    .start()
                    .ifEnded(() => {
                    });
            }
        }
    }
    /* scarcity Style 2*/

    /* scarcity Style 3*/
    function tpgb_scarcity_3(blockId){
        let parent = $('.'+blockId+'.countdown-style-3'),
        scardata = parent.data('countdata'),
        $uid = `tpgb-${scardata.type}-${scardata.style}-${blockId}-${scardata.sctimeli}`,
        addtime = new Date().getTime() + scardata.sctimeli * 60000;

        tpgb_progressbar_sethtml(blockId,parent);
        
        var elements = document.getElementById('s'+blockId),
		elementm = document.getElementById('m'+blockId),
		elementh = document.getElementById('h'+blockId),
		elementd = document.getElementById('d'+blockId),
		param = { duration: 200, color: "#000000", trailColor: "#ddd", strokeWidth: 5, trailWidth: 3 };

        var seconds = new ProgressBar.Circle(elements, param),
        minutes = new ProgressBar.Circle(elementm, param),
        hours = new ProgressBar.Circle(elementh, param),
        days = new ProgressBar.Circle(elementd, param);

        var blockData = $('.'+blockId),
        textDay         = blockData.data('day'),
        textHour        = blockData.data('hour'),
        textMin         = blockData.data('min'),
        textSec         = blockData.data('sec');

        textDay = (textDay != null) ? textDay : '';
        textHour = (textHour != null) ? textHour : '';
        textMin = (textMin != null) ? textMin : '';
        textSec = (textSec != null) ? textSec : '';

        if( scardata.storetype == "cookie"){
            if( !localStorage.getItem($uid) ){
                localStorage.setItem($uid, + new Date(addtime) );
            }
            if(elements) {
                let updatedtime = + localStorage.getItem($uid),
                minit = Math.floor( (updatedtime - new Date()) / 60000);

                loopTime = updatedtime + scardata.delayminit * 60000;

                let CounterDate = new Date(updatedtime).getTime();

                if((minit+1) > 0){
                    var countInterval = setInterval(function() {
                        var now = new Date();
                        var countTo = new Date(CounterDate);
                        var difference = (countTo - now);
                        
                        var day = Math.floor(difference / (60 * 60 * 1000 * 24) * 1);
                        days.animate(day / (day + 5), function() {
                            days.setText("<span class = \"number\">" + day + "</span>" + "<span class=\"label\">"+ textDay +"</span>");
                        });
                        
                        var hour = Math.floor((difference % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
                        hours.animate(hour / 24, function() {
                            hours.setText("<span class=\"number\">" + hour + "</span>" + "<span class=\"label\">"+ textHour +"</span>");
                        });
                        
                        var minute = Math.floor(((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
                        minutes.animate(minute / 60, function() {
                            minutes.setText("<span class = \"number\">" + minute + "</span>" + "<span class = \"label\">"+ textMin +"</span>");
                        });
                        
                        var second = Math.floor((((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
                        seconds.animate(second / 60, function() {
                            seconds.setText("<span class = \"number\">" + second + "</span>" + "<span class = \"label\">"+ textSec +"</span>");
                        });
                        
                        if(day + hour + minute + second == 0) {
                            countdownExpiry(blockId);
                            clearInterval(countInterval);
                        }
        
                    }, 1000);
                }else{
                    countdownExpiry(blockId,$uid);
                }

            }
        }else if(scardata.storetype == "normal"){
            if(elements != null ) {

                let updatedtime = + new Date(addtime),
                CounterDate = new Date(updatedtime).getTime();
    
                var countInterval = setInterval(function() {
                    var now = new Date();
                    var countTo = new Date(CounterDate);
                    var difference = (countTo - now);
                    
                    var day = Math.floor(difference / (60 * 60 * 1000 * 24) * 1);
                    days.animate(day / (day + 5), function() {
                        days.setText("<span class = \"number\">" + day + "</span>" + "<span class=\"label\">"+ textDay +"</span>");
                    });
                    
                    var hour = Math.floor((difference % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
                    hours.animate(hour / 24, function() {
                        hours.setText("<span class=\"number\">" + hour + "</span>" + "<span class=\"label\">"+ textHour +"</span>");
                    });
                    
                    var minute = Math.floor(((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
                    minutes.animate(minute / 60, function() {
                        minutes.setText("<span class = \"number\">" + minute + "</span>" + "<span class = \"label\">"+ textMin +"</span>");
                    });
                    
                    var second = Math.floor((((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
                    seconds.animate(second / 60, function() {
                        seconds.setText("<span class = \"number\">" + second + "</span>" + "<span class = \"label\">"+ textSec +"</span>");
                    });
                    
                    if(day + hour + minute + second == 0) {
                        countdownExpiry(blockId,$uid);
                        clearInterval(countInterval);
                    }
    
                }, 1000);
                
            }
        }
    }
    /* scarcity Style 3*/

    /* Fake Number Count  */
    function tpgb_fakenumber_countdown(blockId){
        let parent = $('.'+blockId),
        faData = parent.data('countdata');

        if( faData && faData.storetype  && faData.storetype  == "cookie"){
            var uid = `tpgb-${faData.type}-${blockId}-${faData.initNum}`;
        }

        let SetInterval,
        digit = parent[0].querySelector('.tpgb-number-count'),
        initNum = faData.initNum,
        end = faData.endNum,
        range = faData.numRange,
        interval = faData.changeInterval,
        loop = faData.fenaLoop,
        massage = faData.massage;

        digit.innerHTML = '<span class="tpgb-fake-text">' + massage.replace('{visible_counter}', `<span class="tpgb-fake-visiblecounter">${initNum}</span>` )+ '</span>';

        if( Number(initNum) >= Number(end) ){
            let startcount,remaining;
            if(faData.storetype == "normal"){
                startcount = initNum;
            }else if(faData.storetype == "cookie"){
                if(!localStorage.getItem(uid)){
                    tpgb_fakereset( uid, initNum);
                }
            }

            SetInterval = setInterval(function(){
                let randomNum = ( Number(range) > 1) ? Math.round( Math.random() * Number(range) ) : Number(1);
                    if(faData.storetype == "normal"){
                        remaining = initNum = initNum - randomNum;
                    }else if(faData.storetype == "cookie"){
                        remaining = initNum = localStorage.getItem(uid) - randomNum;
                        tpgb_fakereset( uid, initNum);
                    }

                    if( Number(remaining) >= Number(end) ){
                        tpgb_fake_addtext(massage,remaining, digit);
                    }else{
                        if(faData.fenaLoop && faData.fenaLoop == 'yes' ){
                            if(faData.storetype == "normal"){
                                initNum = startcount;
                            }else if(faData.storetype == "cookie"){
                                tpgb_fakereset( uid, initNum);
                            }
                        }else{
                            clearInterval(SetInterval);
                        }
                    }
            }, interval * 1000)
        }
        if( initNum <= end) {
            let startcount, remaining;
            if(faData.storetype == "normal"){
                startcount = initNum;
            }else if(faData.storetype == "cookie"){
                startcount = initNum;
            }

            SetInterval = setInterval(function(){
                let randomNum = (Number(range) > 1) ? Math.round( Math.random() * range ) : Number(1);

                    if(faData.storetype == "normal"){
                        remaining = initNum = Number(initNum) + Number(randomNum);
                    }else if(faData.storetype == "cookie"){
                        remaining = initNum = Number(localStorage.getItem(uid)) + Number(randomNum);
                        tpgb_fakereset(uid, initNum);
                    }

                    if( Number(remaining) <= Number(end) ){
                        tpgb_fake_addtext(massage, remaining, digit);
                    }else{
                        if(faData.fenaLoop && faData.fenaLoop == 'yes' ){
                            if(faData.storetype == "normal"){
                                initNum = startcount;
                            }else if(faData.storetype == "cookie"){
                                tpgb_fakereset(uid, startcount);
                            }
                        }else{
                            clearInterval(SetInterval);
                        }
                    }
            }, interval * 1000);
        }
    }
    /* Fake Number Count  */
    function tpgb_fakereset(Name, value) {
        localStorage.setItem(Name,value);
    }

    // Fake Text
    function tpgb_fake_addtext(massage, Plusvalue, digit){
        let Newval =  '<span class="tpgb-fake-text">' + massage.replace('{visible_counter}', `<span class="tpgb-fake-visiblecounter">${ Math.floor( Plusvalue ) }</span>` ) + '</span>';

        digit.innerText = "";
        if(Newval){
            digit.insertAdjacentHTML("afterbegin", Newval)
        }else{
            digit.insertAdjacentHTML("afterbegin", Math.floor( Plusvalue ))
        }
    }

    /*counter Expired*/
	function countdownExpiry(blockId,localuiId) {

        var blockData = $('.'+blockId+'.tp-countdown'),
        Basic = blockData.data('countdata');

        if( ( Basic.fakeloop && Basic.fakeloop == 'yes' && Basic.expiry == 'none' ) ){
            if( Basic.type == "scarcity" ){
                if( Basic.style == "style-1" ){
                   
                    let minittt = Math.floor( ( loopTime - new Date()) / 60000),
                        addtime = new Date().getTime() + Basic.sctimeli * 60000;
                        if( minittt < 0){
                            localStorage.removeItem(localuiId);
                            updatedtime = localStorage.setItem(localuiId, + new Date(addtime));
                            tpgb_scarcity_1(blockId);
                        }
                }
                else if( Basic.style == "style-2" ){
                    let x = setInterval(function() {
                        let minittt = Math.floor( ( loopTime - new Date()) / 60000);
                        blockData[0].innerHTML = "";
                        if( minittt < 0 ){
                            localStorage.removeItem(localuiId);
                            clearInterval(x);
                            tpgb_scarcity_2(blockId);
                        }
                    }, 1000);
                } else if( Basic.style == "style-3" ){
                    let x = setInterval(function() {
                        let minittt = Math.floor( ( loopTime - new Date()) / 60000);
                        blockData[0].innerHTML = "";
                        if( minittt < 0 ){
                            localStorage.removeItem(localuiId);
                            clearInterval(x);
                            tpgb_scarcity_3(blockId);
                        }
                    }, 1000);
                }
            }
        }
               
        if(  Basic.expiry != "none" ){
            let expiry = (Basic) ? Basic.expiry : '',
                minittt = Math.floor( ( loopTime - new Date()) / 60000),
                addtime = new Date().getTime() + Basic.sctimeli * 60000;

                if( expiry == "redirect" ){
                    if( Basic.type == "scarcity" ){
                        if( Basic.style == "style-1" ){
                            if(Basic.fakeloop && Basic.fakeloop == 'yes'){
                                if( minittt < 0){
                                    localStorage.removeItem(localuiId);
                                    updatedtime = localStorage.setItem( localuiId + new Date(addtime));
                                    tpgb_scarcity_1(blockId);
                                }else{
                                    window.location.href = decodeURIComponent(Basic.redirect);
                                }
                            }else{
                                window.location.href = decodeURIComponent(Basic.redirect); 
                            }
                        }else if( Basic.style == "style-2" ){
                            if(Basic.fakeloop && Basic.fakeloop == 'yes'){
                                let x = setInterval(function() {
                                    let minittt = Math.floor( ( loopTime - new Date()) / 60000);
                                        if( minittt < 0 ){
                                            localStorage.removeItem(localuiId);
                                            clearInterval(x);
                                            tpgb_scarcity_2(blockId);
                                        }else{
                                           window.location.href = decodeURIComponent(Basic.redirect);
                                        }
                                }, 1000);
                            }else{
                                window.location.href = decodeURIComponent(Basic.redirect); 
                            }
                        }else if( Basic.style == "style-3" ){
                            let removecountdown = blockData[0].querySelectorAll(".tpgb-countdown-counter");
                            if( removecountdown.length > 0 ){
                                removecountdown[0].remove();
                            }
                            if( Basic.fakeloop && Basic.fakeloop == 'yes' ){
                                let x = setInterval(function() {
                                    let minittt = Math.floor( ( loopTime - new Date()) / 60000);
                                    if( minittt < 0 ){
                                        localStorage.removeItem(localuiId);
                                        clearInterval(x);
                                        tpgb_scarcity_3(blockId);
                                    }else{
                                        window.location.href = decodeURIComponent(Basic.redirect);
                                    }
                                }, 1000);
                            }else{
                               window.location.href = decodeURIComponent(Basic.redirect);  
                            }
                        }
                    }else if( Basic.type == "normal" ){
                        window.location.href = decodeURIComponent(Basic.redirect);  
                    }
                }else if( expiry == "showmsg" ){
                    if( Basic.type == "scarcity" ){
                        if( Basic.style == "style-1" ){
                            let FindCountDown = blockData[0].querySelectorAll(".tpgb-countdown-expiry"),
                                pt_plusclass = blockData[0].querySelectorAll(".tpgb-countdown-counter");

                                if( minittt < 0 ){
                                    localStorage.removeItem(localuiId);
                                    updatedtime = localStorage.setItem(localuiId, + new Date(addtime));

                                    if( FindCountDown.length > 0 ){
                                        FindCountDown[0].remove();
                                    }
                                    pt_plusclass[0].style.display = 'block';
                                    tpgb_scarcity_1(blockId);
                                }else{
                                    if( FindCountDown.length == 0 ){
                                        pt_plusclass[0].style.display = 'none';
                                        blockData[0].insertAdjacentHTML("afterbegin", `<div class="tpgb-countdown-expiry">${Basic.expiryMsg}</div>`);
                                    }
                                }
                        }else if( Basic.style == "style-2" ){
                            if( Basic.fakeloop && Basic.fakeloop == 'yes' ){
                                let x = setInterval(function() {
                                    let minitim = Math.floor( ( loopTime - new Date()) / 60000 );
                                        blockData[0].innerHTML = "";
                                        if( minitim < 0 ){
                                            localStorage.removeItem(localuiId);
                                            clearInterval(x);
                                            tpgb_scarcity_2(blockId);
                                        }else{
                                            blockData[0].innerHTML = `<div class="tpgb-countdown-expiry">${Basic.expiryMsg}</div>`;
                                        }
                                }, 1000);
                            }else{
                                blockData[0].innerHTML = "";
                                blockData[0].innerHTML = `<div class="tpgb-countdown-expiry">${Basic.expiryMsg}</div>`;
                            }
                        }else if( Basic.style == "style-3" ){
                            if(Basic.fakeloop && Basic.fakeloop == 'yes' ){
                                let x = setInterval(function() {
                                    let minittt = Math.floor( ( loopTime - new Date()) / 60000);
                                    if( minittt < 0 ){
                                        localStorage.removeItem(localuiId);
                                        blockData[0].innerHTML = "";
                                        clearInterval(x);
                                        tpgb_scarcity_3(blockId)
                                    }else{
                                        blockData[0].innerHTML = "";
                                        blockData[0].innerHTML = `<div class="tpgb-countdown-expiry">${Basic.expiryMsg}</div>`;
                                    }
                                }, 1000);
                            }else{
                                blockData[0].innerHTML = "";
                                blockData[0].innerHTML = `<div class="tpgb-countdown-expiry">${Basic.expiryMsg}</div>`;
                            }
                        }
                    }else if( Basic.type == "normal" ){
                        blockData[0].innerHTML = "";
                        blockData[0].innerHTML = `<div class="tpgb-countdown-expiry">${Basic.expiryMsg}</div>`;
                    }
                   
                }else if( Basic.expiry == "showtemp" ){
                    let expriytmp = blockData[0].querySelectorAll(".tpgb-countdown-expiry");
                    if( Basic.type == "scarcity" ){
                        if( Basic.style == "style-1" ){
                            let pt_plusclass = blockData[0].querySelectorAll(".tpgb-countdown-counter");
                                if( minittt < 0 ){
                                    localStorage.removeItem(localuiId);
                                    updatedtime = localStorage.setItem(localuiId, + new Date(addtime));
                                    
                                    if( pt_plusclass.length > 0 && pt_plusclass[0].classList.contains('tpgb-hide') ){
                                        pt_plusclass[0].classList.remove('tpgb-hide');
                                    }
                                    if( expriytmp.length > 0 ){
                                        expriytmp[0].classList.add('tpgb-hide');
                                    }
                                    tpgb_scarcity_1(blockId);
                                }else{
                                    pt_plusclass[0].classList.add('tpgb-hide');

                                    if( expriytmp.length > 0 && expriytmp[0].classList.contains('tpgb-hide') ){
                                        expriytmp[0].classList.remove('tpgb-hide');
                                    }
                                    tpgb_resize_ele();
                                }
                        }else if(Basic.style == "style-2"){
                            let removecountdown = blockData[0].querySelectorAll(".flipdown.tpgb-countdown-counter");
                                if( removecountdown.length > 0 ){
                                    removecountdown[0].remove();
                                }
                                if( expriytmp && expriytmp[0] && expriytmp[0].classList.contains('tpgb-hide') ){
                                    expriytmp[0].classList.remove('tpgb-hide');
                                    expriytmp[0].classList.add('tpgb-show');
                                    tpgb_resize_ele();
                                }
                                if(Basic.fakeloop && Basic.fakeloop == 'yes'){
                                    let x = setInterval(function() {
                                        let minittt = Math.floor( (loopTime - new Date()) / 60000);
                                        if( minittt < 0 ){
                                            localStorage.removeItem(localuiId);

                                            if( expriytmp && expriytmp[0] && expriytmp[0].classList.contains('tpgb-show') ){
                                                expriytmp[0].classList.remove('tpgb-show');
                                            }
                                            if( expriytmp && expriytmp[0] && !expriytmp[0].classList.contains('tpgb-hide') ){
                                                expriytmp[0].classList.add('tpgb-hide');
                                            }

                                            clearInterval(x);
                                            tpgb_scarcity_2(blockId)
                                        }else{
                                            //blockData[0].innerHTML = `<div class="tpgb-countdown-expiry"></div>`;
                                        }
                                    }, 1000);
                                }
                        }else if(Basic.style == "style-3"){
                            let removecountdown = blockData[0].querySelectorAll(".tpgb-countdown-counter");
                            if( removecountdown.length > 0 ){
                                removecountdown[0].remove();
                            }
                            if( expriytmp[0].classList.contains('tpgb-hide') ){
                                expriytmp[0].classList.remove('tpgb-hide');
                                expriytmp[0].classList.add('tpgb-show');
                                tpgb_resize_ele();
                            }
                            if( Basic.fakeloop && Basic.fakeloop == 'yes'){
                                let x = setInterval(function() {
                                    let minittt = Math.floor( (loopTime - new Date()) / 60000);
                                    if( minittt < 0 ){
                                        localStorage.removeItem(localuiId);

                                        if( expriytmp[0].classList.contains('tpgb-show') ){
                                            expriytmp[0].classList.remove('tpgb-show');
                                        }
                                        if( !expriytmp[0].classList.contains('tpgb-hide') ){
                                            expriytmp[0].classList.add('tpgb-hide');
                                        }

                                        clearInterval(x);
                                        tpgb_scarcity_3(blockId)
                                    }
                                }, 1000);
                            }
                        }
                    } else if( Basic.type == "normal" ){
                        let expriytmp = blockData[0].querySelectorAll(".tpgb-countdown-expiry"),
                            removecountdown = blockData[0].querySelectorAll(".tpgb-countdown-counter");
                            if( removecountdown.length > 0 ){
                                removecountdown[0].remove();
                            }
                            if( expriytmp[0].classList.contains('tpgb-hide') ){
                                expriytmp[0].classList.remove('tpgb-hide');
                                expriytmp[0].classList.add('tpgb-show');
                                tpgb_resize_ele();
                            }
                    }
                }   
        }
	}
	/*counter Expired*/

    function tpgb_progressbar_sethtml(blockId,mainDiv){
        if(blockId){
            let $HTML = `<div class="tpgb-countdown-counter"><div class="counter-part" id="d${blockId}"></div><div class="counter-part" id="h${blockId}"></div><div class="counter-part" id="m${blockId}"></div><div class="counter-part" id="s${blockId}"></div></div>`;
                        
            mainDiv[0].insertAdjacentHTML("afterbegin", $HTML);
        }
    }

    function tpgb_resize_ele(){

        // For Grid & masonary Layout
        let resize = document.querySelectorAll(".tpgb-isotope");
        resize.forEach(function(item, index) {
            let isoclass = item.querySelector('.post-loop-inner');
            if( isoclass != null ){
                setTimeout(function(){
                    $(isoclass).isotope('reloadItems').isotope();
                }, 300);
            }
        })

        // For Carouse Layout
        let caroDiv = document.querySelectorAll(".tpgb-carousel");
        caroDiv.forEach(function(item, index) {
            var splideInit = slideStore.get(item);
            splideInit.refresh();
        })

        // For Metro Layout
        let metroDiv = document.querySelectorAll(".tpgb-metro");
        if(metroDiv && metroDiv != null){
            tpgb_metro_layout('');
        }
    }
})(jQuery);