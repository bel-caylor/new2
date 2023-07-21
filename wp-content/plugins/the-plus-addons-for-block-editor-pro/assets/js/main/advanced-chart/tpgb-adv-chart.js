/** Chart Js */
window.addEventListener('DOMContentLoaded', (event) => {
    let allChart = document.querySelectorAll('.tpgb-advanced-chart');
    if(allChart){
        allChart.forEach((ac)=>{
            let canvas = ac.querySelector('canvas'),
                data_settings = JSON.parse(ac.getAttribute('data-settings')),
                data_prepost = JSON.parse(ac.getAttribute('data-prepost'));

            if(data_prepost && data_prepost.xPrePost){
                let xPreText = (data_prepost.xPreText) ? data_prepost.xPreText : '',
                    xPostText = (data_prepost.xPostText) ? data_prepost.xPostText : '';
                data_settings.options.scales.xAxes[0].ticks = {callback: function(val, index) { return xPreText+val+xPostText }};
            }
            if(data_prepost && data_prepost.yPrePost){
                let yPreText = (data_prepost.yPreText) ? data_prepost.yPreText : '',
                    yPostText = (data_prepost.yPostText) ? data_prepost.yPostText : '';
                data_settings.options.scales.yAxes[0].ticks = {callback: function(val, index) { return yPreText+val+yPostText }};
            }
            if(data_settings){
                waypoint = new Waypoint({
                    element: canvas,
                    handler: function () {
                        if(!ac.classList.contains('chart-active')) {
                            var ctx = canvas.getContext('2d');
                            new Chart(ctx,data_settings);   
                            ac.classList.add('chart-active');
                        }
                    },
                    offset: 'bottom-in-view'
                });
            }
        });
    }
});