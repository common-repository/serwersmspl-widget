jQuery(document).ready(function($){
    
    $('.serwersms_submit').click(function(){

        var operation = ($(this).attr('id') == 'serwersms_remove') ? 'delete' : 'add';
        var widget_container = $(this).parent().parent().parent();
        
        $('.serwersms_form_phone',widget_container).fadeOut('fast',function(){
            $('.serwersms_loading',widget_container).fadeIn('fast');
        });
        
        var data = {
            action: 'serwersms_process',
            operation: operation,
            phone: $('#serwersms_phone',widget_container).val()
        }
        
        $.post(ajax_options.admin_ajax_url, data, function(response){
            
            var resp = JSON.parse(response);
            $('.serwersms_loading',widget_container).fadeOut('fast',function(){
                
                if(resp.status == 2){
                    $('.serwersms_form_code',widget_container).fadeIn('fast');
                    $('.serwersms_operation',widget_container).val(operation);
                } else if(resp.status == 3){
                    $('.serwersms_message .serwersms_txt',widget_container).html(resp.mess);
                    $('.serwersms_message',widget_container).fadeIn('fast');
                }
                $('.serwersms_loading',widget_container).hide();
                
            });
            
        });
        
    });
    
    
    $('.serwersms_code_submit').click(function(){
        
        var widget_container = $(this).parent().parent().parent();
        var operation = $('.serwersms_operation',widget_container).val();
        
        $('.serwersms_message',widget_container).fadeOut('fast');
        
        $('.serwersms_form_code',widget_container).fadeOut('fast',function(){
            $('.serwersms_loading',widget_container).fadeIn('fast');
        });
        
        var data = {
            action: 'serwersms_process',
            operation: operation,
            code: $('#serwersms_code',widget_container).val()
        }
        
        $.post(ajax_options.admin_ajax_url, data, function(response){
            
            var resp = JSON.parse(response);
            $('.serwersms_loading',widget_container).fadeToggle('fast',function(){
                
                if(resp.status == 2){
                    if(resp.mess){
                        $('.serwersms_message .serwersms_txt',widget_container).html(resp.mess);
                        $('.serwersms_message',widget_container).fadeIn('fast');
                    }
                    $('.serwersms_form_code',widget_container).fadeIn('fast');
                } else if(resp.status == 3){
                    $('.serwersms_message .serwersms_txt',widget_container).html(resp.mess);
                    $('.serwersms_message',widget_container).fadeIn('fast');
                }
                $('.serwersms_loading',widget_container).hide();
                
            });
        });
        
    });
    
    
    $('.serwersms_back').click(function(){
        
        var widget_container = $(this).parent().parent().parent();
        
        $('.serwersms_message',widget_container).fadeOut('fast', function(){
            $('.serwersms_form_phone',widget_container).fadeIn('fast');
        });
        $('.serwersms_loading',widget_container).fadeOut('fast');
        $('.serwersms_form_code',widget_container).fadeOut('fast');
    });
    
});