$(document).ready(function(){    
    $('#account_submit').hide();
    $('#account_history_form').hide();
    $('#edit-acc').on('click',function(){
        $('#account_submit').show();
        $('#account_name').prop('disabled',false);
        $('#account_iban').prop('disabled',false);
        $('#account_bic').prop('disabled',false);
        $(this).hide();
    });
    
    $('#new-acc-hist').on('click',function(){        
        $('#account_history_form').show();
        $('#account_history_date').Zebra_DatePicker({
            format: 'd.m.Y',
            show_clear_date: false,
            days: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
            months: ['Januar','Februar','Maerz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember']
        });
        $(this).hide();
    });
    
    $('.payed-link').on('click',function(event){
        event.preventDefault();
        var datepicker = $(this).siblings().data('Zebra_DatePicker');
        datepicker.show();
    });
    
    $('input.datepicker').Zebra_DatePicker({
            format: 'd.m.Y',
            show_clear_date: false,
            days: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
            months: ['Januar','Februar','Maerz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember']
     });
     
     $('input.datepicker-noico').Zebra_DatePicker({
            show_icon: false,
            format: 'd.m.Y',
            show_clear_date: false,
            days: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
            months: ['Januar','Februar','Maerz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
            onClose: function(view,elements){
                if($(this).val() !== ''){
                    var link = $(this).siblings().attr('href') + '&date=' +$(this).val();
                    console.log($(this).siblings().attr('href') + '&date=' +$(this).val());
                    window.location = link;
                }
            }
     });
     
     $('input.datepicker-range').Zebra_DatePicker({
            format: 'd.m.Y',
            show_clear_date: false,
            days: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
            months: ['Januar','Februar','Maerz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
            onSelect: function(view,elements){
                var link = window.location.pathname;
                link += '?startDate=' +$('#range_start').val();
                link += '&endDate=' +$('#range_end').val();
                window.location = link;
            }
     });
});


