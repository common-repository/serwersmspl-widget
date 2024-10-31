$j = jQuery.noConflict();

$j(function(){

    $j('#text').charactersCounter({
        correctPolishChars: true
    });
    
    $j('#phone').click(function(){
        $j('#send_to_number').trigger('click');
    });
    
    $j('#group_select').click(function(){
        $j('#send_to_group').trigger('click');
    });
    
    $j('#type_recive').change(function(){
        $j('#form_recive').submit();
    });
    
    $j('#group_select').change(function(){
        $j('#form_group').submit();
    });
    
    $j('#reports_select').change(function(){
        $j('#form_reports').submit();
    });
});

(function($j){
    
    $j.fn.charactersCounter = function(param){
        
        var defaults = {
            charsCounterField: "chars",
            messageCounterField: "messages",
            charsLimitField: "chars-limit",
            correctPolishChars: false
        };
        
        var options = $j.extend(defaults,param);
        var exceptionsPattern = /\n|\^|\||~|\[|\]|\{|\}/;

        function countCharacters(textField){
            
            var mess = $j(textField).val();
            var sum = mess.length;
            var count = sum;
            var chars_limit = $j('#'+options.charsLimitField).val();
            var double = 0;
            var parts = 0;
            
            for(var i=0;i<=sum;i++){
                if(mess.charAt(i).match(exceptionsPattern)){
                    count++;
                    double++;
                }
            }
            
            if(count>=chars_limit){
                if(mess.charAt(mess.length-1).match(exceptionsPattern)){
                    mess = mess.substr(0,chars_limit-(double-1));
                } else {
                    mess = mess.substr(0,chars_limit-double);
                }
                count = chars_limit;
            }
            
            if(count<=160) parts = 1;
            else if(count<=306) parts = 2;
            else if(count<=459) parts = 3;
            else if(count<=612) parts = 4;
            else if(count<=765) parts = 5;
            else if(count<=918) parts = 6;
			else if(count<=1071) parts = 7;
			else if(count<=1224) parts = 8;
			else if(count<=1377) parts = 9;
			else if(count<=1530) parts = 10;
            
            $j(textField).val(mess);
            $j('#'+options.charsCounterField).text(count);
            $j('#'+options.messageCounterField).text(parts);
            
            if(options.correctPolishChars === true){
                correctPolishChars(textField);
            }
        }
        
        function correctPolishChars(textField){
            
            var ver = getInternetExplorerVersion();
            
            if(ver < 0) {
                
                var pos = $j(textField).get(0).selectionStart;
                var polishChars = /[\ą\ę\ś\ć\ń\ó\ł\ź\ż\Ą\Ę\Ż\Ź\Ć\Ń\Ł\Ó]/;
              
                if($j(textField).val().match(polishChars)) {

                    var mess = $j(textField).val()
                            .replace(/ą/g, 'a').replace(/Ą/g, 'A')
                            .replace(/ć/g, 'c').replace(/Ć/g, 'C')
                            .replace(/ę/g, 'e').replace(/Ę/g, 'E')
                            .replace(/ł/g, 'l').replace(/Ł/g, 'L')
                            .replace(/ń/g, 'n').replace(/Ń/g, 'N')
                            .replace(/ó/g, 'o').replace(/Ó/g, 'O')
                            .replace(/ś/g, 's').replace(/Ś/g, 'S')
                            .replace(/ż/g, 'z').replace(/Ż/g, 'Z')
                            .replace(/ź/g, 'z').replace(/Ź/g, 'Z');

                    if (document.selection) {
                        //IE, nic nie rób
                    } else {
                        $j(textField).val(mess);
                        $j(textField).get(0).selectionStart = pos;
                        $j(textField).get(0).selectionEnd = pos;
                    }
                }
            }
        }
        
        function getInternetExplorerVersion() {
            
            var rv = -1; // Return value assumes failure.
            
            if (navigator.appName === 'Microsoft Internet Explorer') {
                
                var ua = navigator.userAgent;
                var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
                if (re.exec(ua) !== null)
                rv = parseFloat( RegExp.$1 );
            }
            return rv;
        }
        
        $j(this).ready(function(){
            countCharacters(this);
        });
        
        $j(this).change(function(){
            countCharacters(this);
        });
        
        $j(this).focus(function(){
            countCharacters(this);
        });
        
        $j(this).blur(function(){
            countCharacters(this);
        });
        
        $j(this).click(function(){
            countCharacters(this);
        });
        
        $j(this).keyup(function(){
            countCharacters(this);
        });
        
        $j(this).keydown(function(){
            countCharacters(this);
        });
        
        $j(this).keypress(function(){
            countCharacters(this);
        });
    };
    
})(jQuery);