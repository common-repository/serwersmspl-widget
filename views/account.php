<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="serwersms-header"></div>

<div class="wrap">
    <?php if($result): ?>
    
        <?php if(isset($result['help']['account_maintainer']) and $result['help']['account_maintainer']): ?>

            <div id="account" style="display: block;">
                    <div class="contact">
                        <?php if($result['help']['account_maintainer']->photo): ?>
                            <img alt="<?php echo $result['help']['account_maintainer']->name; ?>" src="<?php echo $result['help']['account_maintainer']->photo; ?>">
                        <?php endif; ?>
                            
                        <?php if($result['help']['account_maintainer']->name): ?>
                        <div class="name">
                            <?php echo $result['help']['account_maintainer']->name; ?>
                        </div>
                        <?php endif; ?>

                        <?php if($result['help']['account_maintainer']->telephone): ?>
                        <div class="phone">
                            <a href="tel:<?php echo $result['help']['account_maintainer']->telephone; ?>"><?php echo $result['help']['account_maintainer']->telephone; ?></a></div>
                        <?php endif; ?>
                            
                        <?php if($result['help']['account_maintainer']->email): ?>
                        <div class="mail">
                            <a href="mailto:<?php echo $result['help']['account_maintainer']->email; ?>"><?php echo $result['help']['account_maintainer']->email; ?></a></div>
                        <?php endif; ?>
                    </div>              

                    <div class="contact-descr">
                        <div class="t1"><?php _e('Maintainer of account','serwersms'); ?></div>
                        <div class="t2">
                            <?php _e('If you have any questions I remain at your disposal.','serwersms'); ?>
                        </div>
                    </div>
            </div>
    
        <?php endif; ?>

    <ul class="params-cont">
        <li class="params-row">
            <div class="param-name">
                SMS ECO
            </div>
            <div class="param-value">
                <?php echo $result['eco'].' '.__('pc','serwersms'); ?>
            </div>
        </li>
        <li class="params-row">
            <div class="param-name">
                SMS FULL
            </div>
            <div class="param-value">
                <?php echo $result['full'].' '.__('pc','serwersms'); ?>
            </div>
        </li>
        <li class="params-row">
            <div class="param-name">
                <?php _e('ACCOUNT TYPE','serwersms'); ?>
            </div>
            <div class="param-value">
                <?php echo $result['type']; ?>
                <?php if($result['type'] == 'PREPAID'): ?>
                    <a href="http://panel.serwersms.pl/index.php?page=kreator" target="_blank" class="serwersms-button" style="margin: -8px 15px -10px"><?php _e('Buy SMS','serwersms'); ?></a>
                <?php endif; ?>
            </div>
        </li>
        <li class="params-row">
            <div class="param-name">
                <?php _e('CONTACT PHONE','serwersms'); ?>
            </div>
            <div class="param-value">
                <?php echo $result['help']['telephone'].', '.$result['help']['info']; ?>
            </div>
        </li>
        <li class="params-row">
            <div class="param-name">
                <?php _e('E-MAIL','serwersms'); ?>
            </div>
            <div class="param-value">
                <a href="mailto:<?php echo $result['help']['email']; ?>"><?php echo $result['help']['email']; ?></a>
            </div>
        </li>
        <li class="params-row">
            <div class="param-name">
                FAQ:
            </div>
            <div class="param-value">
                <a href="<?php echo $result['help']['faq']; ?>"><?php echo $result['help']['faq']; ?></a>
            </div>
        </li>
    </ul>
    <?php else: ?>
        <?php _e('Enter your login and password or if you do not have account on the Platform SerwerSMS.pl, please register now.','serwersms'); ?>
        <br /><br />
        <a href="http://panel.serwersms.pl/rejestracja" target="_blank" class="serwersms-button"><?php _e('Register an account','serwersms'); ?></a>
    <?php endif; ?>
    
</div>