<?php

function wtw_forms_extentions()
{
    $extentions = [];
    foreach (get_field('wtw_forms_extentions', 'option') as $extention) {
        if ($extention['enabled']) {
            $extentions[$extention['acf_fc_layout']][] = $extention;
        }
    }
    return $extentions;
}

function wtw_log($message)
{
    if (!WP_DEBUG) return;

    $primary = get_stylesheet_directory() . '/bitrix.log';
    $fallback = WP_CONTENT_DIR . '/bitrix.log';
    
    if (is_array($message) || is_object($message)) {
        $message = print_r($message, true);
    }
    
    $line = date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
    $ok = false;
    
    if (is_writable(dirname($primary))) {
        $ok = @file_put_contents($primary, $line, FILE_APPEND | LOCK_EX) !== false;
    }
    if (!$ok) {
        $ok = @file_put_contents($fallback, $line, FILE_APPEND | LOCK_EX) !== false;
    }
    if (!$ok) {
        error_log($line);
    }
}

add_action('init', function(){
    // wtw_log('Bitrix logger initialized');
});

function ajaxs_wtw_mail_sent($jx)
{
    // if (!wp_verify_nonce($jx->ajaxs_nonce, 'ajaxs_action'))
    //     $jx->error('Ошибка. Неправильный код проверки!');

    $form_data = $jx->data;

    $forms = get_field('wtw_forms', 'option');

    if (!isset($form_data['__forms'])) {
        $jx->error('Ошибка: Формы не настроены!');
    }

    do_action('mailer_before_parse_fields', $form_data);
    $form_data = apply_filters('mailer_before_parse_fields_filter', $form_data);

    $form_id = $form_data['__forms'];

    $form_setup = $forms[$form_id];

    $extentions = wtw_forms_extentions();

    $result = [];

    $result_fields = [
        'hide',
        'delay',
        'lbox_hide',
        'hide_duration',
        'success_message',
        'error_message',
        'redirect',
        'redirect_url',
        'redirect_new_tab'
    ];

    foreach ($result_fields as $field) {
        $result[$field] = $form_setup[$field];
    }

    $form_data['__fields'] = "";
    foreach ($form_data as $key => $value) {

        if (in_array($key, [
            'ajaxs_nonce',
            'email_confirm',
            'form_time',
            'sfa_captcha_answer',
            'sfa_captcha_keys',
            'cf-turnstile-response',
            ]) || strpos($key, '__') === 0) continue;

        if ($value === 'on') {
            $value = '✔';
        }

        if (is_array($value)) {
            $form_data['__fields'] .= str_replace('_', ' ', $key) . ': <b>' . implode(', ', $value) . '</b> <br />';
        } else {
            if (!empty($value)) {
                $form_data['__fields'] .= str_replace('_', ' ', $key) . ': <b>' . $value . '</b> <br />';
            }
        }
    }

    $subject = wtw_proccesFieldTemplate($form_setup['subject'], $form_data);
    $message = wtw_proccesFieldTemplate($form_setup['message'], $form_data, !$form_setup['show_empty_fields']);

    $headers = [];
    $headers[] = 'Content-Type: text/html; charset=' . get_bloginfo('charset');

    if (!empty($form_setup['addreply'])) {
        if (!empty($form_setup['from'])) {
            $headers[] = 'Reply-To: ' . $form_setup['from'] . ' <' . $form_setup['addreply'] . '>';
        } else {
            $headers[] = 'Reply-To: ' . $form_setup['addreply'];
        }
    }

    if (!empty($form_setup['cc'])) {
        $headers[] = 'Cc: ' . $form_setup['cc'];
    }

    if (!empty($form_setup['bcc'])) {
        $headers[] = 'Bcc: ' . $form_setup['bcc'];
    }

    $email = $form_setup['email'];

    $attachments = wtw_getLoadedFilesList($jx->files);

    wtw_handle_extentions($form_data, $attachments);

    if (!empty($email) && $form_setup['enabled']) {
        $mail_sended = wp_mail($email, $subject, $message, $headers, $attachments);
    } else {
        $mail_sended = true;
    }

    if ($mail_sended) {
        $jx->success($result);
    } else {
        $jx->error($result);
    }
}

function wtw_handle_extentions($form_data, $attachments)
{
    $extentions = get_field('wtw_forms_extentions', 'option');

    $wtw_forms_extentions = get_field('wtw_forms_extentions', 'option');

    $form_setup = [];
    $extentions_data = [];

    foreach ($wtw_forms_extentions as $extention) {
        if (!empty($extention['selector']) && $extention['enabled']) {
            $extentions_data[$extention['acf_fc_layout']][] = $extention;
        }
    }

    $utm = [];
    $args = ltrim($form_data['__query'], '?');
    parse_str($args, $utm);

    foreach ($utm as $key => $value) {
        $form_data[$key] = $value;
    }

    if (is_array($attachments) && !empty($attachments)) {
        $form_data['__attachments'] = $attachments;
    }

    $extention_field = '__google_recaptcha';
    if (isset($form_data[$extention_field])) {

        $setup_id = $form_data[$extention_field];
        $form_setup = $extentions_data[$extention_field][$setup_id];

        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = $form_setup['recaptcha_secret_key'];
        $recaptcha_response = $form_data['__recaptcha_response'];

        if (!empty($recaptcha_response)) {
            $response = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $responseKeys = json_decode($response, true);

            if (!$responseKeys["success"] || $responseKeys["score"] < 0.5) {
                jx()->error('Ошибка: Проверка капчи не прошла!');
            }
        }
    }

    $extention_field = '__sender_reply';
    if (isset($form_data[$extention_field])) {

        $reply_attachments = [];

        $setup_id = $form_data[$extention_field];
        $form_setup = $extentions_data[$extention_field][$setup_id];

        if (!empty($form_setup['reply_file'])) {
            $upload_dir = wp_upload_dir();
            $reply_file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $form_setup['reply_file']);
            $reply_attachments[] = $reply_file;
        }

        $headers = [
            'content-type: text/html',
        ];

        $email = $form_data[$form_setup['reply_email']];

        $subject = wtw_proccesFieldTemplate($form_setup['reply_subject'], $form_data);
        $message = wtw_proccesFieldTemplate($form_setup['reply_message'], $form_data, !$form_setup['show_empty_fields']);

        if (!empty($email)) {
            $mail_sended = wp_mail($email, $subject, $message, $headers, $reply_attachments);

            if (!$mail_sended) {
                jx()->error($result);
            }
        }
    }

    $extention_field = '__telegram';
    if (isset($form_data[$extention_field])) {

        $setup_id = $form_data[$extention_field];
        $form_setup = $extentions_data[$extention_field][$setup_id];

        $message = wtw_proccesFieldTemplate($form_setup['template'], $form_data, !$form_setup['show_empty_fields']);

        $message = str_replace(['<b>', '</b>'], '*', $message);
        $message = str_replace('<br />', "\n", $message);

        $botToken = $form_setup['token'];
        $botURL = "https://api.telegram.org/bot" . $botToken;
        $chatId = $form_setup['chat_id'];

        $params = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $response = wp_remote_post($botURL . '/sendMessage', [
            'body' => $params,
            'sslverify' => false,
        ]);

        if ($form_setup['send_files']) {
            if (is_array($attachments) && count($attachments) > 0) {
                foreach ($attachments as $path) {
                    $params = [
                        'chat_id' => $chatId,
                        'document' => curl_file_create($path, '', basename($path)),
                    ];
                    $ch = curl_init($botURL . '/sendDocument');
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
            } else {
                foreach ($_FILES as $fieldName => $filePost) {
                    $files_array = reArrayFiles($filePost);
                    if ($files_array !== false) {
                        foreach ($files_array as $file) {
                            if ($file['error'] === UPLOAD_ERR_OK) {
                                $params = [
                                    'chat_id' => $chatId,
                                    'document' => curl_filereate($file['tmp_name'], '', $file['name']),
                                ];
                                $ch = curl_init($botURL . '/sendDocument');
                                curl_setopt($ch, CURLOPT_HEADER, false);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                $result = curl_exec($ch);
                                curl_close($ch);
                            }
                        }
                    }
                }
            }
        }

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $jx->error($error_message);
        }
    }

    $extention_field = '__bitrix';
    if (isset($form_data[$extention_field])) {

        if (!function_exists('wtw_createBitrixDeal')) {
            jx()->alert('Расширение BITRIX не активировано! Обратитесь к разработчику.');
        } else {

            wtw_log('Bitrix: extension triggered');

            $lead_id = null;
            $deal_id = null;

            $setup_id = $form_data[$extention_field];
            $form_setup = $extentions_data[$extention_field][$setup_id];

            $api_url = $form_setup['bitrix_api_url'];

            $entities = [];
            foreach ($form_setup['entities'] as $entity) {
                $entities[$entity['acf_fc_layout']] = $entity;
                unset($entities[$entity['acf_fc_layout']]['acf_fc_layout']);
            }

            wtw_log('Bitrix: entities prepared: ' . implode(', ', array_keys($entities)));

            $entity = $entities['contact'];
            if (isset($entity)) {
                wtw_log('Bitrix: create contact');
                $contact_id = wtw_createBitrixContact($api_url, $form_data, $entity);
                wtw_log('Bitrix: contact id=' . ($contact_id ?: '')); 
            }

            $entity = $entities['lead'];
            if (isset($entity)) {
                if (isset($contact_id) && !empty($contact_id)) {
                    $entity['CONTACT_ID'] = $contact_id;
                }
                wtw_log('Bitrix: create lead');
                $lead_id = wtw_createBitrixLead($api_url, $form_data, $entity);
                wtw_log('Bitrix: lead id=' . ($lead_id ?: ''));
            }

            $entity = $entities['deal'];
            if (isset($entity)) {
                if (isset($contact_id) && !empty($contact_id)) {
                    $entity['CONTACT_ID'] = $contact_id;
                }
                wtw_log('Bitrix: create deal');
                $deal_id = wtw_createBitrixDeal($api_url, $form_data, $entity);
                wtw_log('Bitrix: deal id=' . ($deal_id ?: ''));
            }

            $entity = $entities['comment'];
            if (isset($entity)) {
                if (isset($deal_id) && !empty($deal_id)) {
                    $entity['ENTITY_TYPE'] = 'deal';
                    $entity['ENTITY_ID'] = $deal_id;
                    wtw_log('Bitrix: add comment to deal');
                    $comment_id = wtw_createBitrixComment($api_url, $form_data, $entity);
                } else if (isset($lead_id) && !empty($lead_id)) {
                    $entity['ENTITY_TYPE'] = 'lead';
                    $entity['ENTITY_ID'] = $lead_id;
                    wtw_log('Bitrix: add comment to lead');
                    $comment_id = wtw_createBitrixComment($api_url, $form_data, $entity);
                }
            }
        }
    }

    $extention_field = '__amo';
    if (isset($form_data[$extention_field])) {

        if (!function_exists('wtw_createAMOLead')) {
            jx()->alert('Расширение АМО не активировано! Обратитесь к разработчику.');
        } else {

            $setup_id = $form_data[$extention_field];
            $form_setup = $extentions_data[$extention_field][$setup_id];

            $entities = [];
            foreach ($form_setup['entities'] as $entity) {
                $entities[$entity['acf_fc_layout']] = $entity;
                unset($entities[$entity['acf_fc_layout']]['acf_fc_layout']);
            }

            $lead_id = null;

            $form_setup['entities'] = $entities;

            if (isset($entities['lead'])) {
                $lead_id = wtw_createAMOLead($form_setup, $form_data);
            }

            if (isset($entities['note']) && $lead_id !== null) {
                $form_setup['entities']['note']['lead_id'] = $lead_id;
                wtw_createAMONote($form_setup, $form_data);
            }
        }
    }
}

function wtw_proccesFieldTemplate($template, $form_data, $remove_empty_fields = false)
{
    $form_data['__ip'] = $_SERVER['REMOTE_ADDR'];
    $form_data['__site'] = $_SERVER['HTTP_HOST'];
    $form_data['__browser'] = $_SERVER['HTTP_USER_AGENT'];

    foreach ($form_data as $key => $value) {
        if (is_string($value)) {
            $template = str_replace("{{ $key }}", $value, $template);
        }
    }

    $template = str_replace("{{ now }}", date('d.m.Y H:i:s'), $template);

    if ($remove_empty_fields) {
        $template = preg_replace('/^.*\{\{[^\}]*\}\}.*$\n?/m', '', $template);
    } else {
        $template = preg_replace('/\{\{[^\}]*\}\}/', '', $template);
    }

    return $template;
}

function wtw_getLoadedFilesList($files)
{
    $uploaded_files_paths = [];

    $fileFields = array_keys($files);

    foreach ($fileFields as $fieldName) {

        if (!is_array($files[$fieldName]['compact'])) continue;

        foreach ($files[$fieldName]['compact'] as $file) {

            $movefile = wp_handle_upload($file, ['test_form' => false]);

            if ($movefile && !isset($movefile['error'])) {
                $uploaded_files_paths[] = $movefile['file'];
            }
        }
    }
    return $uploaded_files_paths;
}

add_action('wp_mail_failed', 'wtw_log_mailer_errors', 10, 1);
function wtw_log_mailer_errors($wp_error)
{
    error_log($wp_error->get_error_message());
}

add_action('phpmailer_init', 'wtw_smtp_phpmailer_init', 999);
function wtw_smtp_phpmailer_init($phpmailer)
{
    $extention_name = '__smtp';
    $extentions = wtw_forms_extentions();

    if (!isset($extentions[$extention_name])) {
        return;
    }

    $setup = $extentions[$extention_name][0];
    $sender_email = isset($setup['sender_email']) && !empty($setup['sender_email']) ? $setup['sender_email'] : $setup['smtp_user'];
    $sender_name = isset($setup['sender_name']) && !empty($setup['sender_name']) ? $setup['sender_name'] : null;

    $phpmailer->IsSMTP();
    $phpmailer->SMTPAuth   = $setup['smtp_auth'];
    $phpmailer->Host       = $setup['smtp_server'];
    $phpmailer->Port       = $setup['smtp_port'];
    $phpmailer->CharSet    = $setup['smtp_encoding'];
    $phpmailer->SMTPSecure = $setup['smtp_secure'];
    $phpmailer->Username   = $setup['smtp_user'];
    $phpmailer->Password   = $setup['smtp_password'];
    $phpmailer->From       = $sender_email;
    if ($sender_name !== null) $phpmailer->FromName = $sender_name;
    $phpmailer->isHTML(true);
}

function get_wtw_smtp_settings($name){
    $extention_name = '__smtp';
    $extentions = wtw_forms_extentions();

    if (!isset($extentions[$extention_name])) {
        return null;
    }

    $setup = $extentions[$extention_name][0];

    if (!isset($setup[$name])) {
        return null;
    }

    return $setup[$name];
}

add_filter('wp_mail_from', function($email) {
    return get_wtw_smtp_settings('sender_email') ? get_wtw_smtp_settings('sender_email') : get_wtw_smtp_settings('smtp_user');
});

add_filter('wp_mail_from_name', function($name) {
    return get_wtw_smtp_settings('sender_name') ? get_wtw_smtp_settings('sender_name') : $name;
});

add_action('wp_footer', 'wtw_google_recaptcha_load');
function wtw_google_recaptcha_load()
{
    $extention_name = '__google_recaptcha';
    $extentions = wtw_forms_extentions();

    if (!isset($extentions[$extention_name])) {
        return;
    }

    $setup = $extentions[$extention_name][0];
?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $setup['recaptcha_site_key'] ?>"></script>
    <script>
        const RECAPTCHA_SITE_KEY = `<?php echo $setup['recaptcha_site_key'] ?>`;
    </script>
<?php
}

add_action('wp_footer', 'wtw_validate_data_load');
function wtw_validate_data_load()
{
    $extention_name = '__validate';
    $extentions = wtw_forms_extentions();

    if (!isset($extentions[$extention_name])) {
        return;
    }

    $setup = $extentions[$extention_name][0];
?>
    <script>
        const VALIDATE_DATA = <?php echo json_encode($setup['validate_fields']) ?>;
    </script>
<?php
}

function wtw_forms_data_load()
{
    wp_enqueue_script('forms', get_stylesheet_directory_uri() . '/js/forms.js', ['justvalidate'], null, true);

    $wtw_forms = get_field('wtw_forms', 'option');
    $wtw_forms_extentions = get_field('wtw_forms_extentions', 'option');
    $forms_data = ['__forms' => []];

    foreach ($wtw_forms as $form) {
        $forms_data['__forms'][] = $form['selector'];
    }

    if (!empty($wtw_forms_extentions) && is_array($wtw_forms_extentions)) {
        foreach ($wtw_forms_extentions as $extention) {
            if (!empty($extention['selector']) && $extention['enabled']) {
                $forms_data[$extention['acf_fc_layout']][] = $extention['selector'];
            }
        }
    }

    wp_localize_script('forms', 'wtw_forms', $forms_data);
}

add_action('wp_enqueue_scripts', 'wtw_forms_data_load');

add_action('init', function () {
    if (function_exists('acf_add_options_page') && current_user_can('manage_options')) {
        acf_add_options_page([
            'page_title' => __('Формы', 'wtw-translate'),
            'menu_title' => __('Формы', 'wtw-translate'),
            'menu_slug' => 'wtw_forms',
            'icon_url' => 'dashicons-screenoptions',
            'parent_slug' => 'tools.php',
            'update_button' => __('Update'),
            'updated_message' => __('Item updated.'),
            'autoload' => true,
        ]);
    }
});

if (!function_exists('reArrayFiles')) {
    function reArrayFiles(&$file_post)
    {
        if ($file_post === null) {
            return false;
        }
        $files_array = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $files_array[$i][$key] = $file_post[$key][$i];
            }
        }
        return $files_array;
    }
}

// === 1. Генерация canvas капчи и уникального form_id ===
add_action('wp_footer', 'sfa_add_fields_via_js', 20);
function sfa_add_fields_via_js() {
    if (!session_id()) session_start();

    $extention_name = '__antispam';
    $extentions = wtw_forms_extentions();

    if (!isset($extentions[$extention_name])) {
        return;
    }

    $setup = $extentions[$extention_name][0];
    ?>
<script>
window.sfaCaptcha = {
    validate: async function(form) {
        const captchaInput = form.querySelector('input[name="sfa_captcha_answer"]');
        if (!captchaInput) return true;
        const captchaValue = captchaInput.value.trim();
        const parent = captchaInput.parentNode;
        let errorSpan = parent.querySelector('.just-validate-error-label');

        if (errorSpan) {
            errorSpan.textContent = '';
        } else {
            errorSpan = document.createElement('span');
            errorSpan.className = 'just-validate-error-label';
            parent.insertBefore(errorSpan, captchaInput.nextSibling);
        }

        try {
            const response = await fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'check_captcha',
                    sfa_captcha_answer: captchaValue,
                    form_id: form.getAttribute('data-form-id')
                })
            });
            const data = await response.json();
            if (!data.success) {
                const span = document.createElement('span');
                span.className = 'just-validate-error-label';
                span.textContent = '<?php echo $setup['message_for_code'] ?>';
                captchaInput.insertAdjacentElement('afterend', span);
                captchaInput.value = '';
                const canvas = form.querySelector('canvas');
                if (canvas) canvas.click();
                return false;
            }
            return true;
        } catch (error) {
            console.error('Captcha validation error:', error);
            const span = document.createElement('span');
            span.className = 'just-validate-error-label';
            span.textContent = '<?php echo $setup['message_for_error'] ?>';
            captchaInput.insertAdjacentElement('afterend', span);
            return false;
        }
    }
};
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('form').forEach(form => {

        // === Уникальный ID формы ===
        let formId = form.getAttribute('data-form-id');
        if (!formId) {
            formId = 'form_' + Math.random().toString(36).substring(2,12);
            form.setAttribute('data-form-id', formId);
        }
        
        const captchaInput = form.querySelector('input[name="sfa_captcha_answer"]');
        if (captchaInput) {
            captchaInput.dataset.formId = formId;
        }

        // === Honeypot ===
        if (!form.querySelector('input[name="email_confirm"]')) {
            const honeypot = document.createElement('input');
            honeypot.type = 'text';
            honeypot.name = 'email_confirm';
            honeypot.style.display = 'none';
            honeypot.autocomplete = 'off';
            form.appendChild(honeypot);
        }

        // === Таймер ===
        if (!form.querySelector('input[name="form_time"]')) {
            const timer = document.createElement('input');
            timer.type = 'hidden';
            timer.name = 'form_time';
            timer.value = Math.floor(Date.now() / 1000);
            form.appendChild(timer);
        }

        // === Canvas капчи ===
        const captchaContainer = form.querySelector('[data-captcha]');
        if (captchaContainer && !captchaContainer.querySelector('canvas')) {
            const canvas = document.createElement('canvas');
            canvas.width = 160;
            canvas.height = 50;
            canvas.style.display = 'block';
            captchaContainer.appendChild(canvas);
        
            // === Кнопка обновления символов ===
            const refresh = document.createElement('div');
            refresh.textContent = '<?php echo $setup['update_text'] ?>';
            refresh.style.cursor = 'pointer';
            refresh.style.marginTop = '2px';
            refresh.style.fontSize = '13px';
            refresh.style.color = '<?php echo $setup['button_color'] ?>';
            refresh.style.userSelect = 'none';

            captchaContainer.appendChild(refresh);
        
            function generateCaptcha() {
                const letters = '<?php echo $setup['captcha_chars'] ?>';

                const length = Math.floor(Math.random() * 2) + 6; // 6-7 букв
                let word = '';
                for (let i = 0; i < length; i++) {
                    word += letters[Math.floor(Math.random() * letters.length)];
                }
        
                fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'sfa_set_captcha',
                        word: word,
                        form_id: formId
                    })
                });
        
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '<?php echo $setup['bg_color'] ?>';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
        
                const colors = ['#007BFF','#FF4136','#2ECC40','#FF851B','#39CCCC','#F012BE','#01FF70','#FFDC00','#ff8b00','#0bebe8','#85144B','#FF69B4'];
                const spacing = canvas.width / (word.length + 1);
        
                for (let i = 0; i < word.length; i++) {
                    const char = word[i];
                    const isUpper = char === char.toUpperCase() && /[А-ЯЁ]/.test(char);
                    ctx.font = isUpper ? 'bold 28px Arial, sans-serif' : 'normal 22px Arial, sans-serif';
                    ctx.fillStyle = colors[Math.floor(Math.random() * colors.length)];
                    ctx.textBaseline = 'middle';
                    ctx.fillText(char, spacing * (i + 0.5), 25 + Math.random()*20);
                }
        
                for (let i = 0; i < 10; i++) {
                    ctx.fillStyle = colors[Math.floor(Math.random() * colors.length)];
                    ctx.beginPath();
                    ctx.arc(Math.random() * canvas.width, Math.random() * canvas.height, 1 + Math.random()*2, 0, Math.PI*2);
                    ctx.fill();
                }
        
                for (let i = 0; i < 3; i++) {
                    ctx.strokeStyle = colors[Math.floor(Math.random() * colors.length)];
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(Math.random()*canvas.width, Math.random()*canvas.height);
                    ctx.lineTo(Math.random()*canvas.width, Math.random()*canvas.height);
                    ctx.stroke();
                }
            }
        
            generateCaptcha();
        
            canvas.addEventListener('click', generateCaptcha);
            refresh.addEventListener('click', generateCaptcha);
        }       

    });
});
</script>
<?php
}

