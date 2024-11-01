<style>
    .section {
        margin-left: -20px;
        margin-right: -20px;
        font-family: "Raleway", san-serif;
    }

    .section h1 {
        text-align: center;
        text-transform: uppercase;
        color: #808a97;
        font-size: 35px;
        font-weight: 700;
        line-height: normal;
        display: inline-block;
        width: 100%;
        margin: 50px 0 0;
    }

    .section ul {
        list-style-type: disc;
        padding-left: 15px;
    }

    .section:nth-child(even) {
        background-color: #fff;
    }

    .section:nth-child(odd) {
        background-color: #f1f1f1;
    }

    .section .section-title img {
        display: table-cell;
        vertical-align: middle;
        width: auto;
        margin-right: 15px;
    }

    .section h2,
    .section h3 {
        display: inline-block;
        vertical-align: middle;
        padding: 0;
        font-size: 24px;
        font-weight: 700;
        color: #808a97;
        text-transform: uppercase;
    }

    .section .section-title h2 {
        display: table-cell;
        vertical-align: middle;
        line-height: 25px;
    }

    .section-title {
        display: table;
    }

    .section h3 {
        font-size: 14px;
        line-height: 28px;
        margin-bottom: 0;
        display: block;
    }

    .section p {
        font-size: 13px;
        margin: 15px 0;
    }

    .section ul li {
        margin-bottom: 4px;
    }

    .landing-container {
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        padding: 50px 0 30px;
    }

    .landing-container:after {
        display: block;
        clear: both;
        content: '';
    }

    .landing-container .col-1,
    .landing-container .col-2 {
        float: left;
        box-sizing: border-box;
        padding: 0 15px;
    }

    .landing-container .col-1 img {
        width: 100%;
    }

    .landing-container .col-1 {
        width: 55%;
    }

    .landing-container .col-2 {
        width: 45%;
    }

    .premium-cta {
        background-color: #808a97;
        color: #fff;
        border-radius: 6px;
        padding: 20px 15px;
    }

    .premium-cta:after {
        content: '';
        display: block;
        clear: both;
    }

    .premium-cta p {
        margin: 7px 0;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        width: 60%;
    }

    .premium-cta a.button {
        border-radius: 6px;
        height: 60px;
        float: right;
        background: url(<?php echo YITH_WCGEOIP_ASSETS_URL ?>images/upgrade.png) #ff643f no-repeat 13px 13px;
        border-color: #ff643f;
        box-shadow: none;
        outline: none;
        color: #fff;
        position: relative;
        padding: 9px 50px 9px 70px;
    }

    .premium-cta a.button:hover,
    .premium-cta a.button:active,
    .premium-cta a.button:focus {
        color: #fff;
        background: url(<?php echo YITH_WCGEOIP_ASSETS_URL ?>images/upgrade.png) #971d00 no-repeat 13px 13px;
        border-color: #971d00;
        box-shadow: none;
        outline: none;
    }

    .premium-cta a.button:focus {
        top: 1px;
    }

    .premium-cta a.button span {
        line-height: 13px;
    }

    .premium-cta a.button .highlight {
        display: block;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }

    .premium-cta .highlight {
        text-transform: uppercase;
        background: none;
        font-weight: 800;
        color: #fff;
    }

    .section.one {
        background: url(<?php echo YITH_WCGEOIP_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff;
        background-position: 85% 75%
    }

    .section.two {
        background: url(<?php echo YITH_WCGEOIP_ASSETS_URL ?>/images/02-bg.png) no-repeat #fff;
        background-position: 15% 100%;
    }

    .section.three {
        background: url(<?php echo YITH_WCGEOIP_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff;
        background-position: 85% 75%
    }

    .section.four {
        background: url(<?php echo YITH_WCGEOIP_ASSETS_URL ?>/images/04-bg.png) no-repeat #fff;
        background-position: 15% 100%;
    }

    @media (max-width: 768px) {
        .section {
            margin: 0
        }

        .premium-cta p {
            width: 100%;
        }

        .premium-cta {
            text-align: center;
        }

        .premium-cta a.button {
            float: none;
        }
    }

    @media (max-width: 480px) {
        .wrap {
            margin-right: 0;
        }

        .section {
            margin: 0;
        }

        .landing-container .col-1,
        .landing-container .col-2 {
            width: 100%;
            padding: 0 15px;
        }

        .section-odd .col-1 {
            float: left;
            margin-right: -100%;
        }

        .section-odd .col-2 {
            float: right;
            margin-top: 65%;
        }
    }

    @media (max-width: 320px) {
        .premium-cta a.button {
            padding: 9px 20px 9px 70px;
        }

        .section .section-title img {
            display: none;
        }
    }
</style>

<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
					<?php echo sprintf( __( 'Upgrade to %1$spremium version%2$s of %1$sYITH GeoIP Language Redirect for WooCommerce%2$s to benefit from all features!', 'yith-geoip-languague-redirect-for-woocommerce' ), '<span class="highlight">', '</span>' ); ?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e( 'UPGRADE', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></span>
                    <span><?php _e( 'to the premium version', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e( 'Premium Features', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCGEOIP_URL . '/assets' ?>/images/01.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCGEOIP_URL . '/assets' ?>/images/01-icon.png" />
                    <h2><?php _e( 'Choose how to apply the redirect', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></h2>
                </div>
                <p>
					<?php _e( 'The latest thing available in the premium version is the possibility to choose on which site content apply the redirect. ', 'yith-geoip-languague-redirect-for-woocommerce' ); ?>
                </p>
                <p>
					<?php echo sprintf( __( 'Rather than inserting the origin and destination URL manually, you can choose %1$sposts, pages, media, and products%2$s available in your shop.%3$s In few clicks, you will create focused redirecting rules. ', 'yith-geoip-languague-redirect-for-woocommerce' ), '<b>', '</b>', '<br>' ); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCGEOIP_URL . '/assets' ?>/images/02-icon.png" />
                    <h2><?php _e( 'Desktop or mobile?', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></h2>
                </div>
                <p>
					<?php _e( 'Select for which devices the redirect will be valid: apply the rule always or only when the request comes from a desktop or mobile device.', 'yith-geoip-languague-redirect-for-woocommerce' ); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCGEOIP_URL . '/assets' ?>/images/02.png" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCGEOIP_URL . '/assets' ?>/images/03.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCGEOIP_URL . '/assets' ?>/images/03-icon.png" />
                    <h2><?php _e( 'Create an IP address whitelist', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></h2>
                </div>
                <p>
					<?php echo sprintf( __( 'Do you prefer to %1$sexclude one or more IP addresses%2$s from the redirect rules you have created? With the premium version, you can do it. ', 'yith-geoip-languague-redirect-for-woocommerce' ), '<b>', '</b>', '<br>' ); ?>
                </p>
                <p>
					<?php _e( 'There will be no redirect for all the users accessing the site using one of the specified addresses', 'yith-geoip-languague-redirect-for-woocommerce' ); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
					<?php echo sprintf( __( 'Upgrade to %1$spremium version%2$s of %1$sYITH GeoIP Language Redirect for WooCommerce%2$s to benefit from all features!', 'yith-geoip-languague-redirect-for-woocommerce' ), '<span class="highlight">', '</span>' ); ?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e( 'UPGRADE', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></span>
                    <span><?php _e( 'to the premium version', 'yith-geoip-languague-redirect-for-woocommerce' ); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>