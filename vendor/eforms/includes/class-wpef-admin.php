<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPEF_Admin {

	private $option_name = 'wpef_configs';
	private $val_option_name = 'wpef_validation_rules';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_wpef_save_configs', array( $this, 'save_configs' ) );
		add_action( 'wp_ajax_wpef_save_validation', array( $this, 'save_validation' ) );
		add_action( 'wp_ajax_wpef_save_general', array( $this, 'save_general' ) );
		add_action( 'wp_ajax_wpef_import_settings', array( $this, 'import_settings' ) );
		add_action( 'wp_ajax_wpef_get_leads', array( $this, 'ajax_get_leads' ) );
		add_action( 'wp_ajax_wpef_get_logs', array( $this, 'ajax_get_logs' ) );
		add_action( 'wp_ajax_wpef_delete_lead', array( $this, 'ajax_delete_lead' ) );
		add_action( 'wp_ajax_wpef_delete_log', array( $this, 'ajax_delete_log' ) );
		add_action( 'wp_ajax_wpef_export_leads_csv', array( $this, 'export_leads_csv' ) );
		add_action( 'wp_ajax_wpef_export_leads_json', array( $this, 'export_leads_json' ) );
		add_action( 'wp_ajax_wpef_export_logs_csv', array( $this, 'export_logs_csv' ) );
		add_action( 'wp_ajax_wpef_export_logs_json', array( $this, 'export_logs_json' ) );
		add_action( 'wp_ajax_wpef_clear_leads', array( $this, 'ajax_clear_leads' ) );
		add_action( 'wp_ajax_wpef_clear_logs', array( $this, 'ajax_clear_logs' ) );
		add_action( 'wp_ajax_wpef_send_test_email', array( $this, 'send_test_email' ) );
	}

	public function add_admin_menu() {
		add_menu_page(
			__( 'Настройки форм', 'wp-easy-forms' ),
			__( 'Настройки форм', 'wp-easy-forms' ),
			'manage_options',
			'wp-easy-forms',
			array( $this, 'render_settings_page' ),
			'dashicons-email-alt',
			80
		);
	}

	public function enqueue_admin_scripts( $hook ) {
		if ( 'toplevel_page_wp-easy-forms' !== $hook ) {
			return;
		}

		wp_enqueue_media(); // Для загрузки файлов (автоответ)
		
		$cm_settings = array();
		if ( function_exists( 'wp_enqueue_code_editor' ) ) {
			$cm_settings = wp_enqueue_code_editor( array( 'type' => 'application/javascript' ) );
		}

		wp_enqueue_style( 'wp-easy-forms-admin', WPEF_URL . 'assets/css/admin.css', array(), WPEF_VERSION );
		wp_enqueue_script( 'wp-easy-forms-admin', WPEF_URL . 'assets/js/admin.js', array( 'jquery' ), WPEF_VERSION, true );

		$configs = get_option( $this->option_name, array() );
		if ( empty( $configs ) ) {
			$configs = array();
		}

		$validation_rules = get_option( $this->val_option_name, array() );
		if ( empty( $validation_rules ) ) {
			$validation_rules = array();
		}

		$general_settings = get_option( 'wpef_general_settings', array() );

		// Получаем статистику конверсий
		global $wpdb;
		$stats_table = $wpdb->prefix . 'wpef_stats';
		$stats = array();
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$stats_table'" ) == $stats_table ) {
			$stats_raw = $wpdb->get_results( "SELECT * FROM $stats_table", ARRAY_A );
			if ( $stats_raw ) {
				foreach ( $stats_raw as $row ) {
					$stats[ $row['config_id'] ] = $row;
				}
			}
		}

		wp_localize_script( 'wp-easy-forms-admin', 'wpefAdminData', array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'wpef_save_configs_nonce' ),
			'nonce_validation' => wp_create_nonce( 'wpef_save_validation_nonce' ),
			'configs'          => $configs,
			'validation_rules' => $validation_rules,
			'general_settings' => $general_settings,
			'stats'            => $stats,
			'cm_settings'      => $cm_settings,
			'i18n'             => array(
				'loadingData'      => __( 'Загрузка...', 'wp-easy-forms' ),
				'page'             => __( 'Страница ', 'wp-easy-forms' ),
				'of'               => __( ' из ', 'wp-easy-forms' ),
				'btnBack'          => __( '&laquo; Назад', 'wp-easy-forms' ),
				'btnForward'       => __( 'Вперед &raquo;', 'wp-easy-forms' ),
				'confirmDelLead'   => __( 'Удалить эту заявку?', 'wp-easy-forms' ),
				'confirmDelLog'    => __( 'Удалить этот лог?', 'wp-easy-forms' ),
				'errDelete'        => __( 'Ошибка удаления', 'wp-easy-forms' ),
				'confirmClearLds'  => __( 'Вы уверены, что хотите безвозвратно удалить ВСЕ заявки из базы?', 'wp-easy-forms' ),
				'errClearLds'      => __( 'Ошибка очистки таблицы заявок.', 'wp-easy-forms' ),
				'confirmClearLgs'  => __( 'Вы уверены, что хотите полностью очистить журнал ошибок?', 'wp-easy-forms' ),
				'errClearLgs'      => __( 'Ошибка очистки журнала.', 'wp-easy-forms' ),
				'copied'           => __( 'Скопировано!', 'wp-easy-forms' ),
				'confirmDelConfig' => __( 'Вы уверены, что хотите удалить эту конфигурацию формы?', 'wp-easy-forms' ),
				'untitled'         => __( 'Без названия', 'wp-easy-forms' ),
				'errFillRequired'  => __( 'Пожалуйста, заполните все обязательные поля (отмечены звездочкой *) в настройках формы.', 'wp-easy-forms' ),
				'errSave'          => __( 'Ошибка сохранения: ', 'wp-easy-forms' ),
				'errNetwork'       => __( 'Ошибка сети', 'wp-easy-forms' ),
				'allForms'         => __( 'Все формы', 'wp-easy-forms' ),
				'statsViews'       => __( 'Показы форм', 'wp-easy-forms' ),
				'statsSubmits'     => __( 'Успешные отправки', 'wp-easy-forms' ),
				'statsCR'          => __( 'Конверсия (CR)', 'wp-easy-forms' ),
				'newConfig'        => __( 'Новая конфигурация', 'wp-easy-forms' ),
				'defaultEmailSubject'=> __( 'Новая заявка: {{__form_name}}', 'wp-easy-forms' ),
				'defaultEmailBody' => __( "Данные формы:
{{__fields}}", 'wp-easy-forms' ),
				'placeholderFormField'=> __( 'Имя поля формы', 'wp-easy-forms' ),
				'placeholderJsonKey' => __( 'Ключ JSON', 'wp-easy-forms' ),
				'btnSelect'        => __( 'Выбрать', 'wp-easy-forms' ),
				'btnDeleteFile'    => __( 'Удалить файл', 'wp-easy-forms' ),
				'placeholderNameFieldPhone'=> __( 'Имя поля (например, phone)', 'wp-easy-forms' ),
				'placeholderJsonKeyExample'=> __( 'Ключ JSON (например, customer_phone)', 'wp-easy-forms' ),
				'placeholderNameFieldUserPhone'=> __( 'Имя поля (например, user_phone)', 'wp-easy-forms' ),
				'placeholderB24Key'=> __( 'Ключ B24 (например, PHONE)', 'wp-easy-forms' ),
				'selectReplyFile'  => __( 'Выберите файл для автоответа', 'wp-easy-forms' ),
				'errValSelector'   => __( 'Пожалуйста, заполните «Селектор поля» во всех глобальных правилах валидации.', 'wp-easy-forms' ),
				'exAcceptTypes'    => __( 'Например: .png, .jpg, application/pdf', 'wp-easy-forms' ),
				'exRegexp'         => __( 'Например: ^[0-9]+$', 'wp-easy-forms' ),
				'exMaxTotalSize'   => __( 'Например: 7340032 (в байтах)', 'wp-easy-forms' ),
				'exNumber10'       => __( 'Например: 10', 'wp-easy-forms' ),
				'confirmDelValField'=> __( 'Удалить настройки валидации для этого селектора?', 'wp-easy-forms' ),
				'errTestEmail'     => __( 'Пожалуйста, введите email для теста', 'wp-easy-forms' ),
				'on'               => __( 'ВКЛ', 'wp-easy-forms' ),
				'off'              => __( 'ВЫКЛ', 'wp-easy-forms' ),
				'successStrong'    => __( '<strong>Успешно:</strong> ', 'wp-easy-forms' ),
				'errorStrong'      => __( '<strong>Ошибка:</strong> ', 'wp-easy-forms' ),
				'errorNetworkStrong'=> __( '<strong>Ошибка сети.</strong>', 'wp-easy-forms' ),
				'configCopied'     => __( 'Конфигурация скопирована в буфер обмена!', 'wp-easy-forms' ),
				'errEmptyImport'   => __( 'Пожалуйста, вставьте код конфигурации для импорта.', 'wp-easy-forms' ),
				'errInvalidImportFormat'=> __( 'Неверный формат данных. Убедитесь, что скопировали правильный код.', 'wp-easy-forms' ),
				'confirmImport'    => __( 'Вы уверены? Это полностью перезапишет текущие настройки форм и валидации!', 'wp-easy-forms' ),
				'pageWillReload'   => __( 'Страница будет перезагружена.', 'wp-easy-forms' ),
				'errImport'        => __( 'Ошибка импорта: ', 'wp-easy-forms' ),
				'errNetworkImport' => __( 'Ошибка сети при импорте', 'wp-easy-forms' ),
				'errLoadData'      => __( 'Ошибка загрузки данных: ', 'wp-easy-forms' ),
				'errUnknown'       => __( 'Неизвестная ошибка', 'wp-easy-forms' ),
				'errNetworkLoadData'=> __( 'Ошибка сети при загрузке данных. Проверьте консоль браузера.', 'wp-easy-forms' ),
				'noData'           => __( 'Нет данных', 'wp-easy-forms' ),
				'leadData'         => __( 'Данные заявки', 'wp-easy-forms' ),
				'copyToClipboard'  => __( 'Скопировать в буфер', 'wp-easy-forms' ),
				'reqDetails'       => __( 'Детали заявки (JSON)', 'wp-easy-forms' ),
				'yesReceived'      => __( 'Да / Получено', 'wp-easy-forms' )
			)
		) );
	}

	public function save_configs() {
		check_ajax_referer( 'wpef_save_configs_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		$configs = isset( $_POST['configs'] ) ? json_decode( stripslashes( $_POST['configs'] ), true ) : array();

		update_option( $this->option_name, $configs );

		wp_send_json_success( __( 'Настройки сохранены', 'wp-easy-forms' ) );
	}

	public function save_validation() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		$rules = isset( $_POST['rules'] ) ? json_decode( stripslashes( $_POST['rules'] ), true ) : array();
		
		update_option( $this->val_option_name, $rules );

		wp_send_json_success( __( 'Правила валидации сохранены', 'wp-easy-forms' ) );
	}

	public function save_general() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		$general_settings = isset( $_POST['general_settings'] ) ? json_decode( stripslashes( $_POST['general_settings'] ), true ) : array();
		update_option( 'wpef_general_settings', $general_settings );

		wp_send_json_success( __( 'Общие настройки сохранены', 'wp-easy-forms' ) );
	}

	public function import_settings() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		$import_data = isset( $_POST['import_data'] ) ? json_decode( stripslashes( $_POST['import_data'] ), true ) : null;
		
		if ( ! $import_data ) {
			wp_send_json_error( __( 'Неверный формат JSON', 'wp-easy-forms' ) );
		}

		if ( isset( $import_data['configs'] ) ) {
			update_option( $this->option_name, $import_data['configs'] );
		}
		if ( isset( $import_data['validation_rules'] ) ) {
			update_option( $this->val_option_name, $import_data['validation_rules'] );
		}
		if ( isset( $import_data['general_settings'] ) ) {
			update_option( 'wpef_general_settings', $import_data['general_settings'] );
		}

		wp_send_json_success( __( 'Настройки успешно импортированы', 'wp-easy-forms' ) );
	}

	public function send_test_email() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		$to_email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$is_custom = isset( $_POST['is_custom'] ) && $_POST['is_custom'] === 'true';
		$subject  = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
		$message  = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';

		if ( ! is_email( $to_email ) ) {
			wp_send_json_error( __( 'Неверный email адрес', 'wp-easy-forms' ) );
		}

		if ( ! $is_custom ) {
			$subject = __( 'Тестовое сообщение Настроек форм', 'wp-easy-forms' );
			$message = __( "Поздравляем!

Если вы получили это письмо, значит SMTP плагина Настроек форм работает корректно.", 'wp-easy-forms' );
		}

		$headers = array();
		$headers[] = 'Content-Type: text/plain; charset=UTF-8';

		$result = wp_mail( $to_email, $subject, $message, $headers );

		if ( $result ) {
			wp_send_json_success( __( 'Тестовое письмо успешно отправлено!', 'wp-easy-forms' ) );
		} else {
			wp_send_json_error( __( 'Ошибка отправки письма. Проверьте настройки SMTP и журнал ошибок.', 'wp-easy-forms' ) );
		}
	}

	public function ajax_get_leads() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_leads';
		$page = isset( $_POST['paged'] ) ? max( 1, intval( $_POST['paged'] ) ) : 1;
		$per_page = 20;
		$offset = ( $page - 1 ) * $per_page;

		// Check if table exists
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
			wp_send_json_success( array( 'html' => '<p>' . __( 'Таблица заявок не найдена. Пожалуйста, переактивируйте плагин.', 'wp-easy-forms' ) . '</p>', 'total_pages' => 0 ) );
		}

		$search_query = '';
		if ( ! empty( $_POST['search'] ) ) {
			$search_term = $wpdb->esc_like( sanitize_text_field( wp_unslash( $_POST['search'] ) ) );
			$search_query = " WHERE form_data LIKE '%" . $search_term . "%' OR form_name LIKE '%" . $search_term . "%'";
		}

		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table" . $search_query );
		$total_pages = ceil( $total_items / $per_page );

		$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table" . $search_query . " ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset ) );

		ob_start();
		if ( $items ) {
			echo '<table class="wp-list-table widefat fixed striped table-view-list">';
			echo '<thead><tr><th>ID</th><th>' . __( 'Дата', 'wp-easy-forms' ) . '</th><th>' . __( 'Форма', 'wp-easy-forms' ) . '</th><th>' . __( 'Данные', 'wp-easy-forms' ) . '</th><th>IP</th><th>' . __( 'Действия', 'wp-easy-forms' ) . '</th></tr></thead>';
			echo '<tbody>';
			foreach ( $items as $item ) {
				$data = json_decode( $item->form_data, true );
				echo '<tr>';
				echo '<td>' . intval( $item->id ) . '</td>';
				echo '<td>' . esc_html( wp_date( get_option('date_format') . ' H:i', strtotime( $item->created_at ) ) ) . '</td>';
				echo '<td>' . esc_html( $item->form_name ) . '</td>';
				echo '<td>
					<button type="button" class="button button-small wpef-view-data" data-json="' . esc_attr( wp_json_encode( $data ) ) . '">' . __( 'Смотреть данные', 'wp-easy-forms' ) . '</button>
				</td>';
				echo '<td>' . esc_html( $item->ip_address ) . '</td>';
				echo '<td><button class="button button-small wpef-delete-lead" data-id="' . intval( $item->id ) . '" style="color: #d63638; border-color: #d63638;">' . __( 'Удалить', 'wp-easy-forms' ) . '</button></td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<p>' . __( 'Заявок пока нет.', 'wp-easy-forms' ) . '</p>';
		}
		$html = ob_get_clean();

		wp_send_json_success( array( 'html' => $html, 'total_pages' => $total_pages, 'current_page' => $page ) );
	}

	public function ajax_get_logs() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_logs';
		$page = isset( $_POST['paged'] ) ? max( 1, intval( $_POST['paged'] ) ) : 1;
		$per_page = 20;
		$offset = ( $page - 1 ) * $per_page;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
			wp_send_json_success( array( 'html' => '<p>' . __( 'Таблица логов не найдена. Пожалуйста, переактивируйте плагин.', 'wp-easy-forms' ) . '</p>', 'total_pages' => 0 ) );
		}

		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table" );
		$total_pages = ceil( $total_items / $per_page );

		$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset ) );

		ob_start();
		if ( $items ) {
			echo '<table class="wp-list-table widefat fixed striped table-view-list">';
			echo '<thead><tr><th>ID</th><th>' . __( 'Дата', 'wp-easy-forms' ) . '</th><th>' . __( 'Тип ошибки', 'wp-easy-forms' ) . '</th><th>' . __( 'Сообщение об ошибке', 'wp-easy-forms' ) . '</th><th>' . __( 'Данные (JSON)', 'wp-easy-forms' ) . '</th><th>' . __( 'Действия', 'wp-easy-forms' ) . '</th></tr></thead>';
			echo '<tbody>';
			foreach ( $items as $item ) {
				$has_data = !empty($item->form_data) && $item->form_data !== '[]' && $item->form_data !== '{}';
				
				echo '<tr>';
				echo '<td>' . intval( $item->id ) . '</td>';
				echo '<td>' . esc_html( wp_date( get_option('date_format') . ' H:i', strtotime( $item->created_at ) ) ) . '</td>';
				echo '<td>' . esc_html( $item->error_type ) . '</td>';
				echo '<td><code>' . esc_html( $item->error_message ) . '</code></td>';
				if ($has_data) {
					echo '<td><button type="button" class="button button-small wpef-view-json" data-json="' . esc_attr( $item->form_data ) . '">' . __( 'Посмотреть JSON', 'wp-easy-forms' ) . '</button></td>';
				} else {
					echo '<td><button type="button" class="button button-small" disabled title="' . esc_attr__( 'Нет дополнительных данных', 'wp-easy-forms' ) . '">' . __( 'Посмотреть JSON', 'wp-easy-forms' ) . '</button></td>';
				}
				echo '<td><button class="button button-small wpef-delete-log" data-id="' . intval( $item->id ) . '" style="color: #d63638; border-color: #d63638;">' . __( 'Удалить', 'wp-easy-forms' ) . '</button></td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<p>' . __( 'Журнал ошибок пуст.', 'wp-easy-forms' ) . '</p>';
		}
		$html = ob_get_clean();

		wp_send_json_success( array( 'html' => $html, 'total_pages' => $total_pages, 'current_page' => $page ) );
	}

	public function ajax_delete_lead() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

		global $wpdb;
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		if ( $id ) {
			$wpdb->delete( $wpdb->prefix . 'wpef_leads', array( 'id' => $id ), array( '%d' ) );
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	public function ajax_delete_log() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

		global $wpdb;
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		if ( $id ) {
			$wpdb->delete( $wpdb->prefix . 'wpef_logs', array( 'id' => $id ), array( '%d' ) );
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	public function ajax_clear_leads() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_leads';
		$wpdb->query( "TRUNCATE TABLE $table" );
		wp_send_json_success();
	}

	public function ajax_clear_logs() {
		check_ajax_referer( 'wpef_save_validation_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_logs';
		$wpdb->query( "TRUNCATE TABLE $table" );
		wp_send_json_success();
	}

	public function export_leads_csv() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'wpef_export_leads_csv' ) ) {
			wp_die( __( 'Недействительный ключ безопасности', 'wp-easy-forms' ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_leads';
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
			wp_die( __( 'Таблица заявок не найдена', 'wp-easy-forms' ) );
		}

		$items = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );

		// Подготовка CSV
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="wpef-leads-' . date('Y-m-d') . '.csv"');
		
		// Добавляем BOM для корректного отображения кириллицы в Excel
		echo "\xEF\xBB\xBF";

		$output = fopen('php://output', 'w');

		// Заголовки (разделитель - точка с запятой для русского Excel)
		fputcsv($output, array( 'ID', __( 'Дата', 'wp-easy-forms' ), __( 'Форма', 'wp-easy-forms' ), 'IP', __( 'Данные', 'wp-easy-forms' ) ), ';');

		if ( $items ) {
			foreach ( $items as $item ) {
				$data = json_decode( $item->form_data, true );
				$data_str = '';
				if ( is_array( $data ) ) {
					foreach ( $data as $k => $v ) {
						if ( in_array( $k, array('wpef_website_url', 'wpef_client_user_email') ) ) continue;
						$data_str .= $k . ': ' . $v . " | ";
					}
				}
				$data_str = rtrim($data_str, " | ");

				fputcsv($output, array(
					$item->id,
					$item->created_at,
					$item->form_name,
					$item->ip_address,
					$data_str
				), ';');
			}
		}

		fclose($output);
		exit;
	}

	public function export_leads_json() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'wpef_export_leads_json' ) ) {
			wp_die( __( 'Недействительный ключ безопасности', 'wp-easy-forms' ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_leads';
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
			wp_die( __( 'Таблица заявок не найдена', 'wp-easy-forms' ) );
		}

		$items = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );

		$export_data = array();
		if ( $items ) {
			foreach ( $items as $item ) {
				$data = json_decode( $item->form_data, true );
				// Фильтруем технические поля
				if ( is_array( $data ) ) {
					foreach ( array('wpef_website_url', 'wpef_client_user_email') as $tech_field ) {
						if ( isset($data[$tech_field]) ) unset($data[$tech_field]);
					}
				}
				
				$export_data[] = array(
					'id'         => $item->id,
					'created_at' => $item->created_at,
					'form_name'  => $item->form_name,
					'ip_address' => $item->ip_address,
					'data'       => $data
				);
			}
		}

		header('Content-Type: application/json; charset=utf-8');
		header('Content-Disposition: attachment; filename="wpef-leads-' . date('Y-m-d') . '.json"');
		
		echo wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		exit;
	}

	public function export_logs_json() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'wpef_export_leads_csv' ) ) {
			wp_die( __( 'Недействительный ключ безопасности', 'wp-easy-forms' ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_logs';
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
			wp_die( __( 'Таблица логов не найдена', 'wp-easy-forms' ) );
		}

		$items = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );

		$export_data = array();
		if ( $items ) {
			foreach ( $items as $item ) {
				$data = json_decode( $item->form_data, true );
				
				$export_data[] = array(
					'id'            => $item->id,
					'created_at'    => $item->created_at,
					'error_type'    => $item->error_type,
					'error_message' => $item->error_message,
					'data'          => $data
				);
			}
		}

		header('Content-Type: application/json; charset=utf-8');
		header('Content-Disposition: attachment; filename="wpef-logs-' . date('Y-m-d') . '.json"');
		
		echo wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		exit;
	}

	public function export_logs_csv() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'wpef_export_leads_csv' ) ) {
			wp_die( __( 'Недействительный ключ безопасности', 'wp-easy-forms' ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Доступ запрещен', 'wp-easy-forms' ) );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'wpef_logs';
		
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
			wp_die( __( 'Таблица логов не найдена', 'wp-easy-forms' ) );
		}

		$items = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );

		// Подготовка CSV
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="wpef-logs-' . date('Y-m-d') . '.csv"');
		
		// Добавляем BOM для корректного отображения кириллицы в Excel
		echo "\xEF\xBB\xBF";

		$output = fopen('php://output', 'w');

		// Заголовки (разделитель - точка с запятой для русского Excel)
		fputcsv($output, array( 'ID', __( 'Дата', 'wp-easy-forms' ), __( 'Тип ошибки', 'wp-easy-forms' ), __( 'Сообщение об ошибке', 'wp-easy-forms' ), __( 'Данные', 'wp-easy-forms' ) ), ';');

		if ( $items ) {
			foreach ( $items as $item ) {
				fputcsv($output, array(
					$item->id,
					$item->created_at,
					$item->error_type,
					$item->error_message,
					$item->form_data
				), ';');
			}
		}

		fclose($output);
		exit;
	}

	public function render_settings_page() {
		?>
		<div class="wrap wp-easy-forms-wrap">
			<h1><?php esc_html_e( 'Управление Формами', 'wp-easy-forms' ); ?></h1>
			
			<div class="wpef-layout">
				<div class="wpef-main-content">
					<h2 class="nav-tab-wrapper wpef-tabs">
						<a href="#tab-dashboard" class="nav-tab nav-tab-active" data-tab="dashboard"><?php esc_html_e( 'Дашборд', 'wp-easy-forms' ); ?></a>
						<a href="#tab-forms" class="nav-tab" data-tab="forms"><?php esc_html_e( 'Настройки форм', 'wp-easy-forms' ); ?></a>
						<a href="#tab-validation" class="nav-tab" data-tab="validation"><?php esc_html_e( 'Валидация', 'wp-easy-forms' ); ?></a>
						<a href="#tab-leads" class="nav-tab" data-tab="leads"><?php esc_html_e( 'Заявки', 'wp-easy-forms' ); ?></a>
						<a href="#tab-logs" class="nav-tab" data-tab="logs"><?php esc_html_e( 'Журнал ошибок', 'wp-easy-forms' ); ?></a>
						<a href="#tab-smtp" class="nav-tab" data-tab="smtp">SMTP</a>
						<a href="#tab-settings" class="nav-tab" data-tab="settings"><?php esc_html_e( 'Настройки', 'wp-easy-forms' ); ?></a>
						<a href="#tab-hooks" class="nav-tab" data-tab="hooks"><?php esc_html_e( 'Справочник хуков', 'wp-easy-forms' ); ?></a>
					</h2>

					<!-- Вкладка: Дашборд -->
					<div id="tab-dashboard" class="wpef-tab-content wpef-tab-active">
						<div class="wpef-dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
							<?php
							global $wpdb;
							$table_leads = $wpdb->prefix . 'wpef_leads';
							$table_logs = $wpdb->prefix . 'wpef_logs';
							
							$total_leads = 0;
							$month_leads = 0;
							$total_errors = 0;
							$top_forms = array();
							
							if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_leads'" ) == $table_leads ) {
								$total_leads = $wpdb->get_var("SELECT COUNT(id) FROM $table_leads");
								$month_leads = $wpdb->get_var("SELECT COUNT(id) FROM $table_leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
								$top_forms = $wpdb->get_results("SELECT form_name, COUNT(id) as cnt FROM $table_leads GROUP BY form_name ORDER BY cnt DESC LIMIT 5");
							}
							
							if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_logs'" ) == $table_logs ) {
								$total_errors = $wpdb->get_var("SELECT COUNT(id) FROM $table_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
							}
							?>
							
							<div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
								<div style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 10px;"><?php esc_html_e( 'Всего заявок', 'wp-easy-forms' ); ?></div>
								<div style="font-size: 32px; font-weight: 700; color: #0f172a;"><?php echo esc_html($total_leads); ?></div>
							</div>
							
							<div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
								<div style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 10px;"><?php esc_html_e( 'Заявок за 30 дней', 'wp-easy-forms' ); ?></div>
								<div style="font-size: 32px; font-weight: 700; color: #10b981;"><?php echo esc_html($month_leads); ?></div>
							</div>
							
							<div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
								<div style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 10px;"><?php esc_html_e( 'Ошибок за 30 дней', 'wp-easy-forms' ); ?></div>
								<div style="font-size: 32px; font-weight: 700; color: <?php echo $total_errors > 0 ? '#ef4444' : '#0f172a'; ?>;"><?php echo esc_html($total_errors); ?></div>
							</div>
						</div>
						
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
							<div class="wpef-config-block">
								<div class="wpef-config-header">
									<h3 class="wpef-config-title"><?php esc_html_e( 'Популярные формы', 'wp-easy-forms' ); ?></h3>
								</div>
								<div class="wpef-config-body">
								<?php if (empty($top_forms)): ?>
									<p style="color: #64748b;"><?php esc_html_e( 'Нет данных для отображения.', 'wp-easy-forms' ); ?></p>
								<?php else: ?>
									<ul style="margin: 0; padding: 0; list-style: none;">
										<?php foreach($top_forms as $form): ?>
											<li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
												<span style="font-weight: 500;"><?php echo esc_html($form->form_name ? $form->form_name : 'Без названия'); ?></span>
												<span style="background: #f1f5f9; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600; color: #475569;"><?php echo esc_html($form->cnt); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</div>
					</div>
					</div>

					<!-- Вкладка: Настройки форм -->
					<div id="tab-forms" class="wpef-tab-content">
						<div id="wpef-configs-container"></div>

						<!-- Справочник по событиям форм -->
						<div class="wpef-config-block" style="margin-top: 30px;">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Справочник событий JS (Events)', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
							<div class="wpef-intro-text" style="margin-top: 0; padding: 15px; background: #fff; border-left: 4px solid #2271b1; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
								<p style="margin: 0;"><?php esc_html_e( 'Плагин генерирует пользовательские JavaScript-события (Custom Events), к которым можно подключиться из любой части вашей темы для интеграции с аналитикой (Яндекс.Метрика, Яндекс.Цели, Google Analytics, VK Pixel и др.) или другой кастомной логики.', 'wp-easy-forms' ); ?></p>
							</div>
							
							<div class="wpef-validation-help-grid" style="grid-template-columns: 1fr;">
								
								<div class="wpef-help-group">
									<h4><?php esc_html_e( 'Событие успешной отправки:', 'wp-easy-forms' ); ?> <code>wpefFormSuccess</code></h4>
									<p><?php esc_html_e( 'Срабатывает на объекте формы (', 'wp-easy-forms' ); ?><code>&lt;form&gt;</code><?php esc_html_e( ') после получения успешного ответа от сервера.', 'wp-easy-forms' ); ?></p>
									<div style="background: #1e1e1e; padding: 15px; border-radius: 6px; margin-top: 10px; overflow-x: auto;">
										<code style="color: #d4d4d4; background: transparent; padding: 0; display: block; white-space: pre;">// Пример подключения к событию
document.addEventListener('wpefFormSuccess', function(event) {
    var formElement = event.detail.form;     // DOM-элемент отправленной формы
    var serverData = event.detail.response;  // Ответ от сервера

    // Пример: отправка цели в Яндекс.Метрику
    if (typeof ym !== 'undefined') {
        ym(XXXXXX, 'reachGoal', 'form_success');
    }

    console.log('Форма отправлена:', formElement.id);
});</code>
									</div>
								</div>

							</div>
						</div>
					</div>
					</div>

					<!-- Вкладка: Глобальная валидация -->
					<div id="tab-validation" class="wpef-tab-content">
						<div class="wpef-intro-text">
							<p><?php esc_html_e( 'Здесь вы можете настроить глобальные правила валидации для любых полей на сайте. Правила будут применяться ко всем формам, которые обрабатывает плагин, если в них есть поля с указанными селекторами.', 'wp-easy-forms' ); ?></p>
						</div>

						<div id="wpef-global-validation-container"></div>
						
						<!-- Справочник по правилам валидации -->
						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Справочник правил валидации', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
							<div class="wpef-validation-help-grid">
								
								<div class="wpef-help-group">
									<h4><?php esc_html_e( 'Стандартные правила', 'wp-easy-forms' ); ?></h4>
									<ul>
										<li><strong><?php esc_html_e( 'Обязательное поле:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Проверяет, что поле не пустое. Значение не требуется.', 'wp-easy-forms' ); ?></li>
										<li><strong><?php esc_html_e( 'Email адрес:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Проверяет корректность формата email. Значение не требуется.', 'wp-easy-forms' ); ?></li>
										<li><strong><?php esc_html_e( 'Минимум символов:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Задайте минимальную длину строки (например:', 'wp-easy-forms' ); ?> <code>5</code>).</li>
										<li><strong><?php esc_html_e( 'Максимум символов:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Задайте максимальную длину строки (например:', 'wp-easy-forms' ); ?> <code>100</code>).</li>
										<li><strong><?php esc_html_e( 'Пароль:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Проверяет, что строка содержит минимум 8 символов, хотя бы одну букву и одну цифру.', 'wp-easy-forms' ); ?></li>
										<li><strong><?php esc_html_e( 'Строгий пароль:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Проверяет минимум на 8 символов, наличие заглавной и строчной буквы, цифры и спецсимвола.', 'wp-easy-forms' ); ?></li>
										<li><strong><?php esc_html_e( 'Регулярное выражение:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Введите регулярное выражение для проверки (например:', 'wp-easy-forms' ); ?> <code>^[a-zA-Z]+$</code> <?php esc_html_e( 'для латиницы).', 'wp-easy-forms' ); ?></li>
									</ul>
								</div>

								<div class="wpef-help-group">
									<h4><?php esc_html_e( 'Работа с числами', 'wp-easy-forms' ); ?></h4>
									<ul>
										<li><strong><?php esc_html_e( 'Только числа:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Поле должно содержать только цифры. Значение не требуется.', 'wp-easy-forms' ); ?></li>
										<li><strong><?php esc_html_e( 'Только целые числа:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Запрещает ввод дробных значений. Значение не требуется.', 'wp-easy-forms' ); ?></li>
										<li><strong><?php esc_html_e( 'Минимальное число:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Задайте нижний порог (например:', 'wp-easy-forms' ); ?> <code>18</code>).</li>
										<li><strong><?php esc_html_e( 'Максимальное число:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Задайте верхний порог (например:', 'wp-easy-forms' ); ?> <code>99</code>).</li>
									</ul>
								</div>

								<div class="wpef-help-group">
									<h4><?php esc_html_e( 'Работа с файлами (только input type="file")', 'wp-easy-forms' ); ?></h4>
									<ul>
										<li><strong><?php esc_html_e( 'Мин. количество файлов:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Минимальное число прикрепленных файлов (например:', 'wp-easy-forms' ); ?> <code>2</code>).</li>
										<li><strong><?php esc_html_e( 'Макс. количество файлов:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Максимальное число прикрепленных файлов (например:', 'wp-easy-forms' ); ?> <code>5</code>).</li>
										<li><strong><?php esc_html_e( 'Макс. общий вес:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Ограничение по размеру в байтах (например:', 'wp-easy-forms' ); ?> <code>7340032</code> <?php esc_html_e( '= 7МБ).', 'wp-easy-forms' ); ?></li>
										<li><strong><?php esc_html_e( 'Допустимые типы:', 'wp-easy-forms' ); ?></strong> <?php esc_html_e( 'Список расширений или', 'wp-easy-forms' ); ?> <a href="https://developer.mozilla.org/ru/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types" target="_blank" rel="noopener noreferrer" style="color: #2271b1; text-decoration: underline; text-decoration-style: dotted;"><?php esc_html_e( 'MIME-типов', 'wp-easy-forms' ); ?></a> <?php esc_html_e( 'через запятую (например:', 'wp-easy-forms' ); ?> <code>.png, .jpg, application/pdf</code>).</li>
									</ul>
								</div>

							</div>
							
							<div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
								<p style="margin: 0; font-size: 13px; color: #646970;">
									<?php esc_html_e( 'Подробная информация о доступных правилах и их поведении доступна в официальной документации:', 'wp-easy-forms' ); ?> 
									<a href="https://just-validate.dev/docs/intro" target="_blank" rel="noopener noreferrer" style="color: #2271b1; text-decoration: underline;">https://just-validate.dev/docs/intro</a>
								</p>
							</div>
						</div>
					</div>
					</div>

					<!-- Вкладка: Заявки -->
					<div id="tab-leads" class="wpef-tab-content">
						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Список заявок', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
								<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
									<div style="display: flex; align-items: center; gap: 15px;">
										<div style="display: flex; gap: 5px;">
											<input type="search" id="wpef-leads-search" placeholder="<?php esc_attr_e( 'Поиск по email/данным...', 'wp-easy-forms' ); ?>" style="padding: 0 8px; line-height: 2; min-height: 30px; width: 220px;">
											<button type="button" class="button" id="wpef-leads-search-btn"><?php esc_html_e( 'Найти', 'wp-easy-forms' ); ?></button>
										</div>
									</div>
									<div>
										<a href="<?php echo esc_url( admin_url('admin-ajax.php?action=wpef_export_leads_json&nonce=' . wp_create_nonce('wpef_export_leads_json')) ); ?>" class="button button-secondary" style="margin-right: 10px;"><?php esc_html_e( 'Экспорт в JSON', 'wp-easy-forms' ); ?></a>
										<a href="<?php echo esc_url( admin_url('admin-ajax.php?action=wpef_export_leads_csv&nonce=' . wp_create_nonce('wpef_export_leads_csv')) ); ?>" class="button button-primary" style="margin-right: 10px;"><?php esc_html_e( 'Экспорт в CSV', 'wp-easy-forms' ); ?></a>
										<button type="button" class="button wpef-clear-leads" style="color: #d63638; border-color: #d63638;"><?php esc_html_e( 'Очистить все заявки', 'wp-easy-forms' ); ?></button>
									</div>
								</div>
								<div id="wpef-leads-container"><?php esc_html_e( 'Загрузка...', 'wp-easy-forms' ); ?></div>
							</div>
						</div>
					</div>

					<!-- Вкладка: Журнал ошибок -->
					<div id="tab-logs" class="wpef-tab-content">
						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Журнал ошибок', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
								<div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;">
									<div style="display: flex; gap: 10px;">
										<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wpef_export_logs_json&nonce=' . wp_create_nonce('wpef_export_leads_csv') ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Экспорт в JSON', 'wp-easy-forms' ); ?></a>
										<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wpef_export_logs_csv&nonce=' . wp_create_nonce('wpef_export_leads_csv') ) ); ?>" class="button button-primary"><?php esc_html_e( 'Экспорт в CSV', 'wp-easy-forms' ); ?></a>
										<button type="button" class="button wpef-clear-logs" style="color: #d63638; border-color: #d63638;"><?php esc_html_e( 'Очистить журнал', 'wp-easy-forms' ); ?></button>
									</div>
								</div>
								<div id="wpef-logs-container"><?php esc_html_e( 'Загрузка...', 'wp-easy-forms' ); ?></div>
							</div>
						</div>
					</div>

					<!-- Вкладка: Настройки SMTP -->
					<div id="tab-smtp" class="wpef-tab-content">
						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Настройки SMTP (Отправка почты)', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
								<p class="description" style="margin-bottom: 25px;"><?php esc_html_e( 'Настройте внешний SMTP сервер для надежной доставки писем, игнорируя системную функцию wp_mail.', 'wp-easy-forms' ); ?></p>
								
								<div class="wpef-row" style="margin-bottom: 25px;">
									<div class="wpef-col-full">
										<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; font-size: 14px;">
											<label class="wpef-switch" style="margin-bottom: 0;">
												<input type="checkbox" id="wpef-smtp-enable">
												<span class="wpef-slider"></span>
											</label>
											<?php esc_html_e( 'Включить SMTP', 'wp-easy-forms' ); ?>
										</label>
									</div>
								</div>
								
								<div id="wpef-smtp-settings-wrapper" style="display: none; background: #f8fafc; padding: 25px; border-radius: 6px; border: 1px solid #e2e8f0;">
									<div class="wpef-row">
										<div class="wpef-col">
											<label for="wpef-smtp-host"><?php esc_html_e( 'SMTP сервер', 'wp-easy-forms' ); ?></label>
											<input type="text" id="wpef-smtp-host" placeholder="smtp.yandex.ru">
											<p class="description" style="margin-top: 5px;"><?php esc_html_e( 'Адрес вашего почтового сервера.', 'wp-easy-forms' ); ?></p>
										</div>
										<div class="wpef-col" style="flex: 0 0 150px;">
											<label for="wpef-smtp-port"><?php esc_html_e( 'SMTP порт', 'wp-easy-forms' ); ?></label>
											<input type="number" id="wpef-smtp-port" value="465">
										</div>
									</div>
									
									<div class="wpef-row" style="align-items: center; margin-top: 5px;">
										<div class="wpef-col">
											<label><?php esc_html_e( 'Тип шифрования', 'wp-easy-forms' ); ?></label>
											<div style="display: flex; gap: 20px; margin-top: 8px;">
												<label style="display: flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer;"><input type="radio" name="wpef_smtp_encryption" value="none"> <?php esc_html_e( 'Отсутствует', 'wp-easy-forms' ); ?></label>
												<label style="display: flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer;"><input type="radio" name="wpef_smtp_encryption" value="ssl" checked> SSL</label>
												<label style="display: flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer;"><input type="radio" name="wpef_smtp_encryption" value="tls"> TLS</label>
											</div>
										</div>
										<div class="wpef-col">
											<label style="display: flex; align-items: center; gap: 10px; font-weight: 500; cursor: pointer;">
												<label class="wpef-switch" style="margin-bottom: 0;">
													<input type="checkbox" id="wpef-smtp-auto-tls" checked>
													<span class="wpef-slider"></span>
												</label>
												Auto TLS
											</label>
										</div>
									</div>
									
									<hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
									
									<div class="wpef-row">
										<div class="wpef-col-full">
											<label style="display: flex; align-items: center; gap: 10px; font-weight: 500; cursor: pointer; font-size: 14px;">
												<label class="wpef-switch" style="margin-bottom: 0;">
													<input type="checkbox" id="wpef-smtp-auth" checked>
													<span class="wpef-slider"></span>
												</label>
												<?php esc_html_e( 'Аутентификация SMTP', 'wp-easy-forms' ); ?>
											</label>
										</div>
									</div>
									
									<div id="wpef-smtp-auth-wrapper">
										<div class="wpef-row">
											<div class="wpef-col">
												<label for="wpef-smtp-user"><?php esc_html_e( 'Имя пользователя SMTP', 'wp-easy-forms' ); ?></label>
												<input type="text" id="wpef-smtp-user" placeholder="user@yandex.ru">
											</div>
											<div class="wpef-col">
												<label for="wpef-smtp-pass"><?php esc_html_e( 'Пароль SMTP', 'wp-easy-forms' ); ?></label>
												<input type="password" id="wpef-smtp-pass" placeholder="••••••••••••">
											</div>
										</div>
									</div>
									
									<hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
									
									<div class="wpef-row">
										<div class="wpef-col">
											<label for="wpef-smtp-from-email"><?php esc_html_e( 'Адрес отправителя', 'wp-easy-forms' ); ?></label>
											<input type="email" id="wpef-smtp-from-email" placeholder="user@yandex.ru">
											<p class="description" style="margin-top: 5px;"><?php esc_html_e( 'Адрес электронной почты для исходящих сообщений.', 'wp-easy-forms' ); ?></p>
											
											<label style="display: flex; align-items: center; gap: 10px; margin-top: 15px; font-weight: 500; cursor: pointer;">
												<label class="wpef-switch" style="margin-bottom: 0;">
													<input type="checkbox" id="wpef-smtp-force-from-email">
													<span class="wpef-slider"></span>
												</label>
												<?php esc_html_e( 'Принудительно Использовать Адрес Отправителя', 'wp-easy-forms' ); ?>
											</label>
										</div>
										<div class="wpef-col">
											<label for="wpef-smtp-from-name"><?php esc_html_e( 'Имя отправителя', 'wp-easy-forms' ); ?></label>
											<input type="text" id="wpef-smtp-from-name" placeholder="WP Easy Forms">
											<p class="description" style="margin-top: 5px;"><?php esc_html_e( 'Имя, от которого будут приходить письма.', 'wp-easy-forms' ); ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="wpef-config-block" style="margin-top: 20px;">
							<div class="wpef-config-header" style="cursor: pointer;" id="wpef-smtp-test-toggle-header">
								<h3 class="wpef-config-title" style="display: flex; justify-content: space-between; align-items: center;">
									<?php esc_html_e( 'Отправить тестовое письмо', 'wp-easy-forms' ); ?>
									<span class="dashicons dashicons-arrow-down-alt2" id="wpef-smtp-test-icon"></span>
								</h3>
							</div>
							<div class="wpef-config-body" id="wpef-smtp-test-body" style="display: none;">
								<p class="description" style="margin-bottom: 20px; color: #ef4444;"><?php esc_html_e( 'Внимание: перед отправкой тестового письма убедитесь, что вы сохранили текущие настройки SMTP (кнопка справа вверху).', 'wp-easy-forms' ); ?></p>
								<div class="wpef-row">
									<div class="wpef-col" style="flex: 0 0 250px;">
										<label for="wpef-smtp-test-email" style="font-weight: 500;"><?php esc_html_e( 'Адреса почты', 'wp-easy-forms' ); ?></label>
									</div>
									<div class="wpef-col">
										<input type="email" id="wpef-smtp-test-email" placeholder="test@domain.com">
										<p class="description" style="margin-top: 5px;"><?php esc_html_e( 'Введите адрес электронной почты, на который вы хотите отправить тестовое электронное письмо.', 'wp-easy-forms' ); ?></p>
									</div>
								</div>
								
								<hr style="margin: 20px 0; border: 0; border-top: 1px solid #e2e8f0;">
								
								<div class="wpef-row">
									<div class="wpef-col" style="flex: 0 0 250px;">
										<label style="font-weight: 500;"><?php esc_html_e( 'Собственный текст письма', 'wp-easy-forms' ); ?></label>
									</div>
									<div class="wpef-col">
										<label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
											<label class="wpef-switch" style="margin-bottom: 0;">
												<input type="checkbox" id="wpef-smtp-test-custom">
												<span class="wpef-slider"></span>
											</label>
											<span id="wpef-smtp-test-custom-label" style="font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase;"><?php esc_html_e( 'откл', 'wp-easy-forms' ); ?></span>
										</label>
										<p class="description" style="margin-top: 10px;"><?php esc_html_e( 'Замените стандартный шаблон письма на собственный контент.', 'wp-easy-forms' ); ?></p>
									</div>
								</div>
								
								<div id="wpef-smtp-test-custom-fields" style="display: none;">
									<hr style="margin: 20px 0; border: 0; border-top: 1px solid #e2e8f0;">
									
									<div class="wpef-row">
										<div class="wpef-col" style="flex: 0 0 250px;">
											<label for="wpef-smtp-test-subject" style="font-weight: 500;"><?php esc_html_e( 'Тема', 'wp-easy-forms' ); ?></label>
										</div>
										<div class="wpef-col">
											<input type="text" id="wpef-smtp-test-subject" value="<?php esc_attr_e( 'Тестовое сообщение Настроек форм', 'wp-easy-forms' ); ?>" class="large-text">
											<p class="description" style="margin-top: 5px;"><?php esc_html_e( 'Введите тему сообщения.', 'wp-easy-forms' ); ?></p>
										</div>
									</div>
									
									<hr style="margin: 20px 0; border: 0; border-top: 1px solid #e2e8f0;">
									
									<div class="wpef-row">
										<div class="wpef-col" style="flex: 0 0 250px;">
											<label for="wpef-smtp-test-message" style="font-weight: 500;"><?php esc_html_e( 'Сообщение', 'wp-easy-forms' ); ?></label>
										</div>
										<div class="wpef-col">
											<textarea id="wpef-smtp-test-message" rows="6" class="large-text"><?php esc_html_e( 'Это тестовое письмо от плагина Настроек форм.', 'wp-easy-forms' ); ?></textarea>
											<p class="description" style="margin-top: 5px;"><?php esc_html_e( 'Введите текст сообщения.', 'wp-easy-forms' ); ?></p>
										</div>
									</div>
								</div>
								
								<div style="margin-top: 30px;">
									<button type="button" class="button button-primary" id="wpef-smtp-test-btn"><?php esc_html_e( 'Отправить тестовый e-mail', 'wp-easy-forms' ); ?></button>
									<span class="spinner" id="wpef-smtp-test-spinner" style="float: none; margin: 0 10px;"></span>
								</div>
								
								<div id="wpef-smtp-test-result" style="margin-top: 15px; display: none; padding: 10px; border-left: 4px solid; background: #fff;"></div>
							</div>
						</div>
					</div>

					<!-- Вкладка: Настройки -->
					<div id="tab-settings" class="wpef-tab-content">
						
						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Аналитика и Данные', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-enable-send-empty-fields">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Отправлять строки с пустыми значениями полей', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Если опция включена, незаполненные поля формы всё равно будут отправляться на почту и вебхуки со значением "НЕ ЗАПОЛНЕНО". Если выключена — пустые поля будут скрыты.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-use-page-title">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Использовать имя страницы в заголовке письма', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Вместо имени формы в заголовках (и в Telegram/Bitrix24) будет передаваться название страницы (title), с которой отправлена заявка.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-enable-utm">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Включить UTM-трекинг из коробки', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Плагин будет автоматически собирать UTM-метки (source, medium, campaign, term, content) из URL и прикреплять их к заявкам. Метки сохраняются в рамках сессии пользователя.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-enable-device-info">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Сбор данных об устройстве (Client Fingerprint)', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Добавлять к заявкам информацию о браузере (User Agent), разрешении экрана и типе устройства (Mobile/Desktop).', 'wp-easy-forms' ); ?></p>
								</div>
							</div>
							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-enable-basic-tech-info" checked>
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Включить базовую тех. информацию (Страница, Форма, IP)', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Добавлять базовую информацию о заявке в специальный технический блок внизу письма.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>
							
							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<h4 style="margin-top: 10px; margin-bottom: 10px; margin-left: 54px; font-size: 14px;"><?php esc_html_e( 'Автоматическая очистка БД (GDPR)', 'wp-easy-forms' ); ?></h4>
									<div style="margin-left: 54px; display: flex; gap: 20px; align-items: center;">
										<div>
											<label style="font-weight: 500; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Хранить заявки (дней):', 'wp-easy-forms' ); ?></label>
											<input type="number" id="wpef-cleanup-leads-days" value="0" min="0" style="width: 150px;">
											<p class="description" style="margin-top: 5px;"><?php esc_html_e( '0 - хранить вечно', 'wp-easy-forms' ); ?></p>
										</div>
										<div>
											<label style="font-weight: 500; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Хранить логи (дней):', 'wp-easy-forms' ); ?></label>
											<input type="number" id="wpef-cleanup-logs-days" value="30" min="0" style="width: 150px;">
											<p class="description" style="margin-top: 5px;"><?php esc_html_e( '0 - хранить вечно', 'wp-easy-forms' ); ?></p>
										</div>
									</div>
								</div>
							</div>
							</div>
						</div>

						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Работа с файлами', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
							
							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-save-to-media">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Сохранять файлы в медиабиблиотеку WP', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'По умолчанию загруженные файлы прикрепляются к письму и сразу удаляются с сервера. При включении этой опции файлы будут загружаться в Медиабиблиотеку WordPress, а в письмо будет отправляться ссылка на них.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="font-weight: 600; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Максимальный вес вложений для email (в Мегабайтах)', 'wp-easy-forms' ); ?></label>
									<input type="number" id="wpef-max-attachment-size" value="10" min="1" step="1" style="width: 100px;">
									<p class="description" style="margin-top: 5px; margin-bottom: 0;"><?php esc_html_e( 'Почтовые серверы часто блокируют тяжелые письма. Если общий вес прикрепленных файлов превысит этот лимит, плагин автоматически загрузит файлы в Медиабиблиотеку и отправит в письме ссылки на их скачивание.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>
							</div>
						</div>

						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Безопасность и антиспам', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
							
							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-enable-honeypot">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Включить Honeypot (Приманка для ботов)', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Добавляет скрытое поле в форму. Если бот его заполнит, заявка будет отклонена.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-enable-rate-limit">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Включить лимит запросов (Rate Limiting)', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 10px; margin-left: 54px;"><?php esc_html_e( 'Ограничивает количество отправок с одного IP-адреса.', 'wp-easy-forms' ); ?></p>
									
									<div id="wpef-rate-limit-settings" style="margin-left: 54px; display: none; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
										<div style="display: flex; gap: 20px; align-items: center;">
											<div>
												<label style="font-weight: 500; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Максимум отправок:', 'wp-easy-forms' ); ?></label>
												<input type="number" id="wpef-rate-limit-count" value="5" min="1" style="width: 100px;">
											</div>
											<div>
												<label style="font-weight: 500; display: block; margin-bottom: 5px;"><?php esc_html_e( 'За время (минут):', 'wp-easy-forms' ); ?></label>
												<input type="number" id="wpef-rate-limit-time" value="10" min="1" style="width: 100px;">
											</div>
										</div>
									</div>
								</div>
							</div>
							</div>
						</div>

						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Общие настройки плагина', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-enable-cache-compat">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Режим совместимости с кэшированием (AJAX Nonce)', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Включите эту опцию, если вы используете плагины жесткого кэширования (WP Rocket, LiteSpeed Cache и др.). Плагин будет получать свежий токен безопасности (Nonce) через AJAX перед каждой отправкой формы, чтобы предотвратить ошибку "Недействительный ключ безопасности".', 'wp-easy-forms' ); ?></p>
								</div>
							</div>
							
							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-disable-auto-required">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Отключить автоматическую валидацию HTML5 (required)', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Если включено, плагин не будет автоматически добавлять правило "Обязательное поле" для полей с атрибутом', 'wp-easy-forms' ); ?> <code>required</code>.</p>
								</div>
							</div>

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-load-just-validate" checked>
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Подключить библиотеку JustValidate', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Загружать локальный файл JustValidate.js из плагина. Отключите, если библиотека уже подключена в вашей теме.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>

							<div class="wpef-row" style="margin-bottom: 20px;">
								<div class="wpef-col-full">
									<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
										<label class="wpef-switch" style="margin-bottom: 0;">
											<input type="checkbox" id="wpef-load-imask">
											<span class="wpef-slider"></span>
										</label>
										<?php esc_html_e( 'Подключить библиотеку IMask (Маски ввода)', 'wp-easy-forms' ); ?>
									</label>
									<p class="description" style="margin-top: 5px; margin-bottom: 0; margin-left: 54px;"><?php esc_html_e( 'Загружать локальный файл imask.min.js из плагина.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>

							<div class="wpef-row" id="wpef-imask-js-row" style="display: none;">
								<div class="wpef-col-full">
									<label style="font-weight: 600; margin-bottom: 10px;"><?php esc_html_e( 'Код инициализации масок (IMask)', 'wp-easy-forms' ); ?></label>
									<div class="wpef-code-editor-wrapper">
										<textarea id="wpef-imask-js" class="large-text"></textarea>
									</div>
									<p class="description"><?php esc_html_e( 'Скрипт выполнится после загрузки страницы.', 'wp-easy-forms' ); ?></p>
								</div>
							</div>
							</div>
						</div>

						<!-- Экспорт / Импорт -->

						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Экспорт и Импорт конфигураций', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
							
							<div class="wpef-row">
								<div class="wpef-col">
									<label style="font-weight: 600;"><?php esc_html_e( 'Экспорт настроек', 'wp-easy-forms' ); ?></label>
									<textarea id="wpef-export-data" rows="6" readonly class="large-text" style="background: #f0f0f1; cursor: text;" placeholder="<?php esc_attr_e( 'Нажмите кнопку ниже для генерации кода', 'wp-easy-forms' ); ?>"></textarea>
									<button type="button" class="button" id="wpef-btn-export" style="margin-top: 10px;"><?php esc_html_e( 'Сгенерировать код', 'wp-easy-forms' ); ?></button>
								</div>
								<div class="wpef-col">
									<label style="font-weight: 600;"><?php esc_html_e( 'Импорт настроек', 'wp-easy-forms' ); ?></label>
									<textarea id="wpef-import-data" rows="6" class="large-text" placeholder="<?php esc_attr_e( 'Вставьте код конфигурации сюда...', 'wp-easy-forms' ); ?>"></textarea>
									<button type="button" class="button button-primary" id="wpef-btn-import" style="margin-top: 10px;"><?php esc_html_e( 'Импортировать (Внимание: старые настройки будут удалены!)', 'wp-easy-forms' ); ?></button>
								</div>
							</div>
							</div>
						</div>

					</div>
					<!-- Вкладка: Справочник Хуков -->
					<div id="tab-hooks" class="wpef-tab-content" style="display: none;">
						<div class="wpef-config-block">
							<div class="wpef-config-header">
								<h3 class="wpef-config-title"><?php esc_html_e( 'Справочник PHP хуков и фильтров (для разработчиков)', 'wp-easy-forms' ); ?></h3>
							</div>
							<div class="wpef-config-body">
							<p class="description"><?php esc_html_e( 'Плагин предоставляет набор хуков, с помощью которых вы можете изменять поведение форм, логику отправки почты и вебхуков прямо из вашего файла', 'wp-easy-forms' ); ?> <code>functions.php</code>.</p>
							
							<table class="wp-list-table widefat fixed striped">
								<thead>
									<tr>
										<th style="width: 250px;"><?php esc_html_e( 'Имя хука', 'wp-easy-forms' ); ?></th>
										<th style="width: 100px;"><?php esc_html_e( 'Тип', 'wp-easy-forms' ); ?></th>
										<th><?php esc_html_e( 'Описание и пример использования', 'wp-easy-forms' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><code>wpef_email_placeholders</code></td>
										<td>Filter</td>
										<td>
											<?php esc_html_e( 'Позволяет добавить или изменить переменные (шорткоды) для тела письма.', 'wp-easy-forms' ); ?><br>
											<code>add_filter('wpef_email_placeholders', function($placeholders, $post_data, $config) {<br>
											&nbsp;&nbsp;$placeholders['{{__custom_id}}'] = 'ID-123';<br>
											&nbsp;&nbsp;return $placeholders;<br>
											}, 10, 3);</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_email_to</code></td>
										<td>Filter</td>
										<td>
											<?php esc_html_e( 'Позволяет динамически изменить адрес получателя (дополнительно к Smart Routing).', 'wp-easy-forms' ); ?><br>
											<code>add_filter('wpef_email_to', function($email, $config, $post_data) {<br>
											&nbsp;&nbsp;return 'admin@site.com';<br>
											}, 10, 3);</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_email_subject</code></td>
										<td>Filter</td>
										<td>
											<?php esc_html_e( 'Позволяет переопределить тему письма перед отправкой.', 'wp-easy-forms' ); ?><br>
											<code>add_filter('wpef_email_subject', function($subject, $config, $post_data) {<br>
											&nbsp;&nbsp;return '<?php esc_html_e( 'Срочно: ', 'wp-easy-forms' ); ?>' . $subject;<br>
											}, 10, 3);</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_email_headers</code></td>
										<td>Filter</td>
										<td>
											<?php esc_html_e( 'Позволяет добавить кастомные заголовки письма (например, CC, BCC).', 'wp-easy-forms' ); ?><br>
											<code>add_filter('wpef_email_headers', function($headers, $config, $post_data) {<br>
											&nbsp;&nbsp;$headers[] = 'Bcc: copy@site.com';<br>
											&nbsp;&nbsp;return $headers;<br>
											}, 10, 3);</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_email_body</code></td>
										<td>Filter</td>
										<td>
											<?php esc_html_e( 'Позволяет полностью переопределить финальный HTML-код тела письма.', 'wp-easy-forms' ); ?><br>
											<?php esc_html_e( 'Передаются:', 'wp-easy-forms' ); ?> <code>$body, $config, $post_data, $placeholders</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_after_email_send</code></td>
										<td>Action</td>
										<td>
											Срабатывает сразу после попытки отправки письма <code>wp_mail</code><?php esc_html_e( ', но до вебхуков.', 'wp-easy-forms' ); ?><br>
											<?php esc_html_e( 'Передаются:', 'wp-easy-forms' ); ?> <code>$is_sent (bool), $config, $post_data, $attachments</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_webhook_payload_custom</code><br><code>wpef_webhook_payload_telegram</code><br><code>wpef_webhook_payload_bitrix24</code></td>
										<td>Filter</td>
										<td>
											<?php esc_html_e( 'Позволяет изменить массив данных перед отправкой на соответствующий вебхук.', 'wp-easy-forms' ); ?><br>
											<code>add_filter('wpef_webhook_payload_custom', function($payload, $config, $post_data) {<br>
											&nbsp;&nbsp;$payload['api_key'] = 'secret';<br>
											&nbsp;&nbsp;return $payload;<br>
											}, 10, 3);</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_before_lead_save</code></td>
										<td>Filter</td>
										<td>
											<?php esc_html_e( 'Позволяет модифицировать или очистить данные перед сохранением в локальную таблицу', 'wp-easy-forms' ); ?> <code>wpef_leads</code>.<br>
											<?php esc_html_e( 'Передаются:', 'wp-easy-forms' ); ?> <code>$db_form_data, $config, $post_data</code>
										</td>
									</tr>
									<tr>
										<td><code>wpef_after_lead_saved</code></td>
										<td>Action</td>
										<td>
											<?php esc_html_e( 'Срабатывает в самом конце цепочки, после того как заявка сохранена в базу данных.', 'wp-easy-forms' ); ?><br>
											<code>add_action('wpef_after_lead_saved', function($lead_id, $db_data, $config, $post_data) {<br>
											&nbsp;&nbsp;// Ваш кастомный код, например отправка SMS<br>
											}, 10, 4);</code>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					</div>

				</div>

				<!-- Боковая панель (Сайдбар) -->
				<div class="wpef-sidebar">
					<div class="wpef-sidebar-box">
						<h3><?php esc_html_e( 'Действия', 'wp-easy-forms' ); ?></h3>
						<div class="wpef-sidebar-actions">
							<!-- Кнопки для вкладки форм -->
							<button type="button" class="button button-secondary" id="wpef-add-config" style="display: block;"><?php esc_html_e( 'Добавить настройку (селектор)', 'wp-easy-forms' ); ?></button>
							<!-- Кнопки для вкладки валидации -->
							<button type="button" class="button button-secondary" id="wpef-add-global-val-field" style="display: none;"><?php esc_html_e( 'Добавить поле', 'wp-easy-forms' ); ?></button>
							
							<button type="button" class="button button-primary wpef-save-btn" id="wpef-save-configs"><?php esc_html_e( 'Сохранить изменения', 'wp-easy-forms' ); ?></button>
							
							<div style="display: flex; align-items: center; justify-content: center; margin-top: 10px;">
								<span class="spinner" id="wpef-spinner" style="float: none; margin: 0 10px 0 0;"></span>
								<span class="wpef-save-msg" id="wpef-save-msg"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<template id="wpef-config-template">
			<div class="wpef-config-block" data-id="">
				<div class="wpef-config-header">
					<div class="wpef-config-header-left" style="display: flex; align-items: center; gap: 15px;">
						<h3 class="wpef-config-title"><?php esc_html_e( 'Новая настройка', 'wp-easy-forms' ); ?></h3>
						<div class="wpef-config-stats" style="display: none; align-items: center; gap: 10px; font-size: 13px; color: #646970; background: #f0f0f1; padding: 4px 10px; border-radius: 4px;"></div>
					</div>
					<button type="button" class="button wpef-btn-delete wpef-remove-config"><?php esc_html_e( 'Удалить', 'wp-easy-forms' ); ?></button>
				</div>
				<div class="wpef-config-body">
					
					<div class="wpef-row">
						<div class="wpef-col">
							<label><?php esc_html_e( 'Включить конфигурацию', 'wp-easy-forms' ); ?></label>
							<label class="wpef-switch">
								<input type="checkbox" name="enabled">
								<span class="wpef-slider"></span>
							</label>
						</div>
						<div class="wpef-col">
							<label><?php esc_html_e( 'Только валидация (нативная отправка)', 'wp-easy-forms' ); ?></label>
							<label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="validation_only">
									<span class="wpef-slider"></span>
								</label>
								<span style="font-size: 13px; color: #64748b;"><?php esc_html_e( 'Отключить AJAX-отправку', 'wp-easy-forms' ); ?></span>
							</label>
						</div>
					</div>

					<div class="wpef-row">
						<div class="wpef-col">
							<label><?php esc_html_e( 'Название настройки *', 'wp-easy-forms' ); ?></label>
							<input type="text" name="name" class="regular-text" required>
						</div>
						<div class="wpef-col">
							<label><?php esc_html_e( 'Селектор отбора *', 'wp-easy-forms' ); ?></label>
							<div class="wpef-input-group">
								<input type="text" name="selector" class="regular-text" required>
								<span class="wpef-input-suffix"><?php esc_html_e( 'селектор', 'wp-easy-forms' ); ?></span>
							</div>
						</div>
					</div>

					<div class="wpef-row">
						<div class="wpef-col-full">
							<label><?php esc_html_e( 'Email получателя *', 'wp-easy-forms' ); ?></label>
							<div class="wpef-input-group">
								<input type="text" name="email_to" class="large-text" placeholder="admin@site.com" required>
								<span class="wpef-input-suffix"><?php esc_html_e( 'разделять запятой', 'wp-easy-forms' ); ?></span>
							</div>
						</div>
					</div>

					<!-- Умная маршрутизация -->
					<div class="wpef-row">
						<div class="wpef-col-full">
							<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin-bottom: 10px;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="route_enable" class="wpef-route-toggle">
									<span class="wpef-slider"></span>
								</label>
								<?php esc_html_e( 'Включить умную маршрутизацию (Smart Routing)', 'wp-easy-forms' ); ?>
							</label>
							<div class="wpef-route-fields" style="display: none; background: #f0f6fc; padding: 15px; border-left: 4px solid #8b5cf6; margin-bottom: 10px;">
								<p class="description" style="margin-top: 0; margin-bottom: 15px;"><?php esc_html_e( 'Настройте правила отправки писем разным получателям в зависимости от значений полей формы.', 'wp-easy-forms' ); ?> <br><em><?php esc_html_e( 'Если ни одно из правил не сработает, письмо будет отправлено на базовый Email получателя, указанный выше.', 'wp-easy-forms' ); ?></em></p>
								<div class="wpef-routes-container">
									<!-- Правила маршрутизации будут добавляться сюда -->
								</div>
								<button type="button" class="button wpef-add-route-btn" style="margin-top: 5px;"><?php esc_html_e( '+ Добавить правило', 'wp-easy-forms' ); ?></button>
							</div>
						</div>
					</div>

					<div class="wpef-row">
						<div class="wpef-col-full">
							<label><?php esc_html_e( 'Тема письма *', 'wp-easy-forms' ); ?></label>
							<input type="text" name="email_subject" class="large-text" required>
						</div>
					</div>

					<div class="wpef-row">
						<div class="wpef-col-full">
							<label><?php esc_html_e( 'Сообщение *', 'wp-easy-forms' ); ?></label>
							<p class="description" style="margin-top: 0; margin-bottom: 10px;">
								<?php esc_html_e( 'Доступные системные переменные:', 'wp-easy-forms' ); ?><br>
								<code>{{__fields}}</code> &mdash; <?php esc_html_e( 'Вывести все заполненные поля в виде красивой таблицы', 'wp-easy-forms' ); ?><br>
								<code>{{__page}}</code> &mdash; <?php esc_html_e( 'URL страницы, с которой отправлена форма', 'wp-easy-forms' ); ?><br>
								<code>{{__form}}</code> &mdash; <?php esc_html_e( 'Название формы (жирным шрифтом)', 'wp-easy-forms' ); ?><br>
								<code>{{__form_name}}</code> &mdash; <?php esc_html_e( 'Название формы (обычный текст)', 'wp-easy-forms' ); ?><br>
								<code>{{__ip}}</code> &mdash; <?php esc_html_e( 'IP-адрес отправителя', 'wp-easy-forms' ); ?><br>
								<code>{{__site}}</code> &mdash; <?php esc_html_e( 'Название вашего сайта', 'wp-easy-forms' ); ?><br>
								<br>
								<?php esc_html_e( 'Вы также можете выводить значения конкретных полей, используя их атрибут name в двойных фигурных скобках.', 'wp-easy-forms' ); ?><br>
								<?php esc_html_e( 'Например:', 'wp-easy-forms' ); ?> <code>{{Имя}}</code>, <code>{{E-mail}}</code>, <code>{{name="my_field"}}</code> &rarr; <code>{{my_field}}</code>.
							</p>
							<textarea name="email_body" rows="8" class="large-text" required>Данные формы:
{{__fields}}

Страница: <b>{{__page}}</b><br>
Форма: <b>{{__form}}</b><br>
IP: <b>{{__ip}}</b></textarea>
						</div>
					</div>

					<!-- Секция: Ответ отправителю -->
					<h4 class="wpef-section-title"><?php esc_html_e( 'Ответ отправителю', 'wp-easy-forms' ); ?></h4>
					<div class="wpef-row">
						<div class="wpef-col">
							<label><?php esc_html_e( 'Включить ответ отправителю', 'wp-easy-forms' ); ?></label>
							<label class="wpef-switch">
								<input type="checkbox" name="reply_enabled">
								<span class="wpef-slider"></span>
							</label>
						</div>
						<div class="wpef-col">
							<label><?php esc_html_e( 'Селектор поля', 'wp-easy-forms' ); ?> <span class="description"><?php esc_html_e( 'поле, в котором указан email пользователя (например, [name="email"] или #user-email)', 'wp-easy-forms' ); ?></span></label>
							<input type="text" name="reply_email_field" class="regular-text" placeholder='[name="user_email"]'>
						</div>
					</div>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<label><?php esc_html_e( 'Тема письма', 'wp-easy-forms' ); ?></label>
							<input type="text" name="reply_subject" class="large-text" placeholder="<?php esc_attr_e( 'Спасибо за вашу заявку!', 'wp-easy-forms' ); ?>">
						</div>
					</div>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<label><?php esc_html_e( 'Текст письма', 'wp-easy-forms' ); ?></label>
							<textarea name="reply_body" rows="4" class="large-text"></textarea>
						</div>
					</div>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<label><?php esc_html_e( 'Файлы для отправки', 'wp-easy-forms' ); ?> <span class="description"><?php esc_html_e( 'файлы, которые будут прикреплены к письму', 'wp-easy-forms' ); ?></span></label>
							<div class="wpef-reply-files-container">
								<!-- Файлы будут добавляться сюда -->
							</div>
							<button type="button" class="button wpef-add-reply-file-btn" style="margin-top: 5px;"><?php esc_html_e( '+ Добавить файл', 'wp-easy-forms' ); ?></button>
						</div>
					</div>

					<!-- Секция: Событие успешной отправки -->
					<h4 class="wpef-section-title"><?php esc_html_e( 'Событие успешной отправки (Custom JS)', 'wp-easy-forms' ); ?></h4>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<div class="wpef-code-editor-wrapper">
								<textarea name="custom_js" class="large-text"></textarea>
							</div>
							<p class="description"><?php esc_html_e( 'Скрипт выполнится после успешной отправки формы. Доступные переменные:', 'wp-easy-forms' ); ?> <code>form</code> <?php esc_html_e( '(элемент формы),', 'wp-easy-forms' ); ?> <code>response</code> <?php esc_html_e( '(ответ сервера).', 'wp-easy-forms' ); ?></p>
						</div>
					</div>

					<!-- Секция: Условная логика -->
					<h4 class="wpef-section-title"><?php esc_html_e( 'Условная логика (Отображение полей)', 'wp-easy-forms' ); ?></h4>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<p class="description" style="margin-top: 0; margin-bottom: 15px;"><?php esc_html_e( 'Настройте правила, при которых определенные блоки будут показываться или скрываться в зависимости от значений других полей.', 'wp-easy-forms' ); ?></p>
							<div class="wpef-conditions-container">
								<!-- Правила будут добавляться сюда -->
							</div>
							<button type="button" class="button wpef-add-condition-btn" style="margin-top: 5px;"><?php esc_html_e( '+ Добавить правило', 'wp-easy-forms' ); ?></button>
						</div>
					</div>

					<!-- Секция: Поведение после отправки -->
					<h4 class="wpef-section-title"><?php esc_html_e( 'Поведение после отправки', 'wp-easy-forms' ); ?></h4>
					<div class="wpef-row">
						<div class="wpef-col">
							<label><?php esc_html_e( 'Перенаправление на страницу', 'wp-easy-forms' ); ?></label>
							<input type="text" name="redirect_url" class="regular-text" placeholder="https://...">
						</div>
						<div class="wpef-col">
							<label><?php esc_html_e( 'Перенаправлять в новой вкладке', 'wp-easy-forms' ); ?></label>
							<label class="wpef-switch">
								<input type="checkbox" name="redirect_blank">
								<span class="wpef-slider"></span>
							</label>
						</div>
					</div>

					<div class="wpef-row">
						<div class="wpef-col-full">
							<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin-bottom: 10px;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="hide_block_enable" class="wpef-hide-block-toggle">
									<span class="wpef-slider"></span>
								</label>
								<?php esc_html_e( 'Скрыть блок после отправки', 'wp-easy-forms' ); ?>
							</label>
							<div class="wpef-hide-block-fields" style="display: none; background: #f0f6fc; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px;">
								<div style="margin-bottom: 10px;">
									<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Селектор блока для скрытия *', 'wp-easy-forms' ); ?></label>
									<input type="text" name="hide_block_selector" class="regular-text" placeholder="<?php esc_attr_e( '.form-wrapper или #my-form-container', 'wp-easy-forms' ); ?>" style="width: 100%;">
									<p class="description"><?php esc_html_e( 'Укажите CSS селектор блока, который нужно скрыть. Оставьте пустым, чтобы скрыть саму форму.', 'wp-easy-forms' ); ?></p>
								</div>
								<div style="display: flex; gap: 20px;">
									<div style="flex: 1;">
										<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Эффект скрытия', 'wp-easy-forms' ); ?></label>
										<select name="hide_block_effect" class="regular-text" style="width: 100%;">
											<option value="fadeOut"><?php esc_html_e( 'Fade Out (Затухание)', 'wp-easy-forms' ); ?></option>
											<option value="slideUp"><?php esc_html_e( 'Slide Up (Сворачивание)', 'wp-easy-forms' ); ?></option>
										</select>
									</div>
									<div style="flex: 1;">
										<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Длительность эффекта (мс)', 'wp-easy-forms' ); ?></label>
										<input type="number" name="hide_block_duration" class="regular-text" placeholder="500" value="500" style="width: 100%;">
									</div>
									<div style="flex: 1;">
										<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Задержка перед скрытием (мс)', 'wp-easy-forms' ); ?></label>
										<input type="number" name="hide_block_delay" class="regular-text" placeholder="0" value="0" style="width: 100%;">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="wpef-row">
						<div class="wpef-col-full">
							<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin-bottom: 10px;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="show_block_enable" class="wpef-show-block-toggle">
									<span class="wpef-slider"></span>
								</label>
								<?php esc_html_e( 'Отобразить блок после отправки', 'wp-easy-forms' ); ?>
							</label>
							<div class="wpef-show-block-fields" style="display: none; background: #f0f6fc; padding: 15px; border-left: 4px solid #10b981; margin-bottom: 20px;">
								<div style="margin-bottom: 10px;">
									<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Селектор блока для отображения *', 'wp-easy-forms' ); ?></label>
									<input type="text" name="show_block_selector" class="regular-text" placeholder="<?php esc_attr_e( '.success-message или #my-thanks-container', 'wp-easy-forms' ); ?>" style="width: 100%;">
									<p class="description"><?php esc_html_e( 'Укажите CSS селектор блока, который нужно отобразить. Блок изначально должен быть скрыт (например, через', 'wp-easy-forms' ); ?> <code>display: none</code>).</p>
								</div>
								<div style="display: flex; gap: 20px;">
									<div style="flex: 1;">
										<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Эффект появления', 'wp-easy-forms' ); ?></label>
										<select name="show_block_effect" class="regular-text" style="width: 100%;">
											<option value="fadeIn"><?php esc_html_e( 'Fade In (Плавное появление)', 'wp-easy-forms' ); ?></option>
											<option value="slideDown"><?php esc_html_e( 'Slide Down (Разворачивание)', 'wp-easy-forms' ); ?></option>
										</select>
									</div>
									<div style="flex: 1;">
										<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Длительность эффекта (мс)', 'wp-easy-forms' ); ?></label>
										<input type="number" name="show_block_duration" class="regular-text" placeholder="500" value="500" style="width: 100%;">
									</div>
									<div style="flex: 1;">
										<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Задержка перед появлением (мс)', 'wp-easy-forms' ); ?></label>
										<input type="number" name="show_block_delay" class="regular-text" placeholder="0" value="0" style="width: 100%;">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Секция: Визуализация (Лоадер) -->
					<h4 class="wpef-section-title"><?php esc_html_e( 'Визуализация отправки (Лоадер)', 'wp-easy-forms' ); ?></h4>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin-bottom: 15px;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="loader_enable" checked>
									<span class="wpef-slider"></span>
								</label>
								<?php esc_html_e( 'Показывать лоадер поверх формы при отправке', 'wp-easy-forms' ); ?>
							</label>
							
							<div class="wpef-loader-settings" style="display: flex; gap: 20px; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
								<div style="flex: 1;">
									<label style="display: block; margin-bottom: 5px; font-weight: 500;"><?php esc_html_e( 'Цвет спиннера (Кольца)', 'wp-easy-forms' ); ?></label>
									<input type="text" name="loader_color" class="regular-text" placeholder="#4f46e5" value="#4f46e5" style="width: 100%;">
								</div>
								<div style="flex: 1;">
									<label style="display: block; margin-bottom: 5px; font-weight: 500;"><?php esc_html_e( 'Фон оверлея (Затемнение)', 'wp-easy-forms' ); ?></label>
									<input type="text" name="loader_bg" class="regular-text" placeholder="rgba(255, 255, 255, 0.7)" value="rgba(255, 255, 255, 0.7)" style="width: 100%;">
								</div>
							</div>
						</div>
					</div>

					<!-- Секция: Интеграция (Webhooks) -->
					<h4 class="wpef-section-title"><?php esc_html_e( 'Интеграция (Webhooks)', 'wp-easy-forms' ); ?></h4>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<p class="description" style="margin-bottom: 15px;"><?php esc_html_e( 'Вы можете включить сразу несколько интеграций для одной формы.', 'wp-easy-forms' ); ?></p>
							
							<!-- Telegram -->
							<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin-bottom: 10px;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="webhook_tg_enable" class="wpef-webhook-toggle">
									<span class="wpef-slider"></span>
								</label>
								Telegram Bot
							</label>
							<div class="wpef-webhook-fields wpef-webhook-telegram" style="display: none; background: #f0f6fc; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px;">
								<div style="margin-bottom: 10px;">
									<label style="display: block; font-weight: 500; margin-bottom: 5px;">Bot Token *</label>
									<input type="text" name="webhook_tg_token" class="regular-text" placeholder="123456789:ABCdefGHIjklmNOPqrsTUVwxyz">
								</div>
								<div>
									<label style="display: block; font-weight: 500; margin-bottom: 5px;">Chat ID *</label>
									<input type="text" name="webhook_tg_chat" class="regular-text" placeholder="-1001234567890">
								</div>
								<p class="description" style="margin-top: 10px; margin-bottom: 0;"><?php esc_html_e( 'Плагин сам сформирует красивое сообщение и отправит его в Telegram.', 'wp-easy-forms' ); ?></p>
							</div>

							<!-- Bitrix24 -->
							<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin-bottom: 10px;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="webhook_bx_enable" class="wpef-webhook-toggle">
									<span class="wpef-slider"></span>
								</label>
								<?php esc_html_e( 'Bitrix24 (Входящий вебхук CRM)', 'wp-easy-forms' ); ?>
							</label>
							<div class="wpef-webhook-fields wpef-webhook-bitrix24" style="display: none; background: #f0f6fc; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px;">
								<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'URL входящего вебхука (crm.lead.add) *', 'wp-easy-forms' ); ?></label>
								<input type="url" name="webhook_bx_url" class="large-text" placeholder="https://your-domain.bitrix24.ru/rest/1/xxxxxxxxxxxxxx/crm.lead.add.json">
								
								<div style="margin-top: 15px;">
									<h4 style="margin-top:0; margin-bottom: 10px;"><?php esc_html_e( 'Маппинг полей для Bitrix24 (опционально)', 'wp-easy-forms' ); ?></h4>
									<p class="description"><?php esc_html_e( 'Укажите, в какие системные поля (NAME, PHONE, EMAIL, UF_CRM_123) записывать данные. Остальные поля попадут в Комментарий.', 'wp-easy-forms' ); ?></p>
									<div class="wpef-bx-mapping-container" style="margin-top: 10px;">
										<!-- Сюда JS добавит строки маппинга -->
									</div>
									<button type="button" class="button button-small wpef-add-bx-mapping-btn" style="margin-top: 10px;"><?php esc_html_e( '+ Добавить поле', 'wp-easy-forms' ); ?></button>
								</div>
							</div>

							<!-- Custom Webhook -->
							<label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin-bottom: 10px;">
								<label class="wpef-switch" style="margin-bottom: 0;">
									<input type="checkbox" name="webhook_custom_enable" class="wpef-webhook-toggle">
									<span class="wpef-slider"></span>
								</label>
								<?php esc_html_e( 'Custom Webhook (Отправка JSON)', 'wp-easy-forms' ); ?>
							</label>
							<div class="wpef-webhook-fields wpef-webhook-custom" style="display: none; background: #f0f6fc; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px;">
								<label style="display: block; font-weight: 500; margin-bottom: 5px;">Webhook URL *</label>
								<input type="url" name="webhook_url" class="large-text" placeholder="https://your-server.com/api/webhook">
								<p class="description"><?php esc_html_e( 'Данные будут отправлены методом POST в формате JSON.', 'wp-easy-forms' ); ?></p>
								
								<div class="wpef-webhook-custom-mapping" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #c3c4c7;">
									<h5 style="margin-top: 0; margin-bottom: 10px; font-size: 13px;"><?php esc_html_e( 'Кастомный маппинг полей (Опционально)', 'wp-easy-forms' ); ?></h5>
									<p class="description" style="margin-bottom: 10px;"><?php esc_html_e( 'Если вашей CRM нужны строгие ключи, укажите их тут (Например: "Телефон" -> "customer_phone").', 'wp-easy-forms' ); ?></p>
									<div class="wpef-webhook-mapping-container" style="display: flex; flex-direction: column; gap: 5px;">
										<!-- Маппинги -->
									</div>
									<button type="button" class="button button-small wpef-add-mapping-btn" style="margin-top: 10px;"><?php esc_html_e( '+ Добавить маппинг', 'wp-easy-forms' ); ?></button>
								</div>
							</div>

						</div>
					</div>

					<!-- Секция: Интеграция систем аналитики -->
					<h4 class="wpef-section-title"><?php esc_html_e( 'Интеграция систем аналитики', 'wp-easy-forms' ); ?></h4>
					<div class="wpef-row">
						<div class="wpef-col-full">
							<p class="description" style="margin-bottom: 15px;"><?php esc_html_e( 'Укажите идентификаторы событий. Они будут автоматически отправляться при успешной отправке формы.', 'wp-easy-forms' ); ?></p>

							<table class="form-table" style="margin-top: 0;">
								<tr>
									<th scope="row" style="padding: 10px 10px 10px 0;"><?php esc_html_e( 'Яндекс.Метрика (ID счетчика)', 'wp-easy-forms' ); ?></th>
									<td style="padding: 10px 10px 10px 0;">
										<input type="text" name="analytics_ym_id" class="regular-text" placeholder="12345678">
									</td>
								</tr>
								<tr>
									<th scope="row" style="padding: 10px 10px 10px 0;"><?php esc_html_e( 'Яндекс.Метрика (ID цели)', 'wp-easy-forms' ); ?></th>
									<td style="padding: 10px 10px 10px 0;">
										<input type="text" name="analytics_ym_goal" class="regular-text" placeholder="form_submit">
									</td>
								</tr>
								<tr>
									<th scope="row" style="padding: 10px 10px 10px 0;"><?php esc_html_e( 'Google Analytics (Событие)', 'wp-easy-forms' ); ?></th>
									<td style="padding: 10px 10px 10px 0;">
										<input type="text" name="analytics_ga_event" class="regular-text" placeholder="generate_lead">
									</td>
								</tr>
								<tr>
									<th scope="row" style="padding: 10px 10px 10px 0;"><?php esc_html_e( 'VK Pixel (Событие)', 'wp-easy-forms' ); ?></th>
									<td style="padding: 10px 10px 10px 0;">
										<input type="text" name="analytics_vk_event" class="regular-text" placeholder="lead">
									</td>
								</tr>
							</table>
						</div>
					</div>

				</div>
			</div>
		</template>

		<template id="wpef-condition-template">
			<div class="wpef-condition-item" style="background: #f8f9fa; border: 1px solid #c3c4c7; padding: 15px; margin-bottom: 10px; position: relative;">
				<button type="button" class="button-link wpef-remove-condition-btn" style="position: absolute; top: 10px; right: 10px; color: #d63638; text-decoration: none;"><?php esc_html_e( '&times; Удалить', 'wp-easy-forms' ); ?></button>
				<div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 10px;">
					<div style="flex: 1; min-width: 150px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Имя поля (name)', 'wp-easy-forms' ); ?></label>
						<input type="text" name="cond_trigger_name" class="regular-text" placeholder="delivery" style="width: 100%;">
					</div>
					<div style="flex: 1; min-width: 120px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Условие', 'wp-easy-forms' ); ?></label>
						<select name="cond_operator" style="width: 100%;">
							<option value="equals"><?php esc_html_e( 'Равно', 'wp-easy-forms' ); ?></option>
							<option value="not_equals"><?php esc_html_e( 'Не равно', 'wp-easy-forms' ); ?></option>
							<option value="contains"><?php esc_html_e( 'Содержит', 'wp-easy-forms' ); ?></option>
							<option value="checked"><?php esc_html_e( 'Отмечено (Checkbox/Radio)', 'wp-easy-forms' ); ?></option>
							<option value="not_checked"><?php esc_html_e( 'Не отмечено (Checkbox/Radio)', 'wp-easy-forms' ); ?></option>
						</select>
					</div>
					<div style="flex: 1; min-width: 150px;" class="wpef-cond-value-wrapper">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Значение', 'wp-easy-forms' ); ?></label>
						<input type="text" name="cond_trigger_value" class="regular-text" placeholder="yes" style="width: 100%;">
					</div>
				</div>
				<div style="display: flex; gap: 15px; flex-wrap: wrap;">
					<div style="flex: 2; min-width: 200px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Целевой блок (Селектор)', 'wp-easy-forms' ); ?></label>
						<input type="text" name="cond_target_selector" class="regular-text" placeholder=".address-wrapper" style="width: 100%;">
					</div>
					<div style="flex: 1; min-width: 120px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Действие', 'wp-easy-forms' ); ?></label>
						<select name="cond_action" style="width: 100%;">
							<option value="show"><?php esc_html_e( 'Показать', 'wp-easy-forms' ); ?></option>
							<option value="hide"><?php esc_html_e( 'Скрыть', 'wp-easy-forms' ); ?></option>
						</select>
					</div>
					<div style="flex: 1; min-width: 120px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Эффект', 'wp-easy-forms' ); ?></label>
						<select name="cond_effect" style="width: 100%;">
							<option value="slide">Slide</option>
							<option value="fade">Fade</option>
						</select>
					</div>
					<div style="flex: 1; min-width: 120px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Скорость (мс)', 'wp-easy-forms' ); ?></label>
						<input type="number" name="cond_duration" class="regular-text" placeholder="300" value="300" style="width: 100%;">
					</div>
					<div style="flex: 1; min-width: 120px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Задержка (мс)', 'wp-easy-forms' ); ?></label>
						<input type="number" name="cond_delay" class="regular-text" placeholder="0" value="0" style="width: 100%;">
					</div>
				</div>
			</div>
		</template>

		<template id="wpef-route-template">
			<div class="wpef-route-item" style="background: #fff; border: 1px solid #c3c4c7; padding: 15px; margin-bottom: 10px; position: relative;">
				<button type="button" class="button-link wpef-remove-route-btn" style="position: absolute; top: 10px; right: 10px; color: #d63638; text-decoration: none;"><?php esc_html_e( '&times; Удалить', 'wp-easy-forms' ); ?></button>
				<div style="display: flex; gap: 15px; flex-wrap: wrap;">
					<div style="flex: 1; min-width: 150px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Имя поля (name)', 'wp-easy-forms' ); ?></label>
						<input type="text" name="route_trigger_name" class="regular-text" placeholder="city" style="width: 100%;">
					</div>
					<div style="flex: 1; min-width: 120px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Условие', 'wp-easy-forms' ); ?></label>
						<select name="route_operator" style="width: 100%;">
							<option value="equals"><?php esc_html_e( 'Равно', 'wp-easy-forms' ); ?></option>
							<option value="not_equals"><?php esc_html_e( 'Не равно', 'wp-easy-forms' ); ?></option>
							<option value="contains"><?php esc_html_e( 'Содержит', 'wp-easy-forms' ); ?></option>
						</select>
					</div>
					<div style="flex: 1; min-width: 150px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Значение', 'wp-easy-forms' ); ?></label>
						<input type="text" name="route_trigger_value" class="regular-text" placeholder="<?php esc_attr_e( 'Москва', 'wp-easy-forms' ); ?>" style="width: 100%;">
					</div>
					<div style="flex: 2; min-width: 200px;">
						<label style="display: block; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Email получателя', 'wp-easy-forms' ); ?></label>
						<input type="text" name="route_target_email" class="regular-text" placeholder="msk@site.com" style="width: 100%;">
					</div>
				</div>
			</div>
		</template>

		<!-- Шаблон глобального поля валидации -->
		<template id="wpef-global-val-field-template">
			<div class="wpef-global-val-field-block wpef-config-block" style="margin-bottom: 20px;">
				<div class="wpef-config-header" style="display: flex; gap: 20px; align-items: flex-end;">
					<div style="flex: 1;">
						<label style="font-weight: 600; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Селектор поля:', 'wp-easy-forms' ); ?></label>
						<input type="text" name="val_field_selector" class="regular-text" placeholder="<?php esc_attr_e( 'Например: input[name=\\\'phone\\\'] или #file-upload', 'wp-easy-forms' ); ?>" required style="width: 100%;">
					</div>
					<div>
						<button type="button" class="button wpef-btn-delete wpef-remove-val-field"><?php esc_html_e( 'Удалить поле', 'wp-easy-forms' ); ?></button>
					</div>
				</div>
				<div class="wpef-config-body">
					<div class="wpef-val-rules-container"></div>
					<button type="button" class="button wpef-add-val-rule-btn" style="margin-top: 10px;"><?php esc_html_e( '+ Добавить правило', 'wp-easy-forms' ); ?></button>
				</div>
			</div>
		</template>

		<!-- Шаблон одного правила валидации -->
		<template id="wpef-val-rule-template">
			<div class="wpef-val-rule-block">
				<div class="wpef-row-flex">
					<div class="wpef-col-type">
						<label style="font-size: 13px;"><?php esc_html_e( 'Тип правила *', 'wp-easy-forms' ); ?></label>
						<select name="val_rule_type" class="wpef-val-rule-type-select" style="width: 100%;">
							<optgroup label="<?php esc_attr_e( 'Стандартные', 'wp-easy-forms' ); ?>">
								<option value="required"><?php esc_html_e( 'Обязательное поле', 'wp-easy-forms' ); ?></option>
								<option value="email"><?php esc_html_e( 'Email адрес', 'wp-easy-forms' ); ?></option>
								<option value="minLength"><?php esc_html_e( 'Минимум символов', 'wp-easy-forms' ); ?></option>
								<option value="maxLength"><?php esc_html_e( 'Максимум символов', 'wp-easy-forms' ); ?></option>
								<option value="password"><?php esc_html_e( 'Пароль (мин. 8, буква, цифра)', 'wp-easy-forms' ); ?></option>
								<option value="strongPassword"><?php esc_html_e( 'Строгий пароль (Сложный)', 'wp-easy-forms' ); ?></option>
								<option value="customRegexp"><?php esc_html_e( 'Регулярное выражение', 'wp-easy-forms' ); ?></option>
							</optgroup>
							<optgroup label="<?php esc_attr_e( 'Числа', 'wp-easy-forms' ); ?>">
								<option value="number"><?php esc_html_e( 'Только числа', 'wp-easy-forms' ); ?></option>
								<option value="integer"><?php esc_html_e( 'Только целые числа', 'wp-easy-forms' ); ?></option>
								<option value="minNumber"><?php esc_html_e( 'Минимальное число', 'wp-easy-forms' ); ?></option>
								<option value="maxNumber"><?php esc_html_e( 'Максимальное число', 'wp-easy-forms' ); ?></option>
							</optgroup>
							<optgroup label="<?php esc_attr_e( 'Файлы (только для type=\\\'file\\\')', 'wp-easy-forms' ); ?>">
								<option value="minFiles"><?php esc_html_e( 'Мин. количество файлов', 'wp-easy-forms' ); ?></option>
								<option value="maxFiles"><?php esc_html_e( 'Макс. количество файлов', 'wp-easy-forms' ); ?></option>
								<option value="maxTotalSize"><?php esc_html_e( 'Макс. общий вес (в байтах)', 'wp-easy-forms' ); ?></option>
								<option value="acceptTypes"><?php esc_html_e( 'Допустимые типы (напр. .png, .jpg)', 'wp-easy-forms' ); ?></option>
							</optgroup>
						</select>
					</div>
					<div class="wpef-val-rule-value-wrap wpef-col-value">
						<label style="font-size: 13px;"><?php esc_html_e( 'Значение (опц.)', 'wp-easy-forms' ); ?></label>
						<input type="text" name="val_rule_value" placeholder="<?php esc_attr_e( 'Например: 10', 'wp-easy-forms' ); ?>" style="width: 100%;">
					</div>
					<div class="wpef-col-error">
						<label style="font-size: 13px;"><?php esc_html_e( 'Текст ошибки', 'wp-easy-forms' ); ?></label>
						<input type="text" name="val_rule_error" placeholder="<?php esc_attr_e( 'Ошибка валидации', 'wp-easy-forms' ); ?>" style="width: 100%;">
					</div>
					<div class="wpef-col-action">
						<button type="button" class="wpef-btn-delete-icon wpef-remove-val-rule" title="<?php esc_attr_e( 'Удалить правило', 'wp-easy-forms' ); ?>">&times;</button>
					</div>
				</div>
			</div>
		</template>
		<?php
	}
}