// === AJAX для установки ключа капчи с привязкой к form_id ===
add_action('wp_ajax_sfa_set_captcha', 'sfa_set_captcha');
add_action('wp_ajax_nopriv_sfa_set_captcha', 'sfa_set_captcha');
function sfa_set_captcha() {
    if (!session_id()) session_start();
    $word = $_POST['word'] ?? '';
    $form_id = $_POST['form_id'] ?? '';
    if ($form_id) {
        $_SESSION['sfa_captcha_keys'][$form_id] = $word;
    }
    wp_send_json_success();
}

// === AJAX для проверки капчи через JustValidate по form_id ===
add_action('wp_ajax_check_captcha', 'check_captcha');
add_action('wp_ajax_nopriv_check_captcha', 'check_captcha');
function check_captcha() {
    if (!session_id()) session_start();
    $answer = trim($_POST['sfa_captcha_answer'] ?? '');
  $form_id = $_POST['form_id'] ?? '';
  $key = $_SESSION['sfa_captcha_keys'][$form_id] ?? '';
  $valid = mb_strtolower($answer, 'UTF-8') === mb_strtolower($key, 'UTF-8');

    if ($valid) unset($_SESSION['sfa_captcha_keys'][$form_id]);
    wp_send_json(['success' => $valid]);
}

