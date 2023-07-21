( function( $ ) {
	"use strict";
	$('.tpgb-heading-animation').each(function(){
		var $this =  $(this),
			settings = $this.data('settings');
		if('textAnim' === settings.style){
			tpgbHeadingAnimation( $this, settings.animStyle )
		}
	});
	function tpgbHeadingAnimation(el, animStyle){
		var animDelay= 2500,
			charDelay= 70,
			revealDurationTime= 600,
			revealAnimateDelay= 1500;
		
		function getNextWord(word) {
			return word.is(':last-child') ? word.parent().children().eq(0) : word.next();
		}
		
		function ReplaceActWord(oldWord, newWord) {
			oldWord.removeClass('heading-text-active').addClass('heading-text-inactive');
			newWord.removeClass('heading-text-inactive').addClass('heading-text-active');
			setWrapWidth(newWord);
		}
		
		function setWrapWidth(newWord) {
			if (animStyle=='style-2' || animStyle=='style-3' || animStyle=='style-5' || animStyle=='style-6' || animStyle=='style-7' || animStyle=='style-8') {
			  newWord.closest('.heading-text-wrap').css('width', newWord.width());
			}else if(animStyle=='style-4'){
				setTimeout(function(){ newWord.closest('.heading-text-wrap').css('width', newWord.width()); }, 600);
			}
		}
		
		function hideChar(letter, word, bool, duration) {
			word.find('.letter').removeClass('letter-anim-in');
			if(!letter.is(':last-child')) {
				setTimeout(function(){ hideChar(letter.next(), word, bool, duration) }, duration);  
			} else if(bool) { 
				setTimeout(function(){ hideWord(getNextWord(word)) }, 1000);
			}
		}
		
		function showChar(letter, word, bool, duration) {
			letter.addClass('letter-anim-in');
			if (!letter.is(':last-child')) {
				setTimeout(function(){ showChar(letter.next(), word, bool, duration) }, duration);
			} else if (!bool) {
				var delayDuration = 1000;
				if(animStyle=='style-2' || animStyle=='style-3' || animStyle=='style-5' || animStyle=='style-6' || animStyle=='style-7' || animStyle=='style-8' ){
					delayDuration = 1000
				}
				setTimeout(function(){ hideWord(word) }, delayDuration);
			}
		}
		
		function showWord(word) {
			if (animStyle=='style-1') {
				if(word[0].dataset && word[0].dataset.duration){
					revealDurationTime = parseInt(word[0].dataset.duration);
				}else{
					revealDurationTime = 600;
				}
			  word.closest('.heading-text-wrap').animate({
				width: word.width() + 10
			  }, revealDurationTime, function () {
				setTimeout(function(){ hideWord(word) }, revealAnimateDelay);
			  });
			}else if(animStyle=='style-4'){
				setTimeout(function(){ hideWord(word) }, 600);
			}
		}
		
		function hideWord(word) {
			var nextWord = getNextWord(word),
				letterSelector = '.letter';
			if (animStyle=='style-1') {
				word.closest('.heading-text-wrap').animate({
					width: '2px'
				}, revealDurationTime, function(){
					ReplaceActWord(word, nextWord);
					showWord(nextWord);
				});
			}else if(animStyle=='style-2' || animStyle=='style-3' || animStyle=='style-5' || animStyle=='style-6' || animStyle=='style-7'|| animStyle=='style-8'){
				if(animStyle=='style-3'){
					charDelay= 150
				}else if(animStyle=='style-5' || animStyle=='style-6'){
					charDelay = 50
				}else if(animStyle=='style-7'){
					charDelay = 45
				}else if(animStyle=='style-8'){
					charDelay = 30
				}
				var bool = (word.children(letterSelector).length >= nextWord.children(letterSelector).length) ? true : false;
				hideChar(word.find(letterSelector).eq(0), word, bool, charDelay);
				showChar(nextWord.find(letterSelector).eq(0), nextWord, bool, charDelay);
				setWrapWidth(nextWord);
			}else if(animStyle=='style-4'){
				setTimeout(function(){
					ReplaceActWord(word, nextWord);
					showWord(nextWord); 
				},1400);
			}
		}
		
		function onInit(){
			var TextWrap = $(el).find('.heading-text-wrap');
			if(TextWrap.find('.letter').length > 0){
				TextWrap.find('.letter').each(function(){
					$(this).html($(this).text().replace(/ /g, '&nbsp;'));
				})
			}
			if(animStyle=='style-1'){
				TextWrap.width(TextWrap.width() + 10);
			}
			if(animStyle=='style-4'){
				TextWrap.width(TextWrap.width());
				animDelay = 800
			}
			if(animStyle=='style-2'){
				animDelay = 950
			}
			if(animStyle=='style-3'){
				animDelay = 2250
			}
			if(animStyle=='style-5' || animStyle=='style-6'){
				animDelay = 750
			}
			if(animStyle=='style-7'){
				animDelay = 1300
			}
			if(animStyle=='style-8'){
				animDelay = 1400
			}
			setTimeout(function(){
				hideWord(jQuery(el).find('.heading-text-active').eq(0)) 
			}, animDelay);
		}
		
		onInit()
	}
})(jQuery);