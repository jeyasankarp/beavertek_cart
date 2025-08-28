function countdown_timer(main_div_id){
    var days_left=parseInt(jQuery('#'+main_div_id+' .days').text());
    var hours_left=parseInt(jQuery('#'+main_div_id+' .hours').text());;
    var minutes_left=parseInt(jQuery('#'+main_div_id+' .minutes').text());;
    var secondes_left=parseInt(jQuery('#'+main_div_id+' .seconds').text()); 
    var all_time=days_left*24*3600+hours_left*3600+minutes_left*60+secondes_left; 
    all_time--;
    days_left=parseInt(all_time/(3600*24));
    hours_left=parseInt((all_time-days_left*3600*24)/(3600));
    minutes_left=parseInt((all_time-days_left*3600*24-hours_left*3600)/(60));
    secondes_left=parseInt((all_time-days_left*3600*24-hours_left*3600-minutes_left*60));
    
    if((""+days_left+"").length>1)
        jQuery('#'+main_div_id+' .days').html(days_left);
    else
        jQuery('#'+main_div_id+' .days').html('0'+days_left);
    
    if((""+hours_left+"").length>1)
        jQuery('#'+main_div_id+' .hours').html(hours_left);
    else
        jQuery('#'+main_div_id+' .hours').html('0'+hours_left);
    
    if((""+minutes_left+"").length>1)
        jQuery('#'+main_div_id+' .minutes').html(minutes_left);
    else
        jQuery('#'+main_div_id+' .minutes').html('0'+minutes_left);
    
    if((""+secondes_left+"").length>1)
        jQuery('#'+main_div_id+' .seconds').html(secondes_left);
    else
        jQuery('#'+main_div_id+' .seconds').html('0'+secondes_left);
  
    if(days_left<=0 && hours_left<=0 && minutes_left<=0 && secondes_left<=0){
        window.location=document.URL;
    }
}