// === Проверка при отправке формы (Honeypot + таймер) ===
add_action('init', 'sfa_check_antispam');
function sfa_check_antispam() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!session_id()) session_start();

    if (!empty($_POST['email_confirm'])) wp_die('Спам обнаружен.');

    if (isset($_POST['form_time'])) {
        $diff = time() - (int)$_POST['form_time'];
        if ($diff < 3) wp_die('Слишком быстро, похоже на бота.');
    }
}
?><?php

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_forms_59089a20ba7d8',
	'title' => 'Формы',
	'fields' => array(
		array(
			'key' => 'field_forms_663dcc562ee12',
			'label' => 'Формы',
			'name' => '',
			'aria-label' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
			'selected' => 0,
		),
		array(
			'key' => 'field_forms_593406b27e390',
			'label' => 'Формы',
			'name' => 'wtw_forms',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'block',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => 'field_forms_6631cd5cdf89f',
			'button_label' => 'Добавить настройку',
			'rows_per_page' => 20,
			'acfe_repeater_stylised_button' => 0,
			'sub_fields' => array(
				array(
					'key' => 'field_6661d91198cf5',
					'label' => 'Включить отправку',
					'name' => 'enabled',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 1,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_6631cd5cdf89f',
					'label' => 'Название настройки',
					'name' => 'desc',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Все формы',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_593437c01afba',
					'label' => 'Селектор отбора',
					'name' => 'selector',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'form',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => 'селектор',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_593406fb7e391',
					'label' => 'Email получателя',
					'name' => 'email',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => 'разделять запятой',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_5934076d7e392',
					'label' => 'Тема письма',
					'name' => 'subject',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Новое сообщение с сайта {{ __site }}',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_5934077e7e393',
					'label' => 'Сообщение',
					'name' => 'message',
					'aria-label' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Данные формы:
{{ __fields }}

Страница: <b>{{ __page }}</b>
Заголовок: <b>{{ __title }}</b>
Форма: <b>{{ __form }}</b>
Запрос: <b>{{ __query }}</b>
Браузер: <b>{{ __browser }}</b>
IP: <b>{{ __ip }}</b>',
					'maxlength' => '',
					'rows' => 10,
					'placeholder' => '',
					'new_lines' => 'br',
					'parent_repeater' => 'field_forms_593406b27e390',
					'acfe_textarea_code' => 0,
				),
				array(
					'key' => 'field_6791b3dbe8db3',
					'label' => 'Показывать строки с пустыми значениями полей',
					'name' => 'show_empty_fields',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_59343c0f18dbc',
					'label' => 'Адрес для ответа',
					'name' => 'addreply',
					'aria-label' => '',
					'type' => 'email',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_593409615f453',
					'label' => 'Имя отправителя',
					'name' => 'from',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_593409945f454',
					'label' => 'Копия письма (CC)',
					'name' => 'cc',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_593409c15f455',
					'label' => 'Скрытая копия (BCC)',
					'name' => 'bcc',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_59340fc4b1930',
					'label' => 'Сообщение об отправке',
					'name' => 'success_message',
					'aria-label' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_forms_663dd8ebbd8b8',
								'operator' => '!=',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Ваше сообщение отправлено!
Спасибо за обращение.',
					'maxlength' => '',
					'rows' => 2,
					'placeholder' => '',
					'new_lines' => 'br',
					'parent_repeater' => 'field_forms_593406b27e390',
					'acfe_textarea_code' => 0,
				),
				array(
					'key' => 'field_forms_59341000b1931',
					'label' => 'Сообщение об ошибке',
					'name' => 'error_message',
					'aria-label' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_forms_663dd8ebbd8b8',
								'operator' => '!=',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Ошибка отправки!
Попробуйте позже.',
					'maxlength' => '',
					'rows' => 2,
					'placeholder' => '',
					'new_lines' => 'br',
					'parent_repeater' => 'field_forms_593406b27e390',
					'acfe_textarea_code' => 0,
				),
				array(
					'key' => 'field_forms_59340e7668e14',
					'label' => 'Скрыть форму после отправки',
					'name' => 'hide',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_59340ebd68e15',
					'label' => 'Скрыть сообщение об отправке',
					'name' => 'delay',
					'aria-label' => '',
					'type' => 'number',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'min' => '',
					'max' => '',
					'placeholder' => '',
					'step' => '',
					'prepend' => '',
					'append' => 'секунд',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_5934130ec534c',
					'label' => 'Скрыть блок после отправки',
					'name' => 'lbox_hide',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => 'селектор',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_693829fc239e1',
					'label' => 'Длительность затухания',
					'name' => 'hide_duration',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'default_value' => 1000,
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => 'мс',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_663dd8ebbd8b8',
					'label' => 'Редирект после отправки',
					'name' => 'redirect',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '33',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_59340f1168e16',
					'label' => 'Перенаправление на страницу',
					'name' => 'redirect_url',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_forms_663dd8ebbd8b8',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '33',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_forms_593406b27e390',
				),
				array(
					'key' => 'field_forms_59340e7668e15',
					'label' => 'Перенаправлять в новом окне',
					'name' => 'redirect_new_tab',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_forms_663dd8ebbd8b8',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '30',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
					'parent_repeater' => 'field_forms_593406b27e390',
				),
			),
		),
		array(
			'key' => 'field_forms_663dcc3b2ee11',
			'label' => 'Расширения',
			'name' => '',
			'aria-label' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
			'selected' => 0,
		),
		array(
			'key' => 'field_forms_663dcc742ee13',
			'label' => 'Расширения',
			'name' => 'wtw_forms_extentions',
			'aria-label' => '',
			'type' => 'flexible_content',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'acfe_flexible_advanced' => 0,
			'layouts' => array(
				'layout_691e32044e7e4' => array(
					'key' => 'layout_691e32044e7e4',
					'name' => '__antispam',
					'label' => 'Антиспам',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_691e331917109',
							'label' => 'Включить антиспам',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'allow_in_bindings' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_692047149582e',
							'label' => 'Обновить символы',
							'name' => 'update_text',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'Обновить символы',
							'maxlength' => '',
							'allow_in_bindings' => 0,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_691e32784e7e8',
							'label' => 'Ошибка проверки',
							'name' => 'message_for_error',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'Ошибка проверки капчи. Пожалуйста, попробуйте еще раз.',
							'maxlength' => '',
							'allow_in_bindings' => 0,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_691e328a4e7e9',
							'label' => 'Неверный код',
							'name' => 'message_for_code',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'Неверный код с изображения',
							'maxlength' => '',
							'allow_in_bindings' => 0,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_691e32244e7e6',
							'label' => 'Цвет кнопки',
							'name' => 'button_color',
							'aria-label' => '',
							'type' => 'color_picker',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '#000000',
							'enable_opacity' => 0,
							'return_format' => 'string',
							'allow_in_bindings' => 0,
							'show_custom_palette' => 0,
							'show_color_wheel' => 1,
							'custom_palette_source' => '',
							'palette_colors' => '',
						),
						array(
							'key' => 'field_691e32554e7e7',
							'label' => 'Цвет фона',
							'name' => 'bg_color',
							'aria-label' => '',
							'type' => 'color_picker',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '#ffffff',
							'enable_opacity' => 0,
							'return_format' => 'string',
							'allow_in_bindings' => 0,
							'show_custom_palette' => 0,
							'show_color_wheel' => 1,
							'custom_palette_source' => '',
							'palette_colors' => '',
						),
						array(
							'key' => 'field_69201ed4e05ca',
							'label' => 'Язык капчи',
							'name' => 'captcha_chars',
							'aria-label' => '',
							'type' => 'radio',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'АБВГДЕЁЖЗИЙКЛМНПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнпрстуфхцчшщъыьэюя0123456789' => 'RU',
								'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz0123456789' => 'EN',
							),
							'default_value' => '',
							'return_format' => 'value',
							'allow_null' => 0,
							'other_choice' => 0,
							'allow_in_bindings' => 0,
							'layout' => 'vertical',
							'save_other_choice' => 0,
						),
					),
					'min' => '',
					'max' => '',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
				'layout_forms_663dcdada936c' => array(
					'key' => 'layout_forms_663dcdada936c',
					'name' => '__google_recaptcha',
					'label' => 'Google Recaptcha',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_forms_663dcdada936d',
							'label' => 'Включить',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663de480ca996',
							'label' => 'Отбор по селектору',
							'name' => 'selector',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'form',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dd171e47df',
							'label' => 'Ключ сайта',
							'name' => 'recaptcha_site_key',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dd1b9e47e0',
							'label' => 'Секретный ключ',
							'name' => 'recaptcha_secret_key',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
					),
					'min' => '',
					'max' => '1',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
				'layout_forms_663dce0aa9370' => array(
					'key' => 'layout_forms_663dce0aa9370',
					'name' => '__validate',
					'label' => 'Валидация полей',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_forms_663dcfa0fd57a',
							'label' => 'Включить',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_65b4ee68e7177',
							'label' => 'Добавить поле для валидации',
							'name' => 'validate_fields',
							'aria-label' => '',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'layout' => 'block',
							'min' => 0,
							'max' => 0,
							'collapsed' => 'field_forms_65b4eeade7178',
							'button_label' => 'Добавить поле',
							'rows_per_page' => 20,
							'acfe_repeater_stylised_button' => 0,
							'sub_fields' => array(
								array(
									'key' => 'field_forms_65b4eeade7178',
									'label' => 'Селектор поля',
									'name' => 'selector',
									'aria-label' => '',
									'type' => 'text',
									'instructions' => 'Класс, id или атрибут',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'maxlength' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'parent_repeater' => 'field_forms_65b4ee68e7177',
								),
								array(
									'key' => 'field_forms_65b4f9aee7179',
									'label' => 'Добавить правило',
									'name' => 'rules',
									'aria-label' => '',
									'type' => 'repeater',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'layout' => 'block',
									'min' => 0,
									'max' => 0,
									'collapsed' => 'field_forms_65b4f9d8e717a',
									'button_label' => 'Добавить правило',
									'rows_per_page' => 20,
									'parent_repeater' => 'field_forms_65b4ee68e7177',
									'acfe_repeater_stylised_button' => 0,
									'sub_fields' => array(
										array(
											'key' => 'field_forms_65b4f9d8e717a',
											'label' => 'Правило',
											'name' => 'rule',
											'aria-label' => '',
											'type' => 'select',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'choices' => array(
												'required' => 'Обязательное поле',
												'email' => 'Валидация поля на наличие email',
												'minLength' => 'Минимальное количество символов',
												'maxLength' => 'Максимальное количество символов',
												'number' => 'Валидация поля на наличие числа',
												'integer' => 'Валидация поля на наличие целого числа',
												'minNumber' => 'Минимально допустимое для ввода число',
												'maxNumber' => 'Максимально допустимое для ввода число',
												'password' => 'Валидация поля на наличие пароля',
												'strongPassword' => 'Валидация поля на наличие строгого пароля',
												'customRegexp' => 'Пользовательское регулярное выражение',
												'files' => 'Валидация прикрепленных файлов',
												'minFilesCount' => 'Минимальное количество файлов',
												'maxFilesCount' => 'Максимальное количество файлов',
												'custom_code' => 'Своя формула',
											),
											'default_value' => false,
											'return_format' => 'value',
											'multiple' => 0,
											'allow_custom' => 0,
											'search_placeholder' => '',
											'allow_null' => 0,
											'ui' => 1,
											'ajax' => 0,
											'placeholder' => '',
											'create_options' => 0,
											'save_options' => 0,
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b505dcd57b8',
											'label' => 'Текст оповещения об ошибке',
											'name' => 'error_message',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b50215401d0',
											'label' => 'Минимальное число символов',
											'name' => 'min_length',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'minLength',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b50234401d1',
											'label' => 'Максимальное число символов',
											'name' => 'max_length',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'maxLength',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b5029822614',
											'label' => 'Минимальное число',
											'name' => 'min_number',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'minNumber',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b502cd22615',
											'label' => 'Максимальное число',
											'name' => 'max_number',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'maxNumber',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b502fb22616',
											'label' => 'Регулярное выражение',
											'name' => 'custom_regexp',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'customRegexp',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b5032e22617',
											'label' => 'Минимальное количество файлов',
											'name' => 'min_files_count',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'minFilesCount',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b5034b22618',
											'label' => 'Максимальное количество файлов',
											'name' => 'max_files_count',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'maxFilesCount',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b5039022619',
											'label' => 'Допустимые форматы файлов',
											'name' => 'extensions',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => 'Форматы файлов указывать в одинарных кавычках через запятую, пример: \'jpeg\', \'jpg\', \'png\'',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'files',
													),
												),
											),
											'wrapper' => array(
												'width' => '50',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b504682261a',
											'label' => 'Допустимые форматы файлов в формате MIME types',
											'name' => 'types',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => 'Форматы файлов указывать в одинарных кавычках через запятую, пример: \'image/jpeg\', \'image/jpg\', \'image/png\'. <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types">Перечень MIME types</a>',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'files',
													),
												),
											),
											'wrapper' => array(
												'width' => '50',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b50566d57b6',
											'label' => 'Минимальный размер файла в байтах',
											'name' => 'min_size',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'files',
													),
												),
											),
											'wrapper' => array(
												'width' => '25',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_forms_65b505bad57b7',
											'label' => 'Максимальный размер файла в байтах',
											'name' => 'max_size',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==contains',
														'value' => 'files',
													),
												),
											),
											'wrapper' => array(
												'width' => '25',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
										array(
											'key' => 'field_6916cbcf74270',
											'label' => 'Произвольный код',
											'name' => 'custom_code',
											'aria-label' => '',
											'type' => 'acf_code_field',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_forms_65b4f9d8e717a',
														'operator' => '==',
														'value' => 'custom_code',
													),
												),
											),
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'placeholder' => '',
											'mode' => 'htmlmixed',
											'theme' => 'monokai',
											'parent_repeater' => 'field_forms_65b4f9aee7179',
										),
									),
								),
							),
						),
						array(
							'key' => 'field_forms_65b4fd551da98',
							'label' => 'Обзор правил',
							'name' => '',
							'aria-label' => '',
							'type' => 'message',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '<blockquote style="background: #EAECF0; padding: 5px; margin-bottom: 10px; font-weight: 600">Обратите внимание, правила не вызывают ошибку проверки, если указанное поле не будет заполнено.
		В противном случае следует выбрать параметр "Обязательное поле"</blockquote>

<div style="padding: 8px">
		<strong>Селектор поля</strong> — указывает на то, к какому полю подключатся текущие правила.

		Можно указать:
		<strong>form</strong> - все формы;
		<strong>.class</strong> класс поля;
		<strong>#id</strong> идентификатор поля;
		<strong>[attribute=value]</strong> атрибут поля
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Обязательное поле</strong> — делает поле обязательным. Наличие атрибута required на самом поле не обязательно.
</div>
<div style="padding: 8px">
		<strong>Валидация поля на наличие email</strong> — в поле можно ввести только email адрес
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Минимальное количество символов</strong> — ограничивает минимальную длину текста
</div>
<div style="padding: 8px">
		<strong>Максимальное количество символов</strong> — ограничивает максимальную длину текста
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Валидация поля на наличие числа</strong> — ограничивает ввод числом (целым или с плавающей точкой)
</div>
<div style="padding: 8px">
		<strong>Валидация поля на наличие целого числа</strong> — ограничивает ввод целым числом
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Минимально допустимое для ввода число</strong> — указанное в поле ввода число должно быть больше заданного значения
</div>
<div style="padding: 8px">
		<strong>Максимально допустимое для ввода число</strong> — указанное в поле ввода число должно быть меньше заданного значения
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Валидация поля на наличие пароля</strong> — минимум восемь символов, по крайней мере одна буква и одна цифра.
</div>
<div style="padding: 8px">
		<strong>Валидация поля на наличие строгого пароля</strong> — минимум восемь символов, по крайней мере одна заглавная буква, одна строчная буква, одна цифра и один специальный символ.
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Пользовательское регулярное выражение</strong> — возможность указать свое регулярное выражения для валидации поля
</div>
<div style="padding: 8px">
		<strong>Валидация прикрепленных файлов</strong> — атрибуты загруженных файлов должны соответствовать конфигурации указанных значений. Среди обязательных атрибутов находятся допустимые разрешения файлов и они же в формате MIME types, минимальный и максимальный размер файлов.
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Минимальное количество файлов</strong> — ограничивает добавление файлов минимально указанным количеством
</div>
<div style="padding: 8px">
		<strong>Максимальное количество файлов</strong> — ограничивает добавление файлов максимально указанным количеством
</div>
<div style="background: #EAECF0; padding: 8px">
		<strong>Текст оповещения об ошибке</strong> — всплывающее оповещение об ошибке валидации поля, к которому относится текущее правило.
		<br><br>
		Настроить вид всплывающего поля можно через класс <strong>just-validate-error-label</strong>.
		Так же можно настроить стили состояния самого поля для успешной и не успешной валидации:<br>
		<strong>just-validate-error-field</strong> — класс поля неудачной валидации;<br>
		<strong>just-validate-success-field</strong> — класс поля удачной валидации;<br>
</div>',
							'new_lines' => 'wpautop',
							'esc_html' => 0,
						),
					),
					'min' => '',
					'max' => '1',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
				'layout_forms_663dcc7bb3995' => array(
					'key' => 'layout_forms_663dcc7bb3995',
					'name' => '__sender_reply',
					'label' => 'Ответ отправителю',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_forms_59340ccbbd521',
							'label' => 'Включить',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663de4d3ca998',
							'label' => 'Отбор по селектору',
							'name' => 'selector',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'form',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dd07800b6e',
							'label' => 'Название поля email',
							'name' => 'reply_email',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'Email',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dd08500b6f',
							'label' => 'Тема письма',
							'name' => 'reply_subject',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dd09e00b70',
							'label' => 'Текcт письма',
							'name' => 'reply_message',
							'aria-label' => '',
							'type' => 'textarea',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'rows' => '',
							'placeholder' => '',
							'new_lines' => 'br',
							'acfe_textarea_code' => 0,
						),
						array(
							'key' => 'field_6791b43be8db4',
							'label' => 'Показывать строки с пустыми значениями полей',
							'name' => 'show_empty_fields',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663dd0b000b71',
							'label' => 'Файл для отправки',
							'name' => 'reply_file',
							'aria-label' => '',
							'type' => 'file',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'return_format' => 'url',
							'library' => 'all',
							'min_size' => '',
							'max_size' => '',
							'mime_types' => '',
							'uploader' => '',
						),
					),
					'min' => '',
					'max' => '',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
				'layout_forms_663dcd3da936a' => array(
					'key' => 'layout_forms_663dcd3da936a',
					'name' => '__smtp',
					'label' => 'Отправка через SMTP',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_forms_663dcd3da936b',
							'label' => 'Включить',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663dedf4545ce',
							'label' => 'Сервер SMTP',
							'name' => 'smtp_server',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'smtp.yandex.ru',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dee47545d1',
							'label' => 'Порт',
							'name' => 'smtp_port',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => 465,
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dee0c545cf',
							'label' => 'Имя пользователя (email)',
							'name' => 'smtp_user',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663dee2c545d0',
							'label' => 'Пароль',
							'name' => 'smtp_password',
							'aria-label' => '',
							'type' => 'password',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6937d507d80ee',
							'label' => 'Имя отправителя',
							'name' => 'sender_name',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6937d536d80ef',
							'label' => 'Email отправителя',
							'name' => 'sender_email',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '49',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663defbf545d3',
							'label' => 'Авторизация',
							'name' => 'smtp_auth',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '33',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663dee5b545d2',
							'label' => 'Защита',
							'name' => 'smtp_secure',
							'aria-label' => '',
							'type' => 'button_group',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '33',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'ssl' => 'SSL',
								'tls' => 'TLS',
							),
							'default_value' => 'ssl',
							'return_format' => 'value',
							'allow_null' => 0,
							'layout' => 'horizontal',
						),
						array(
							'key' => 'field_forms_663df009545d4',
							'label' => 'Кодировка',
							'name' => 'smtp_encoding',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '33',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'utf-8',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
					),
					'min' => '',
					'max' => '1',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
				'layout_forms_663dcde3a936e' => array(
					'key' => 'layout_forms_663dcde3a936e',
					'name' => '__telegram',
					'label' => 'Отправка в Телеграм',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_forms_663de54eca99a',
							'label' => 'Включить',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663de4fdca999',
							'label' => 'Отбор по селектору',
							'name' => 'selector',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'form',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663df22f7715a',
							'label' => 'Токен бота',
							'name' => 'token',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663df2407715b',
							'label' => 'ID группы / пользователя',
							'name' => 'chat_id',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663df2567715c',
							'label' => 'Шаблон отправки',
							'name' => 'template',
							'aria-label' => '',
							'type' => 'textarea',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'Отправка формы с сайта:
{{ __fields }}',
							'maxlength' => '',
							'rows' => '',
							'placeholder' => '',
							'new_lines' => '',
							'acfe_textarea_code' => 0,
						),
						array(
							'key' => 'field_6791b52f28c06',
							'label' => 'Показывать строки с пустыми значениями полей',
							'name' => 'show_empty_fields',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_6937d5f0c0e4e',
							'label' => 'Отправлять файлы',
							'name' => 'send_files',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
					),
					'min' => '',
					'max' => '',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
				'layout_forms_663dce43a9371' => array(
					'key' => 'layout_forms_663dce43a9371',
					'name' => '__bitrix',
					'label' => 'Отправка в Bitrix24',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_forms_663de5bbe32e3',
							'label' => 'Включить',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663de576ca99b',
							'label' => 'Отбор по селектору',
							'name' => 'selector',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'form',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663df4615132a',
							'label' => 'Секретная cсылка API',
							'name' => 'bitrix_api_url',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_forms_663df4fb5132b',
							'label' => 'Создать в BITRIX24',
							'name' => 'entities',
							'aria-label' => '',
							'type' => 'flexible_content',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'acfe_flexible_advanced' => 0,
							'layouts' => array(
								'layout_681b8eff52cea' => array(
									'key' => 'layout_681b8eff52cea',
									'name' => 'lead',
									'label' => 'Лид',
									'display' => 'block',
									'sub_fields' => array(
										array(
											'key' => 'field_681b8eff52ceb',
											'label' => 'Заголовок',
											'name' => 'title',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => 'Новая сделка с сайта {{ site }}',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
										),
										array(
											'key' => 'field_681b8eff52cec',
											'label' => 'Поля лида',
											'name' => 'fields',
											'aria-label' => '',
											'type' => 'repeater',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'layout' => 'block',
											'min' => 0,
											'max' => 0,
											'collapsed' => '',
											'button_label' => 'Добавить поле',
											'rows_per_page' => 20,
											'acfe_repeater_stylised_button' => 0,
											'sub_fields' => array(
												array(
													'key' => 'field_681b8eff52ced',
													'label' => 'Название поля',
													'name' => 'name',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
														'ASSIGNED_BY_ID' => 'Ответственный',
														'CATEGORY_ID' => 'Воронка',
														'STAGE_ID' => 'Этап сделки',
														'SOURCE_ID' => 'Источник',
														'OPPORTUNITY' => 'Сумма',
														'PHONE' => 'Телефон',
														'EMAIL' => 'Email',
														'CUSTOM' => 'Произвольное поле',
													),
													'default_value' => false,
													'return_format' => 'value',
													'multiple' => 0,
													'allow_custom' => 0,
													'search_placeholder' => '',
													'allow_null' => 0,
													'ui' => 1,
													'ajax' => 0,
													'placeholder' => '',
													'parent_repeater' => 'field_681b8eff52cec',
												),
												array(
													'key' => 'field_681b8eff52cee',
													'label' => 'Произвольное поле',
													'name' => 'custom_field',
													'aria-label' => '',
													'type' => 'text',
													'instructions' => '',
													'required' => 0,
													'conditional_logic' => array(
														array(
															array(
																'field' => 'field_681b8eff52ced',
																'operator' => '==',
																'value' => 'CUSTOM',
															),
														),
													),
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'default_value' => '',
													'maxlength' => '',
													'placeholder' => '',
													'prepend' => '',
													'append' => '',
													'parent_repeater' => 'field_681b8eff52cec',
												),
												array(
													'key' => 'field_681b8eff52cef',
													'label' => 'Значение поля',
													'name' => 'value',
													'aria-label' => '',
													'type' => 'text',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'default_value' => '',
													'maxlength' => '',
													'placeholder' => '',
													'prepend' => '',
													'append' => '',
													'parent_repeater' => 'field_681b8eff52cec',
												),
												array(
													'key' => 'field_6933238efe90b',
													'label' => 'Тип',
													'name' => 'type',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 0,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
														'text' => 'Текст',
														'number' => 'Число',
														'file' => 'Файл',
														'array' => 'Массив',
													),
													'default_value' => false,
													'return_format' => 'value',
													'multiple' => 0,
													'allow_null' => 0,
													'ui' => 0,
													'ajax' => 0,
													'placeholder' => '',
													'allow_custom' => 0,
													'search_placeholder' => '',
													'parent_repeater' => 'field_681b8eff52cec',
												),
											),
										),
									),
									'min' => '',
									'max' => '1',
									'acfe_flexible_render_template' => false,
									'acfe_flexible_render_style' => false,
									'acfe_flexible_render_script' => false,
									'acfe_flexible_thumbnail' => false,
									'acfe_flexible_settings' => false,
									'acfe_flexible_settings_size' => false,
									'acfe_flexible_modal_edit_size' => false,
									'acfe_flexible_category' => false,
								),
								'layout_forms_663df65851331' => array(
									'key' => 'layout_forms_663df65851331',
									'name' => 'deal',
									'label' => 'Сделка',
									'display' => 'block',
									'sub_fields' => array(
										array(
											'key' => 'field_forms_663df65851332',
											'label' => 'Заголовок',
											'name' => 'title',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => 'Новая сделка с сайта {{ site }}',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
										),
										array(
											'key' => 'field_forms_66604fa407aa6',
											'label' => 'Поля сделки',
											'name' => 'fields',
											'aria-label' => '',
											'type' => 'repeater',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'layout' => 'block',
											'min' => 0,
											'max' => 0,
											'collapsed' => 'field_forms_66604fb507aa7',
											'button_label' => 'Добавить поле',
											'rows_per_page' => 20,
											'acfe_repeater_stylised_button' => 0,
											'sub_fields' => array(
												array(
													'key' => 'field_668e62211dc51',
													'label' => 'Название поля',
													'name' => 'name',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
														'ASSIGNED_BY_ID' => 'Ответственный',
														'CATEGORY_ID' => 'Воронка',
														'STAGE_ID' => 'Этап сделки',
														'SOURCE_ID' => 'Источник',
														'PHONE' => 'Телефон',
														'EMAIL' => 'Email',
														'OPPORTUNITY' => 'Сумма',
														'CUSTOM' => 'Произвольное поле',
													),
													'default_value' => false,
													'return_format' => 'value',
													'multiple' => 0,
													'allow_custom' => 0,
													'search_placeholder' => '',
													'allow_null' => 0,
													'ui' => 1,
													'ajax' => 0,
													'placeholder' => '',
													'parent_repeater' => 'field_forms_66604fa407aa6',
												),
												array(
													'key' => 'field_forms_6661432b2de83',
													'label' => 'Произвольное поле',
													'name' => 'custom_field',
													'aria-label' => '',
													'type' => 'text',
													'instructions' => '',
													'required' => 0,
													'conditional_logic' => array(
														array(
															array(
																'field' => 'field_668e62211dc51',
																'operator' => '==',
																'value' => 'CUSTOM',
															),
														),
													),
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'default_value' => '',
													'maxlength' => '',
													'placeholder' => '',
													'prepend' => '',
													'append' => '',
													'parent_repeater' => 'field_forms_66604fa407aa6',
												),
												array(
													'key' => 'field_forms_666050a407aa8',
													'label' => 'Значение поля',
													'name' => 'value',
													'aria-label' => '',
													'type' => 'text',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'default_value' => '',
													'maxlength' => '',
													'placeholder' => '',
													'prepend' => '',
													'append' => '',
													'parent_repeater' => 'field_forms_66604fa407aa6',
												),
												array(
													'key' => 'field_693326666b0dd',
													'label' => 'Тип',
													'name' => 'type',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 0,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
														'text' => 'Текст',
														'number' => 'Число',
														'file' => 'Файл',
														'array' => 'Массив',
													),
													'default_value' => false,
													'return_format' => 'value',
													'multiple' => 0,
													'allow_custom' => 0,
													'search_placeholder' => '',
													'allow_null' => 0,
													'ui' => 0,
													'ajax' => 0,
													'placeholder' => '',
													'parent_repeater' => 'field_forms_66604fa407aa6',
												),
											),
										),
									),
									'min' => '',
									'max' => '1',
									'acfe_flexible_render_template' => false,
									'acfe_flexible_render_style' => false,
									'acfe_flexible_render_script' => false,
									'acfe_flexible_thumbnail' => false,
									'acfe_flexible_settings' => false,
									'acfe_flexible_settings_size' => false,
									'acfe_flexible_modal_edit_size' => false,
									'acfe_flexible_category' => false,
								),
								'layout_forms_663df65851333' => array(
									'key' => 'layout_forms_663df65851333',
									'name' => 'contact',
									'label' => 'Контакт',
									'display' => 'block',
									'sub_fields' => array(
										array(
											'key' => 'field_forms_663df65851334',
											'label' => 'Заголовок',
											'name' => 'title',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => '',
											'required' => 1,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => 'Новое сообщение с сайта',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
										),
										array(
											'key' => 'field_forms_66604dea581d5',
											'label' => 'Поля контакта',
											'name' => 'fields',
											'aria-label' => '',
											'type' => 'repeater',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'layout' => 'table',
											'min' => 0,
											'max' => 0,
											'collapsed' => 'field_forms_66604e04581d6',
											'button_label' => 'Добавить поле',
											'rows_per_page' => 20,
											'acfe_repeater_stylised_button' => 0,
											'sub_fields' => array(
												array(
													'key' => 'field_forms_66604e04581d6',
													'label' => 'Название поля',
													'name' => 'name',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
														'EMAIL' => 'Email',
														'PHONE' => 'Телефон',
													),
													'default_value' => false,
													'return_format' => 'value',
													'multiple' => 0,
													'allow_custom' => 0,
													'search_placeholder' => '',
													'allow_null' => 0,
													'ui' => 1,
													'ajax' => 0,
													'placeholder' => '',
													'parent_repeater' => 'field_forms_66604dea581d5',
												),
												array(
													'key' => 'field_forms_66604e28581d7',
													'label' => 'Значение',
													'name' => 'value',
													'aria-label' => '',
													'type' => 'text',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'default_value' => '',
													'maxlength' => '',
													'placeholder' => '',
													'prepend' => '',
													'append' => '',
													'parent_repeater' => 'field_forms_66604dea581d5',
												),
												array(
													'key' => 'field_6933268e6b0de',
													'label' => 'Тип',
													'name' => 'type',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 0,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '25',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
														'text' => 'Текст',
														'number' => 'Число',
														'file' => 'Файл',
														'array' => 'Массив',
													),
													'default_value' => false,
													'return_format' => 'value',
													'multiple' => 0,
													'allow_custom' => 0,
													'search_placeholder' => '',
													'allow_null' => 0,
													'ui' => 0,
													'ajax' => 0,
													'placeholder' => '',
													'parent_repeater' => 'field_forms_66604dea581d5',
												),
											),
										),
									),
									'min' => '',
									'max' => '1',
									'acfe_flexible_render_template' => false,
									'acfe_flexible_render_style' => false,
									'acfe_flexible_render_script' => false,
									'acfe_flexible_thumbnail' => false,
									'acfe_flexible_settings' => false,
									'acfe_flexible_settings_size' => false,
									'acfe_flexible_modal_edit_size' => false,
									'acfe_flexible_category' => false,
								),
								'layout_forms_663df6075132f' => array(
									'key' => 'layout_forms_663df6075132f',
									'name' => 'comment',
									'label' => 'Комментарий',
									'display' => 'block',
									'sub_fields' => array(
										array(
											'key' => 'field_forms_663df60751330',
											'label' => 'Текст комментария',
											'name' => 'content',
											'aria-label' => '',
											'type' => 'textarea',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => 'Данные формы:
{{ __fields }}

Страница: {{ __page }}
Заголовок: {{ __title }}
Форма: {{ __form }}
Запрос: {{ __query }}
Браузер: {{ __browser }}
IP: {{ __ip }}',
											'maxlength' => '',
											'rows' => 10,
											'placeholder' => '',
											'new_lines' => '',
											'acfe_textarea_code' => 0,
										),
										array(
											'key' => 'field_6791b55728c07',
											'label' => 'Показывать строки с пустыми значениями полей',
											'name' => 'show_empty_fields',
											'aria-label' => '',
											'type' => 'true_false',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'message' => '',
											'default_value' => 0,
											'ui_on_text' => '',
											'ui_off_text' => '',
											'ui' => 1,
										),
									),
									'min' => '',
									'max' => '1',
									'acfe_flexible_render_template' => false,
									'acfe_flexible_render_style' => false,
									'acfe_flexible_render_script' => false,
									'acfe_flexible_thumbnail' => false,
									'acfe_flexible_settings' => false,
									'acfe_flexible_settings_size' => false,
									'acfe_flexible_modal_edit_size' => false,
									'acfe_flexible_category' => false,
								),
							),
							'min' => '',
							'max' => '',
							'button_label' => 'Добавить сущность',
							'acfe_flexible_stylised_button' => false,
							'acfe_flexible_hide_empty_message' => false,
							'acfe_flexible_empty_message' => '',
							'acfe_flexible_layouts_templates' => false,
							'acfe_flexible_layouts_previews' => false,
							'acfe_flexible_layouts_placeholder' => false,
							'acfe_flexible_layouts_thumbnails' => false,
							'acfe_flexible_modal_settings' => array(
								'acfe_flexible_modal_settings_enabled' => false,
								'acfe_flexible_modal_settings_size' => 'large',
								'acfe_flexible_modal_settings_close' => true,
								'acfe_flexible_modal_settings_close_label' => '',
							),
							'acfe_flexible_async' => array(
							),
							'acfe_flexible_add_actions' => array(
							),
							'acfe_flexible_close_button_label' => '',
							'acfe_flexible_remove_button' => array(
							),
							'acfe_flexible_remove_top_actions' => array(
							),
							'acfe_flexible_layouts_state' => false,
							'acfe_flexible_modal_edit' => array(
								'acfe_flexible_modal_edit_enabled' => false,
								'acfe_flexible_modal_edit_size' => 'large',
							),
							'acfe_flexible_modal' => array(
								'acfe_flexible_modal_enabled' => false,
								'acfe_flexible_modal_title' => false,
								'acfe_flexible_modal_size' => 'xlarge',
								'acfe_flexible_modal_col' => '4',
								'acfe_flexible_modal_categories' => false,
							),
						),
					),
					'min' => '',
					'max' => '',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
				'layout_forms_663dce55a9372' => array(
					'key' => 'layout_forms_663dce55a9372',
					'name' => '__amo',
					'label' => 'Отправка в AMO',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_forms_663de5e6e32e5',
							'label' => 'Включить',
							'name' => 'enabled',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
						),
						array(
							'key' => 'field_forms_663de5d7e32e4',
							'label' => 'Отбор по селектору',
							'name' => 'selector',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'form',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6669b14e560d2',
							'label' => 'Поддомен АМО',
							'name' => 'domen',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6669b15f560d3',
							'label' => 'Секретный ключ',
							'name' => 'secret_key',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6669b1ae560d4',
							'label' => 'ID интеграции',
							'name' => 'client_id',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6669b1cf560d5',
							'label' => 'Код авторизации',
							'name' => 'auth_code',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6672f3860fe5a',
							'label' => 'Редирект',
							'name' => 'redirect',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_667e8769bcca2',
							'label' => 'Загрузить поля из АМО',
							'name' => '',
							'aria-label' => '',
							'type' => 'message',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '<a href="" class="AMOFields button button-primary button-large">Обновить поля</a>&nbsp;&nbsp;<a href="" class="AMOToken button button-primary button-large">Получить токен</a>',
							'new_lines' => '',
							'esc_html' => 0,
						),
						array(
							'key' => 'field_6669b38168a08',
							'label' => 'Создать в AMO',
							'name' => 'entities',
							'aria-label' => '',
							'type' => 'flexible_content',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'layouts' => array(
								'layout_667e6a2bc36fb' => array(
									'key' => 'layout_667e6a2bc36fb',
									'name' => 'contact',
									'label' => 'Контакт',
									'display' => 'block',
									'sub_fields' => array(
										array(
											'key' => 'field_667e6a2bc36fc',
											'label' => 'Заголовок',
											'name' => 'title',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
										),
										array(
											'key' => 'field_667e7d1f125ba',
											'label' => 'Произвольные поля',
											'name' => 'fields',
											'aria-label' => '',
											'type' => 'repeater',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'layout' => 'table',
											'min' => 0,
											'max' => 0,
											'collapsed' => 'field_667e7d3d125bb',
											'button_label' => 'Добавить',
											'rows_per_page' => 20,
											'acfe_repeater_stylised_button' => 0,
											'sub_fields' => array(
												array(
													'key' => 'field_667e7d3d125bb',
													'label' => 'Название поля',
													'name' => 'field',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
													),
													'default_value' => false,
													'return_format' => 'array',
													'multiple' => 0,
													'allow_null' => 0,
													'ui' => 0,
													'ajax' => 0,
													'placeholder' => '',
													'parent_repeater' => 'field_667e7d1f125ba',
													'allow_custom' => 0,
													'search_placeholder' => '',
													'create_options' => 0,
													'save_options' => 0,
												),
												array(
													'key' => 'field_667e7d4c125bc',
													'label' => 'Значение',
													'name' => 'value',
													'aria-label' => '',
													'type' => 'text',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '',
														'class' => '',
														'id' => '',
													),
													'default_value' => '',
													'maxlength' => '',
													'placeholder' => '',
													'prepend' => '',
													'append' => '',
													'parent_repeater' => 'field_667e7d1f125ba',
												),
											),
										),
									),
									'min' => '',
									'max' => '1',
									'acfe_flexible_render_template' => false,
									'acfe_flexible_render_style' => false,
									'acfe_flexible_render_script' => false,
									'acfe_flexible_thumbnail' => false,
									'acfe_flexible_settings' => false,
									'acfe_flexible_settings_size' => false,
									'acfe_flexible_modal_edit_size' => false,
									'acfe_flexible_category' => false,
								),
								'layout_6669b5a4b2699' => array(
									'key' => 'layout_6669b5a4b2699',
									'name' => 'lead',
									'label' => 'Сделка',
									'display' => 'block',
									'sub_fields' => array(
										array(
											'key' => 'field_6669b5a4b269a',
											'label' => 'Заголовок',
											'name' => 'title',
											'aria-label' => '',
											'type' => 'text',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '66',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'maxlength' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
										),
										array(
											'key' => 'field_668421a5e647b',
											'label' => 'Сумма',
											'name' => 'price',
											'aria-label' => '',
											'type' => 'number',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '33',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'min' => '',
											'max' => '',
											'placeholder' => '',
											'step' => '',
											'prepend' => '',
											'append' => '',
										),
										array(
											'key' => 'field_6678119b3166b',
											'label' => 'Воронка',
											'name' => 'pipeline_id',
											'aria-label' => '',
											'type' => 'select',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '33',
												'class' => '',
												'id' => '',
											),
											'choices' => array(
											),
											'default_value' => false,
											'return_format' => 'value',
											'multiple' => 0,
											'allow_null' => 1,
											'ui' => 1,
											'ajax' => 0,
											'placeholder' => '',
											'allow_custom' => 0,
											'search_placeholder' => '',
											'create_options' => 0,
											'save_options' => 0,
										),
										array(
											'key' => 'field_667811bf3166d',
											'label' => 'Статус сделки',
											'name' => 'status_id',
											'aria-label' => '',
											'type' => 'select',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '33',
												'class' => '',
												'id' => '',
											),
											'choices' => array(
											),
											'default_value' => false,
											'return_format' => 'value',
											'multiple' => 0,
											'allow_null' => 1,
											'ui' => 1,
											'ajax' => 0,
											'placeholder' => '',
											'allow_custom' => 0,
											'search_placeholder' => '',
											'create_options' => 0,
											'save_options' => 0,
										),
										array(
											'key' => 'field_667811563166a',
											'label' => 'Ответственный',
											'name' => 'responsible_user_id',
											'aria-label' => '',
											'type' => 'select',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '33',
												'class' => '',
												'id' => '',
											),
											'choices' => array(
											),
											'default_value' => false,
											'return_format' => 'value',
											'multiple' => 0,
											'allow_null' => 1,
											'ui' => 1,
											'ajax' => 0,
											'placeholder' => '',
											'allow_custom' => 0,
											'search_placeholder' => '',
											'create_options' => 0,
											'save_options' => 0,
										),
										array(
											'key' => 'field_6673b1db22869',
											'label' => 'Произвольные поля',
											'name' => 'fields',
											'aria-label' => '',
											'type' => 'repeater',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'layout' => 'table',
											'min' => 0,
											'max' => 0,
											'collapsed' => 'field_6673b1db2286a',
											'button_label' => 'Добавить поле',
											'rows_per_page' => 20,
											'acfe_repeater_stylised_button' => 0,
											'sub_fields' => array(
												array(
													'key' => 'field_6673b1db2286a',
													'label' => 'Название поля',
													'name' => 'field',
													'aria-label' => '',
													'type' => 'select',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '33',
														'class' => '',
														'id' => '',
													),
													'choices' => array(
													),
													'default_value' => false,
													'return_format' => 'array',
													'multiple' => 0,
													'allow_null' => 0,
													'ui' => 1,
													'ajax' => 0,
													'placeholder' => '',
													'parent_repeater' => 'field_6673b1db22869',
													'allow_custom' => 0,
													'search_placeholder' => '',
													'create_options' => 0,
													'save_options' => 0,
												),
												array(
													'key' => 'field_6673b1db2286b',
													'label' => 'Значение',
													'name' => 'value',
													'aria-label' => '',
													'type' => 'text',
													'instructions' => '',
													'required' => 1,
													'conditional_logic' => 0,
													'wrapper' => array(
														'width' => '',
														'class' => '',
														'id' => '',
													),
													'default_value' => '',
													'maxlength' => '',
													'placeholder' => '',
													'prepend' => '',
													'append' => '',
													'parent_repeater' => 'field_6673b1db22869',
												),
											),
										),
									),
									'min' => '',
									'max' => '1',
									'acfe_flexible_render_template' => false,
									'acfe_flexible_render_style' => false,
									'acfe_flexible_render_script' => false,
									'acfe_flexible_thumbnail' => false,
									'acfe_flexible_settings' => false,
									'acfe_flexible_settings_size' => false,
									'acfe_flexible_modal_edit_size' => false,
									'acfe_flexible_category' => false,
								),
								'layout_6669b5c5b269b' => array(
									'key' => 'layout_6669b5c5b269b',
									'name' => 'note',
									'label' => 'Примечание',
									'display' => 'block',
									'sub_fields' => array(
										array(
											'key' => 'field_6669b5c5b269c',
											'label' => 'Текст комментария',
											'name' => 'content',
											'aria-label' => '',
											'type' => 'textarea',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'maxlength' => '',
											'rows' => '',
											'placeholder' => '',
											'new_lines' => '',
											'acfe_textarea_code' => 0,
										),
										array(
											'key' => 'field_6791b57528c08',
											'label' => 'Показывать строки с пустыми значениями полей',
											'name' => 'show_empty_fields',
											'aria-label' => '',
											'type' => 'true_false',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'message' => '',
											'default_value' => 0,
											'ui_on_text' => '',
											'ui_off_text' => '',
											'ui' => 1,
										),
									),
									'min' => '',
									'max' => '1',
									'acfe_flexible_render_template' => false,
									'acfe_flexible_render_style' => false,
									'acfe_flexible_render_script' => false,
									'acfe_flexible_thumbnail' => false,
									'acfe_flexible_settings' => false,
									'acfe_flexible_settings_size' => false,
									'acfe_flexible_modal_edit_size' => false,
									'acfe_flexible_category' => false,
								),
							),
							'min' => '',
							'max' => '',
							'button_label' => 'Добавить сущность',
							'acfe_flexible_advanced' => false,
							'acfe_flexible_stylised_button' => false,
							'acfe_flexible_hide_empty_message' => false,
							'acfe_flexible_empty_message' => '',
							'acfe_flexible_layouts_templates' => false,
							'acfe_flexible_layouts_previews' => false,
							'acfe_flexible_layouts_placeholder' => false,
							'acfe_flexible_layouts_thumbnails' => false,
							'acfe_flexible_modal_settings' => array(
								'acfe_flexible_modal_settings_enabled' => false,
								'acfe_flexible_modal_settings_size' => 'large',
								'acfe_flexible_modal_settings_close' => true,
								'acfe_flexible_modal_settings_close_label' => '',
							),
							'acfe_flexible_async' => array(
							),
							'acfe_flexible_add_actions' => array(
							),
							'acfe_flexible_close_button_label' => '',
							'acfe_flexible_remove_button' => array(
							),
							'acfe_flexible_remove_top_actions' => array(
							),
							'acfe_flexible_layouts_state' => false,
							'acfe_flexible_modal_edit' => array(
								'acfe_flexible_modal_edit_enabled' => false,
								'acfe_flexible_modal_edit_size' => 'large',
							),
							'acfe_flexible_modal' => array(
								'acfe_flexible_modal_enabled' => false,
								'acfe_flexible_modal_title' => false,
								'acfe_flexible_modal_size' => 'xlarge',
								'acfe_flexible_modal_col' => '4',
								'acfe_flexible_modal_categories' => false,
							),
						),
					),
					'min' => '',
					'max' => '',
					'acfe_flexible_render_template' => false,
					'acfe_flexible_render_style' => false,
					'acfe_flexible_render_script' => false,
					'acfe_flexible_thumbnail' => false,
					'acfe_flexible_settings' => false,
					'acfe_flexible_settings_size' => false,
					'acfe_flexible_modal_edit_size' => false,
					'acfe_flexible_category' => false,
				),
			),
			'min' => '',
			'max' => '',
			'button_label' => 'Добавить расширение',
			'acfe_flexible_stylised_button' => false,
			'acfe_flexible_hide_empty_message' => false,
			'acfe_flexible_empty_message' => '',
			'acfe_flexible_layouts_templates' => false,
			'acfe_flexible_layouts_previews' => false,
			'acfe_flexible_layouts_placeholder' => false,
			'acfe_flexible_layouts_thumbnails' => false,
			'acfe_flexible_modal_settings' => array(
				'acfe_flexible_modal_settings_enabled' => false,
				'acfe_flexible_modal_settings_size' => 'large',
				'acfe_flexible_modal_settings_close' => true,
				'acfe_flexible_modal_settings_close_label' => '',
			),
			'acfe_flexible_async' => array(
			),
			'acfe_flexible_add_actions' => array(
			),
			'acfe_flexible_close_button_label' => '',
			'acfe_flexible_remove_button' => array(
			),
			'acfe_flexible_remove_top_actions' => array(
			),
			'acfe_flexible_layouts_state' => false,
			'acfe_flexible_modal_edit' => array(
				'acfe_flexible_modal_edit_enabled' => false,
				'acfe_flexible_modal_edit_size' => 'large',
			),
			'acfe_flexible_modal' => array(
				'acfe_flexible_modal_enabled' => false,
				'acfe_flexible_modal_title' => false,
				'acfe_flexible_modal_size' => 'xlarge',
				'acfe_flexible_modal_col' => '4',
				'acfe_flexible_modal_categories' => false,
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'wtw_forms',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
	'acfe_display_title' => '',
	'acfe_autosync' => '',
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
) );
} );

