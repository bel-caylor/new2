( function( $ ) {
	"use strict";

    var PlusExtra = {
		tpgb_Sticky_Column : function(scope) {
            var settings = scope.data('sticky-column'),
            stickyInst = null,
            stickyInstOptions = {
                topSpacing: 40,
                bottomSpacing: 40,
                containerSelector: (scope.hasClass('tpgb-container-col')) ? '.tpgb-container-row' : '.tpgb-container' ,
                innerWrapperSelector: (scope.hasClass('tpgb-container-col')) ? '' :  '.tpgb-column',
                minWidth: 100,
            },
            screenWidth = screen.width ; 

            if ( scope.hasClass('tpgb-column-sticky') ) {
                if( true === settings['sticky'] ){

                   if( (screenWidth >= 1201  && -1 !== settings['stickyOn'].indexOf( 'desktop' )) || (screenWidth <= 1200 && screenWidth >= 768  && -1 !== settings['stickyOn'].indexOf( 'tablet' )) || (screenWidth <= 767 && -1 !== settings['stickyOn'].indexOf( 'mobile' ))){
                      tpgb_stickyColumn();
                      $( window ).on( 'resize orientationchange', tpgbExtraTools.debounce( 50, columnResizeDebounce ) );
                    }
                }
            }
        
            function tpgb_stickyColumn(){
                stickyInstOptions.topSpacing = settings['topSpacing'];
                stickyInstOptions.bottomSpacing = settings['bottomSpacing'];
                scope.data('stickyColumnInit', true);
                stickyInst = new StickySidebar( scope[0], stickyInstOptions );
            }

            function columnResizeDebounce() {
                var availableDevices  = settings['stickyOn'] || [],
                    isInitColumn   = scope.data( 'stickyColumnInit' );

                if ( [] !== availableDevices ) {
                    scope.data( 'stickyColumnInit', true );
                    stickyInst = new StickySidebar( scope[0], stickyInstOptions );
                    stickyInst.updateSticky();
                } else {
                    scope.data( 'stickyColumnInit', false );
                    stickyInst.destroy();
                }
            }
        }
    }
    
    var tpgbExtraTools = {
        debounce: function( threshold, callback ) {
            var timeout;

            return function debounced( $event ) {
                function delayed() {
                    callback.call( this, $event );
                    timeout = null;
                }

                if ( timeout ) {
                    clearTimeout( timeout );
                }

                timeout = setTimeout( delayed, threshold );
            };
        }
    }

    if($('.tpgb-column.tpgb-column-sticky,.tpgb-container-col.tpgb-column-sticky').length ){
		$(window).on('load',function(){
			$('.tpgb-column.tpgb-column-sticky,.tpgb-container-col.tpgb-column-sticky').each(function(){
				PlusExtra.tpgb_Sticky_Column($(this))
			});
		})
    }
})(jQuery);