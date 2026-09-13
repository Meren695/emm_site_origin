<?php

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( function_exists( 'wtw_is_legacy_mailer_disabled' ) ) {
        if ( ! wtw_is_legacy_mailer_disabled() ) {
                return;
        }
} elseif ( function_exists( 'get_field' ) ) {
        $site_settings = get_field( 'wtw_site_settings', 'options' );
        if ( empty( $site_settings['disable_legacy_mailer'] ) ) {
                return;
        }
} else {
        return;
}

if ( ! defined( 'WPEF_VERSION' ) ) {
        define( 'WPEF_VERSION', '1.5.0' );
}

if ( ! defined( 'WPEF_DIR' ) ) {
        define( 'WPEF_DIR', get_template_directory() . '/vendor/eforms/' );
}

if ( ! defined( 'WPEF_URL' ) ) {
        define( 'WPEF_URL', get_template_directory_uri() . '/vendor/eforms/' );
}

// Подключаем файлы обработчика форм из темы.
require_once WPEF_DIR . 'includes/class-wpef-admin.php';
require_once WPEF_DIR . 'includes/class-wpef-handler.php';

if ( ! function_exists( 'wpef_load_textdomain' ) ) {
        function wpef_load_textdomain() {
                $domain = 'wp-easy-forms';
                $locale = function_exists( 'determine_locale' ) ? determine_locale() : ( function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale() );
	
                $mofile = WPEF_DIR . 'languages/' . $domain . '-' . $locale . '.mo';
	
                if ( file_exists( $mofile ) ) {
                        load_textdomain( $domain, $mofile );
                } else {
                        load_theme_textdomain( $domain, WPEF_DIR . 'languages' );
                }
	}
}

if ( ! function_exists( 'wpef_activate_plugin' ) ) {
        /**
         * Создает или обновляет таблицы форм.
         */
        function wpef_activate_plugin() {
                global $wpdb;
                $charset_collate = $wpdb->get_charset_collate();

                $table_leads = $wpdb->prefix . 'wpef_leads';
                $sql_leads = "CREATE TABLE $table_leads (
                        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        config_id varchar(50) NOT NULL,
                        form_name varchar(255) NOT NULL,
                        form_data longtext NOT NULL,
                        ip_address varchar(100) NOT NULL,
                        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                        PRIMARY KEY  (id),
                        KEY config_id (config_id),
                        KEY created_at (created_at)
                ) $charset_collate;";

                $table_logs = $wpdb->prefix . 'wpef_logs';
                $sql_logs = "CREATE TABLE $table_logs (
                        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        error_type varchar(100) NOT NULL,
                        error_message text NOT NULL,
                        form_data longtext,
                        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                        PRIMARY KEY  (id),
                        KEY created_at (created_at)
                ) $charset_collate;";

                $table_stats = $wpdb->prefix . 'wpef_stats';
                $sql_stats = "CREATE TABLE $table_stats (
                        config_id varchar(50) NOT NULL,
                        views int(11) NOT NULL DEFAULT 0,
                        submits int(11) NOT NULL DEFAULT 0,
                        PRIMARY KEY  (config_id)
                ) $charset_collate;";

                if ( ! function_exists( 'dbDelta' ) ) {
                        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
                }
                dbDelta( $sql_leads );
                dbDelta( $sql_logs );
                dbDelta( $sql_stats );
	}
}

if ( ! function_exists( 'wpef_deactivate_plugin' ) ) {
        /**
         * Вспомогательная функция для ручной остановки cron-задачи.
         */
        function wpef_deactivate_plugin() {
                $timestamp = wp_next_scheduled( 'wpef_daily_cleanup_event' );
                if ( $timestamp ) {
                        wp_unschedule_event( $timestamp, 'wpef_daily_cleanup_event' );
                }
	}
}

if ( ! function_exists( 'wpef_init' ) ) {
        /**
         * Инициализирует функциональность форм из темы.
         */
        function wpef_init() {
                $db_version_opt = get_option( 'wpef_db_version', '1.0.0' );
                if ( $db_version_opt !== WPEF_VERSION ) {
                        wpef_activate_plugin();
                        update_option( 'wpef_db_version', WPEF_VERSION );
                }

                if ( is_admin() ) {
                        new WPEF_Admin();
                }

                new WPEF_Handler();
	}
}

add_action( 'after_setup_theme', 'wpef_load_textdomain' );
add_action( 'after_setup_theme', 'wpef_init' );