?><?php
?><?php

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('justvalidate', get_stylesheet_directory_uri() . '/js/just-validate.js', [], null, ['strategy' => 'defer']);
});
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'sekcii-glavnoj-stranicy',
'title' => 'Секции главной страницы',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'index.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Блоки Главной страницы',
    'name' => 'bloki_glavnoj_stranicy',
    'key' => 'field_bloki_glavnoj_stranicy',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая Секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение для ПК',
            'name' => 'izobrazhenie_dlya_pk',
            'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_izobrazhenie_dlya_pk',
            'type' => 'image',
            'return_format' => 'array',
          ),
          1 => 
          array (
            'label' => 'Изображение для моб',
            'name' => 'izobrazhenie_dlya_mob',
            'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_izobrazhenie_dlya_mob',
            'type' => 'image',
            'return_format' => 'array',
          ),
          2 => 
          array (
            'label' => 'Мини текст выше заголовка',
            'name' => 'mini_tekst_vyshe_zagolovka',
            'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_mini_tekst_vyshe_zagolovka',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Заголовок H1',
            'name' => 'zagolovok_h1',
            'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_zagolovok_h1',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Мини текст ниже заголовка',
            'name' => 'mini_tekst_nizhe_zagolovka',
            'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_mini_tekst_nizhe_zagolovka',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Кнопка на первом экране',
            'name' => 'knopka_na_pervom_ekrane',
            'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_knopka_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_knopka_na_pervom_ekrane_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_glavnoj_stranicy_pervaya_sekciya_knopka_na_pervom_ekrane_knopka',
                'type' => 'text',
              ),
            ),
          ),
          6 => NULL,
          7 => NULL,
          8 => NULL,
          9 => NULL,
          10 => NULL,
          11 => NULL,
        ),
      ),
      5 => 
      array (
        'label' => 'Секция предложение гостям',
        'name' => 'sekciya_predlozhenie_gostyam',
        'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст перед заголовком',
            'name' => 'mini_tekst_pered_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_mini_tekst_pered_zagolovkom',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Ссылка 1',
            'name' => 'ssylka_1',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_1',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'url',
          ),
          5 => 
          array (
            'label' => 'Заголовок Н3 1',
            'name' => 'zagolovok_n3_1',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_1',
            'type' => 'text',
          ),
          6 => 
          array (
            'label' => 'Описание 1',
            'name' => 'opisanie_1',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_1',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          7 => 
          array (
            'label' => 'Ссылка 2',
            'name' => 'ssylka_2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_2',
            'type' => 'text',
          ),
          8 => 
          array (
            'label' => 'Изображение 2',
            'name' => 'izobrazhenie_2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_2',
            'type' => 'image',
            'return_format' => 'url',
          ),
          9 => 
          array (
            'label' => 'Заголовок Н3 2',
            'name' => 'zagolovok_n3_2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_2',
            'type' => 'text',
          ),
          10 => 
          array (
            'label' => 'Описание 2',
            'name' => 'opisanie_2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_2',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          11 => 
          array (
            'label' => 'Ссылка 3',
            'name' => 'ssylka_3',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_3',
            'type' => 'text',
          ),
          12 => 
          array (
            'label' => 'Изображение 3',
            'name' => 'izobrazhenie_3',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_3',
            'type' => 'image',
            'return_format' => 'url',
          ),
          13 => 
          array (
            'label' => 'Заголовок Н3 3',
            'name' => 'zagolovok_n3_3',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_3',
            'type' => 'text',
          ),
          14 => 
          array (
            'label' => 'Описание 3',
            'name' => 'opisanie_3',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_3',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          15 => 
          array (
            'label' => 'Ссылка 4',
            'name' => 'ssylka_4',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_4',
            'type' => 'text',
          ),
          16 => 
          array (
            'label' => 'Изображение 4',
            'name' => 'izobrazhenie_4',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_4',
            'type' => 'image',
            'return_format' => 'url',
          ),
          17 => 
          array (
            'label' => 'Заголовок Н3 4',
            'name' => 'zagolovok_n3_4',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_4',
            'type' => 'text',
          ),
          18 => 
          array (
            'label' => 'Описание 4',
            'name' => 'opisanie_4',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_4',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          19 => 
          array (
            'label' => 'Ссылка 5',
            'name' => 'ssylka_5',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_5',
            'type' => 'text',
          ),
          20 => 
          array (
            'label' => 'Изображение 5',
            'name' => 'izobrazhenie_5',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_5',
            'type' => 'image',
            'return_format' => 'url',
          ),
          21 => 
          array (
            'label' => 'Заголовок Н3 5',
            'name' => 'zagolovok_n3_5',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_5',
            'type' => 'text',
          ),
          22 => 
          array (
            'label' => 'Описание 5',
            'name' => 'opisanie_5',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_5',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          23 => 
          array (
            'label' => 'Ссылка 6',
            'name' => 'ssylka_6',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_6',
            'type' => 'text',
          ),
          24 => 
          array (
            'label' => 'Изображение 6',
            'name' => 'izobrazhenie_6',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_6',
            'type' => 'image',
            'return_format' => 'url',
          ),
          25 => 
          array (
            'label' => 'Заголовок Н3 6',
            'name' => 'zagolovok_n3_6',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_6',
            'type' => 'text',
          ),
          26 => 
          array (
            'label' => 'Описание 6',
            'name' => 'opisanie_6',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_6',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          27 => 
          array (
            'label' => 'Ссылка 7',
            'name' => 'ssylka_7',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_7',
            'type' => 'text',
          ),
          28 => 
          array (
            'label' => 'Изображение 7',
            'name' => 'izobrazhenie_7',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_7',
            'type' => 'image',
            'return_format' => 'url',
          ),
          29 => 
          array (
            'label' => 'Заголовок Н3 7',
            'name' => 'zagolovok_n3_7',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_7',
            'type' => 'text',
          ),
          30 => 
          array (
            'label' => 'Описание 7',
            'name' => 'opisanie_7',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_7',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          31 => 
          array (
            'label' => 'Ссылка 8',
            'name' => 'ssylka_8',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_ssylka_8',
            'type' => 'text',
          ),
          32 => 
          array (
            'label' => 'Изображение 8',
            'name' => 'izobrazhenie_8',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_izobrazhenie_8',
            'type' => 'image',
            'return_format' => 'url',
          ),
          33 => 
          array (
            'label' => 'Заголовок Н3 8',
            'name' => 'zagolovok_n3_8',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_zagolovok_n3_8',
            'type' => 'text',
          ),
          34 => 
          array (
            'label' => 'Описание 8',
            'name' => 'opisanie_8',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_predlozhenie_gostyam_opisanie_8',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Секция О нас',
        'name' => 'sekciya_o_nas',
        'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение 2',
            'name' => 'izobrazhenie_2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_izobrazhenie_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          1 => 
          array (
            'label' => 'Изображение 4',
            'name' => 'izobrazhenie_4',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_izobrazhenie_4',
            'type' => 'image',
            'return_format' => 'array',
          ),
          2 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          3 => 
          array (
            'label' => 'Изображение 3',
            'name' => 'izobrazhenie_3',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_izobrazhenie_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
          4 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_opisanie_mini',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_zagolovok_n2',
            'type' => 'text',
          ),
          6 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          7 => 
          array (
            'label' => 'Ссылка кнопки',
            'name' => 'ssylka_knopki',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_ssylka_knopki',
            'type' => 'text',
          ),
          8 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_o_nas_knopka',
            'type' => 'text',
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Секция с карточками',
        'name' => 'sekciya_s_kartochkami',
        'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Карточка 1',
            'name' => 'kartochka_1',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 1',
                'name' => 'izobrazhenie_1',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_1_izobrazhenie_1',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_1_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание ПТ',
                'name' => 'opisanie_pt',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_1_opisanie_pt',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          1 => 
          array (
            'label' => 'Карточка 2',
            'name' => 'kartochka_2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 2',
                'name' => 'izobrazhenie_2',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_2_izobrazhenie_2',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_2_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание ПТ',
                'name' => 'opisanie_pt',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_2_opisanie_pt',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Карточка 3',
            'name' => 'kartochka_3',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 3',
                'name' => 'izobrazhenie_3',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_3_izobrazhenie_3',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_3_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание ПТ',
                'name' => 'opisanie_pt',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_3_opisanie_pt',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          3 => 
          array (
            'label' => 'Карточка 4',
            'name' => 'kartochka_4',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_4',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 4',
                'name' => 'izobrazhenie_4',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_4_izobrazhenie_4',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_4_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание ПТ',
                'name' => 'opisanie_pt',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_4_opisanie_pt',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Карточка 5',
            'name' => 'kartochka_5',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_5',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 5',
                'name' => 'izobrazhenie_5',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_5_izobrazhenie_5',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_5_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание ПТ',
                'name' => 'opisanie_pt',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_kartochkami_kartochka_5_opisanie_pt',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
        ),
      ),
      8 => 
      array (
        'label' => 'Секция с бронью',
        'name' => 'sekciya_s_bronyu',
        'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
          3 => 
          array (
            'label' => 'Скрыть скидку',
            'name' => 'skryt_skidku',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu_skryt_skidku',
            'type' => 'true_false',
            'ui' => 1,
          ),
          4 => 
          array (
            'label' => 'Скидка',
            'name' => 'skidka',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_s_bronyu_skidka',
            'type' => 'text',
          ),
        ),
      ),
      9 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      10 => 
      array (
        'label' => 'Кнопка под блоком бронирования',
        'name' => 'knopka_pod_blokom_bronirovaniya',
        'key' => 'field_bloki_glavnoj_stranicy_knopka_pod_blokom_bronirovaniya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Ссылка кнопки  БР',
            'name' => 'ssylka_knopki__br',
            'key' => 'field_bloki_glavnoj_stranicy_knopka_pod_blokom_bronirovaniya_ssylka_knopki__br',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Кнопка  БР',
            'name' => 'knopka__br',
            'key' => 'field_bloki_glavnoj_stranicy_knopka_pod_blokom_bronirovaniya_knopka__br',
            'type' => 'text',
          ),
        ),
      ),
      11 => 
      array (
        'label' => 'Банный комплекс',
        'name' => 'bannyj_kompleks',
        'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Заголовок Н3',
            'name' => 'zagolovok_n3',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_zagolovok_n3',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_opisanie_mini',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          4 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Характеристика',
                'name' => 'harakteristika',
                'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_harakteristiki_harakteristika',
                'type' => 'text',
              ),
            ),
          ),
          5 => 
          array (
            'label' => 'Скрыть кнопку',
            'name' => 'skryt_knopku',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_skryt_knopku',
            'type' => 'true_false',
            'ui' => 1,
          ),
          6 => 
          array (
            'label' => 'Ссылка кнопки',
            'name' => 'ssylka_knopki',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_ssylka_knopki',
            'type' => 'text',
          ),
          7 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_knopka',
            'type' => 'text',
          ),
          8 => 
          array (
            'label' => 'Скрыть кнопку 2',
            'name' => 'skryt_knopku_2',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_skryt_knopku_2',
            'type' => 'true_false',
            'ui' => 1,
          ),
          9 => 
          array (
            'label' => 'Ссылка кнопки 2',
            'name' => 'ssylka_knopki_2',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_ssylka_knopki_2',
            'type' => 'text',
          ),
          10 => 
          array (
            'label' => 'Кнопка 2',
            'name' => 'knopka_2',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_knopka_2',
            'type' => 'text',
          ),
          11 => 
          array (
            'label' => 'Добавить слайды бани',
            'name' => 'dobavit_slajdy_bani',
            'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_dobavit_slajdy_bani',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_bloki_glavnoj_stranicy_bannyj_kompleks_dobavit_slajdy_bani_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
        ),
      ),
      12 => 
      array (
        'label' => 'Верховая езда',
        'name' => 'verhovaya_ezda',
        'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание',
            'name' => 'opisanie',
            'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda_opisanie',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_bloki_glavnoj_stranicy_verhovaya_ezda_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      13 => 
      array (
        'label' => 'Питание',
        'name' => 'pitanie',
        'key' => 'field_bloki_glavnoj_stranicy_pitanie',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Карточка 1',
            'name' => 'kartochka_1',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_1_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_1_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Карточка 2',
            'name' => 'kartochka_2',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_2_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_2_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          5 => 
          array (
            'label' => 'Карточка 3',
            'name' => 'kartochka_3',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_3_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_3_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          6 => 
          array (
            'label' => 'Карточка 4',
            'name' => 'kartochka_4',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_4',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_4_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_4_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          7 => 
          array (
            'label' => 'Карточка 5',
            'name' => 'kartochka_5',
            'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_5',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_5_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_glavnoj_stranicy_pitanie_kartochka_5_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
        ),
      ),
      14 => 
      array (
        'label' => 'Блок с статьями',
        'name' => 'blok_s_statyami',
        'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Скрыть блок со слайдером',
            'name' => 'skryt_blok_so_slajderom',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_skryt_blok_so_slajderom',
            'type' => 'true_false',
            'ui' => 1,
          ),
          1 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_mini_tekst',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_zagolovok_n2',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      15 => 
      array (
        'label' => 'Экскурсии',
        'name' => 'ekskursii',
        'key' => 'field_bloki_glavnoj_stranicy_ekskursii',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          3 => 
          array (
            'label' => 'Заголовок Н4 1',
            'name' => 'zagolovok_n4_1',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_zagolovok_n4_1',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Описание 1',
            'name' => 'opisanie_1',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_opisanie_1',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          5 => 
          array (
            'label' => 'Изображение 2',
            'name' => 'izobrazhenie_2',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_izobrazhenie_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          6 => 
          array (
            'label' => 'Заголовок Н4 2',
            'name' => 'zagolovok_n4_2',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_zagolovok_n4_2',
            'type' => 'text',
          ),
          7 => 
          array (
            'label' => 'Описание 2',
            'name' => 'opisanie_2',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_opisanie_2',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          8 => 
          array (
            'label' => 'Изображение 3',
            'name' => 'izobrazhenie_3',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_izobrazhenie_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
          9 => 
          array (
            'label' => 'Заголовок Н4 3',
            'name' => 'zagolovok_n4_3',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_zagolovok_n4_3',
            'type' => 'text',
          ),
          10 => 
          array (
            'label' => 'Описание 3',
            'name' => 'opisanie_3',
            'key' => 'field_bloki_glavnoj_stranicy_ekskursii_opisanie_3',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      16 => 
      array (
        'label' => 'Информация для гостей',
        'name' => 'informaciya_dlya_gostej',
        'key' => 'field_bloki_glavnoj_stranicy_informaciya_dlya_gostej',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение для гостей',
            'name' => 'izobrazhenie_dlya_gostej',
            'key' => 'field_bloki_glavnoj_stranicy_informaciya_dlya_gostej_izobrazhenie_dlya_gostej',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_informaciya_dlya_gostej_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание',
            'name' => 'opisanie',
            'key' => 'field_bloki_glavnoj_stranicy_informaciya_dlya_gostej_opisanie',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Список',
            'name' => 'spisok',
            'key' => 'field_bloki_glavnoj_stranicy_informaciya_dlya_gostej_spisok',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'текст',
                'name' => 'tekst',
                'key' => 'field_bloki_glavnoj_stranicy_informaciya_dlya_gostej_spisok_tekst',
                'type' => 'text',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Изображение для гостей',
            'name' => 'izobrazhenie_dlya_gostej',
            'key' => 'field_bloki_glavnoj_stranicy_informaciya_dlya_gostej_izobrazhenie_dlya_gostej',
            'type' => 'image',
            'return_format' => 'url',
          ),
        ),
      ),
      17 => 
      array (
        'label' => 'Схема',
        'name' => 'shema',
        'key' => 'field_bloki_glavnoj_stranicy_shema',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Схема проезда',
            'name' => 'shema_proezda',
            'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Схема проезда',
            'name' => 'shema_proezda',
            'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda',
            'type' => 'image',
            'return_format' => 'url',
          ),
          2 => 
          array (
            'label' => 'Схема проезда в тексте',
            'name' => 'shema_proezda_v_tekste',
            'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_zagolovok_n2',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Схема проезда',
                'name' => 'shema_proezda',
                'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_shema_proezda',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Текст',
                    'name' => 'tekst',
                    'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_shema_proezda_tekst',
                    'type' => 'text',
                  ),
                  1 => 
                  array (
                    'label' => 'Описание',
                    'name' => 'opisanie',
                    'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_shema_proezda_opisanie',
                    'type' => 'text',
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Последний пункт',
                'name' => 'poslednij_punkt',
                'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_poslednij_punkt',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Блок трансфер',
                'name' => 'blok_transfer',
                'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_blok_transfer',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Тест',
                    'name' => 'test',
                    'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_blok_transfer_test',
                    'type' => 'text',
                  ),
                  1 => 
                  array (
                    'label' => 'Описание трансфер',
                    'name' => 'opisanie_transfer',
                    'key' => 'field_bloki_glavnoj_stranicy_shema_shema_proezda_v_tekste_blok_transfer_opisanie_transfer',
                    'type' => 'textarea',
                    'new_lines' => 'br',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      18 => 
      array (
        'label' => 'Секция отзывов',
        'name' => 'sekciya_otzyvov',
        'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Описание',
            'name' => 'opisanie',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_opisanie',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Оценка в ЯД',
            'name' => 'ocenka_v_yad',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_ocenka_v_yad',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Добавить отзыв',
            'name' => 'dobavit_otzyv',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_dobavit_otzyv',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Аватвр',
                'name' => 'avatvr',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_dobavit_otzyv_avatvr',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Имя написавшего, фамилия, ник',
                'name' => 'imya_napisavshego,_familiya,_nik',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_dobavit_otzyv_imya_napisavshego,_familiya,_nik',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Дата',
                'name' => 'data',
                'instructions' => '28 янв. 2025 г.',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_dobavit_otzyv_data',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Отзыв',
                'name' => 'otzyv',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_dobavit_otzyv_otzyv',
                'type' => 'text',
              ),
              4 => 
              array (
                'label' => 'Ссылка на отзыв',
                'name' => 'ssylka_na_otzyv',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_otzyvov_dobavit_otzyv_ssylka_na_otzyv',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      19 => 
      array (
        'label' => 'Скрыть блок',
        'name' => 'skryt_blok',
        'key' => 'field_bloki_glavnoj_stranicy_skryt_blok',
        'type' => 'true_false',
        'ui' => 1,
      ),
      20 => 
      array (
        'label' => 'Секция работы',
        'name' => 'sekciya_raboty',
        'key' => 'field_bloki_glavnoj_stranicy_sekciya_raboty',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_raboty_opisanie_mini',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_raboty_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_raboty_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_raboty_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_raboty_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_glavnoj_stranicy_sekciya_raboty_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      21 => 
      array (
        'label' => 'Скрыть блок СЕО',
        'name' => 'skryt_blok_seo',
        'key' => 'field_bloki_glavnoj_stranicy_skryt_blok_seo',
        'type' => 'true_false',
        'ui' => 1,
      ),
      22 => 
      array (
        'label' => 'Секция CEO',
        'name' => 'sekciya_ceo',
        'key' => 'field_bloki_glavnoj_stranicy_sekciya_ceo',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Дополнительный Текст',
            'name' => 'dopolnitelnyj_tekst',
            'key' => 'field_bloki_glavnoj_stranicy_sekciya_ceo_dopolnitelnyj_tekst',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      23 => 
      array (
        'label' => 'Блок с статьями',
        'name' => 'blok_s_statyami',
        'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Скрыть текст',
            'name' => 'skryt_tekst',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_skryt_tekst',
            'type' => 'true_false',
            'ui' => 1,
          ),
          1 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_mini_tekst',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_zagolovok_n2',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_glavnoj_stranicy_blok_s_statyami_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      24 => NULL,
      25 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      26 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      27 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_bloki_glavnoj_stranicy_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      28 => NULL,
      29 => NULL,
      30 => NULL,
      31 => NULL,
      32 => NULL,
      33 => NULL,
      34 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      35 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'sekcii-chem-zanyatsya',
'title' => 'Секции Чем заняться',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'chem-zanyatsya.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Блоки страницы Чем заняться',
    'name' => 'bloki_stranicy_chem_zanyatsya',
    'key' => 'field_bloki_stranicy_chem_zanyatsya',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_bloki_stranicy_chem_zanyatsya_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение верхней секции',
            'name' => 'izobrazhenie_verhnej_sekcii',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_pervaya_sekciya_izobrazhenie_verhnej_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_pervaya_sekciya_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание ПТ',
            'name' => 'opisanie_pt',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_pervaya_sekciya_opisanie_pt',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Кнопка синяя',
            'name' => 'knopka_sinyaya',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_pervaya_sekciya_knopka_sinyaya',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_pervaya_sekciya_knopka_sinyaya_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_pervaya_sekciya_knopka_sinyaya_knopka',
                'type' => 'text',
              ),
            ),
          ),
          4 => NULL,
          5 => NULL,
          6 => NULL,
          7 => NULL,
          8 => NULL,
          9 => NULL,
        ),
      ),
      5 => 
      array (
        'label' => 'Секция Мы предлагаем',
        'name' => 'sekciya_my_predlagaem',
        'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_my_predlagaem',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_my_predlagaem_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_my_predlagaem_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Блок',
            'name' => 'blok',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_my_predlagaem_blok',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 1',
                'name' => 'izobrazhenie_1',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_my_predlagaem_blok_izobrazhenie_1',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Номер',
                'name' => 'nomer',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_my_predlagaem_blok_nomer',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Текст',
                'name' => 'tekst',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_my_predlagaem_blok_tekst',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Секция Досуга',
        'name' => 'sekciya_dosuga',
        'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_opisanie_mini',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Блоки досуга',
            'name' => 'bloki_dosuga',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Добавить класс',
                'name' => 'dobavit_klass',
                'instructions' => 'card-1, card-2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_dobavit_klass',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              3 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Текст',
                    'name' => 'tekst',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_spisok_tekst',
                    'type' => 'text',
                  ),
                ),
              ),
              4 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
              5 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_ssylka_knopki',
                'type' => 'text',
              ),
              6 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_knopka',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              8 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_ssylka_knopki_2',
                'type' => 'text',
              ),
              9 => 
              array (
                'label' => 'Кнопка 2',
                'name' => 'knopka_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_knopka_2',
                'type' => 'text',
              ),
              10 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              11 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              12 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              13 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_zagolovok_n2',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          6 => 
          array (
            'label' => 'Блоки Активный отдых',
            'name' => 'bloki_aktivnyj_otdyh',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Добавить класс',
                'name' => 'dobavit_klass',
                'instructions' => 'card-1, card-2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_dobavit_klass',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              3 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Текст',
                    'name' => 'tekst',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_spisok_tekst',
                    'type' => 'text',
                  ),
                ),
              ),
              4 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
              5 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_ssylka_knopki',
                'type' => 'text',
              ),
              6 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_knopka',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              8 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_ssylka_knopki_2',
                'type' => 'text',
              ),
              9 => 
              array (
                'label' => 'Кнопка 2',
                'name' => 'knopka_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_knopka_2',
                'type' => 'text',
              ),
              10 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              11 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              12 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekciya_dosuga_bloki_aktivnyj_otdyh_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
            ),
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Секции Активный отдых',
        'name' => 'sekcii_aktivnyj_otdyh',
        'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Текст мини',
            'name' => 'tekst_mini',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_tekst_mini',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Блок Отдых и развлечения',
            'name' => 'blok_otdyh_i_razvlecheniya',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание мини',
                'name' => 'opisanie_mini',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_opisanie_mini',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              3 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok_punkt',
                    'type' => 'text',
                  ),
                  1 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok_punkt',
                    'type' => 'text',
                  ),
                  2 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok_punkt',
                    'type' => 'text',
                  ),
                  3 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok_punkt',
                    'type' => 'text',
                  ),
                  4 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok_punkt',
                    'type' => 'text',
                  ),
                  5 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok_punkt',
                    'type' => 'text',
                  ),
                  6 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok_punkt',
                    'type' => 'text',
                  ),
                ),
              ),
              4 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
              5 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_ssylka_knopki',
                'type' => 'text',
              ),
              6 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_knopka',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              8 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_ssylka_knopki_2',
                'type' => 'text',
              ),
              9 => 
              array (
                'label' => 'Кнопка 2',
                'name' => 'knopka_2',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_knopka_2',
                'type' => 'text',
              ),
              10 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              11 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              12 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              13 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              14 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_chem_zanyatsya_sekcii_aktivnyj_otdyh_blok_otdyh_i_razvlecheniya_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
            ),
          ),
        ),
      ),
      8 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      9 => 
      array (
        'label' => 'Блок статей',
        'name' => 'blok_statej',
        'key' => 'field_bloki_stranicy_chem_zanyatsya_blok_statej',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Дополнительный Текст',
            'name' => 'dopolnitelnyj_tekst',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_blok_statej_dopolnitelnyj_tekst',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      10 => 
      array (
        'label' => 'Скрыть блок СЕО',
        'name' => 'skryt_blok_seo',
        'key' => 'field_bloki_stranicy_chem_zanyatsya_skryt_blok_seo',
        'type' => 'true_false',
        'ui' => 1,
      ),
      11 => 
      array (
        'label' => 'Блок с статьями',
        'name' => 'blok_s_statyami',
        'key' => 'field_bloki_stranicy_chem_zanyatsya_blok_s_statyami',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_blok_s_statyami_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_blok_s_statyami_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_chem_zanyatsya_blok_s_statyami_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      12 => NULL,
      13 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      14 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      15 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_bloki_stranicy_chem_zanyatsya_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      16 => NULL,
      17 => NULL,
      18 => NULL,
      19 => NULL,
      20 => NULL,
      21 => NULL,
      22 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      23 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'sekcii-restoran',
'title' => 'Секции Ресторан',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'restoran.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Блоки страницы Ресторан',
    'name' => 'bloki_stranicy_restoran',
    'key' => 'field_bloki_stranicy_restoran',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_bloki_stranicy_restoran_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение Верхней секции',
            'name' => 'izobrazhenie_verhnej_sekcii',
            'key' => 'field_bloki_stranicy_restoran_pervaya_sekciya_izobrazhenie_verhnej_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н1',
            'name' => 'zagolovok_n1',
            'key' => 'field_bloki_stranicy_restoran_pervaya_sekciya_zagolovok_n1',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_restoran_pervaya_sekciya_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_stranicy_restoran_pervaya_sekciya_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_stranicy_restoran_pervaya_sekciya_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_stranicy_restoran_pervaya_sekciya_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
          4 => NULL,
          5 => NULL,
          6 => NULL,
          7 => NULL,
          8 => NULL,
          9 => NULL,
        ),
      ),
      5 => 
      array (
        'label' => 'Секция Досуга',
        'name' => 'sekciya_dosuga',
        'key' => 'field_bloki_stranicy_restoran_sekciya_dosuga',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Блоки ресторана',
            'name' => 'bloki_restorana',
            'key' => 'field_bloki_stranicy_restoran_sekciya_dosuga_bloki_restorana',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Блок ресторан',
                'name' => 'blok_restoran',
                'key' => 'field_bloki_stranicy_restoran_sekciya_dosuga_bloki_restorana_blok_restoran',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Добавить класс',
                    'name' => 'dobavit_klass',
                    'instructions' => 'card-1, card-2',
                    'key' => 'field_bloki_stranicy_restoran_sekciya_dosuga_bloki_restorana_blok_restoran_dobavit_klass',
                    'type' => 'text',
                  ),
                  1 => 
                  array (
                    'label' => 'Изображение',
                    'name' => 'izobrazhenie',
                    'key' => 'field_bloki_stranicy_restoran_sekciya_dosuga_bloki_restorana_blok_restoran_izobrazhenie',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                  2 => 
                  array (
                    'label' => 'Заголовок Н3',
                    'name' => 'zagolovok_n3',
                    'key' => 'field_bloki_stranicy_restoran_sekciya_dosuga_bloki_restorana_blok_restoran_zagolovok_n3',
                    'type' => 'text',
                  ),
                  3 => 
                  array (
                    'label' => 'Описание',
                    'name' => 'opisanie',
                    'key' => 'field_bloki_stranicy_restoran_sekciya_dosuga_bloki_restorana_blok_restoran_opisanie',
                    'type' => 'textarea',
                    'new_lines' => 'br',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Секция Все включено',
        'name' => 'sekciya_vse_vklyucheno',
        'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_opisanie_mini',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Блок 1',
            'name' => 'blok_1',
            'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_1_izobrazhenie',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Текст мини',
                'name' => 'tekst_mini',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_1_tekst_mini',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Текст выделенный',
                'name' => 'tekst_vydelennyj',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_1_tekst_vydelennyj',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_1_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
              4 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_1_ssylka_knopki',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_1_knopka',
                'type' => 'text',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Блок 2',
            'name' => 'blok_2',
            'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_izobrazhenie',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Текст мини',
                'name' => 'tekst_mini',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_tekst_mini',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Текст выделенный',
                'name' => 'tekst_vydelennyj',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_tekst_vydelennyj',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Скрыть кнопку 1',
                'name' => 'skryt_knopku_1',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_skryt_knopku_1',
                'type' => 'true_false',
                'ui' => 1,
              ),
              4 => 
              array (
                'label' => 'Ссылка кнопки 1',
                'name' => 'ssylka_knopki_1',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_ssylka_knopki_1',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Кнопка 1',
                'name' => 'knopka_1',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_knopka_1',
                'type' => 'text',
              ),
              6 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              7 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_ssylka_knopki_2',
                'type' => 'text',
              ),
              8 => 
              array (
                'label' => 'Кнопка 2',
                'name' => 'knopka_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_2_knopka_2',
                'type' => 'text',
              ),
            ),
          ),
          5 => 
          array (
            'label' => 'Блок 3',
            'name' => 'blok_3',
            'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_3_izobrazhenie',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Текст выделенный',
                'name' => 'tekst_vydelennyj',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_3_tekst_vydelennyj',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Текст мини',
                'name' => 'tekst_mini',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_3_tekst_mini',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_3_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              4 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_3_ssylka_knopki_2',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Кнопка 2',
                'name' => 'knopka_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_3_knopka_2',
                'type' => 'text',
              ),
            ),
          ),
          6 => 
          array (
            'label' => 'Блок 4',
            'name' => 'blok_4',
            'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_4',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Блок 4',
                'name' => 'blok_4',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_4_blok_4',
                'type' => 'image',
                'return_format' => 'url',
              ),
              1 => 
              array (
                'label' => 'Текст выделенный',
                'name' => 'tekst_vydelennyj',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_4_tekst_vydelennyj',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Текст мини',
                'name' => 'tekst_mini',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_4_tekst_mini',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_4_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              4 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_4_ssylka_knopki_2',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Кнопка 2',
                'name' => 'knopka_2',
                'key' => 'field_bloki_stranicy_restoran_sekciya_vse_vklyucheno_blok_4_knopka_2',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      7 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      8 => NULL,
      9 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      10 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      11 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_bloki_stranicy_restoran_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      12 => NULL,
      13 => NULL,
      14 => NULL,
      15 => NULL,
      16 => NULL,
      17 => NULL,
      18 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      19 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'sekcii-o-nas',
'title' => 'Секции О нас',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'o-nas.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Блоки страницы О нас',
    'name' => 'bloki_stranicy_o_nas',
    'key' => 'field_bloki_stranicy_o_nas',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Изображение Верхней секции',
        'name' => 'izobrazhenie_verhnej_sekcii',
        'key' => 'field_bloki_stranicy_o_nas_izobrazhenie_verhnej_sekcii',
        'type' => 'image',
        'return_format' => 'url',
      ),
      5 => 
      array (
        'label' => 'Цвет заголовка',
        'name' => 'cvet_zagolovka',
        'key' => 'field_bloki_stranicy_o_nas_cvet_zagolovka',
        'type' => 'color_picker',
        'enable_opacity' => true,
      ),
      6 => 
      array (
        'label' => 'Заголовок Н2',
        'name' => 'zagolovok_n2',
        'key' => 'field_bloki_stranicy_o_nas_zagolovok_n2',
        'type' => 'text',
      ),
      7 => 
      array (
        'label' => 'Цвет текста',
        'name' => 'cvet_teksta',
        'key' => 'field_bloki_stranicy_o_nas_cvet_teksta',
        'type' => 'color_picker',
        'enable_opacity' => true,
      ),
      8 => 
      array (
        'label' => 'Описание под заголовком',
        'name' => 'opisanie_pod_zagolovkom',
        'key' => 'field_bloki_stranicy_o_nas_opisanie_pod_zagolovkom',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      9 => 
      array (
        'label' => 'Скрыть кнопку',
        'name' => 'skryt_knopku',
        'key' => 'field_bloki_stranicy_o_nas_skryt_knopku',
        'type' => 'true_false',
        'ui' => 1,
      ),
      10 => 
      array (
        'label' => 'Ссылка кнопки',
        'name' => 'ssylka_knopki',
        'key' => 'field_bloki_stranicy_o_nas_ssylka_knopki',
        'type' => 'text',
      ),
      11 => 
      array (
        'label' => 'Кнопка',
        'name' => 'knopka',
        'key' => 'field_bloki_stranicy_o_nas_knopka',
        'type' => 'text',
      ),
      12 => NULL,
      13 => NULL,
      14 => NULL,
      15 => NULL,
      16 => NULL,
      17 => NULL,
      18 => 
      array (
        'label' => 'Мы предлагаем',
        'name' => 'my_predlagaem',
        'key' => 'field_bloki_stranicy_o_nas_my_predlagaem',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_o_nas_my_predlagaem_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_bloki_stranicy_o_nas_my_predlagaem_opisanie_mini',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_bloki_stranicy_o_nas_my_predlagaem_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      19 => 
      array (
        'label' => 'О нас',
        'name' => 'o_nas',
        'key' => 'field_bloki_stranicy_o_nas_o_nas',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_bloki_stranicy_o_nas_o_nas_izobrazhenie',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Блок с текстом',
            'name' => 'blok_s_tekstom',
            'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_zagolovok_n2',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_opisanie',
                'type' => 'wysiwyg',
                'toolbar' => 'full',
              ),
              2 => 
              array (
                'label' => 'Характеристики',
                'name' => 'harakteristiki',
                'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_harakteristiki',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Характеристика',
                    'name' => 'harakteristika',
                    'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_harakteristiki_harakteristika',
                    'type' => 'text',
                  ),
                ),
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Блок с текстом 2',
            'name' => 'blok_s_tekstom_2',
            'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_2_zagolovok_n2',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_2_opisanie',
                'type' => 'wysiwyg',
                'toolbar' => 'full',
              ),
              2 => 
              array (
                'label' => 'Характеристики',
                'name' => 'harakteristiki',
                'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_2_harakteristiki',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Характеристика 1',
                    'name' => 'harakteristika_1',
                    'key' => 'field_bloki_stranicy_o_nas_o_nas_blok_s_tekstom_2_harakteristiki_harakteristika_1',
                    'type' => 'text',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      20 => 
      array (
        'label' => 'Видео',
        'name' => 'video',
        'key' => 'field_bloki_stranicy_o_nas_video',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_o_nas_video_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Загрузить видео',
            'name' => 'zagruzit_video',
            'key' => 'field_bloki_stranicy_o_nas_video_zagruzit_video',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Видео',
                'name' => 'video',
                'key' => 'field_bloki_stranicy_o_nas_video_zagruzit_video_video',
                'type' => 'oembed',
              ),
              1 => 
              array (
                'label' => 'Видео',
                'name' => 'video',
                'instructions' => 'Видео подгружается в WordPress. Берется ссылка на это видео и вставляется в код',
                'key' => 'field_bloki_stranicy_o_nas_video_zagruzit_video_video',
                'type' => 'acf_code_field',
                'mode' => 'htmlmixed',
                'theme' => 'elegant',
              ),
              2 => 
              array (
                'label' => 'Обложка',
                'name' => 'oblozhka',
                'key' => 'field_bloki_stranicy_o_nas_video_zagruzit_video_oblozhka',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
        ),
      ),
      21 => 
      array (
        'label' => 'Фото',
        'name' => 'foto',
        'key' => 'field_bloki_stranicy_o_nas_foto',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => NULL,
      ),
      22 => 
      array (
        'label' => 'Фото',
        'name' => 'foto',
        'key' => 'field_bloki_stranicy_o_nas_foto',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_o_nas_foto_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Галерея загруж Фотогалерея',
            'name' => 'galereya_zagruzh_fotogalereya',
            'key' => 'field_bloki_stranicy_o_nas_foto_galereya_zagruzh_fotogalereya',
            'type' => 'gallery',
          ),
        ),
      ),
      23 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      24 => 
      array (
        'label' => 'Блок статей',
        'name' => 'blok_statej',
        'key' => 'field_bloki_stranicy_o_nas_blok_statej',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Дополнительный Текст',
            'name' => 'dopolnitelnyj_tekst',
            'key' => 'field_bloki_stranicy_o_nas_blok_statej_dopolnitelnyj_tekst',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      25 => 
      array (
        'label' => 'Скрыть блок СЕО',
        'name' => 'skryt_blok_seo',
        'key' => 'field_bloki_stranicy_o_nas_skryt_blok_seo',
        'type' => 'true_false',
        'ui' => 1,
      ),
      26 => 
      array (
        'label' => 'Блок с статьями',
        'name' => 'blok_s_statyami',
        'key' => 'field_bloki_stranicy_o_nas_blok_s_statyami',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_bloki_stranicy_o_nas_blok_s_statyami_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_o_nas_blok_s_statyami_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_o_nas_blok_s_statyami_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      27 => NULL,
      28 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      29 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      30 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_bloki_stranicy_o_nas_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      31 => NULL,
      32 => NULL,
      33 => NULL,
      34 => NULL,
      35 => NULL,
      36 => NULL,
      37 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      38 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'stranica-meropriyatiya',
'title' => 'Страница Мероприятия',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'meropriyatiya.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Блоки страницы Мероприятия',
    'name' => 'bloki_stranicy_meropriyatiya',
    'key' => 'field_bloki_stranicy_meropriyatiya',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_bloki_stranicy_meropriyatiya_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение Верхней секции',
            'name' => 'izobrazhenie_verhnej_sekcii',
            'key' => 'field_bloki_stranicy_meropriyatiya_pervaya_sekciya_izobrazhenie_verhnej_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н1',
            'name' => 'zagolovok_n1',
            'key' => 'field_bloki_stranicy_meropriyatiya_pervaya_sekciya_zagolovok_n1',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_meropriyatiya_pervaya_sekciya_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Скрыть кнопку',
            'name' => 'skryt_knopku',
            'key' => 'field_bloki_stranicy_meropriyatiya_pervaya_sekciya_skryt_knopku',
            'type' => 'true_false',
            'ui' => 1,
          ),
          4 => 
          array (
            'label' => 'Ссылка кнопки',
            'name' => 'ssylka_knopki',
            'key' => 'field_bloki_stranicy_meropriyatiya_pervaya_sekciya_ssylka_knopki',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_stranicy_meropriyatiya_pervaya_sekciya_knopka',
            'type' => 'text',
          ),
          6 => NULL,
          7 => NULL,
          8 => NULL,
          9 => NULL,
          10 => NULL,
          11 => NULL,
        ),
      ),
      5 => 
      array (
        'label' => 'Мы предлагаем',
        'name' => 'my_predlagaem',
        'key' => 'field_bloki_stranicy_meropriyatiya_my_predlagaem',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_meropriyatiya_my_predlagaem_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_bloki_stranicy_meropriyatiya_my_predlagaem_opisanie_mini',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_bloki_stranicy_meropriyatiya_my_predlagaem_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Скрыть блок Видео',
        'name' => 'skryt_blok_video',
        'key' => 'field_bloki_stranicy_meropriyatiya_skryt_blok_video',
        'type' => 'true_false',
        'ui' => 1,
      ),
      7 => 
      array (
        'label' => 'Видео',
        'name' => 'video',
        'key' => 'field_bloki_stranicy_meropriyatiya_video',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Видео Н2',
            'name' => 'zagolovok_video_n2',
            'key' => 'field_bloki_stranicy_meropriyatiya_video_zagolovok_video_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Добавить видео',
            'name' => 'dobavit_video',
            'key' => 'field_bloki_stranicy_meropriyatiya_video_dobavit_video',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Видео',
                'name' => 'video',
                'key' => 'field_bloki_stranicy_meropriyatiya_video_dobavit_video_video',
                'type' => 'oembed',
              ),
              1 => 
              array (
                'label' => 'Видео',
                'name' => 'video',
                'instructions' => 'Видео подгружается в WordPress. Берется ссылка на это видео и вставляется в код',
                'key' => 'field_bloki_stranicy_meropriyatiya_video_dobavit_video_video',
                'type' => 'acf_code_field',
                'mode' => 'htmlmixed',
                'theme' => 'elegant',
              ),
              2 => 
              array (
                'label' => 'Обложка',
                'name' => 'oblozhka',
                'key' => 'field_bloki_stranicy_meropriyatiya_video_dobavit_video_oblozhka',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
        ),
      ),
      8 => 
      array (
        'label' => 'Фото',
        'name' => 'foto',
        'key' => 'field_bloki_stranicy_meropriyatiya_foto',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Фото Н2',
            'name' => 'zagolovok_foto_n2',
            'key' => 'field_bloki_stranicy_meropriyatiya_foto_zagolovok_foto_n2',
            'type' => 'text',
          ),
        ),
      ),
      9 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      10 => NULL,
      11 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      12 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      13 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_bloki_stranicy_meropriyatiya_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      14 => NULL,
      15 => NULL,
      16 => NULL,
    ),
  ),
  1 => NULL,
  2 => NULL,
  3 => NULL,
  4 => 
  array (
    0 => NULL,
    1 => NULL,
    2 => NULL,
  ),
  5 => NULL,
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'stranica-kontakty',
'title' => 'Страница Контакты',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'contacts.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Блоки страницы Контакты',
    'name' => 'bloki_stranicy_kontakty',
    'key' => 'field_bloki_stranicy_kontakty',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Контакты',
        'name' => 'kontakty',
        'key' => 'field_bloki_stranicy_kontakty_kontakty',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Контактная инф.',
            'name' => 'kontaktnaya_inf.',
            'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf.',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н1',
                'name' => 'zagolovok_n1',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._zagolovok_n1',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Адрес отеля',
                'name' => 'adres_otelya',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._adres_otelya',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Ссылка на Яндекс карты',
                'name' => 'ssylka_na_yandeks_karty',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._ssylka_na_yandeks_karty',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Адрес',
                'name' => 'adres',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._adres',
                'type' => 'text',
              ),
              4 => 
              array (
                'label' => 'GPS Координаты',
                'name' => 'gps_koordinaty',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._gps_koordinaty',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Ссылка на Яндекс карты GPS',
                'name' => 'ssylka_na_yandeks_karty_gps',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._ssylka_na_yandeks_karty_gps',
                'type' => 'text',
              ),
              6 => 
              array (
                'label' => 'Координаты',
                'name' => 'koordinaty',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._koordinaty',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Телефон',
                'name' => 'telefon',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._telefon',
                'type' => 'text',
              ),
              8 => NULL,
              9 => NULL,
              10 => NULL,
              11 => NULL,
              12 => 
              array (
                'label' => 'Почта',
                'name' => 'pochta',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._pochta',
                'type' => 'text',
              ),
              13 => NULL,
              14 => 
              array (
                'label' => 'Email',
                'name' => 'email',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_kontaktnaya_inf._email',
                'type' => 'text',
              ),
            ),
          ),
          1 => 
          array (
            'label' => 'Реквизиты инф.',
            'name' => 'rekvizity_inf.',
            'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf.',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._zagolovok_n3',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Юр. инфа',
                'name' => 'yur._infa',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._yur._infa',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Текст 1',
                'name' => 'tekst_1',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._tekst_1',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'ИНН',
                'name' => 'inn',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._inn',
                'type' => 'text',
              ),
              4 => 
              array (
                'label' => 'Текст 2',
                'name' => 'tekst_2',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._tekst_2',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'ОГРН',
                'name' => 'ogrn',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._ogrn',
                'type' => 'text',
              ),
              6 => 
              array (
                'label' => 'Текст 3',
                'name' => 'tekst_3',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._tekst_3',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Юр. адрес',
                'name' => 'yur._adres',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._yur._adres',
                'type' => 'text',
              ),
              8 => 
              array (
                'label' => 'Текст 4',
                'name' => 'tekst_4',
                'key' => 'field_bloki_stranicy_kontakty_kontakty_rekvizity_inf._tekst_4',
                'type' => 'text',
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_bloki_stranicy_kontakty_kontakty_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      5 => 
      array (
        'label' => 'Блок с картой',
        'name' => 'blok_s_kartoj',
        'key' => 'field_bloki_stranicy_kontakty_blok_s_kartoj',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Текст',
            'name' => 'tekst',
            'key' => 'field_bloki_stranicy_kontakty_blok_s_kartoj_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Код карты',
            'name' => 'kod_karty',
            'key' => 'field_bloki_stranicy_kontakty_blok_s_kartoj_kod_karty',
            'type' => 'acf_code_field',
            'mode' => 'htmlmixed',
            'theme' => 'elegant',
          ),
        ),
      ),
      6 => NULL,
      7 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      8 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      9 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_bloki_stranicy_kontakty_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      10 => NULL,
      11 => NULL,
      12 => NULL,
      13 => NULL,
      14 => NULL,
      15 => NULL,
      16 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      17 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'stranica-korporativ',
