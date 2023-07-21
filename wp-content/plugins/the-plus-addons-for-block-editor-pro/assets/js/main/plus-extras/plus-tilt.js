/* Global Tilt */
window.addEventListener('DOMContentLoaded', (event) => {
    let allTilt = document.querySelectorAll('.tpgb-jstilt');
    if(allTilt){
        allTilt.forEach((at)=>{
            let settings = at.getAttribute('data-tiltsetting');
            settings = JSON.parse(settings);
            VanillaTilt.init(at, {
                ...settings
            });
        });
    }
});