// Js For Splide Slider
let slideStore = new Map();

document.addEventListener('DOMContentLoaded', function() {
    var scope = document.querySelectorAll('.tpgb-carousel');
    if(scope){
        scope.forEach(function(obj){
            splide_init(obj)
        });
    }
});

function splide_init(ele){
    var slide = new Splide( ele ).mount();
	slideStore.set( ele, slide);
}