'title' => 'Страница Корпоратив',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'korporativ.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Блоки страницы Корпоратив',
    'name' => 'bloki_stranicy_korporativ',
    'key' => 'field_bloki_stranicy_korporativ',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Первая секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_bloki_stranicy_korporativ_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение Верхней секции',
            'name' => 'izobrazhenie_verhnej_sekcii',
            'key' => 'field_bloki_stranicy_korporativ_pervaya_sekciya_izobrazhenie_verhnej_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н1',
            'name' => 'zagolovok_n1',
            'key' => 'field_bloki_stranicy_korporativ_pervaya_sekciya_zagolovok_n1',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_bloki_stranicy_korporativ_pervaya_sekciya_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => NULL,
          4 => NULL,
          5 => NULL,
          6 => NULL,
          7 => NULL,
          8 => NULL,
        ),
      ),
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => NULL,
      5 => 
      array (
        'label' => 'Мы предлагаем',
        'name' => 'my_predlagaem',
        'key' => 'field_bloki_stranicy_korporativ_my_predlagaem',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Блок 1',
            'name' => 'blok_1',
            'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 1',
                'name' => 'izobrazhenie_1',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_1_izobrazhenie_1',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_1_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_1_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_1_spisok_punkt',
                    'type' => 'text',
                  ),
                ),
              ),
            ),
          ),
          1 => 
          array (
            'label' => 'Блок 2',
            'name' => 'blok_2',
            'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 2',
                'name' => 'izobrazhenie_2',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_2_izobrazhenie_2',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_2_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_2_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_2_spisok_punkt',
                    'type' => 'text',
                  ),
                ),
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Блок 3',
            'name' => 'blok_3',
            'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение 3',
                'name' => 'izobrazhenie_3',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_3_izobrazhenie_3',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_3_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_3_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Пункт',
                    'name' => 'punkt',
                    'key' => 'field_bloki_stranicy_korporativ_my_predlagaem_blok_3_spisok_punkt',
                    'type' => 'text',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Баннер с бронью',
        'name' => 'banner_s_bronyu',
        'key' => 'field_bloki_stranicy_korporativ_banner_s_bronyu',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_bloki_stranicy_korporativ_banner_s_bronyu_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_bloki_stranicy_korporativ_banner_s_bronyu_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Страница',
                'name' => 'stranica',
                'key' => 'field_bloki_stranicy_korporativ_banner_s_bronyu_knopka_stranica',
                'type' => 'link',
                'return_format' => 'url',
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_bloki_stranicy_korporativ_banner_s_bronyu_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Галерея',
        'name' => 'galereya',
        'key' => 'field_bloki_stranicy_korporativ_galereya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Фото 1',
            'name' => 'foto_1',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          1 => 
          array (
            'label' => 'Фото 1',
            'name' => 'foto_1',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          2 => 
          array (
            'label' => 'Фото 2',
            'name' => 'foto_2',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          3 => 
          array (
            'label' => 'Фото 2',
            'name' => 'foto_2',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          4 => 
          array (
            'label' => 'Фото',
            'name' => 'foto',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto',
            'type' => 'image',
            'return_format' => 'array',
          ),
          5 => 
          array (
            'label' => 'Фото',
            'name' => 'foto',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto',
            'type' => 'image',
            'return_format' => 'array',
          ),
          6 => 
          array (
            'label' => 'Фото 3',
            'name' => 'foto_3',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
          7 => 
          array (
            'label' => 'Фото 3',
            'name' => 'foto_3',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
          8 => 
          array (
            'label' => 'Фото 4',
            'name' => 'foto_4',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_4',
            'type' => 'image',
            'return_format' => 'array',
          ),
          9 => 
          array (
            'label' => 'Фото 4',
            'name' => 'foto_4',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_4',
            'type' => 'image',
            'return_format' => 'array',
          ),
          10 => 
          array (
            'label' => 'Фото 5',
            'name' => 'foto_5',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_5',
            'type' => 'image',
            'return_format' => 'array',
          ),
          11 => 
          array (
            'label' => 'Фото 5',
            'name' => 'foto_5',
            'key' => 'field_bloki_stranicy_korporativ_galereya_foto_5',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      8 => NULL,
      9 => NULL,
      10 => NULL,
      11 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      12 => 
      array (
        'label' => 'Секция с картой',
        'name' => 'sekciya_s_kartoj',
        'key' => 'field_bloki_stranicy_korporativ_sekciya_s_kartoj',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Текст',
            'name' => 'tekst',
            'key' => 'field_bloki_stranicy_korporativ_sekciya_s_kartoj_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Код карты',
            'name' => 'kod_karty',
            'key' => 'field_bloki_stranicy_korporativ_sekciya_s_kartoj_kod_karty',
            'type' => 'acf_code_field',
            'mode' => 'htmlmixed',
            'theme' => 'elegant',
          ),
          2 => 
          array (
            'label' => 'Изображение карты',
            'name' => 'izobrazhenie_karty',
            'key' => 'field_bloki_stranicy_korporativ_sekciya_s_kartoj_izobrazhenie_karty',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      13 => NULL,
      14 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      15 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      16 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_bloki_stranicy_korporativ_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      17 => NULL,
      18 => NULL,
      19 => NULL,
      20 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      21 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'opcii',
'title' => 'Опции',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'options_page',
      'operator' => '==',
      'value' => 'options',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Меню',
    'name' => 'menyu',
    'key' => 'field_menyu',
    'type' => 'tab',
  ),
  1 => 
  array (
    'label' => 'Телефон 1',
    'name' => 'telefon_1',
    'key' => 'field_telefon_1',
    'type' => 'text',
  ),
  2 => NULL,
  3 => 
  array (
    'label' => 'Ссылка кнопки',
    'name' => 'ssylka_knopki',
    'key' => 'field_ssylka_knopki',
    'type' => 'text',
  ),
  4 => 
  array (
    'label' => 'Кнопка',
    'name' => 'knopka',
    'key' => 'field_knopka',
    'type' => 'text',
  ),
  5 => 
  array (
    'label' => 'Футер',
    'name' => 'futer',
    'key' => 'field_futer',
    'type' => 'tab',
  ),
  6 => 
  array (
    'label' => 'Заголовок Меню',
    'name' => 'zagolovok_menyu',
    'key' => 'field_zagolovok_menyu',
    'type' => 'text',
  ),
  7 => 
  array (
    'label' => 'Полезное',
    'name' => 'poleznoe',
    'key' => 'field_poleznoe',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Заголовок Полезное',
        'name' => 'zagolovok_poleznoe',
        'key' => 'field_poleznoe_zagolovok_poleznoe',
        'type' => 'text',
      ),
      1 => 
      array (
        'label' => 'Ссылка 1',
        'name' => 'ssylka_1',
        'key' => 'field_poleznoe_ssylka_1',
        'type' => 'text',
      ),
      2 => 
      array (
        'label' => 'Текст ссылки 1',
        'name' => 'tekst_ssylki_1',
        'key' => 'field_poleznoe_tekst_ssylki_1',
        'type' => 'text',
      ),
      3 => 
      array (
        'label' => 'Ссылка 2',
        'name' => 'ssylka_2',
        'key' => 'field_poleznoe_ssylka_2',
        'type' => 'text',
      ),
      4 => 
      array (
        'label' => 'Текст ссылки 2',
        'name' => 'tekst_ssylki_2',
        'key' => 'field_poleznoe_tekst_ssylki_2',
        'type' => 'text',
      ),
      5 => 
      array (
        'label' => 'Ссылка 3',
        'name' => 'ssylka_3',
        'key' => 'field_poleznoe_ssylka_3',
        'type' => 'text',
      ),
      6 => 
      array (
        'label' => 'Текст ссылки 3',
        'name' => 'tekst_ssylki_3',
        'key' => 'field_poleznoe_tekst_ssylki_3',
        'type' => 'text',
      ),
      7 => 
      array (
        'label' => 'Ссылка 4',
        'name' => 'ssylka_4',
        'key' => 'field_poleznoe_ssylka_4',
        'type' => 'text',
      ),
      8 => 
      array (
        'label' => 'Текст ссылки 4',
        'name' => 'tekst_ssylki_4',
        'key' => 'field_poleznoe_tekst_ssylki_4',
        'type' => 'text',
      ),
      9 => 
      array (
        'label' => 'Режим для слабовидящих',
        'name' => 'rezhim_dlya_slabovidyaschih',
        'key' => 'field_poleznoe_rezhim_dlya_slabovidyaschih',
        'type' => 'text',
      ),
    ),
  ),
  8 => 
  array (
    'label' => 'Реквизиты',
    'name' => 'rekvizity',
    'key' => 'field_rekvizity',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Заголовок Реквизиты',
        'name' => 'zagolovok_rekvizity',
        'key' => 'field_rekvizity_zagolovok_rekvizity',
        'type' => 'text',
      ),
      1 => 
      array (
        'label' => 'Текст 1',
        'name' => 'tekst_1',
        'key' => 'field_rekvizity_tekst_1',
        'type' => 'text',
      ),
      2 => 
      array (
        'label' => 'Текст 2',
        'name' => 'tekst_2',
        'key' => 'field_rekvizity_tekst_2',
        'type' => 'text',
      ),
      3 => 
      array (
        'label' => 'Текст 3',
        'name' => 'tekst_3',
        'key' => 'field_rekvizity_tekst_3',
        'type' => 'text',
      ),
      4 => 
      array (
        'label' => 'Текст 4',
        'name' => 'tekst_4',
        'key' => 'field_rekvizity_tekst_4',
        'type' => 'text',
      ),
      5 => 
      array (
        'label' => 'Текст 5',
        'name' => 'tekst_5',
        'key' => 'field_rekvizity_tekst_5',
        'type' => 'text',
      ),
    ),
  ),
  9 => 
  array (
    'label' => 'Контакты',
    'name' => 'kontakty',
    'key' => 'field_kontakty',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Заголовок Контакты',
        'name' => 'zagolovok_kontakty',
        'key' => 'field_kontakty_zagolovok_kontakty',
        'type' => 'text',
      ),
      1 => 
      array (
        'label' => 'Ссылка на карту',
        'name' => 'ssylka_na_kartu',
        'key' => 'field_kontakty_ssylka_na_kartu',
        'type' => 'text',
      ),
      2 => 
      array (
        'label' => 'Адрес',
        'name' => 'adres',
        'key' => 'field_kontakty_adres',
        'type' => 'text',
      ),
      3 => 
      array (
        'label' => 'Телефон 2',
        'name' => 'telefon_2',
        'key' => 'field_kontakty_telefon_2',
        'type' => 'text',
      ),
      4 => NULL,
      5 => 
      array (
        'label' => 'Телефон 1',
        'name' => 'telefon_1',
        'key' => 'field_kontakty_telefon_1',
        'type' => 'text',
      ),
      6 => NULL,
      7 => NULL,
      8 => 
      array (
        'label' => 'Email',
        'name' => 'email',
        'key' => 'field_kontakty_email',
        'type' => 'text',
      ),
      9 => 
      array (
        'label' => 'Кнопка синяя',
        'name' => 'knopka_sinyaya',
        'key' => 'field_kontakty_knopka_sinyaya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Ссылка на Соцеть 1',
            'name' => 'ssylka_na_socet_1',
            'key' => 'field_kontakty_knopka_sinyaya_ssylka_na_socet_1',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Coцсеть 1',
            'name' => 'cocset_1',
            'key' => 'field_kontakty_knopka_sinyaya_cocset_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          2 => 
          array (
            'label' => 'Ссылка на Соцеть 2',
            'name' => 'ssylka_na_socet_2',
            'key' => 'field_kontakty_knopka_sinyaya_ssylka_na_socet_2',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Coцсеть 2',
            'name' => 'cocset_2',
            'key' => 'field_kontakty_knopka_sinyaya_cocset_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          4 => 
          array (
            'label' => 'Ссылка на Соцеть 3',
            'name' => 'ssylka_na_socet_3',
            'key' => 'field_kontakty_knopka_sinyaya_ssylka_na_socet_3',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Coцсеть 3',
            'name' => 'cocset_3',
            'key' => 'field_kontakty_knopka_sinyaya_cocset_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
    ),
  ),
  10 => 
  array (
    'label' => 'Код Бронирования',
    'name' => 'kod_bronirovaniya',
    'key' => 'field_kod_bronirovaniya',
    'type' => 'tab',
  ),
  11 => 
  array (
    'label' => 'Код панели бронирования',
    'name' => 'kod_paneli_bronirovaniya',
    'key' => 'field_kod_paneli_bronirovaniya',
    'type' => 'acf_code_field',
    'mode' => 'htmlmixed',
    'theme' => 'elegant',
  ),
  12 => 
  array (
    'label' => 'Скрыть блок под бронированием',
    'name' => 'skryt_blok_pod_bronirovaniem',
    'key' => 'field_skryt_blok_pod_bronirovaniem',
    'type' => 'true_false',
    'ui' => 1,
  ),
  13 => 
  array (
    'label' => 'Текст НГ',
    'name' => 'tekst_ng',
    'key' => 'field_tekst_ng',
    'type' => 'text',
  ),
  14 => 
  array (
    'label' => 'Скрыть кнопку 2',
    'name' => 'skryt_knopku_2',
    'key' => 'field_skryt_knopku_2',
    'type' => 'true_false',
    'ui' => 1,
  ),
  15 => 
  array (
    'label' => 'Ссылка кнопки НГ',
    'name' => 'ssylka_knopki_ng',
    'key' => 'field_ssylka_knopki_ng',
    'type' => 'text',
  ),
  16 => 
  array (
    'label' => 'Кнопка НГ',
    'name' => 'knopka_ng',
    'key' => 'field_knopka_ng',
    'type' => 'text',
  ),
  17 => 
  array (
    'label' => 'Бронирование номеров',
    'name' => 'bronirovanie_nomerov',
    'key' => 'field_bronirovanie_nomerov',
    'type' => 'tab',
  ),
  18 => 
  array (
    'label' => 'Секция Бронирования номеров',
    'name' => 'sekciya_bronirovaniya_nomerov',
    'key' => 'field_sekciya_bronirovaniya_nomerov',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Заголовок Н2',
        'name' => 'zagolovok_n2',
        'key' => 'field_sekciya_bronirovaniya_nomerov_zagolovok_n2',
        'type' => 'text',
      ),
      1 => 
      array (
        'label' => 'Описание поз заголовком',
        'name' => 'opisanie_poz_zagolovkom',
        'key' => 'field_sekciya_bronirovaniya_nomerov_opisanie_poz_zagolovkom',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      2 => 
      array (
        'label' => 'Слайдер Бронирования номеров',
        'name' => 'slajder_bronirovaniya_nomerov',
        'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov',
        'type' => 'repeater',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение Слайда',
            'name' => 'izobrazhenie_slajda',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_izobrazhenie_slajda',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Характеристика 1',
                'name' => 'harakteristika_1',
                'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki_harakteristika_1',
                'type' => 'text',
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Заголовок номера',
            'name' => 'zagolovok_nomera',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_zagolovok_nomera',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Текст',
            'name' => 'tekst',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_tekst',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
          5 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          6 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          7 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          8 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          9 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          10 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          11 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          12 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          13 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          14 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          15 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          16 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          17 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          18 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          19 => 
          array (
            'label' => 'Характеристики',
            'name' => 'harakteristiki',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_harakteristiki',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          20 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_bronirovaniya_nomerov_slajder_bronirovaniya_nomerov_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
        ),
      ),
    ),
  ),
  19 => 
  array (
    'label' => 'Call to Acrion',
    'name' => 'call_to_acrion',
    'key' => 'field_call_to_acrion',
    'type' => 'tab',
  ),
  20 => 
  array (
    'label' => 'Ссылка на Вотцап футер',
    'name' => 'ssylka_na_votcap_futer',
    'key' => 'field_ssylka_na_votcap_futer',
    'type' => 'text',
  ),
  21 => 
  array (
    'label' => 'Ссылка на Телеграм футер',
    'name' => 'ssylka_na_telegram_futer',
    'key' => 'field_ssylka_na_telegram_futer',
    'type' => 'text',
  ),
  22 => NULL,
  23 => 
  array (
    'label' => 'ПОПАП',
    'name' => 'popap',
    'key' => 'field_popap',
    'type' => 'tab',
  ),
  24 => 
  array (
    'label' => 'Скрыть попап',
    'name' => 'skryt_popap',
    'key' => 'field_skryt_popap',
    'type' => 'true_false',
    'ui' => 1,
  ),
  25 => 
  array (
    'label' => 'Заголовок Попап',
    'name' => 'zagolovok_popap',
    'key' => 'field_zagolovok_popap',
    'type' => 'text',
  ),
  26 => 
  array (
    'label' => 'Текст попап',
    'name' => 'tekst_popap',
    'key' => 'field_tekst_popap',
    'type' => 'wysiwyg',
    'toolbar' => 'full',
  ),
  27 => 
  array (
    'label' => 'Кнопки на ПОПАП',
    'name' => 'knopki_na_popap',
    'key' => 'field_knopki_na_popap',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Скрыть кнопку 1',
        'name' => 'skryt_knopku_1',
        'key' => 'field_knopki_na_popap_skryt_knopku_1',
        'type' => 'true_false',
        'ui' => 1,
      ),
      1 => 
      array (
        'label' => 'Ссылка кнопки ПОПАП 1',
        'name' => 'ssylka_knopki_popap_1',
        'key' => 'field_knopki_na_popap_ssylka_knopki_popap_1',
        'type' => 'text',
      ),
      2 => 
      array (
        'label' => 'Кнопка ПОПАП 1',
        'name' => 'knopka_popap_1',
        'key' => 'field_knopki_na_popap_knopka_popap_1',
        'type' => 'text',
      ),
    ),
  ),
  28 => 
  array (
    'label' => 'Изображение ПОПАП',
    'name' => 'izobrazhenie_popap',
    'key' => 'field_izobrazhenie_popap',
    'type' => 'image',
    'return_format' => 'array',
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'bani-i-kupel',
'title' => 'Бани и Купель',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'bani-i-kupel.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Секции страницы Бани и купель',
    'name' => 'sekcii_stranicy_bani_i_kupel',
    'key' => 'field_sekcii_stranicy_bani_i_kupel',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение БекГраунд секции',
            'name' => 'izobrazhenie_bekgraund_sekcii',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_pervaya_sekciya_izobrazhenie_bekgraund_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н1',
            'name' => 'zagolovok_n1',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_pervaya_sekciya_zagolovok_n1',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_pervaya_sekciya_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Скрыть кнопку',
            'name' => 'skryt_knopku',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_pervaya_sekciya_skryt_knopku',
            'type' => 'true_false',
            'ui' => 1,
          ),
          4 => 
          array (
            'label' => 'Ссылка кнопки',
            'name' => 'ssylka_knopki',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_pervaya_sekciya_ssylka_knopki',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_pervaya_sekciya_knopka',
            'type' => 'text',
          ),
          6 => NULL,
          7 => NULL,
          8 => NULL,
          9 => NULL,
          10 => NULL,
          11 => NULL,
        ),
      ),
      5 => 
      array (
        'label' => 'Секция бани 2',
        'name' => 'sekciya_bani_2',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_2',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_2_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_2_opisanie_mini',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание мини 2',
            'name' => 'opisanie_mini_2',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_2_opisanie_mini_2',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Описание мини 3',
            'name' => 'opisanie_mini_3',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_2_opisanie_mini_3',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_2_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Секция бани 3',
        'name' => 'sekciya_bani_3',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_3',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Слайдер 1',
            'name' => 'slajder_1',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_3_slajder_1',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Загрузить фото',
                'name' => 'zagruzit_foto',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_3_slajder_1_zagruzit_foto',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
          1 => 
          array (
            'label' => 'Характеристика',
            'name' => 'harakteristika',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_3_harakteristika',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_3_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_3_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_3_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Секция бани 4',
        'name' => 'sekciya_bani_4',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_4',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Слайдер 2',
            'name' => 'slajder_2',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_4_slajder_2',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Загрузить фото',
                'name' => 'zagruzit_foto',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_4_slajder_2_zagruzit_foto',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
          1 => 
          array (
            'label' => 'Характеристика',
            'name' => 'harakteristika',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_4_harakteristika',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_4_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_4_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_4_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      8 => 
      array (
        'label' => 'Секция бани 5',
        'name' => 'sekciya_bani_5',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_5',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Слайдер 3',
            'name' => 'slajder_3',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_5_slajder_3',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Загрузить фото',
                'name' => 'zagruzit_foto',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_5_slajder_3_zagruzit_foto',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
          1 => 
          array (
            'label' => 'Характеристика',
            'name' => 'harakteristika',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_5_harakteristika',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_5_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_5_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_bani_5_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      9 => 
      array (
        'label' => 'Секция с карточками бани',
        'name' => 'sekciya_s_kartochkami_bani',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Карточка 1',
            'name' => 'kartochka_1',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_1_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_1_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Карточка 3',
            'name' => 'kartochka_3',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_3_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_3_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          5 => 
          array (
            'label' => 'Карточка 2',
            'name' => 'kartochka_2',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_2_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_2_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          6 => 
          array (
            'label' => 'Карточка 4',
            'name' => 'kartochka_4',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_4',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_4_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_4_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
          7 => 
          array (
            'label' => 'Карточка 5',
            'name' => 'kartochka_5',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_5',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_5_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_kartochkami_bani_kartochka_5_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
        ),
      ),
      10 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      11 => 
      array (
        'label' => 'Секция с баннером Биг',
        'name' => 'sekciya_s_bannerom_big',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание',
            'name' => 'opisanie',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big_opisanie',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_sekciya_s_bannerom_big_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      12 => 
      array (
        'label' => 'Блок CEO',
        'name' => 'blok_ceo',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_blok_ceo',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Дополнительный Текст',
            'name' => 'dopolnitelnyj_tekst',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_blok_ceo_dopolnitelnyj_tekst',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      13 => 
      array (
        'label' => 'Блок с статьями',
        'name' => 'blok_s_statyami',
        'key' => 'field_sekcii_stranicy_bani_i_kupel_blok_s_statyami',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_blok_s_statyami_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_blok_s_statyami_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_sekcii_stranicy_bani_i_kupel_blok_s_statyami_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      14 => NULL,
      15 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      16 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      17 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_sekcii_stranicy_bani_i_kupel_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      18 => NULL,
      19 => NULL,
      20 => NULL,
      21 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      22 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'stranicy-novyj-god',
'title' => 'Страницы Новый год',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'new-year.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Секции Страницы Новый Год',
    'name' => 'sekcii_stranicy_novyj_god',
    'key' => 'field_sekcii_stranicy_novyj_god',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая Секция НГ',
        'name' => 'pervaya_sekciya_ng',
        'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение первой секции НГ',
            'name' => 'izobrazhenie_pervoj_sekcii_ng',
            'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_izobrazhenie_pervoj_sekcii_ng',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Мини текст выше заголовка',
            'name' => 'mini_tekst_vyshe_zagolovka',
            'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_mini_tekst_vyshe_zagolovka',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Заголовок H1',
            'name' => 'zagolovok_h1',
            'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_zagolovok_h1',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Мини текст ниже заголовка',
            'name' => 'mini_tekst_nizhe_zagolovka',
            'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_mini_tekst_nizhe_zagolovka',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Кнопки на первом экране',
            'name' => 'knopki_na_pervom_ekrane',
            'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_knopki_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_knopki_na_pervom_ekrane_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
              1 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_knopki_na_pervom_ekrane_ssylka_knopki',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_knopki_na_pervom_ekrane_knopka',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_knopki_na_pervom_ekrane_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              4 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_knopki_na_pervom_ekrane_ssylka_knopki_2',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Кнопка2',
                'name' => 'knopka2',
                'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_knopki_na_pervom_ekrane_knopka2',
                'type' => 'text',
              ),
            ),
          ),
          5 => 
          array (
            'label' => 'Код панели бронирования',
            'name' => 'kod_paneli_bronirovaniya',
            'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_kod_paneli_bronirovaniya',
            'type' => 'acf_code_field',
            'mode' => 'htmlmixed',
            'theme' => 'elegant',
          ),
          6 => 
          array (
            'label' => 'Текст вконце первого экрана',
            'name' => 'tekst_vkonce_pervogo_ekrana',
            'key' => 'field_sekcii_stranicy_novyj_god_pervaya_sekciya_ng_tekst_vkonce_pervogo_ekrana',
            'type' => 'text',
          ),
        ),
      ),
      5 => 
      array (
        'label' => 'Секция 2',
        'name' => 'sekciya_2',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_2',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_2_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_2_opisanie_mini',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Описание мини 2',
            'name' => 'opisanie_mini_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_2_opisanie_mini_2',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Описание мини 3',
            'name' => 'opisanie_mini_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_2_opisanie_mini_3',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          4 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_2_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Секция 3',
        'name' => 'sekciya_3',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение 2',
            'name' => 'izobrazhenie_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_izobrazhenie_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          1 => 
          array (
            'label' => 'Изображение 4',
            'name' => 'izobrazhenie_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_izobrazhenie_4',
            'type' => 'image',
            'return_format' => 'array',
          ),
          2 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          3 => 
          array (
            'label' => 'Изображение 3',
            'name' => 'izobrazhenie_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_izobrazhenie_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
          4 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_zagolovok_n2',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Скидка 1',
            'name' => 'skidka_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_skidka_1',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          6 => 
          array (
            'label' => 'Кнопка на первом экране',
            'name' => 'knopka_na_pervom_ekrane',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_knopka_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_knopka_na_pervom_ekrane_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
              1 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_knopka_na_pervom_ekrane_ssylka_knopki',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_3_knopka_na_pervom_ekrane_knopka',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Секция 4',
        'name' => 'sekciya_4',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Описание на зеленом',
            'name' => 'opisanie_na_zelenom',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_opisanie_na_zelenom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          1 => 
          array (
            'label' => 'Преимущество 1',
            'name' => 'preimuschestvo_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_1_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_1_opisanie',
                'type' => 'text',
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Преимущество 2',
            'name' => 'preimuschestvo_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_2_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_2_opisanie',
                'type' => 'text',
              ),
            ),
          ),
          3 => 
          array (
            'label' => 'Преимущество 3',
            'name' => 'preimuschestvo_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_3_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_3_opisanie',
                'type' => 'text',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Описание в середине',
            'name' => 'opisanie_v_seredine',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_opisanie_v_seredine',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          5 => 
          array (
            'label' => 'Преимущество 4',
            'name' => 'preimuschestvo_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_4',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_4_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_4_opisanie',
                'type' => 'text',
              ),
            ),
          ),
          6 => 
          array (
            'label' => 'Преимущество 5',
            'name' => 'preimuschestvo_5',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_5',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_5_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_5_opisanie',
                'type' => 'text',
              ),
            ),
          ),
          7 => 
          array (
            'label' => 'Преимущество 6',
            'name' => 'preimuschestvo_6',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_6',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_6_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_4_preimuschestvo_6_opisanie',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      8 => 
      array (
        'label' => 'Секция 5',
        'name' => 'sekciya_5',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'url',
          ),
          2 => 
          array (
            'label' => 'Заголовок Н3 1',
            'name' => 'zagolovok_n3_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_zagolovok_n3_1',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Кнопка на первом экране',
            'name' => 'knopka_na_pervom_ekrane',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Скрыть',
                'name' => 'skryt',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane_skryt',
                'type' => 'true_false',
                'ui' => 1,
              ),
              1 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane_ssylka_knopki',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane_knopka',
                'type' => 'text',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Изображение 2',
            'name' => 'izobrazhenie_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_izobrazhenie_2',
            'type' => 'image',
            'return_format' => 'url',
          ),
          5 => 
          array (
            'label' => 'Заголовок Н3 2',
            'name' => 'zagolovok_n3_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_zagolovok_n3_2',
            'type' => 'text',
          ),
          6 => 
          array (
            'label' => 'Кнопка на первом экране 2',
            'name' => 'knopka_na_pervom_ekrane_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Скрыть',
                'name' => 'skryt',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane_2_skryt',
                'type' => 'true_false',
                'ui' => 1,
              ),
              1 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane_2_ssylka_knopki',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_5_knopka_na_pervom_ekrane_2_knopka',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      9 => 
      array (
        'label' => 'Секция 6',
        'name' => 'sekciya_6',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Скрыть',
                'name' => 'skryt',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_knopka_skryt',
                'type' => 'true_false',
                'ui' => 1,
              ),
              1 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_knopka_knopka',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Скрыть2',
                'name' => 'skryt2',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_knopka_skryt2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              4 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_knopka_ssylka_knopki_2',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Кнопка2',
                'name' => 'knopka2',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_knopka_knopka2',
                'type' => 'text',
              ),
            ),
          ),
          2 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_6_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      10 => 
      array (
        'label' => 'Секция 7',
        'name' => 'sekciya_7',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_7',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_7_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание мини',
            'name' => 'opisanie_mini',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_7_opisanie_mini',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      11 => 
      array (
        'label' => 'Секция 8',
        'name' => 'sekciya_8',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_8',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_8_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_8_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      12 => 
      array (
        'label' => 'Секция 9',
        'name' => 'sekciya_9',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          3 => 
          array (
            'label' => 'Заголовок Н4 1',
            'name' => 'zagolovok_n4_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_1',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Описание 1',
            'name' => 'opisanie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_1',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          5 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          6 => 
          array (
            'label' => 'Заголовок Н4 1',
            'name' => 'zagolovok_n4_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_1',
            'type' => 'text',
          ),
          7 => 
          array (
            'label' => 'Описание 1',
            'name' => 'opisanie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_1',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          8 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          9 => 
          array (
            'label' => 'Заголовок Н4 1',
            'name' => 'zagolovok_n4_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_1',
            'type' => 'text',
          ),
          10 => 
          array (
            'label' => 'Описание 1',
            'name' => 'opisanie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_1',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          11 => 
          array (
            'label' => 'Изображение 2',
            'name' => 'izobrazhenie_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          12 => 
          array (
            'label' => 'Заголовок Н4 2',
            'name' => 'zagolovok_n4_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_2',
            'type' => 'text',
          ),
          13 => 
          array (
            'label' => 'Описание 2',
            'name' => 'opisanie_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_2',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          14 => 
          array (
            'label' => 'Изображение 3',
            'name' => 'izobrazhenie_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
          15 => 
          array (
            'label' => 'Заголовок Н4 3',
            'name' => 'zagolovok_n4_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_3',
            'type' => 'text',
          ),
          16 => 
          array (
            'label' => 'Описание 3',
            'name' => 'opisanie_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_3',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          17 => 
          array (
            'label' => 'Изображение 4',
            'name' => 'izobrazhenie_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_4',
            'type' => 'image',
            'return_format' => 'array',
          ),
          18 => 
          array (
            'label' => 'Заголовок Н4 4',
            'name' => 'zagolovok_n4_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_4',
            'type' => 'text',
          ),
          19 => 
          array (
            'label' => 'Описание 4',
            'name' => 'opisanie_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_4',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      13 => 
      array (
        'label' => 'Секция 9',
        'name' => 'sekciya_9',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          2 => 
          array (
            'label' => 'Изображение 1',
            'name' => 'izobrazhenie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_1',
            'type' => 'image',
            'return_format' => 'array',
          ),
          3 => 
          array (
            'label' => 'Заголовок Н4 1',
            'name' => 'zagolovok_n4_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_1',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Описание 1',
            'name' => 'opisanie_1',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_1',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          5 => 
          array (
            'label' => 'Изображение 2',
            'name' => 'izobrazhenie_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_2',
            'type' => 'image',
            'return_format' => 'array',
          ),
          6 => 
          array (
            'label' => 'Заголовок Н4 2',
            'name' => 'zagolovok_n4_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_2',
            'type' => 'text',
          ),
          7 => 
          array (
            'label' => 'Описание 2',
            'name' => 'opisanie_2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_2',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          8 => 
          array (
            'label' => 'Изображение 3',
            'name' => 'izobrazhenie_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_3',
            'type' => 'image',
            'return_format' => 'array',
          ),
          9 => 
          array (
            'label' => 'Заголовок Н4 3',
            'name' => 'zagolovok_n4_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_3',
            'type' => 'text',
          ),
          10 => 
          array (
            'label' => 'Описание 3',
            'name' => 'opisanie_3',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_3',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          11 => 
          array (
            'label' => 'Изображение 4',
            'name' => 'izobrazhenie_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_izobrazhenie_4',
            'type' => 'image',
            'return_format' => 'array',
          ),
          12 => 
          array (
            'label' => 'Заголовок Н4 4',
            'name' => 'zagolovok_n4_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_zagolovok_n4_4',
            'type' => 'text',
          ),
          13 => 
          array (
            'label' => 'Описание 4',
            'name' => 'opisanie_4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_9_opisanie_4',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      14 => 
      array (
        'label' => 'Секция 10',
        'name' => 'sekciya_10',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н4',
            'name' => 'zagolovok_n4',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_zagolovok_n4',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание',
            'name' => 'opisanie',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_opisanie',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Скрыть',
                'name' => 'skryt',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_knopka_skryt',
                'type' => 'true_false',
                'ui' => 1,
              ),
              1 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_knopka_knopka',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Скрыть2',
                'name' => 'skryt2',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_knopka_skryt2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              4 => 
              array (
                'label' => 'Ссылка кнопки 2',
                'name' => 'ssylka_knopki_2',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_knopka_ssylka_knopki_2',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Кнопка2',
                'name' => 'knopka2',
                'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_knopka_knopka2',
                'type' => 'text',
              ),
            ),
          ),
          4 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_10_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      15 => 
      array (
        'label' => 'Схема Проезда 11',
        'name' => 'shema_proezda_11',
        'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Схема проезда',
            'name' => 'shema_proezda',
            'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Схема проезда',
            'name' => 'shema_proezda',
            'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda',
            'type' => 'image',
            'return_format' => 'url',
          ),
          2 => 
          array (
            'label' => 'Схема проезда в тексте',
            'name' => 'shema_proezda_v_tekste',
            'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_zagolovok_n2',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Схема проезда',
                'name' => 'shema_proezda',
                'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_shema_proezda',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Текст',
                    'name' => 'tekst',
                    'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_shema_proezda_tekst',
                    'type' => 'text',
                  ),
                  1 => 
                  array (
                    'label' => 'Описание',
                    'name' => 'opisanie',
                    'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_shema_proezda_opisanie',
                    'type' => 'text',
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Последний пункт',
                'name' => 'poslednij_punkt',
                'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_poslednij_punkt',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Блок трансфер',
                'name' => 'blok_transfer',
                'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_blok_transfer',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Тест',
                    'name' => 'test',
                    'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_blok_transfer_test',
                    'type' => 'text',
                  ),
                  1 => 
                  array (
                    'label' => 'Описание трансфер',
                    'name' => 'opisanie_transfer',
                    'key' => 'field_sekcii_stranicy_novyj_god_shema_proezda_11_shema_proezda_v_tekste_blok_transfer_opisanie_transfer',
                    'type' => 'textarea',
                    'new_lines' => 'br',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      16 => 
      array (
        'label' => 'Секция 12',
        'name' => 'sekciya_12',
        'key' => 'field_sekcii_stranicy_novyj_god_sekciya_12',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_novyj_god_sekciya_12_zagolovok_n2',
            'type' => 'text',
          ),
        ),
      ),
      17 => NULL,
      18 => NULL,
      19 => NULL,
      20 => NULL,
      21 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      22 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      23 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_sekcii_stranicy_novyj_god_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
    ),
  ),
  1 => 
  array (
    'label' => 'Секция Нового Года',
    'name' => 'sekciya_novogo_goda',
    'key' => 'field_sekciya_novogo_goda',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Заголовок Н2',
        'name' => 'zagolovok_n2',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n2',
        'type' => 'text',
      ),
      1 => 
      array (
        'label' => 'Описание под заголовком',
        'name' => 'opisanie_pod_zagolovkom',
        'key' => 'field_sekciya_novogo_goda_opisanie_pod_zagolovkom',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      2 => 
      array (
        'label' => 'Изображение 1',
        'name' => 'izobrazhenie_1',
        'key' => 'field_sekciya_novogo_goda_izobrazhenie_1',
        'type' => 'image',
        'return_format' => 'array',
      ),
      3 => 
      array (
        'label' => 'Заголовок Н4 1',
        'name' => 'zagolovok_n4_1',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n4_1',
        'type' => 'text',
      ),
      4 => 
      array (
        'label' => 'Описание 1',
        'name' => 'opisanie_1',
        'key' => 'field_sekciya_novogo_goda_opisanie_1',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      5 => 
      array (
        'label' => 'Изображение 1',
        'name' => 'izobrazhenie_1',
        'key' => 'field_sekciya_novogo_goda_izobrazhenie_1',
        'type' => 'image',
        'return_format' => 'array',
      ),
      6 => 
      array (
        'label' => 'Заголовок Н4 1',
        'name' => 'zagolovok_n4_1',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n4_1',
        'type' => 'text',
      ),
      7 => 
      array (
        'label' => 'Описание 1',
        'name' => 'opisanie_1',
        'key' => 'field_sekciya_novogo_goda_opisanie_1',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      8 => 
      array (
        'label' => 'Изображение 1',
        'name' => 'izobrazhenie_1',
        'key' => 'field_sekciya_novogo_goda_izobrazhenie_1',
        'type' => 'image',
        'return_format' => 'array',
      ),
      9 => 
      array (
        'label' => 'Заголовок Н4 1',
        'name' => 'zagolovok_n4_1',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n4_1',
        'type' => 'text',
      ),
      10 => 
      array (
        'label' => 'Описание 1',
        'name' => 'opisanie_1',
        'key' => 'field_sekciya_novogo_goda_opisanie_1',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      11 => 
      array (
        'label' => 'Изображение 2',
        'name' => 'izobrazhenie_2',
        'key' => 'field_sekciya_novogo_goda_izobrazhenie_2',
        'type' => 'image',
        'return_format' => 'array',
      ),
      12 => 
      array (
        'label' => 'Заголовок Н4 2',
        'name' => 'zagolovok_n4_2',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n4_2',
        'type' => 'text',
      ),
      13 => 
      array (
        'label' => 'Описание 2',
        'name' => 'opisanie_2',
        'key' => 'field_sekciya_novogo_goda_opisanie_2',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      14 => 
      array (
        'label' => 'Изображение 3',
        'name' => 'izobrazhenie_3',
        'key' => 'field_sekciya_novogo_goda_izobrazhenie_3',
        'type' => 'image',
        'return_format' => 'array',
      ),
      15 => 
      array (
        'label' => 'Заголовок Н4 3',
        'name' => 'zagolovok_n4_3',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n4_3',
        'type' => 'text',
      ),
      16 => 
      array (
        'label' => 'Описание 3',
        'name' => 'opisanie_3',
        'key' => 'field_sekciya_novogo_goda_opisanie_3',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      17 => 
      array (
        'label' => 'Изображение 4',
        'name' => 'izobrazhenie_4',
        'key' => 'field_sekciya_novogo_goda_izobrazhenie_4',
        'type' => 'image',
        'return_format' => 'array',
      ),
      18 => 
      array (
        'label' => 'Заголовок Н4 4',
        'name' => 'zagolovok_n4_4',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n4_4',
        'type' => 'text',
      ),
      19 => 
      array (
        'label' => 'Описание 4',
        'name' => 'opisanie_4',
        'key' => 'field_sekciya_novogo_goda_opisanie_4',
        'type' => 'textarea',
        'new_lines' => 'br',
      ),
      20 => 
      array (
        'label' => 'Заголовок Н2',
        'name' => 'zagolovok_n2',
        'key' => 'field_sekciya_novogo_goda_zagolovok_n2',
        'type' => 'text',
      ),
      21 => 
      array (
        'label' => 'Кнопка',
        'name' => 'knopka',
        'key' => 'field_sekciya_novogo_goda_knopka',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Скрыть',
            'name' => 'skryt',
            'key' => 'field_sekciya_novogo_goda_knopka_skryt',
            'type' => 'true_false',
            'ui' => 1,
          ),
          1 => 
          array (
            'label' => 'Ссылка кнопки',
            'name' => 'ssylka_knopki',
            'key' => 'field_sekciya_novogo_goda_knopka_ssylka_knopki',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekciya_novogo_goda_knopka_knopka',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Скрыть2',
            'name' => 'skryt2',
            'key' => 'field_sekciya_novogo_goda_knopka_skryt2',
            'type' => 'true_false',
            'ui' => 1,
          ),
          4 => 
          array (
            'label' => 'Ссылка кнопки 2',
            'name' => 'ssylka_knopki_2',
            'key' => 'field_sekciya_novogo_goda_knopka_ssylka_knopki_2',
            'type' => 'text',
          ),
          5 => 
          array (
            'label' => 'Кнопка2',
            'name' => 'knopka2',
            'key' => 'field_sekciya_novogo_goda_knopka_knopka2',
            'type' => 'text',
          ),
        ),
      ),
      22 => 
      array (
        'label' => 'Изображение',
        'name' => 'izobrazhenie',
        'key' => 'field_sekciya_novogo_goda_izobrazhenie',
        'type' => 'image',
        'return_format' => 'array',
      ),
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'korporativ-novye',
'title' => 'Корпоратив Новые',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'meropriyatiya-novye.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Секции Страницы Корпоратив новый',
    'name' => 'sekcii_stranicy_korporativ_novyj',
    'key' => 'field_sekcii_stranicy_korporativ_novyj',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая Секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение первой секции',
            'name' => 'izobrazhenie_pervoj_sekcii',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_izobrazhenie_pervoj_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Мини текст выше заголовка',
            'name' => 'mini_tekst_vyshe_zagolovka',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_mini_tekst_vyshe_zagolovka',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Заголовок H1',
            'name' => 'zagolovok_h1',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_zagolovok_h1',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Мини текст ниже заголовка',
            'name' => 'mini_tekst_nizhe_zagolovka',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_mini_tekst_nizhe_zagolovka',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Кнопка на первом экране',
            'name' => 'knopka_na_pervom_ekrane',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_knopka_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          5 => 
          array (
            'label' => 'Секция предложение гостям',
            'name' => 'sekciya_predlozhenie_gostyam',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Описание под заголовком',
                'name' => 'opisanie_pod_zagolovkom',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_opisanie_pod_zagolovkom',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n2',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание под заголовком 2',
                'name' => 'opisanie_pod_zagolovkom_2',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_opisanie_pod_zagolovkom_2',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              3 => 
              array (
                'label' => 'Изображение 1',
                'name' => 'izobrazhenie_1',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_izobrazhenie_1',
                'type' => 'image',
                'return_format' => 'url',
              ),
              4 => 
              array (
                'label' => 'Заголовок Н3 1',
                'name' => 'zagolovok_n3_1',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n3_1',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Изображение 2',
                'name' => 'izobrazhenie_2',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_izobrazhenie_2',
                'type' => 'image',
                'return_format' => 'url',
              ),
              6 => 
              array (
                'label' => 'Заголовок Н3 2',
                'name' => 'zagolovok_n3_2',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n3_2',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Изображение 3',
                'name' => 'izobrazhenie_3',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_izobrazhenie_3',
                'type' => 'image',
                'return_format' => 'url',
              ),
              8 => 
              array (
                'label' => 'Заголовок Н3 3',
                'name' => 'zagolovok_n3_3',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n3_3',
                'type' => 'text',
              ),
            ),
          ),
          6 => 
          array (
            'label' => 'Преимущества',
            'name' => 'preimuschestva',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_preimuschestva',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          7 => 
          array (
            'label' => 'Секция 2',
            'name' => 'sekciya_2',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_2_zagolovok_n2',
                'type' => 'text',
              ),
            ),
          ),
          8 => 
          array (
            'label' => 'Секция со слайдером 1',
            'name' => 'sekciya_so_slajderom_1',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              3 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              4 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_1_zagolovok_n2',
                'type' => 'text',
              ),
            ),
          ),
          9 => 
          array (
            'label' => 'Секция со слайдером 2',
            'name' => 'sekciya_so_slajderom_2',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              1 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2_zagolovok_n4',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_2_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
            ),
          ),
          10 => 
          array (
            'label' => 'Секция со слайдером 3',
            'name' => 'sekciya_so_slajderom_3',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                  1 => 
                  array (
                    'label' => 'Скрыть кнопку',
                    'name' => 'skryt_knopku',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_knopka_skryt_knopku',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              3 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_3_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
            ),
          ),
          11 => 
          array (
            'label' => 'Секция со слайдером 4',
            'name' => 'sekciya_so_slajderom_4',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              1 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_zagolovok_n4',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                  1 => 
                  array (
                    'label' => 'Скрыть кнопку',
                    'name' => 'skryt_knopku',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_4_knopka_skryt_knopku',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
            ),
          ),
          12 => 
          array (
            'label' => 'Секция со слайдером 5',
            'name' => 'sekciya_so_slajderom_5',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              3 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_sekciya_so_slajderom_5_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
            ),
          ),
          13 => 
          array (
            'label' => 'Баннер биг синий',
            'name' => 'banner_big_sinij',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_banner_big_sinij',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Мини текст',
                'name' => 'mini_tekst',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_banner_big_sinij_mini_tekst',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_banner_big_sinij_zagolovok_n2',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_banner_big_sinij_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть кнопку',
                    'name' => 'skryt_knopku',
                    'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_banner_big_sinij_knopka_skryt_knopku',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
              3 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_banner_big_sinij_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
          14 => 
          array (
            'label' => 'Блок с картой',
            'name' => 'blok_s_kartoj',
            'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_blok_s_kartoj',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Текст',
                'name' => 'tekst',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_blok_s_kartoj_tekst',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              1 => 
              array (
                'label' => 'Код карты',
                'name' => 'kod_karty',
                'key' => 'field_sekcii_stranicy_korporativ_novyj_pervaya_sekciya_blok_s_kartoj_kod_karty',
                'type' => 'acf_code_field',
                'mode' => 'htmlmixed',
                'theme' => 'elegant',
              ),
            ),
          ),
        ),
      ),
      5 => NULL,
      6 => NULL,
      7 => NULL,
      8 => NULL,
      9 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      10 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      11 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_sekcii_stranicy_korporativ_novyj_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      12 => NULL,
      13 => NULL,
      14 => NULL,
      15 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      16 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'svadby',
'title' => 'Свадьбы',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'svadby.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Секции Страницы Свадьбы',
    'name' => 'sekcii_stranicy_svadby',
    'key' => 'field_sekcii_stranicy_svadby',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая Секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение первой секции',
            'name' => 'izobrazhenie_pervoj_sekcii',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_izobrazhenie_pervoj_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Мини текст выше заголовка',
            'name' => 'mini_tekst_vyshe_zagolovka',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_mini_tekst_vyshe_zagolovka',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Заголовок H1',
            'name' => 'zagolovok_h1',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_zagolovok_h1',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Мини текст ниже заголовка',
            'name' => 'mini_tekst_nizhe_zagolovka',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_mini_tekst_nizhe_zagolovka',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Кнопка на первом экране',
            'name' => 'knopka_na_pervom_ekrane',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_knopka_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          5 => 
          array (
            'label' => 'Секция предложение гостям',
            'name' => 'sekciya_predlozhenie_gostyam',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Описание под заголовком',
                'name' => 'opisanie_pod_zagolovkom',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_opisanie_pod_zagolovkom',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n2',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание под заголовком 2',
                'name' => 'opisanie_pod_zagolovkom_2',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_opisanie_pod_zagolovkom_2',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              3 => 
              array (
                'label' => 'Изображение 1',
                'name' => 'izobrazhenie_1',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_izobrazhenie_1',
                'type' => 'image',
                'return_format' => 'url',
              ),
              4 => 
              array (
                'label' => 'Заголовок Н3 1',
                'name' => 'zagolovok_n3_1',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n3_1',
                'type' => 'text',
              ),
              5 => 
              array (
                'label' => 'Изображение 2',
                'name' => 'izobrazhenie_2',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_izobrazhenie_2',
                'type' => 'image',
                'return_format' => 'url',
              ),
              6 => 
              array (
                'label' => 'Заголовок Н3 2',
                'name' => 'zagolovok_n3_2',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n3_2',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Изображение 3',
                'name' => 'izobrazhenie_3',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_izobrazhenie_3',
                'type' => 'image',
                'return_format' => 'url',
              ),
              8 => 
              array (
                'label' => 'Заголовок Н3 3',
                'name' => 'zagolovok_n3_3',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n3_3',
                'type' => 'text',
              ),
              9 => 
              array (
                'label' => 'Изображение 4',
                'name' => 'izobrazhenie_4',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_izobrazhenie_4',
                'type' => 'image',
                'return_format' => 'url',
              ),
              10 => 
              array (
                'label' => 'Заголовок Н3 4',
                'name' => 'zagolovok_n3_4',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_predlozhenie_gostyam_zagolovok_n3_4',
                'type' => 'text',
              ),
            ),
          ),
          6 => 
          array (
            'label' => 'Секция Преимущества',
            'name' => 'sekciya_preimuschestva',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_preimuschestva',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
          7 => 
          array (
            'label' => 'Секция слайдер 1',
            'name' => 'sekciya_slajder_1',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                  1 => 
                  array (
                    'label' => 'Скрыть кнопку',
                    'name' => 'skryt_knopku',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_knopka_skryt_knopku',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
              3 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              4 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_1_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
            ),
          ),
          8 => 
          array (
            'label' => 'Секция слайдер 2',
            'name' => 'sekciya_slajder_2',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              1 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_zagolovok_n4',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              4 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                  1 => 
                  array (
                    'label' => 'Скрыть кнопку',
                    'name' => 'skryt_knopku',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_2_knopka_skryt_knopku',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
            ),
          ),
          9 => 
          array (
            'label' => 'Секция слайдер 3',
            'name' => 'sekciya_slajder_3',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3_zagolovok_n4',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              3 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_3_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
            ),
          ),
          10 => 
          array (
            'label' => 'Секция слайдер 4',
            'name' => 'sekciya_slajder_4',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              1 => 
              array (
                'label' => 'Слайдер Тверь',
                'name' => 'slajder_tver',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_slajder_tver',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Загрузить фото',
                    'name' => 'zagruzit_foto',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_slajder_tver_zagruzit_foto',
                    'type' => 'image',
                    'return_format' => 'array',
                  ),
                ),
              ),
              2 => 
              array (
                'label' => 'Заголовок Н4',
                'name' => 'zagolovok_n4',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_zagolovok_n4',
                'type' => 'text',
              ),
              3 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              4 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть2',
                    'name' => 'skryt2',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_slajder_4_knopka_skryt2',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
            ),
          ),
          11 => 
          array (
            'label' => 'Секция Мечты',
            'name' => 'sekciya_mechty',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_mechty',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_mechty_zagolovok_n2',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_sekciya_mechty_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
          12 => 
          array (
            'label' => 'Верховая Баннер биг',
            'name' => 'verhovaya_banner_big',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_verhovaya_banner_big',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Мини текст',
                'name' => 'mini_tekst',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_verhovaya_banner_big_mini_tekst',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н2',
                'name' => 'zagolovok_n2',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_verhovaya_banner_big_zagolovok_n2',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_verhovaya_banner_big_knopka',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Скрыть кнопку',
                    'name' => 'skryt_knopku',
                    'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_verhovaya_banner_big_knopka_skryt_knopku',
                    'type' => 'true_false',
                    'ui' => 1,
                  ),
                ),
              ),
              3 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_verhovaya_banner_big_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
            ),
          ),
          13 => 
          array (
            'label' => 'Блок с картой',
            'name' => 'blok_s_kartoj',
            'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_blok_s_kartoj',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Текст',
                'name' => 'tekst',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_blok_s_kartoj_tekst',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              1 => 
              array (
                'label' => 'Код карты',
                'name' => 'kod_karty',
                'key' => 'field_sekcii_stranicy_svadby_pervaya_sekciya_blok_s_kartoj_kod_karty',
                'type' => 'acf_code_field',
                'mode' => 'htmlmixed',
                'theme' => 'elegant',
              ),
            ),
          ),
        ),
      ),
      5 => NULL,
      6 => NULL,
      7 => NULL,
      8 => NULL,
      9 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      10 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      11 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_sekcii_stranicy_svadby_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      12 => NULL,
      13 => NULL,
      14 => NULL,
      15 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      16 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'detskie-moropriyatiya',
