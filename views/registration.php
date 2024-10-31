<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if($options['phone_registration_field'] == 1): ?>
    <p>
        <label for="phone"><?php _e('Phone','serwersms'); ?><br />
            <input type="text" name="phone" id="phone" class="input" value="<?php echo esc_attr(wp_unslash($phone)); ?>" size="25" />
        </label>
    </p>
    <p id="ssms-code" style="display: none">
        <label for="code"><?php _e('SMS code','serwersms'); ?><br />
            <input type="text" name="code" id="code" class="input" size="25" />
        </label>
    </p>

    <?php if($options['verification_code_registration'] == 1): ?>
        <script>
            $j(function(){
                startForm();
                $j('p.submit').prepend($j('<input>').attr({
                    id: 'ssms-back',
                    class: 'button button-secondary button-large',
                    type: 'button',
                    value: '<?php _e('Back','serwersms'); ?>',
                    style: "display: none"
                }));
            });
        </script>

        <p>
            <input type="button" class="button button-primary button-large" value="<?php _e('Next','serwersms'); ?>" id="ssms-next" />
        </p>
    <?php endif; ?>
<?php endif; ?>