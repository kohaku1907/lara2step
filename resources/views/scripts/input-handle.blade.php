<script>
    $(function() {

        // Check for on keypress
        $("input").on("keyup", function(event){

            var self = $(this);
            var controls = [37, 38, 39, 40, 46];
            var specialChars = [8, 9, 13, 16, 17, 18, 20, 27, 32, 33, 34, 35, 36, 45, 144, 145];

            var combined = controls.concat(specialChars);

            // Handle Autostepper
            if($.inArray(event.which, controls.concat(specialChars)) === -1){
                if (self.hasClass('last-input')) {
                    $('#confirm-btn').focus();
                } else {
                    self.next().focus();
                }
            }

        });
        // Check for cop and paste
        $("input").on("input", function(){
            var regexp = /[^a-zA-Z0-9]/g;
            if($(this).val().match(regexp)){
                $(this).val( $(this).val().replace(regexp,'') );
            }
        });

        //handle backspace
        $("input").on("keydown", function(event){
            if(event.which == 8){
                if($(this).val() == ""){
                    $(this).prev().focus();
                }
            }
        });

        //handle paste
        $('input[name="code[]"]').on('paste', function(e) {
            var data = e.originalEvent.clipboardData.getData('text');
            if (data) {
                var inputs = $('input[name="code[]"]');
                for (var i = 0; i < data.length; i++) {
                    $(inputs[i]).val(data[i]);
                }
                e.preventDefault();
            }
        });

        //enable confirm button when all inputs are filled
        $('input[name="code[]"]').on('input', function(e) {
            var inputs = $('input[name="code[]"]');
            var filled = true;
            for (var i = 0; i < inputs.length; i++) {
                if (!$(inputs[i]).val()) {
                    filled = false;
                    break;
                }
            }
            if (filled) {
                $('#confirm-btn').prop('disabled', false);
            } else {
                $('#confirm-btn').prop('disabled', true);
            }
        });
    });
</script>