'title' => 'Детские мороприятия',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'detskie.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Секции Страницы Детские',
    'name' => 'sekcii_stranicy_detskie',
    'key' => 'field_sekcii_stranicy_detskie',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_sekcii_stranicy_detskie_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение первой секции',
            'name' => 'izobrazhenie_pervoj_sekcii',
            'key' => 'field_sekcii_stranicy_detskie_pervaya_sekciya_izobrazhenie_pervoj_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Мини текст выше заголовка',
            'name' => 'mini_tekst_vyshe_zagolovka',
            'key' => 'field_sekcii_stranicy_detskie_pervaya_sekciya_mini_tekst_vyshe_zagolovka',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Заголовок H1',
            'name' => 'zagolovok_h1',
            'key' => 'field_sekcii_stranicy_detskie_pervaya_sekciya_zagolovok_h1',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Мини текст ниже заголовка',
            'name' => 'mini_tekst_nizhe_zagolovka',
            'key' => 'field_sekcii_stranicy_detskie_pervaya_sekciya_mini_tekst_nizhe_zagolovka',
            'type' => 'text',
          ),
          4 => 
          array (
            'label' => 'Кнопка на первом экране',
            'name' => 'knopka_na_pervom_ekrane',
            'key' => 'field_sekcii_stranicy_detskie_pervaya_sekciya_knopka_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => NULL,
          ),
        ),
      ),
      5 => 
      array (
        'label' => 'Секция 2',
        'name' => 'sekciya_2',
        'key' => 'field_sekcii_stranicy_detskie_sekciya_2',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_detskie_sekciya_2_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_detskie_sekciya_2_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Баннер биг 1',
        'name' => 'banner_big_1',
        'key' => 'field_sekcii_stranicy_detskie_banner_big_1',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_1_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_1_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_1_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_sekcii_stranicy_detskie_banner_big_1_knopka_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
            ),
          ),
          3 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_1_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Секция 4',
        'name' => 'sekciya_4',
        'key' => 'field_sekcii_stranicy_detskie_sekciya_4',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_detskie_sekciya_4_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      8 => 
      array (
        'label' => 'Баннер биг 2',
        'name' => 'banner_big_2',
        'key' => 'field_sekcii_stranicy_detskie_banner_big_2',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_2_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_2_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Кнопка',
            'name' => 'knopka',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_2_knopka',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_stranicy_detskie_banner_big_2_knopka_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_stranicy_detskie_banner_big_2_knopka_knopka',
                'type' => 'text',
              ),
            ),
          ),
          3 => 
          array (
            'label' => 'Изображение',
            'name' => 'izobrazhenie',
            'key' => 'field_sekcii_stranicy_detskie_banner_big_2_izobrazhenie',
            'type' => 'image',
            'return_format' => 'array',
          ),
        ),
      ),
      9 => 
      array (
        'label' => 'Блок с картой',
        'name' => 'blok_s_kartoj',
        'key' => 'field_sekcii_stranicy_detskie_blok_s_kartoj',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Текст',
            'name' => 'tekst',
            'key' => 'field_sekcii_stranicy_detskie_blok_s_kartoj_tekst',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          1 => 
          array (
            'label' => 'Код карты',
            'name' => 'kod_karty',
            'key' => 'field_sekcii_stranicy_detskie_blok_s_kartoj_kod_karty',
            'type' => 'acf_code_field',
            'mode' => 'htmlmixed',
            'theme' => 'elegant',
          ),
        ),
      ),
      10 => NULL,
      11 => NULL,
      12 => NULL,
      13 => NULL,
      14 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      15 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      16 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_sekcii_stranicy_detskie_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
      17 => NULL,
      18 => NULL,
      19 => NULL,
      20 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      21 => NULL,
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'stranica-razmescheniya',
'title' => 'Страница размещения',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'razmeshchenie.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Страница Бронировнаия',
    'name' => 'stranica_bronirovnaiya',
    'key' => 'field_stranica_bronirovnaiya',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Блок статей',
        'name' => 'blok_statej',
        'key' => 'field_stranica_bronirovnaiya_blok_statej',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н1',
            'name' => 'zagolovok_n1',
            'key' => 'field_stranica_bronirovnaiya_blok_statej_zagolovok_n1',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Вставить код',
            'name' => 'vstavit_kod',
            'key' => 'field_stranica_bronirovnaiya_blok_statej_vstavit_kod',
            'type' => 'acf_code_field',
            'mode' => 'htmlmixed',
            'theme' => 'elegant',
          ),
        ),
      ),
      5 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Блок статей',
        'name' => 'blok_statej',
        'key' => 'field_stranica_bronirovnaiya_blok_statej',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Дополнительный Текст',
            'name' => 'dopolnitelnyj_tekst',
            'key' => 'field_stranica_bronirovnaiya_blok_statej_dopolnitelnyj_tekst',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Блок статей',
        'name' => 'blok_statej',
        'key' => 'field_stranica_bronirovnaiya_blok_statej',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Мини текст',
            'name' => 'mini_tekst',
            'key' => 'field_stranica_bronirovnaiya_blok_statej_mini_tekst',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_stranica_bronirovnaiya_blok_statej_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание под заголовком',
            'name' => 'opisanie_pod_zagolovkom',
            'key' => 'field_stranica_bronirovnaiya_blok_statej_opisanie_pod_zagolovkom',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
        ),
      ),
      8 => NULL,
      9 => NULL,
      10 => NULL,
      11 => NULL,
      12 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      13 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      14 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_stranica_bronirovnaiya_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'skrytaya-stranica',
