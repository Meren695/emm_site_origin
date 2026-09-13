<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPEF_Handler {

	public function __construct() {
		// Подключение скриптов на фронте
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// Подключение скриптов на странице авторизации (wp-login.php)
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// AJAX обработчики
		add_action( 'wp_ajax_wpef_submit_form', array( $this, 'handle_form_submit' ) );
		add_action( 'wp_ajax_nopriv_wpef_submit_form', array( $this, 'handle_form_submit' ) );

		// AJAX для совместимости с жестким кэшем (Nonce)
		add_action( 'wp_ajax_wpef_get_nonce', array( $this, 'ajax_get_nonce' ) );
		add_action( 'wp_ajax_nopriv_wpef_get_nonce', array( $this, 'ajax_get_nonce' ) );

		// AJAX для аналитики (Показы форм)
		add_action( 'wp_ajax_wpef_track_view', array( $this, 'track_view' ) );
		add_action( 'wp_ajax_nopriv_wpef_track_view', array( $this, 'track_view' ) );

		// Встроенный SMTP-транспорт
		add_action( 'phpmailer_init', array( $this, 'configure_smtp' ), 999 );

		// Автоматическая очистка логов и заявок (Cron)
		if ( ! wp_next_scheduled( 'wpef_daily_cleanup_event' ) ) {
			wp_schedule_event( time(), 'daily', 'wpef_daily_cleanup_event' );
		}
		add_action( 'wpef_daily_cleanup_event', array( $this, 'perform_daily_cleanup' ) );
	}

	public function ajax_get_nonce() {
		wp_send_json_success( wp_create_nonce( 'wpef_submit_form_nonce' ) );
	}

	public function track_view() {
		if ( empty( $_POST['config_id'] ) ) {
			wp_send_json_error();
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wpef_stats';
		$config_id = sanitize_text_field( wp_unslash( $_POST['config_id'] ) );
		
		$wpdb->query( $wpdb->prepare(
			"INSERT INTO $table (config_id, views, submits) VALUES (%s, 1, 0) ON DUPLICATE KEY UPDATE views = views + 1",
			$config_id
		) );
		wp_send_json_success();
	}

	public function configure_smtp( $phpmailer ) {
		$general_settings = get_option( 'wpef_general_settings', array() );
		
		if ( isset( $general_settings['smtp_enable'] ) && $general_settings['smtp_enable'] === 'on' ) {
			$phpmailer->isSMTP();
			$phpmailer->Host       = isset( $general_settings['smtp_host'] ) ? $general_settings['smtp_host'] : '';
			$phpmailer->Port       = isset( $general_settings['smtp_port'] ) ? intval( $general_settings['smtp_port'] ) : 465;
			
			$auth = isset( $general_settings['smtp_auth'] ) && $general_settings['smtp_auth'] === 'on';
			$phpmailer->SMTPAuth   = $auth;
			
			if ( $auth ) {
				$phpmailer->Username   = isset( $general_settings['smtp_user'] ) ? $general_settings['smtp_user'] : '';
				$phpmailer->Password   = isset( $general_settings['smtp_pass'] ) ? $general_settings['smtp_pass'] : '';
			}

			$encryption = isset( $general_settings['smtp_encryption'] ) ? $general_settings['smtp_encryption'] : 'none';
			if ( $encryption !== 'none' ) {
				$phpmailer->SMTPSecure = $encryption;
			} else {
				$phpmailer->SMTPSecure = false;
				$phpmailer->SMTPAutoTLS = false;
			}
			
			if ( isset( $general_settings['smtp_auto_tls'] ) && $general_settings['smtp_auto_tls'] === 'on' ) {
				$phpmailer->SMTPAutoTLS = true;
			} else {
				$phpmailer->SMTPAutoTLS = false;
			}

			$from_email = isset( $general_settings['smtp_from_email'] ) ? $general_settings['smtp_from_email'] : '';
			$force_from_email = isset( $general_settings['smtp_force_from_email'] ) && $general_settings['smtp_force_from_email'] === 'on';
			$from_name = isset( $general_settings['smtp_from_name'] ) ? $general_settings['smtp_from_name'] : '';
			
			if ( ! empty( $from_email ) ) {
				if ( $force_from_email || $phpmailer->From === 'wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST ) ) {
					$phpmailer->From = $from_email;
				}
			}
			
			if ( ! empty( $from_name ) ) {
				if ( $force_from_email || $phpmailer->FromName === 'WordPress' ) {
					$phpmailer->FromName = $from_name;
				}
			}
		}
	}

	public function perform_daily_cleanup() {
		global $wpdb;
		$general_settings = get_option( 'wpef_general_settings', array() );
		
		// Очистка заявок
		$leads_days = isset( $general_settings['cleanup_leads_days'] ) ? intval( $general_settings['cleanup_leads_days'] ) : 0;
		if ( $leads_days > 0 ) {
			$table_leads = $wpdb->prefix . 'wpef_leads';
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM $table_leads WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$leads_days
			) );
		}

		// Очистка логов
		$logs_days = isset( $general_settings['cleanup_logs_days'] ) ? intval( $general_settings['cleanup_logs_days'] ) : 30; // 30 по умолчанию
		if ( $logs_days > 0 ) {
			$table_logs = $wpdb->prefix . 'wpef_logs';
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM $table_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$logs_days
			) );
		}

		// Очистка временных файлов (старше 24 часов)
		$upload_dir = wp_upload_dir();
		$wpef_temp_dir = $upload_dir['basedir'] . '/wpef_temp';
		
		if ( file_exists( $wpef_temp_dir ) && is_dir( $wpef_temp_dir ) ) {
			$files = glob( $wpef_temp_dir . '/*' );
			$now = time();
			
			if ( is_array( $files ) ) {
				foreach ( $files as $file ) {
					if ( is_file( $file ) ) {
						// Если файл старше 24 часов (86400 секунд)
						if ( $now - filemtime( $file ) >= 86400 ) {
							@unlink( $file );
						}
					}
				}
			}
		}
	}

	public function custom_upload_dir( $dirs ) {
		$wpef_upload_path = $dirs['basedir'] . '/wpef_temp';
		$wpef_upload_url = $dirs['baseurl'] . '/wpef_temp';
		
		$dirs['path'] = $wpef_upload_path;
		$dirs['url'] = $wpef_upload_url;
		$dirs['subdir'] = '/wpef_temp';
		
		return $dirs;
	}

	public function enqueue_scripts() {
		// Получаем общие настройки
		$general_settings = get_option( 'wpef_general_settings', array( 'disable_auto_required' => false ) );
		$load_just_validate = isset( $general_settings['load_just_validate'] ) ? $general_settings['load_just_validate'] : true;
		$load_imask = isset( $general_settings['load_imask'] ) ? $general_settings['load_imask'] : false;

		// Получаем настройки селекторов из БД
		$configs = get_option( 'wpef_configs', array() );

		// Фильтруем только активные (enabled)
		$active_configs = array();
		if ( is_array( $configs ) ) {
			foreach ( $configs as $config ) {
				if ( isset( $config['enabled'] ) && $config['enabled'] ) {
					$active_configs[] = $config;
				}
			}
		}

		// Если нет активных конфигов, нам вообще нечего грузить
		if ( empty( $active_configs ) ) {
			return;
		}

		$validation_rules = get_option( 'wpef_validation_rules', array() );

		// Регистрируем скрипты, но НЕ загружаем их сразу (кроме лоадера)
		wp_register_script( 'wpef-loader', false );
		wp_enqueue_script( 'wpef-loader' );

		// Передаем все данные и URL'ы скриптов в JS
		wp_localize_script( 'wpef-loader', 'wpefData', array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'wpef_submit_form_nonce' ),
			'configs'          => $active_configs,
			'validation_rules' => is_array( $validation_rules ) ? $validation_rules : array(),
			'general_settings' => is_array( $general_settings ) ? $general_settings : array(),
			'urls'             => array(
				'just_validate' => WPEF_URL . 'assets/js/just-validate.min.js',
				'imask'         => WPEF_URL . 'assets/js/imask.min.js',
				'front'         => WPEF_URL . 'assets/js/front.js?ver=' . WPEF_VERSION,
			),
			'flags'            => array(
				'load_just_validate' => $load_just_validate,
				'load_imask'         => $load_imask,
			),
			'i18n'             => array(
				'reqField'           => __( 'Обязательное поле', 'wp-easy-forms' ),
				'attachMin'          => __( 'Прикрепите минимум файлов: ', 'wp-easy-forms' ),
				'maxSizeExceeded'    => __( 'Превышен максимальный размер файлов: ', 'wp-easy-forms' ),
				'unsupportedFormat1' => __( 'Файл "', 'wp-easy-forms' ),
				'unsupportedFormat2' => __( '" имеет неподдерживаемый формат', 'wp-easy-forms' ),
				'formUnavailable'    => __( 'Форма временно недоступна. Обновите страницу.', 'wp-easy-forms' ),
				'submitFailed'       => __( 'Не удалось отправить форму. Попробуйте позже.', 'wp-easy-forms' ),
				'networkError'       => __( 'Ошибка сети. Попробуйте позже.', 'wp-easy-forms' ),
				'errEmail'           => __( 'Неверный email', 'wp-easy-forms' ),
				'errMinLength'       => __( 'Значение слишком короткое', 'wp-easy-forms' ),
				'errMaxLength'       => __( 'Значение слишком длинное', 'wp-easy-forms' ),
				'errPassword'        => __( 'Пароль слишком простой', 'wp-easy-forms' ),
				'errStrongPass'      => __( 'Пароль должен быть сложным', 'wp-easy-forms' ),
				'errNumber'          => __( 'Введите число', 'wp-easy-forms' ),
				'errInteger'         => __( 'Введите целое число', 'wp-easy-forms' ),
				'errMinNumber'       => __( 'Значение слишком мало', 'wp-easy-forms' ),
				'errMaxNumber'       => __( 'Значение слишком велико', 'wp-easy-forms' ),
				'errCustomRegexp'    => __( 'Неверный формат', 'wp-easy-forms' ),
				'errInvalid'         => __( 'Недопустимое значение', 'wp-easy-forms' ),
				'maxFiles'           => __( 'Максимум файлов: ', 'wp-easy-forms' ),
				'deleteFile'         => __( 'Удалить файл', 'wp-easy-forms' )
			)
		) );

		// Инлайн-скрипт: проверяет наличие формы и только тогда загружает тяжелые JS
		$inline_loader = "
		document.addEventListener('DOMContentLoaded', function() {
			var data = window.wpefData;
			if (!data || !data.configs) return;
			
			var hasForms = false;
			for (var i = 0; i < data.configs.length; i++) {
				if (data.configs[i].selector && document.querySelector(data.configs[i].selector)) {
					hasForms = true;
					break;
				}
			}
			
			if (hasForms) {
				var loadScript = function(src) {
					return new Promise(function(resolve, reject) {
						var s = document.createElement('script');
						s.src = src;
						s.onload = resolve;
						s.onerror = reject;
						document.body.appendChild(s);
					});
				};
				
				var deps = [];
				if (data.flags.load_just_validate) {
					deps.push(loadScript(data.urls.just_validate));
				}
				if (data.flags.load_imask) {
					deps.push(loadScript(data.urls.imask));
				}
				
				Promise.all(deps).then(function() {
					loadScript(data.urls.front);
				}).catch(function(err) {
					console.error('WP Easy Forms: failed to load scripts', err);
				});
			}
		});
		";
		
		wp_add_inline_script( 'wpef-loader', $inline_loader );
	}

	private function get_client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		return sanitize_text_field( wp_unslash( $ip ) );
	}

	private function log_error( $error_type, $error_message, $form_data = array() ) {
		global $wpdb;
		$logs_table = $wpdb->prefix . 'wpef_logs';
		
		$wpdb->insert(
			$logs_table,
			array(
				'error_type'    => $error_type,
				'error_message' => $error_message,
				'form_data'     => !empty($form_data) ? wp_json_encode($form_data, JSON_UNESCAPED_UNICODE) : '',
				'created_at'    => current_time( 'mysql' )
			),
			array( '%s', '%s', '%s', '%s' )
		);
	}

	public function handle_form_submit() {
		// Проверка глобального Nonce плагина
		if ( ! isset( $_POST['wpef_nonce'] ) || ! wp_verify_nonce( $_POST['wpef_nonce'], 'wpef_submit_form_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Ошибка безопасности (nonce).', 'wp-easy-forms' ) ) );
		}

		$general_settings = get_option( 'wpef_general_settings', array() );
		$ip_address = $this->get_client_ip();

		// Проверка Honeypot
		if ( ! empty( $general_settings['enable_honeypot'] ) ) {
			if ( ! empty( $_POST['wpef_website_url'] ) ) {
				// Логируем попытку спама
				$this->log_error( __( 'Защита от спама', 'wp-easy-forms' ), __( 'Заблокировано Honeypot (заполнено скрытое поле).', 'wp-easy-forms' ), $_POST );
				
				// Бот заполнил скрытое поле - тихо возвращаем успех
				wp_send_json_success( array( 'message' => __( 'Спасибо! Ваша заявка отправлена.', 'wp-easy-forms' ) ) );
			}
		}

		// Проверка Rate Limit
		if ( ! empty( $general_settings['enable_rate_limit'] ) ) {
			$limit_count = isset( $general_settings['rate_limit_count'] ) ? intval( $general_settings['rate_limit_count'] ) : 5;
			$limit_time_minutes = isset( $general_settings['rate_limit_time'] ) ? intval( $general_settings['rate_limit_time'] ) : 10;
			
			$transient_key = 'wpef_rl_' . md5( $ip_address );
			$requests = get_transient( $transient_key );
			
			if ( false === $requests ) {
				$requests = array();
			}

			// Очищаем старые запросы
			$current_time = time();
			$time_window = $current_time - ( $limit_time_minutes * 60 );
			$requests = array_filter( $requests, function( $timestamp ) use ( $time_window ) {
				return $timestamp > $time_window;
			} );

			if ( count( $requests ) >= $limit_count ) {
				wp_send_json_error( array( 'message' => __( 'Слишком много запросов. Подождите немного.', 'wp-easy-forms' ) ) );
			}

			// Добавляем текущий запрос
			$requests[] = $current_time;
			set_transient( $transient_key, $requests, $limit_time_minutes * 60 );
		}

		// Защита от спама (Время заполнения - оставляем как было)
		if ( isset( $_POST['form_ts'] ) ) {
			$form_ts = intval( $_POST['form_ts'] );
			$current_ts = time();
			if ( ( $current_ts - $form_ts ) < 3 ) {
				wp_send_json_error( array( 'message' => __( 'Форма заполнена слишком быстро. Возможно, вы бот.', 'wp-easy-forms' ) ) );
			}
		}

		// Находим конфиг по переданному ID или селектору (передадим из JS)
		$config_id = isset( $_POST['wpef_config_id'] ) ? sanitize_text_field( $_POST['wpef_config_id'] ) : '';
		$configs = get_option( 'wpef_configs', array() );
		$current_config = null;

		foreach ( $configs as $cfg ) {
			if ( $cfg['id'] === $config_id ) {
				$current_config = $cfg;
				break;
			}
		}

		if ( ! $current_config || ! $current_config['enabled'] ) {
			wp_send_json_error( array( 'message' => __( 'Конфигурация формы не найдена или отключена.', 'wp-easy-forms' ) ) );
		}

		// 1. Сбор полей
		// Расширенный список системных полей, которые не должны попадать в письмо
		$ignore_fields = array(
			'action', 
			'wpef_nonce', 
			'wpef_config_id', 
			'hp_field', 
			'form_ts', 
			'attachments', 
			'_wp_http_referer', 
			'form_nonce', 
			'form_name',
			'__title',
			'__page',
			'__form',
			'__query',
			'wpef_website_url',
			'wpef_client_user_email',
			'wpef_user_agent',
			'wpef_screen_res',
			'wpef_device_type',
			'wpef_utm_source',
			'wpef_utm_medium',
			'wpef_utm_campaign',
			'wpef_utm_term',
			'wpef_utm_content',
			'cf-turnstile-response',
			'g-recaptcha-response'
		);
		
		$ignore_fields = apply_filters( 'wpef_ignore_fields', $ignore_fields );
		
		$fields_html = '<div style="background: #ffffff; border-radius: 8px; padding: 15px; border: 1px solid #e2e8f0; margin: 20px 0;"><table style="width: 100%; border-collapse: collapse; font-family: \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif;">';

		$send_empty = !empty( $general_settings['enable_send_empty_fields'] );
		
		$custom_field_placeholders = array();

		foreach ( $_POST as $key => $value ) {
			if ( in_array( $key, $ignore_fields ) ) continue;
			
			$clean_val = is_array( $value ) ? implode(', ', array_map('sanitize_text_field', $value)) : sanitize_text_field( $value );
			$is_empty = trim($clean_val) === '';

			// Сохраняем значение для индивидуальных плейсхолдеров до применения HTML-форматирования
			$raw_placeholder_val = $clean_val;
			if ( is_array( $value ) ) {
				$mapped_raw_vals = array();
				foreach ( $value as $v ) {
					$v_clean = sanitize_text_field( $v );
					$mapped_raw_vals[] = (mb_strtolower( trim( $v_clean ), 'UTF-8' ) === 'on') ? esc_html__( 'Да', 'wp-easy-forms' ) : $v_clean;
				}
				$raw_placeholder_val = implode(', ', $mapped_raw_vals);
			} elseif ( mb_strtolower( trim($raw_placeholder_val), 'UTF-8' ) === 'on' ) {
				$raw_placeholder_val = esc_html__( 'Да', 'wp-easy-forms' );
			}
			
			// PHP заменяет пробелы и точки в ключах $_POST на подчеркивания.
			// Добавляем варианты плейсхолдеров как с подчеркиванием, так и с пробелом.
			$custom_field_placeholders['{{' . $key . '}}'] = $raw_placeholder_val;
			if ( strpos($key, '_') !== false ) {
				$custom_field_placeholders['{{' . str_replace('_', ' ', $key) . '}}'] = $raw_placeholder_val;
			}

			if ( $is_empty && !$send_empty ) {
				continue;
			}
			
			// Делаем красивые ключи (заменяем подчеркивания и тире на пробелы и делаем с большой буквы)
			$clean_key = sanitize_text_field( str_replace(array('_', '-'), ' ', $key) );
			$clean_key = mb_convert_case($clean_key, MB_CASE_TITLE, "UTF-8");
			
			if ( $is_empty ) {
				$clean_val = '<span style="color: #ef4444; font-weight: 600; font-size: 13px;">' . esc_html__( 'НЕ ЗАПОЛНЕНО', 'wp-easy-forms' ) . '</span>';
			} elseif ( is_array( $value ) ) {
				// Если это массив (группа чекбоксов), проверим нет ли там 'on', и заменим
				$mapped_vals = array();
				foreach ( $value as $v ) {
					$v_clean = sanitize_text_field( $v );
					if ( mb_strtolower( trim( $v_clean ), 'UTF-8' ) === 'on' ) {
						$mapped_vals[] = esc_html__( 'Да / Получено', 'wp-easy-forms' );
					} else {
						$mapped_vals[] = $v_clean;
					}
				}
				$clean_val = implode(', ', $mapped_vals);
			} elseif ( mb_strtolower( trim($clean_val), 'UTF-8' ) === 'on' ) {
				$clean_val = '<span style="display: inline-block; background: rgba(16, 185, 129, 0.1); color: #059669; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">&#10004; ' . esc_html__( 'Да / Получено', 'wp-easy-forms' ) . '</span>';
			}
			
			$fields_html .= "
			<tr>
				<td style=\"padding: 12px 15px; border-bottom: 1px solid #e2e8f0; width: 35%; color: #64748b; font-size: 14px; vertical-align: top;\">{$clean_key}</td>
				<td style=\"padding: 12px 15px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-size: 15px; font-weight: 500; vertical-align: top;\">{$clean_val}</td>
			</tr>";
		}

		$fields_html .= '</table></div>';

		// 2. Обработка файлов (с проверкой MIME)
		$uploaded_files = array();
		$uploaded_urls = array();
		$attachments = array();
		$media_links = array();
		$total_files_size = 0;
		$upload_dir = wp_upload_dir();
		// Мы используем кастомную папку wpef_temp, так как файлы могут прикрепляться к письму,
		// и нам нужно будет их потом удалить (если они не сохраняются в медиабиблиотеку).
		$wpef_upload_path = $upload_dir['basedir'] . '/wpef_temp/';
		if ( ! file_exists( $wpef_upload_path ) ) {
			wp_mkdir_p( $wpef_upload_path );
		}
		
		if ( ! empty( $_FILES ) ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
			}

			// Разрешенные безопасные MIME-типы
			$allowed_mimes = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
				'webp'         => 'image/webp',
				'pdf'          => 'application/pdf',
				'doc'          => 'application/msword',
				'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'xls'          => 'application/vnd.ms-excel',
				'xlsx'         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'zip'          => 'application/zip',
				'rar'          => 'application/x-rar-compressed',
				'txt'          => 'text/plain',
				'csv'          => 'text/csv'
			);

			foreach ( $_FILES as $file_key => $file_array ) {
				// Если это массив файлов (multiple)
				if ( is_array( $file_array['name'] ) ) {
					$file_count = count( $file_array['name'] );
					for ( $i = 0; $i < $file_count; $i++ ) {
						if ( $file_array['error'][ $i ] === UPLOAD_ERR_OK ) {
							
							// Проверка MIME
							$filetype = wp_check_filetype( $file_array['name'][ $i ], $allowed_mimes );
							if ( ! $filetype['ext'] ) {
								$this->log_error('Security', __( 'Заблокирована загрузка файла с запрещенным расширением: ', 'wp-easy-forms' ) . $file_array['name'][ $i ]);
								continue;
							}

							$total_files_size += $file_array['size'][ $i ];
							
							$file = array(
								'name'     => $file_array['name'][ $i ],
								'type'     => $file_array['type'][ $i ],
								'tmp_name' => $file_array['tmp_name'][ $i ],
								'error'    => $file_array['error'][ $i ],
								'size'     => $file_array['size'][ $i ]
							);
							$upload_overrides = array( 'test_form' => false, 'mimes' => $allowed_mimes );
							
							// Фильтр для загрузки в нашу временную папку
							add_filter( 'upload_dir', array( $this, 'custom_upload_dir' ) );
							$movefile = wp_handle_upload( $file, $upload_overrides );
							remove_filter( 'upload_dir', array( $this, 'custom_upload_dir' ) );

							if ( $movefile && ! isset( $movefile['error'] ) ) {
								$uploaded_files[] = $movefile['file'];
							}
						}
					}
				} else {
					if ( $file_array['error'] === UPLOAD_ERR_OK ) {
						
						// Проверка MIME
						$filetype = wp_check_filetype( $file_array['name'], $allowed_mimes );
						if ( ! $filetype['ext'] ) {
							$this->log_error('Security', __( 'Заблокирована загрузка файла с запрещенным расширением: ', 'wp-easy-forms' ) . $file_array['name']);
							continue;
						}

						$total_files_size += $file_array['size'];

						$file = array(
							'name'     => $file_array['name'],
							'type'     => $file_array['type'],
							'tmp_name' => $file_array['tmp_name'],
							'error'    => $file_array['error'],
							'size'     => $file_array['size']
						);
						$upload_overrides = array( 'test_form' => false, 'mimes' => $allowed_mimes );
						
						add_filter( 'upload_dir', array( $this, 'custom_upload_dir' ) );
						$movefile = wp_handle_upload( $file, $upload_overrides );
						remove_filter( 'upload_dir', array( $this, 'custom_upload_dir' ) );

						if ( $movefile && ! isset( $movefile['error'] ) ) {
							$uploaded_files[] = $movefile['file'];
						}
					}
				}
			}
		}

		// Логика прикрепления файлов или загрузки в медиа
		$save_to_media = ! empty( $general_settings['save_to_media'] );
		$max_attachment_size_mb = isset( $general_settings['max_attachment_size'] ) ? floatval( $general_settings['max_attachment_size'] ) : 10;
		$max_attachment_bytes = $max_attachment_size_mb * 1024 * 1024;
		
		$is_files_too_large = $total_files_size > $max_attachment_bytes;

		if ( !empty($uploaded_files) ) {
			if ( $save_to_media || $is_files_too_large ) {
				// Загружаем в медиабиблиотеку WP
				foreach ( $uploaded_files as $file_path ) {
					$wp_filetype = wp_check_filetype( basename( $file_path ), null );
					
					// Так как файлы сейчас лежат во временной папке wpef_temp, 
					// нам нужно перенести их в правильную папку медиабиблиотеки (год/месяц)
					$upload_dir = wp_upload_dir();
					$new_file_path = $upload_dir['path'] . '/' . basename($file_path);
					
					// Копируем файл
					if ( copy($file_path, $new_file_path) ) {
						
						$attachment = array(
							'guid'           => $upload_dir['url'] . '/' . basename( $new_file_path ), // URL для базы данных
							'post_mime_type' => $wp_filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $new_file_path ) ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);
						
						// Вставляем запись в БД (нужен абсолютный путь)
						$attach_id = wp_insert_attachment( $attachment, $new_file_path );
						if ( ! is_wp_error( $attach_id ) ) {
							require_once ABSPATH . 'wp-admin/includes/image.php';
							require_once ABSPATH . 'wp-admin/includes/file.php';
							require_once ABSPATH . 'wp-admin/includes/media.php';
							
							// Генерируем миниатюры
							$attach_data = wp_generate_attachment_metadata( $attach_id, $new_file_path );
							
							if ( ! is_wp_error( $attach_data ) && ! empty( $attach_data ) ) {
								wp_update_attachment_metadata( $attach_id, $attach_data );
							}
							
							$media_links[] = wp_get_attachment_url( $attach_id );
						} else {
							$this->log_error('Media Error', __( 'Не удалось создать вложение в БД', 'wp-easy-forms' ), array('error' => $attach_id->get_error_message(), 'file' => $new_file_path));
						}
					}
					
					// Обязательно удаляем исходный временный файл из wpef_temp (если пути не совпадают)
					if ( $file_path !== $new_file_path && file_exists($file_path) ) {
						@unlink($file_path);
					}
				}
				// Не прикрепляем к письму, так как даем ссылки
				$attachments = array();
			} else {
				// Просто прикрепляем к письму
				$attachments = $uploaded_files;
			}
		}

		// Определяем реальное имя формы (если передано с фронта)
		$actual_form_name = ! empty( $_POST['form_name'] ) ? sanitize_text_field( $_POST['form_name'] ) : $current_config['name'];

		// Переопределяем на имя страницы, если включена соответствующая настройка
		if ( ! empty( $general_settings['use_page_title'] ) && ! empty( $_POST['__title'] ) ) {
			// Декодируем HTML сущности (например, &nbsp;), чтобы они не ломали заголовки письма и парсеры
			$decoded_title = html_entity_decode( wp_unslash( $_POST['__title'] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			// Очищаем от переносов строк, которые могут вызвать ошибку SMTP (header injection protection)
			$decoded_title = str_replace( array("\r", "\n"), ' ', $decoded_title );
			// Вырезаем все HTML теги, если они случайно туда попали
			$decoded_title = wp_strip_all_tags( $decoded_title );
			$actual_form_name = sanitize_text_field( $decoded_title );
		} elseif ( ! empty( $general_settings['use_page_title'] ) && ! empty( $_POST['__page'] ) ) {
			// Фолбэк: если __title пуст, но настройка включена, берем __page (URL)
			$actual_form_name = sanitize_text_field( wp_unslash( $_POST['__page'] ) );
		}

		// Добавляем ссылки на загруженные медиа-файлы в HTML письма
		$media_links_html = '';
		if ( ! empty( $media_links ) ) {
			$media_links_html = '<div style="margin-top: 20px; padding: 15px; background: #f1f5f9; border-radius: 8px; border: 1px solid #e2e8f0;">';
			$media_links_html .= '<h3 style="margin-top: 0; margin-bottom: 10px; font-size: 14px; color: #475569;">' . esc_html__( 'Прикрепленные файлы:', 'wp-easy-forms' ) . '</h3>';
			$media_links_html .= '<ul style="margin: 0; padding-left: 20px;">';
			foreach ( $media_links as $link ) {
				$media_links_html .= '<li><a href="' . esc_url( $link ) . '" target="_blank" style="color: #4f46e5; text-decoration: none;">' . esc_html( basename( $link ) ) . '</a></li>';
			}
			$media_links_html .= '</ul></div>';
		}

		// Добавляем техническую информацию (UTM и устройство) в письмо
		$tech_info_html = '';
		$enable_basic_tech = !isset($general_settings['enable_basic_tech_info']) || !empty($general_settings['enable_basic_tech_info']);
		
		if ( ! empty( $general_settings['enable_utm'] ) || ! empty( $general_settings['enable_device_info'] ) || $enable_basic_tech ) {
			$tech_info_html = '<div style="margin-top: 25px; padding-top: 20px; border-top: 1px dashed #e2e8f0; font-size: 13px; color: #64748b;">';
			$tech_info_html .= '<h4 style="margin-top: 0; margin-bottom: 10px; color: #94a3b8; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">' . esc_html__( 'Техническая информация', 'wp-easy-forms' ) . '</h4>';
			$tech_info_html .= '<table style="width: 100%; border-collapse: collapse;">';
			
			// Базовая информация
			if ( $enable_basic_tech ) {
				$tech_info_html .= '<tr><td style="padding: 4px 0; width: 40%;"><strong>' . esc_html__( 'Страница отправки', 'wp-easy-forms' ) . '</strong></td><td style="padding: 4px 0;"><a href="' . (isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : '') . '" style="color: #4f46e5; text-decoration: none;">' . (isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : __( 'Неизвестно', 'wp-easy-forms' )) . '</a></td></tr>';
				$tech_info_html .= '<tr><td style="padding: 4px 0; width: 40%;"><strong>' . esc_html__( 'Форма', 'wp-easy-forms' ) . '</strong></td><td style="padding: 4px 0;">' . esc_html($actual_form_name) . '</td></tr>';
				$tech_info_html .= '<tr><td style="padding: 4px 0; width: 40%;"><strong>' . esc_html__( 'IP-адрес', 'wp-easy-forms' ) . '</strong></td><td style="padding: 4px 0;"><span style="font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #475569;">' . esc_html($_SERVER['REMOTE_ADDR']) . '</span></td></tr>';
			}

			// UTM
			$utm_keys = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content');
			foreach ( $utm_keys as $utm ) {
				if ( ! empty( $_POST['wpef_' . $utm] ) ) {
					$tech_info_html .= '<tr><td style="padding: 4px 0; width: 40%;"><strong>' . esc_html($utm) . '</strong></td><td style="padding: 4px 0;">' . esc_html($_POST['wpef_' . $utm]) . '</td></tr>';
				}
			}
			// Device Info
			if ( ! empty( $_POST['wpef_user_agent'] ) ) {
				$tech_info_html .= '<tr><td style="padding: 4px 0; width: 40%;"><strong>' . esc_html__( 'Браузер (User Agent)', 'wp-easy-forms' ) . '</strong></td><td style="padding: 4px 0;">' . esc_html($_POST['wpef_user_agent']) . '</td></tr>';
			}
			if ( ! empty( $_POST['wpef_screen_res'] ) ) {
				$tech_info_html .= '<tr><td style="padding: 4px 0; width: 40%;"><strong>' . esc_html__( 'Разрешение экрана', 'wp-easy-forms' ) . '</strong></td><td style="padding: 4px 0;">' . esc_html($_POST['wpef_screen_res']) . '</td></tr>';
			}
			if ( ! empty( $_POST['wpef_device_type'] ) ) {
				$tech_info_html .= '<tr><td style="padding: 4px 0; width: 40%;"><strong>' . esc_html__( 'Тип устройства', 'wp-easy-forms' ) . '</strong></td><td style="padding: 4px 0;">' . esc_html($_POST['wpef_device_type']) . '</td></tr>';
			}
			
			$tech_info_html .= '</table></div>';
		}

		// 3. Подготовка шаблона
		$placeholders = array(
			'{{__fields}}'    => $fields_html,
			'{{__page}}'      => isset($_SERVER['HTTP_REFERER']) ? '<a href="'.esc_url($_SERVER['HTTP_REFERER']).'" style="color: #4f46e5; text-decoration: none;">'.esc_url($_SERVER['HTTP_REFERER']).'</a>' : __( 'Неизвестно', 'wp-easy-forms' ),
			'{{__form}}'      => '<span style="font-weight: 600; color: #334155;">' . $actual_form_name . '</span>',
			'{{__form_name}}' => $actual_form_name,
			'{{__ip}}'        => '<span style="font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #475569;">' . $_SERVER['REMOTE_ADDR'] . '</span>',
			'{{__site}}'      => get_bloginfo('name')
		);
		
		// Добавляем индивидуальные поля (например, {{Имя}}, {{Телефон}})
		$placeholders = array_merge($custom_field_placeholders, $placeholders);

		$placeholders = apply_filters( 'wpef_email_placeholders', $placeholders, $_POST, $current_config );

		$email_to = apply_filters( 'wpef_email_to', $current_config['email_to'], $current_config, $_POST );
		$subject  = str_replace( array_keys($placeholders), array_values($placeholders), $current_config['email_subject'] );
		$subject  = apply_filters( 'wpef_email_subject', $subject, $current_config, $_POST );
		
		// Очищаем тему письма от возможных HTML-тегов и переносов строк
		// Используем wp_strip_all_tags до декодирования, чтобы вырезать теги, 
		// затем декодируем сущности, затем еще раз зачищаем пробелы и переносы
		$subject = wp_strip_all_tags( $subject );
		$subject = html_entity_decode( $subject, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$subject = preg_replace( '/[\r\n\t]+/', ' ', $subject );
		$subject = trim( preg_replace( '/\s+/', ' ', $subject ) );
		// Финальная защита от пустой темы, которая тоже может вызывать ошибку на некоторых SMTP
		if ( empty( $subject ) ) {
			$subject = __( 'Новая заявка с сайта', 'wp-easy-forms' );
		}

		// Подготавливаем текст письма (превращаем переносы строк в <br>)
		// Очищаем текст от лишних пробелов и переносов в начале и конце
		$raw_body = trim($current_config['email_body']);
		
		// Заменяем множественные переносы строк на один
		$raw_body = preg_replace("/[\r\n]+/", "\n", $raw_body);
		
		// Конвертируем переносы в <br> и подставляем данные
		$raw_body = nl2br( $raw_body );
		$raw_body = str_replace( array_keys($placeholders), array_values($placeholders), $raw_body );

		// Удаляем дубликаты базовой информации из тела письма, если включен новый технический блок
		if ( $enable_basic_tech ) {
			$raw_body = preg_replace('/(' . preg_quote(__( 'Страница', 'wp-easy-forms' ), '/') . '|Страница):.*?<br>\s*/i', '', $raw_body);
			$raw_body = preg_replace('/(' . preg_quote(__( 'Форма', 'wp-easy-forms' ), '/') . '|Форма):.*?<br>\s*/i', '', $raw_body);
			$raw_body = preg_replace('/IP:.*?<br>\s*/i', '', $raw_body);
			$raw_body = preg_replace('/(' . preg_quote(__( 'Страница', 'wp-easy-forms' ), '/') . '|Страница):.*?\\n/i', '', $raw_body);
			$raw_body = preg_replace('/(' . preg_quote(__( 'Форма', 'wp-easy-forms' ), '/') . '|Форма):.*?\\n/i', '', $raw_body);
			$raw_body = preg_replace('/IP:.*?\\n/i', '', $raw_body);
		}
		
		$body = "
		<div style=\"background-color: #f8fafc; padding: 40px 20px; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155;\">
			<div style=\"max-width: 850px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0;\">
				
				<!-- Header -->
				<div style=\"background-color: #4f46e5; padding: 25px 30px; text-align: center;\">
					<h2 style=\"color: #ffffff; margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;\">{$actual_form_name}</h2>
				</div>

				<!-- Content -->
				<div style=\"padding: 35px 30px; font-size: 15px; color: #475569;\">
					<div style=\"line-height: 1.8;\">
						{$raw_body}
					</div>
					{$media_links_html}
					{$tech_info_html}
				</div>
			</div>
		</div>";

		$headers = array('Content-Type: text/html; charset=UTF-8');
		$headers = apply_filters( 'wpef_email_headers', $headers, $current_config, $_POST );
		
		$body = apply_filters( 'wpef_email_body', $body, $current_config, $_POST, $placeholders );

		// 3.5 Умная маршрутизация (Smart Routing)
		if ( ! empty( $current_config['route_enable'] ) && ! empty( $current_config['routes'] ) && is_array( $current_config['routes'] ) ) {
			// Временный лог для отладки маршрутизации
			$debug_routing = array(
				'post_keys' => array_keys($_POST),
				'routes' => $current_config['routes']
			);
			
			foreach ( $current_config['routes'] as $route ) {
				$trigger_name = isset( $route['trigger_name'] ) ? $route['trigger_name'] : '';
				$operator = isset( $route['operator'] ) ? $route['operator'] : 'equals';
				$trigger_value = isset( $route['trigger_value'] ) ? $route['trigger_value'] : '';
				$target_email = isset( $route['target_email'] ) ? sanitize_text_field( $route['target_email'] ) : '';

				if ( empty( $trigger_name ) || empty( $target_email ) ) continue;

				// PHP автоматически заменяет пробелы и точки в ключах $_POST на подчеркивания
				$normalized_trigger_name = str_replace( array( ' ', '.' ), '_', $trigger_name );
				
				// Ищем значение в POST, игнорируя регистр ключей и возможные проблемы с кодировкой URL
				$posted_val = null;
				foreach ( $_POST as $post_key => $post_val ) {
					// Приводим все к нижнему регистру для надежного сравнения ключей
					$safe_post_key = mb_strtolower( trim($post_key), 'UTF-8' );
					$safe_trigger_name = mb_strtolower( trim($trigger_name), 'UTF-8' );
					$safe_normalized = mb_strtolower( trim($normalized_trigger_name), 'UTF-8' );
					$safe_urldecoded = mb_strtolower( trim(urldecode($post_key)), 'UTF-8' );

					if ( $safe_post_key === $safe_trigger_name || 
						 $safe_post_key === $safe_normalized || 
						 $safe_urldecoded === $safe_trigger_name ) {
						$posted_val = $post_val;
						break;
					}
				}

				if ( $posted_val !== null ) {
					$posted_val = is_array( $posted_val ) ? implode( ', ', $posted_val ) : wp_unslash( $posted_val );
					$is_match = false;
					
					// Нормализуем строки для сравнения (убираем лишние пробелы и приводим к нижнему регистру для надежности)
					$posted_val_cmp = mb_strtolower( trim( $posted_val ), 'UTF-8' );
					$trigger_val_cmp = mb_strtolower( trim( $trigger_value ), 'UTF-8' );

					$debug_routing['checks'][] = array(
						'rule_name' => $trigger_name,
						'posted_raw' => $posted_val,
						'posted_cmp' => $posted_val_cmp,
						'trigger_cmp' => $trigger_val_cmp,
						'operator' => $operator
					);

					if ( $operator === 'equals' && $posted_val_cmp === $trigger_val_cmp ) {
						$is_match = true;
					} elseif ( $operator === 'not_equals' && $posted_val_cmp !== $trigger_val_cmp ) {
						$is_match = true;
					} elseif ( $operator === 'contains' && mb_strpos( $posted_val_cmp, $trigger_val_cmp, 0, 'UTF-8' ) !== false ) {
						$is_match = true;
					}

					if ( $is_match ) {
						$email_to = $target_email;
						$debug_routing['matched'] = $target_email;
						break; // Применяем первое совпавшее правило и выходим
					}
				} else {
					$debug_routing['checks'][] = "Key '$trigger_name' not found in POST";
				}
			}
		}

		// 4. Отправка основного письма
		// Подключаемся к экшену wp_mail_failed, чтобы перехватить точную ошибку PHPMailer
		$mail_error_msg = '';
		$error_handler = function( $wp_error ) use ( &$mail_error_msg ) {
			if ( is_wp_error( $wp_error ) ) {
				$raw_error = $wp_error->get_error_message();
				// Перевод основных технических ошибок PHPMailer на русский
				$translations = array(
					'You must provide at least one recipient email address.' => __( 'Не указан email получателя. Проверьте настройки.', 'wp-easy-forms' ),
					'SMTP connect() failed.' => __( 'Не удалось подключиться к SMTP. Проверьте настройки хостинга.', 'wp-easy-forms' ),
					'Could not instantiate mail function.' => __( 'Функция отправки почты отключена на хостинге.', 'wp-easy-forms' ),
					'Invalid address' => __( 'Указан неверный email', 'wp-easy-forms' ),
					'SMTP server error' => __( 'Ошибка SMTP сервера', 'wp-easy-forms' ),
					'Message body empty' => __( 'Пустое тело сообщения', 'wp-easy-forms' ),
				);
				$translated_error = $raw_error;
				foreach ($translations as $en => $ru) {
					if (strpos($raw_error, $en) !== false) {
						$translated_error = str_replace($en, $ru, $raw_error);
						break;
					}
				}
				$mail_error_msg = $translated_error;
				
				$this->log_error( 'PHPMailer Error', $raw_error );
			}
		};
		add_action( 'wp_mail_failed', $error_handler );

		$mail_sent = wp_mail( $email_to, $subject, $body, $headers, $attachments );

		remove_action( 'wp_mail_failed', $error_handler );

		// Hook: после отправки письма, перед вебхуками
		do_action( 'wpef_after_email_send', $mail_sent, $current_config, $_POST, $attachments );

		// 4.1 Отправка Webhook (если настроено)
		$wh_tg_enable = !empty( $current_config['webhook_tg_enable'] );
		$wh_bx_enable = !empty( $current_config['webhook_bx_enable'] );
		$wh_custom_enable = !empty( $current_config['webhook_custom_enable'] );
		
		// Фолбэк для старой версии (когда не было типа webhook, а был просто URL)
		$webhook_type = isset( $current_config['webhook_type'] ) ? $current_config['webhook_type'] : '';
		if ( !empty($webhook_type) ) {
			if ( $webhook_type === 'telegram' ) $wh_tg_enable = true;
			if ( $webhook_type === 'bitrix24' ) $wh_bx_enable = true;
			if ( $webhook_type === 'custom' ) $wh_custom_enable = true;
		} elseif ( !empty($current_config['webhook_url']) && !$wh_tg_enable && !$wh_bx_enable && !$wh_custom_enable ) {
			$wh_custom_enable = true;
		}
		
		if ( $wh_tg_enable || $wh_bx_enable || $wh_custom_enable ) {
			
			// Сбор данных для вебхуков
			$wh_fields = array();
			foreach ( $_POST as $key => $value ) {
				if ( in_array( $key, $ignore_fields ) ) continue;
				$clean_val = is_array( $value ) ? implode(', ', array_map('sanitize_text_field', $value)) : sanitize_text_field( $value );
				$is_empty = trim($clean_val) === '';

				if ( $is_empty && !$send_empty ) {
					continue;
				}

				$clean_key = sanitize_text_field( str_replace(array('_', '-'), ' ', $key) );
				$clean_key = mb_convert_case($clean_key, MB_CASE_TITLE, "UTF-8");
				
				if ( $is_empty ) {
					$clean_val = __( 'НЕ ЗАПОЛНЕНО', 'wp-easy-forms' );
				} elseif ( mb_strtolower( trim($clean_val), 'UTF-8' ) === 'on' ) {
					$clean_val = __( 'Да / Получено', 'wp-easy-forms' );
				}

				$wh_fields[$clean_key] = $clean_val;
			}
			if ( !empty($uploaded_files) ) {
				$wh_fields[__( 'Файлы (Вложения)', 'wp-easy-forms' )] = implode(', ', array_map('basename', $uploaded_files));
			}
			if ( !empty($media_links) ) {
				$wh_fields[__( 'Ссылки на файлы', 'wp-easy-forms' )] = implode(', ', $media_links);
			}

			// --- Custom JSON Webhook ---
			if ( $wh_custom_enable && ! empty( $current_config['webhook_url'] ) ) {
				$webhook_data = array(
					'form_name' => $actual_form_name,
					'page_url'  => isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : '',
					'ip'        => $_SERVER['REMOTE_ADDR']
				);

				// Кастомный маппинг
				if ( !empty($current_config['webhook_mapping']) && is_array($current_config['webhook_mapping']) ) {
					$mapped_fields = array();
					$used_keys = array();
					
					foreach ( $current_config['webhook_mapping'] as $map ) {
						if ( empty($map['form_field']) || empty($map['json_key']) ) continue;
						
						// Ищем поле в $wh_fields по названию (игнорируя регистр)
						foreach ( $wh_fields as $k => $v ) {
							if ( mb_strtolower($k, 'UTF-8') === mb_strtolower($map['form_field'], 'UTF-8') ) {
								$webhook_data[ $map['json_key'] ] = $v;
								$used_keys[] = $k;
							}
						}
					}
					
					// Оставшиеся не-мапленные поля добавляем в 'fields' (или в корень)
					$unmapped = array();
					foreach ( $wh_fields as $k => $v ) {
						if ( !in_array($k, $used_keys) ) {
							$unmapped[$k] = $v;
						}
					}
					if ( !empty($unmapped) ) {
						$webhook_data['fields'] = $unmapped;
					}
				} else {
					$webhook_data['fields'] = $wh_fields;
				}
				
				$webhook_data = apply_filters( 'wpef_webhook_payload_custom', $webhook_data, $current_config, $_POST );

				$webhook_response = wp_remote_post( $current_config['webhook_url'], array(
					'body'    => wp_json_encode( $webhook_data ),
					'headers' => array( 'Content-Type' => 'application/json' ),
					'timeout' => 5,
					'blocking' => true
				) );

				if ( is_wp_error( $webhook_response ) ) {
					$this->log_error( 'Webhook Error (Custom)', $webhook_response->get_error_message(), $webhook_data );
				}
			} 
			
			// --- Telegram Webhook ---
			if ( $wh_tg_enable && ! empty( $current_config['webhook_tg_token'] ) && ! empty( $current_config['webhook_tg_chat'] ) ) {
				$tg_text = "📬 <b>" . __( 'Новая заявка:', 'wp-easy-forms' ) . " {$actual_form_name}</b>\n\n";
				foreach ( $wh_fields as $k => $v ) {
					$tg_text .= "<b>{$k}:</b> {$v}\n";
				}
				$tg_text .= "\n🌐 <b>" . __( 'Страница:', 'wp-easy-forms' ) . "</b> " . (isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : __( 'Неизвестно', 'wp-easy-forms' ));
				
				$tg_text = apply_filters( 'wpef_webhook_payload_telegram', $tg_text, $wh_fields, $current_config, $_POST );

				$tg_url = "https://api.telegram.org/bot" . trim($current_config['webhook_tg_token']) . "/sendMessage";
				
				$tg_response = wp_remote_post( $tg_url, array(
					'body'    => array(
						'chat_id'    => trim($current_config['webhook_tg_chat']),
						'text'       => $tg_text,
						'parse_mode' => 'HTML',
						'disable_web_page_preview' => true
					),
					'timeout' => 5,
					'blocking' => true
				) );

				if ( is_wp_error( $tg_response ) ) {
					$this->log_error( 'Webhook Error (Telegram)', $tg_response->get_error_message(), array('chat_id' => $current_config['webhook_tg_chat']) );
				} else {
					$tg_body = wp_remote_retrieve_body( $tg_response );
					$tg_json = json_decode( $tg_body, true );
					if ( isset($tg_json['ok']) && $tg_json['ok'] === false ) {
						$this->log_error( 'Webhook Error (Telegram API)', $tg_json['description'], array('chat_id' => $current_config['webhook_tg_chat']) );
					}
				}
			}

			// --- Bitrix24 Webhook ---
			if ( $wh_bx_enable && ! empty( $current_config['webhook_bx_url'] ) ) {
				// Формируем комментарий из всех полей
				$bx_comments = "" . __( 'Заявка с формы:', 'wp-easy-forms' ) . " {$actual_form_name}\n\n";
				foreach ( $wh_fields as $k => $v ) {
					$bx_comments .= "{$k}: {$v}\n";
				}
				$bx_comments .= "\n" . __( 'Страница:', 'wp-easy-forms' ) . " " . (isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : __( 'Неизвестно', 'wp-easy-forms' ));

				// Базовые поля для Битрикс24 crm.lead.add
				$bx_data = array(
					'FIELDS' => array(
						'TITLE'    => $actual_form_name . ' (' . wp_date('H:i') . ')',
						'COMMENTS' => $bx_comments,
						'SOURCE_ID' => 'WEB'
					),
					'PARAMS' => array('REGISTER_SONET_EVENT' => 'Y')
				);

				// Пытаемся разложить поля по маппингу
				if ( !empty($current_config['webhook_bx_mapping']) && is_array($current_config['webhook_bx_mapping']) ) {
					foreach ( $current_config['webhook_bx_mapping'] as $map ) {
						if ( empty($map['form_field']) || empty($map['json_key']) ) continue;
						
						$bx_key = strtoupper(trim($map['json_key']));
						
						foreach ( $wh_fields as $k => $v ) {
							if ( mb_strtolower($k, 'UTF-8') === mb_strtolower($map['form_field'], 'UTF-8') ) {
								if ($bx_key === 'PHONE' || $bx_key === 'EMAIL') {
									$bx_data['FIELDS'][$bx_key] = array( array('VALUE' => $v, 'VALUE_TYPE' => 'WORK') );
								} else {
									$bx_data['FIELDS'][$bx_key] = $v;
								}
							}
						}
					}
				} else {
					// Fallback: старая эвристика, если маппинг не настроен
					foreach ( $wh_fields as $k => $v ) {
						$k_lower = mb_strtolower($k);
						if ( strpos($k_lower, 'имя') !== false || strpos($k_lower, 'name') !== false || strpos($k_lower, 'фио') !== false || strpos($k_lower, 'first_name') !== false ) {
							if (!isset($bx_data['FIELDS']['NAME'])) $bx_data['FIELDS']['NAME'] = $v;
						}
						if ( strpos($k_lower, 'телефон') !== false || strpos($k_lower, 'phone') !== false ) {
							if (!isset($bx_data['FIELDS']['PHONE'])) $bx_data['FIELDS']['PHONE'] = array( array('VALUE' => $v, 'VALUE_TYPE' => 'WORK') );
						}
						if ( strpos($k_lower, 'email') !== false || strpos($k_lower, 'почта') !== false ) {
							if (!isset($bx_data['FIELDS']['EMAIL'])) $bx_data['FIELDS']['EMAIL'] = array( array('VALUE' => $v, 'VALUE_TYPE' => 'WORK') );
						}
						// UTM метки
						if ( strpos($k_lower, 'utm_source') !== false ) $bx_data['FIELDS']['UTM_SOURCE'] = $v;
						if ( strpos($k_lower, 'utm_medium') !== false ) $bx_data['FIELDS']['UTM_MEDIUM'] = $v;
						if ( strpos($k_lower, 'utm_campaign') !== false ) $bx_data['FIELDS']['UTM_CAMPAIGN'] = $v;
						if ( strpos($k_lower, 'utm_term') !== false ) $bx_data['FIELDS']['UTM_TERM'] = $v;
						if ( strpos($k_lower, 'utm_content') !== false ) $bx_data['FIELDS']['UTM_CONTENT'] = $v;
					}
				}

				$bx_data = apply_filters( 'wpef_webhook_payload_bitrix24', $bx_data, $current_config, $_POST );

				$bx_response = wp_remote_post( $current_config['webhook_bx_url'], array(
					'body'    => wp_json_encode( $bx_data ),
					'headers' => array( 'Content-Type' => 'application/json' ),
					'timeout' => 5,
					'blocking' => true
				) );

				if ( is_wp_error( $bx_response ) ) {
					$this->log_error( 'Webhook Error (Bitrix24)', $bx_response->get_error_message(), $bx_data );
				} else {
					$bx_body = wp_remote_retrieve_body( $bx_response );
					$bx_json = json_decode( $bx_body, true );
					if ( isset($bx_json['error']) ) {
						$this->log_error( 'Webhook Error (Bitrix24 API)', $bx_json['error_description'], $bx_data );
					}
				}
			}
		}

		// 4.2 Сохранение заявки в локальную БД
		global $wpdb;
		$leads_table = $wpdb->prefix . 'wpef_leads';
		
		$db_form_data = array();
		foreach ( $_POST as $key => $value ) {
			if ( in_array( $key, $ignore_fields ) ) continue;
			$val = is_array( $value ) ? implode(', ', array_map('sanitize_text_field', $value)) : sanitize_text_field( $value );
			$is_empty = trim($val) === '';
			
			if ( $is_empty && !$send_empty ) {
				continue;
			}
			
			if ( $is_empty ) {
				$val = __( 'НЕ ЗАПОЛНЕНО', 'wp-easy-forms' );
			} elseif ( mb_strtolower( trim($val), 'UTF-8' ) === 'on' ) {
				$val = __( 'Да / Получено', 'wp-easy-forms' );
			}
			
			$db_form_data[$key] = $val;
		}
		if ( !empty($uploaded_files) ) {
			$db_form_data['files'] = implode(', ', array_map('basename', $uploaded_files));
		}
		
		$db_form_data = apply_filters( 'wpef_before_lead_save', $db_form_data, $current_config, $_POST );

		$wpdb->insert(
			$leads_table,
			array(
				'config_id'  => $config_id,
				'form_name'  => $actual_form_name,
				'form_data'  => wp_json_encode( $db_form_data, JSON_UNESCAPED_UNICODE ),
				'ip_address' => $ip_address,
				'created_at' => current_time( 'mysql' )
			),
			array( '%s', '%s', '%s', '%s', '%s' )
		);
		
		$lead_id = $wpdb->insert_id;
		do_action( 'wpef_after_lead_saved', $lead_id, $db_form_data, $current_config, $_POST );

		// 5. Отправка автоответа (если включено)
		if ( ! empty( $current_config['reply_enabled'] ) && ! empty( $current_config['reply_email_field'] ) ) {
			$user_email_selector = trim( $current_config['reply_email_field'] );
			$user_email_value = '';
			
			// Сначала проверяем, не прислал ли фронтенд точное значение по селектору
			if ( ! empty( $_POST['wpef_client_user_email'] ) ) {
				$user_email_value = trim( $_POST['wpef_client_user_email'] );
			} 
			// Fallback: пытаемся найти по ключам POST (если селектор это просто name)
			else {
				// Очищаем селектор от скобок, если пользователь ввел [name="my_email"]
				$clean_name = str_replace( array('[name="', '"]', "[name='", "']"), '', $user_email_selector );
				$php_email_field = str_replace( array(' ', '.'), '_', $clean_name );
				
				foreach ( $_POST as $k => $v ) {
					if ( $k === $clean_name || $k === $php_email_field ) {
						$user_email_value = trim( is_array($v) ? $v[0] : $v );
						break;
					}
				}
			}

			if ( ! empty( $user_email_value ) && is_email( $user_email_value ) ) {
				$user_email = sanitize_email( $user_email_value );
				$reply_subject = str_replace( array_keys($placeholders), array_values($placeholders), $current_config['reply_subject'] );
				
				$raw_reply_body = trim($current_config['reply_body']);
				$raw_reply_body = preg_replace("/[\r\n]+/", "\n", $raw_reply_body);
				$raw_reply_body = nl2br( $raw_reply_body );
				$raw_reply_body = str_replace( array_keys($placeholders), array_values($placeholders), $raw_reply_body );

				// Оборачиваем автоответ в тот же HTML-шаблон, что и основное письмо
				$reply_body = "
		<div style=\"background-color: #f8fafc; padding: 40px 20px; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155;\">
			<div style=\"max-width: 850px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0;\">
				
				<!-- Header -->
				<div style=\"background-color: #4f46e5; padding: 25px 30px; text-align: center;\">
					<h2 style=\"color: #ffffff; margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;\">{$actual_form_name}</h2>
				</div>

				<!-- Content -->
				<div style=\"padding: 35px 30px; font-size: 15px; color: #475569;\">
					<div style=\"line-height: 1.8;\">
						{$raw_reply_body}
					</div>
				</div>
			</div>
		</div>";

				$reply_attachments = array();
				if ( ! empty( $current_config['reply_files'] ) && is_array( $current_config['reply_files'] ) ) {
					foreach ( $current_config['reply_files'] as $file_url ) {
						if ( empty( $file_url ) ) continue;
						$local_path = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file_url );
						if ( file_exists( $local_path ) ) {
							$reply_attachments[] = $local_path;
						}
					}
				}

				// Перехват ошибок автоответа
				$reply_error_msg = '';
				$reply_error_handler = function( $wp_error ) use ( &$reply_error_msg ) {
					if ( is_wp_error( $wp_error ) ) {
						$reply_error_msg = $wp_error->get_error_message();
					}
				};
				add_action( 'wp_mail_failed', $reply_error_handler );

				$reply_sent = wp_mail( $user_email, $reply_subject, $reply_body, $headers, $reply_attachments );
				
				remove_action( 'wp_mail_failed', $reply_error_handler );

				if ( ! $reply_sent ) {
					$mail_error_msg .= __( ' | Ошибка автоответа (', 'wp-easy-forms' ) . $user_email . '): ' . ( !empty($reply_error_msg) ? $reply_error_msg : __( 'Неизвестная ошибка отправки', 'wp-easy-forms' ) );
				}

			} else {
				$mail_error_msg .= __( ' | Автоответ не отправлен: поле email (', 'wp-easy-forms' ) . $user_email_field . __( ') не найдено или содержит неверный email (Получено: ', 'wp-easy-forms' ) . esc_attr($user_email_value) . ')';
			}
		}

		// 6. Очистка временных файлов
		foreach ( $uploaded_files as $file_path ) {
			if ( file_exists( $file_path ) ) {
				@unlink( $file_path );
			}
		}

		$config_id = isset( $_POST['wpef_config_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wpef_config_id'] ) ) : '';
		if ( $config_id ) {
			$table_stats = $wpdb->prefix . 'wpef_stats';
			$wpdb->query( $wpdb->prepare(
				"INSERT INTO $table_stats (config_id, views, submits) VALUES (%s, 0, 1) ON DUPLICATE KEY UPDATE submits = submits + 1",
				$config_id
			) );
		}

		if ( $mail_sent ) {
			wp_send_json_success( array( 'message' => __( 'Заявка успешно отправлена!', 'wp-easy-forms' ) ) );
		} else {
			wp_send_json_error( array( 
				'message'     => __( 'Ошибка отправки почты на сервере.', 'wp-easy-forms' ),
				'debug_error' => $mail_error_msg
			) );
		}
	}
}
