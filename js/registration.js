$j = jQuery.noConflict();

$j(function(){

    $j('#ssms-next').click(function(){

        var phone = $j('#phone').val();
        var regex = /[0-9\+]{8,20}/;
        
        if(phone != '' && regex.test(phone)){
            
            var container = $j(this).parent().parent();

            $j('#ssms-back').hide();
            $j('p',container).hide();
            $j('#reg_passmail').show();
            $j('p.submit').show();
            $j('#wp-submit').show();
            $j('#ssms-back').show();
            $j('#ssms-code').show();

            var data = {
                action: 'serwersms_process',
                operation: 'registration',
                phone: phone
            }

            $j.post(ajax_options.admin_ajax_url, data, function(response){

                var resp = JSON.parse(response);

            });
        }
        return false;
    });
    
    $j(document).on('click','#ssms-back',function(){
        var container = $j(this).parent().parent();
        $j('p',container).show();
        startForm();
    });
    
    $j('#code').on('keyup',function(){
        
        var data = {
            action: 'serwersms_process',
            operation: 'verification',
            code: $j('#code').val()
        }
        
        $j.post(ajax_options.admin_ajax_url, data, function(response){
            
            var resp = JSON.parse(response);
            
            $j('#login_error').remove();
            
            if(resp.success === false){
                $j('<div>').attr('id','login_error').insertAfter($j('.message'));
                $j('#login_error').html($j('#login_error').val()+(resp.error+'<br>'));
                return false;
            }
            
        });
        
    });
    
});

function startForm(){
    $j('#wp-submit').hide();
    $j('#reg_passmail').hide();
    $j('#ssms-back').hide();
    $j('#ssms-code').hide();
}