'title' => 'Скрытая страница',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'skrytaya-stranica.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Секции Скрытой страницы',
    'name' => 'sekcii_skrytoj_stranicy',
    'key' => 'field_sekcii_skrytoj_stranicy',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => 
      array (
        'label' => 'Первая Секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_sekcii_skrytoj_stranicy_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение первой секции',
            'name' => 'izobrazhenie_pervoj_sekcii',
            'key' => 'field_sekcii_skrytoj_stranicy_pervaya_sekciya_izobrazhenie_pervoj_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок H1',
            'name' => 'zagolovok_h1',
            'key' => 'field_sekcii_skrytoj_stranicy_pervaya_sekciya_zagolovok_h1',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Мини текст ниже заголовка',
            'name' => 'mini_tekst_nizhe_zagolovka',
            'key' => 'field_sekcii_skrytoj_stranicy_pervaya_sekciya_mini_tekst_nizhe_zagolovka',
            'type' => 'text',
          ),
          3 => 
          array (
            'label' => 'Кнопка на первом экране',
            'name' => 'knopka_na_pervom_ekrane',
            'key' => 'field_sekcii_skrytoj_stranicy_pervaya_sekciya_knopka_na_pervom_ekrane',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_skrytoj_stranicy_pervaya_sekciya_knopka_na_pervom_ekrane_ssylka_knopki',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_skrytoj_stranicy_pervaya_sekciya_knopka_na_pervom_ekrane_knopka',
                'type' => 'text',
              ),
            ),
          ),
        ),
      ),
      1 => 
      array (
        'label' => 'Секция Досуга',
        'name' => 'sekciya_dosuga',
        'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Добавить класс',
            'name' => 'dobavit_klass',
            'instructions' => 'card-1, card-2',
            'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_dobavit_klass',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н3',
            'name' => 'zagolovok_n3',
            'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_zagolovok_n3',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Скрыть кнопку 2',
            'name' => 'skryt_knopku_2',
            'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_skryt_knopku_2',
            'type' => 'true_false',
            'ui' => 1,
          ),
          3 => 
          array (
            'label' => 'Блоки досуга',
            'name' => 'bloki_dosuga',
            'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Добавить класс',
                'name' => 'dobavit_klass',
                'instructions' => 'card-1, card-2',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_dobavit_klass',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Заголовок Н3',
                'name' => 'zagolovok_n3',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_zagolovok_n3',
                'type' => 'text',
              ),
              2 => 
              array (
                'label' => 'Описание',
                'name' => 'opisanie',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_opisanie',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
              3 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'Текст',
                    'name' => 'tekst',
                    'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_spisok_tekst',
                    'type' => 'text',
                  ),
                ),
              ),
              4 => 
              array (
                'label' => 'Скрыть кнопку',
                'name' => 'skryt_knopku',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_skryt_knopku',
                'type' => 'true_false',
                'ui' => 1,
              ),
              5 => 
              array (
                'label' => 'Ссылка кнопки',
                'name' => 'ssylka_knopki',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_ssylka_knopki',
                'type' => 'text',
              ),
              6 => 
              array (
                'label' => 'Кнопка',
                'name' => 'knopka',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_knopka',
                'type' => 'text',
              ),
              7 => 
              array (
                'label' => 'Скрыть кнопку 2',
                'name' => 'skryt_knopku_2',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_skryt_knopku_2',
                'type' => 'true_false',
                'ui' => 1,
              ),
              8 => 
              array (
                'label' => 'Изображение',
                'name' => 'izobrazhenie',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_izobrazhenie',
                'type' => 'image',
                'return_format' => 'array',
              ),
              9 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              10 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
              11 => 
              array (
                'label' => 'Список',
                'name' => 'spisok',
                'key' => 'field_sekcii_skrytoj_stranicy_sekciya_dosuga_bloki_dosuga_spisok',
                'type' => 'repeater',
                'layout' => 'block',
                'sub_fields' => NULL,
              ),
            ),
          ),
        ),
      ),
      2 => NULL,
      3 => NULL,
      4 => NULL,
      5 => NULL,
      6 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      7 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      8 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_sekcii_skrytoj_stranicy_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'stranica-akcij-mnogo',
