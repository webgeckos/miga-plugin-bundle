/* script for countdown shortcode */
var timecircleanimation = timecircledata2.time_animation;
var timestop = timecircledata2.timestop;
var timemessage = timecircledata2.message;
var color_bg = timecircledata2.circle_bg_color;
jQuery(document).ready(function($) {
    $(".timecircle").TimeCircles({
        animation: timecircleanimation,
        circle_bg_color: color_bg,
        count_past_zero: parseInt(timestop,10)
    }).addListener(function(unit, value, total) {
        if(total === 0) {
            alert(timemessage);
        }
    });
});