'title' => 'Страница акций много',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'akcii.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Секции страницы акций',
    'name' => 'sekcii_stranicy_akcij',
    'key' => 'field_sekcii_stranicy_akcij',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Первая секция',
        'name' => 'pervaya_sekciya',
        'key' => 'field_sekcii_stranicy_akcij_pervaya_sekciya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение верхней секции',
            'name' => 'izobrazhenie_verhnej_sekcii',
            'key' => 'field_sekcii_stranicy_akcij_pervaya_sekciya_izobrazhenie_verhnej_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_sekcii_stranicy_akcij_pervaya_sekciya_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Описание Страницы',
            'name' => 'opisanie_stranicy',
            'key' => 'field_sekcii_stranicy_akcij_pervaya_sekciya_opisanie_stranicy',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => NULL,
          4 => NULL,
          5 => NULL,
          6 => NULL,
          7 => NULL,
          8 => NULL,
        ),
      ),
      5 => 
      array (
        'label' => 'Секция CEO',
        'name' => 'sekciya_ceo',
        'key' => 'field_sekcii_stranicy_akcij_sekciya_ceo',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Дополнительный Текст',
            'name' => 'dopolnitelnyj_tekst',
            'key' => 'field_sekcii_stranicy_akcij_sekciya_ceo_dopolnitelnyj_tekst',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      6 => NULL,
      7 => NULL,
      8 => NULL,
      9 => NULL,
      10 => NULL,
      11 => NULL,
      12 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      13 => NULL,
      14 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      15 => NULL,
      16 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      17 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      18 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_sekcii_stranicy_akcij_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
    ),
  ),
),
));
endif;
?><?php
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
'key' => 'stranica-akcii',
'title' => 'Страница акции',
'menu_order' => 0,
'location' => array(
  array(
    array(
      'param' => 'post_template',
      'operator' => '==',
      'value' => 'stranica-akcii.php',
    ),
  ),
),
'hide_on_screen' => array(
  0 => 'the_content',
),
'fields' => array (
  0 => 
  array (
    'label' => 'Страница Акций',
    'name' => 'stranica_akcij',
    'key' => 'field_stranica_akcij',
    'type' => 'group',
    'layout' => 'block',
    'sub_fields' => 
    array (
      0 => NULL,
      1 => NULL,
      2 => NULL,
      3 => NULL,
      4 => 
      array (
        'label' => 'Секция первая',
        'name' => 'sekciya_pervaya',
        'key' => 'field_stranica_akcij_sekciya_pervaya',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Изображение верхней секции',
            'name' => 'izobrazhenie_verhnej_sekcii',
            'key' => 'field_stranica_akcij_sekciya_pervaya_izobrazhenie_verhnej_sekcii',
            'type' => 'image',
            'return_format' => 'url',
          ),
          1 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_stranica_akcij_sekciya_pervaya_zagolovok_n2',
            'type' => 'text',
          ),
          2 => 
          array (
            'label' => 'Мини Описание Акции',
            'name' => 'mini_opisanie_akcii',
            'key' => 'field_stranica_akcij_sekciya_pervaya_mini_opisanie_akcii',
            'type' => 'textarea',
            'new_lines' => 'br',
          ),
          3 => NULL,
          4 => NULL,
          5 => NULL,
          6 => NULL,
          7 => NULL,
          8 => NULL,
        ),
      ),
      5 => 
      array (
        'label' => 'Секция Описание Акции',
        'name' => 'sekciya_opisanie_akcii',
        'key' => 'field_stranica_akcij_sekciya_opisanie_akcii',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Текст акции',
            'name' => 'tekst_akcii',
            'key' => 'field_stranica_akcij_sekciya_opisanie_akcii_tekst_akcii',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      6 => 
      array (
        'label' => 'Секция FAQ',
        'name' => 'sekciya_faq',
        'key' => 'field_stranica_akcij_sekciya_faq',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_stranica_akcij_sekciya_faq_zagolovok_n2',
            'type' => 'text',
          ),
          1 => 
          array (
            'label' => 'Добавить Вопрос',
            'name' => 'dobavit_vopros',
            'key' => 'field_stranica_akcij_sekciya_faq_dobavit_vopros',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'label' => 'Вопрос',
                'name' => 'vopros',
                'key' => 'field_stranica_akcij_sekciya_faq_dobavit_vopros_vopros',
                'type' => 'text',
              ),
              1 => 
              array (
                'label' => 'Ответ',
                'name' => 'otvet',
                'key' => 'field_stranica_akcij_sekciya_faq_dobavit_vopros_otvet',
                'type' => 'textarea',
                'new_lines' => 'br',
              ),
            ),
          ),
        ),
      ),
      7 => 
      array (
        'label' => 'Секция другие акции',
        'name' => 'sekciya_drugie_akcii',
        'key' => 'field_stranica_akcij_sekciya_drugie_akcii',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Заголовок Н2',
            'name' => 'zagolovok_n2',
            'key' => 'field_stranica_akcij_sekciya_drugie_akcii_zagolovok_n2',
            'type' => 'text',
          ),
        ),
      ),
      8 => 
      array (
        'label' => 'Секция CEO',
        'name' => 'sekciya_ceo',
        'key' => 'field_stranica_akcij_sekciya_ceo',
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => 
        array (
          0 => 
          array (
            'label' => 'Дополнительный Текст',
            'name' => 'dopolnitelnyj_tekst',
            'key' => 'field_stranica_akcij_sekciya_ceo_dopolnitelnyj_tekst',
            'type' => 'wysiwyg',
            'toolbar' => 'full',
          ),
        ),
      ),
      9 => NULL,
      10 => NULL,
      11 => NULL,
      12 => NULL,
      13 => NULL,
      14 => NULL,
      15 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
      ),
      16 => NULL,
      17 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => 
        array (
          0 => NULL,
          1 => 
          array (
            0 => NULL,
          ),
          2 => NULL,
          3 => NULL,
          4 => 
          array (
            0 => NULL,
            1 => NULL,
          ),
        ),
      ),
      18 => NULL,
      19 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => NULL,
      ),
      20 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
      ),
      21 => 
      array (
        0 => NULL,
        1 => NULL,
        2 => NULL,
        3 => NULL,
        4 => NULL,
        5 => NULL,
        6 => NULL,
        7 => NULL,
        8 => NULL,
        9 => 
        array (
          'label' => 'Кнопка синяя',
          'name' => 'knopka_sinyaya',
          'key' => 'field_stranica_akcij_kontakty_knopka_sinyaya',
          'type' => 'group',
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => NULL,
            1 => NULL,
            2 => NULL,
            3 => NULL,
            4 => NULL,
            5 => NULL,
          ),
        ),
      ),
    ),
  ),
),
));
endif;
?>