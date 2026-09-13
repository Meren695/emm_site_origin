document.addEventListener('DOMContentLoaded', function() {
    // Инициализация вкладок
    const tabs = document.querySelectorAll('.wpef-tabs .nav-tab');
    const contents = document.querySelectorAll('.wpef-tab-content');
    const btnAddConfig = document.getElementById('wpef-add-config');
    const btnAddField = document.getElementById('wpef-add-global-val-field');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Переключаем активный класс у табов
            tabs.forEach(t => t.classList.remove('nav-tab-active'));
            this.classList.add('nav-tab-active');
            
            const tabName = this.getAttribute('data-tab');

            // Переключаем активный контент
            const targetId = 'tab-' + tabName;
            contents.forEach(c => {
                c.style.display = c.id === targetId ? 'block' : 'none';
            });

            // Скрытие сайдбара на определенных вкладках
            const sidebar = document.querySelector('.wpef-sidebar');
            if (sidebar) {
                if (tabName === 'dashboard' || tabName === 'leads' || tabName === 'logs') {
                    sidebar.style.display = 'none';
                } else {
                    sidebar.style.display = 'block';
                }
            }

            // Лечим баг CodeMirror (он не умеет рисоваться в display: none)
            if (this.getAttribute('data-tab') === 'forms') {
                const blocks = document.querySelectorAll('.wpef-config-block');
                blocks.forEach(block => {
                    if (block.cmInstance) {
                        block.cmInstance.refresh();
                    }
                });
            }

            // Лечим баг CodeMirror для IMask
            if (this.getAttribute('data-tab') === 'settings' && window.wpefImaskCmInstance) {
                window.wpefImaskCmInstance.refresh();
            }

            // Переключаем видимость кнопок в сайдбаре
            if (this.getAttribute('data-tab') === 'forms') {
                btnAddConfig.style.display = 'block';
                btnAddField.style.display = 'none';
            } else if (this.getAttribute('data-tab') === 'validation') {
                btnAddConfig.style.display = 'none';
                btnAddField.style.display = 'block';
            } else {
                btnAddConfig.style.display = 'none';
                btnAddField.style.display = 'none';
            }
        });
    });

    function generateId() {
        return '_' + Math.random().toString(36).substr(2, 9);
    }

    // ==========================================
    // Вкладка: Настройки форм (Configs)
    // ==========================================
    const configsContainer = document.getElementById('wpef-configs-container');
    const configTemplate = document.getElementById('wpef-config-template').innerHTML;
    const addConfigBtn = document.getElementById('wpef-add-config');
    const saveBtn = document.getElementById('wpef-save-configs');
    const spinner = document.getElementById('wpef-spinner');
    const saveMsg = document.getElementById('wpef-save-msg');

    saveBtn.addEventListener('click', function() {
        const activeTab = document.querySelector('.wpef-tabs .nav-tab-active');
        if (!activeTab) return;
        const tabName = activeTab.getAttribute('data-tab');

        // --- СОХРАНЕНИЕ НАСТРОЕК ФОРМ ---
        if (tabName === 'forms') {
            const blocks = configsContainer.querySelectorAll('.wpef-config-block');
            const configs = [];
            let hasError = false;

            blocks.forEach(block => {
                if (hasError) return;

                const name = block.querySelector('[name="name"]').value.trim();
                const selector = block.querySelector('[name="selector"]').value.trim();
                const email_to = block.querySelector('[name="email_to"]').value.trim();
                const email_subject = block.querySelector('[name="email_subject"]').value.trim();
                const email_body = block.querySelector('[name="email_body"]').value.trim();

                if (!name || !selector || !email_to || !email_subject || !email_body) {
                    alert(wpefAdminData.i18n.errFillRequired);
                    hasError = true;
                    return;
                }

                if (block.cmInstance) {
                    block.querySelector('[name="custom_js"]').value = block.cmInstance.getValue();
                }

                const ruleBlocks = block.querySelectorAll('.wpef-rule-block');
                const rules = [];
                ruleBlocks.forEach(rb => {
                    const ruleSelector = rb.querySelector('[name="rule_selector"]').value.trim();
                    if (ruleSelector) {
                        rules.push({
                            selector: ruleSelector,
                            type: rb.querySelector('[name="rule_type"]').value,
                            value: rb.querySelector('[name="rule_value"]').value,
                            error: rb.querySelector('[name="rule_error"]').value
                        });
                    }
                });

                configs.push({
                    id: block.dataset.id,
                    enabled: block.querySelector('[name="enabled"]').checked,
                    name: name,
                    selector: selector,
                    email_to: email_to,
                    email_subject: email_subject,
                    email_body: email_body,
                    
                    reply_enabled: block.querySelector('[name="reply_enabled"]').checked,
                    reply_email_field: block.querySelector('[name="reply_email_field"]').value,
                    reply_subject: block.querySelector('[name="reply_subject"]').value,
                    reply_body: block.querySelector('[name="reply_body"]').value,
                    reply_files: Array.from(block.querySelectorAll('[name="reply_files[]"]')).map(inp => inp.value).filter(val => val.trim() !== ''),
                    redirect_url: block.querySelector('[name="redirect_url"]').value,
                    redirect_blank: block.querySelector('[name="redirect_blank"]').checked,
                    validation_only: block.querySelector('[name="validation_only"]') ? block.querySelector('[name="validation_only"]').checked : false,
                    hide_block_enable: block.querySelector('[name="hide_block_enable"]') ? block.querySelector('[name="hide_block_enable"]').checked : false,
                    hide_block_selector: block.querySelector('[name="hide_block_selector"]') ? block.querySelector('[name="hide_block_selector"]').value : '',
                    hide_block_effect: block.querySelector('[name="hide_block_effect"]') ? block.querySelector('[name="hide_block_effect"]').value : 'fadeOut',
                    hide_block_duration: block.querySelector('[name="hide_block_duration"]') ? block.querySelector('[name="hide_block_duration"]').value : '500',
                    hide_block_delay: block.querySelector('[name="hide_block_delay"]') ? block.querySelector('[name="hide_block_delay"]').value : '0',
                    show_block_enable: block.querySelector('[name="show_block_enable"]') ? block.querySelector('[name="show_block_enable"]').checked : false,
                    show_block_selector: block.querySelector('[name="show_block_selector"]') ? block.querySelector('[name="show_block_selector"]').value : '',
                    show_block_effect: block.querySelector('[name="show_block_effect"]') ? block.querySelector('[name="show_block_effect"]').value : 'fadeIn',
                    show_block_duration: block.querySelector('[name="show_block_duration"]') ? block.querySelector('[name="show_block_duration"]').value : '500',
                    show_block_delay: block.querySelector('[name="show_block_delay"]') ? block.querySelector('[name="show_block_delay"]').value : '0',
                    webhook_tg_enable: block.querySelector('[name="webhook_tg_enable"]') ? block.querySelector('[name="webhook_tg_enable"]').checked : false,
                    webhook_tg_token: block.querySelector('[name="webhook_tg_token"]') ? block.querySelector('[name="webhook_tg_token"]').value : '',
                    webhook_tg_chat: block.querySelector('[name="webhook_tg_chat"]') ? block.querySelector('[name="webhook_tg_chat"]').value : '',
                    webhook_bx_enable: block.querySelector('[name="webhook_bx_enable"]') ? block.querySelector('[name="webhook_bx_enable"]').checked : false,
                    webhook_bx_url: block.querySelector('[name="webhook_bx_url"]') ? block.querySelector('[name="webhook_bx_url"]').value : '',
                    webhook_bx_mapping: Array.from(block.querySelectorAll('.wpef-bx-mapping-row')).map(row => {
                        return {
                            form_field: row.querySelector('[name="bx_map_form_field"]').value.trim(),
                            json_key: row.querySelector('[name="bx_map_json_key"]').value.trim()
                        };
                    }).filter(map => map.form_field !== '' && map.json_key !== ''),
                    webhook_custom_enable: block.querySelector('[name="webhook_custom_enable"]') ? block.querySelector('[name="webhook_custom_enable"]').checked : false,
                    webhook_url: block.querySelector('[name="webhook_url"]') ? block.querySelector('[name="webhook_url"]').value : '',
                    webhook_mapping: Array.from(block.querySelectorAll('.wpef-mapping-row')).map(row => {
                        return {
                            form_field: row.querySelector('[name="map_form_field"]').value.trim(),
                            json_key: row.querySelector('[name="map_json_key"]').value.trim()
                        };
                    }).filter(map => map.form_field !== '' && map.json_key !== ''),
                    analytics_ym_id: block.querySelector('[name="analytics_ym_id"]') ? block.querySelector('[name="analytics_ym_id"]').value.trim() : '',
                    analytics_ym_goal: block.querySelector('[name="analytics_ym_goal"]') ? block.querySelector('[name="analytics_ym_goal"]').value.trim() : '',
                    analytics_ga_event: block.querySelector('[name="analytics_ga_event"]') ? block.querySelector('[name="analytics_ga_event"]').value.trim() : '',
                    analytics_vk_event: block.querySelector('[name="analytics_vk_event"]') ? block.querySelector('[name="analytics_vk_event"]').value.trim() : '',
                    conditions: Array.from(block.querySelectorAll('.wpef-condition-item')).map(row => {
                        return {
                            trigger_name: row.querySelector('[name="cond_trigger_name"]').value.trim(),
                            operator: row.querySelector('[name="cond_operator"]').value,
                            trigger_value: row.querySelector('[name="cond_trigger_value"]').value.trim(),
                            target_selector: row.querySelector('[name="cond_target_selector"]').value.trim(),
                            action: row.querySelector('[name="cond_action"]').value,
                            effect: row.querySelector('[name="cond_effect"]') ? row.querySelector('[name="cond_effect"]').value : 'slide',
                            duration: row.querySelector('[name="cond_duration"]') ? row.querySelector('[name="cond_duration"]').value.trim() : '300',
                            delay: row.querySelector('[name="cond_delay"]') ? row.querySelector('[name="cond_delay"]').value.trim() : '0'
                        };
                    }).filter(cond => cond.trigger_name !== '' && cond.target_selector !== ''),
                    route_enable: block.querySelector('[name="route_enable"]') ? block.querySelector('[name="route_enable"]').checked : false,
                    routes: Array.from(block.querySelectorAll('.wpef-route-item')).map(row => {
                        return {
                            trigger_name: row.querySelector('[name="route_trigger_name"]').value.trim(),
                            operator: row.querySelector('[name="route_operator"]').value,
                            trigger_value: row.querySelector('[name="route_trigger_value"]').value.trim(),
                            target_email: row.querySelector('[name="route_target_email"]').value.trim()
                        };
                    }).filter(route => route.trigger_name !== '' && route.target_email !== ''),
                    loader_enable: block.querySelector('[name="loader_enable"]') ? block.querySelector('[name="loader_enable"]').checked : true,
                    loader_color: block.querySelector('[name="loader_color"]') ? block.querySelector('[name="loader_color"]').value : '#4f46e5',
                    loader_bg: block.querySelector('[name="loader_bg"]') ? block.querySelector('[name="loader_bg"]').value : 'rgba(255, 255, 255, 0.7)',
                    
                    validation_rules: rules,
                    custom_js: block.cmInstance ? block.cmInstance.getValue() : block.querySelector('[name="custom_js"]').value
                });
            });

            if (hasError) return;

            spinner.classList.add('is-active');
            saveMsg.style.display = 'none';

            const formData = new FormData();
            formData.append('action', 'wpef_save_configs');
            formData.append('nonce', wpefAdminData.nonce);
            formData.append('configs', JSON.stringify(configs));

            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                spinner.classList.remove('is-active');
                if (res.success) {
                    saveMsg.textContent = res.data;
                    saveMsg.style.display = 'inline-block';
                    setTimeout(() => saveMsg.style.display = 'none', 3000);
                    wpefAdminData.configs = configs;
                } else {
                    alert(wpefAdminData.i18n.errSave + res.data);
                }
            })
            .catch(err => {
                spinner.classList.remove('is-active');
                alert(wpefAdminData.i18n.errNetwork);
            });
            return;
        }

        // --- СОХРАНЕНИЕ ОБЩИХ НАСТРОЕК (Вкладка "Настройки" и "SMTP") ---
        if (tabName === 'settings' || tabName === 'smtp') {
            spinner.classList.add('is-active');
            saveMsg.style.display = 'none';

            // Новые настройки
            const enableUtm = document.getElementById('wpef-enable-utm');
            const enableDeviceInfo = document.getElementById('wpef-enable-device-info');
            const enableBasicTechInfo = document.getElementById('wpef-enable-basic-tech-info');
            const usePageTitle = document.getElementById('wpef-use-page-title');
            const enableSendEmptyFields = document.getElementById('wpef-enable-send-empty-fields');
            const saveToMedia = document.getElementById('wpef-save-to-media');
            const maxAttachmentSize = document.getElementById('wpef-max-attachment-size');
            const cleanupLeadsDays = document.getElementById('wpef-cleanup-leads-days');
            const cleanupLogsDays = document.getElementById('wpef-cleanup-logs-days');
            const enableCacheCompat = document.getElementById('wpef-enable-cache-compat');
            
            const smtpEnable = document.getElementById('wpef-smtp-enable');
            const smtpHost = document.getElementById('wpef-smtp-host');
            const smtpPort = document.getElementById('wpef-smtp-port');
            const smtpAutoTls = document.getElementById('wpef-smtp-auto-tls');
            const smtpAuth = document.getElementById('wpef-smtp-auth');
            const smtpUser = document.getElementById('wpef-smtp-user');
            const smtpPass = document.getElementById('wpef-smtp-pass');
            const smtpFromEmail = document.getElementById('wpef-smtp-from-email');
            const smtpForceFromEmail = document.getElementById('wpef-smtp-force-from-email');
            const smtpFromName = document.getElementById('wpef-smtp-from-name');
            const smtpEncRadio = document.querySelector('input[name="wpef_smtp_encryption"]:checked');

            const generalSettings = {
                disable_auto_required: disableAutoRequired ? disableAutoRequired.checked : false,
                load_just_validate: loadJustValidate ? loadJustValidate.checked : true,
                load_imask: loadImask ? loadImask.checked : false,
                imask_js: window.wpefImaskCmInstance ? window.wpefImaskCmInstance.getValue() : (imaskJsField ? imaskJsField.value : ''),
                enable_honeypot: enableHoneypot ? enableHoneypot.checked : false,
                enable_rate_limit: enableRateLimit ? enableRateLimit.checked : false,
                rate_limit_count: rateLimitCount ? parseInt(rateLimitCount.value, 10) : 5,
                rate_limit_time: rateLimitTime ? parseInt(rateLimitTime.value, 10) : 10,
                enable_utm: enableUtm ? enableUtm.checked : false,
                enable_device_info: enableDeviceInfo ? enableDeviceInfo.checked : false,
                enable_basic_tech_info: enableBasicTechInfo ? enableBasicTechInfo.checked : true,
                use_page_title: usePageTitle ? usePageTitle.checked : false,
                enable_send_empty_fields: enableSendEmptyFields ? enableSendEmptyFields.checked : false,
                save_to_media: saveToMedia ? saveToMedia.checked : false,
                max_attachment_size: maxAttachmentSize ? maxAttachmentSize.value : 10,
                cleanup_leads_days: cleanupLeadsDays ? parseInt(cleanupLeadsDays.value, 10) : 0,
                cleanup_logs_days: cleanupLogsDays ? parseInt(cleanupLogsDays.value, 10) : 30,
                enable_cache_compat: enableCacheCompat && enableCacheCompat.checked ? 'on' : 'off',
                
                smtp_enable: smtpEnable && smtpEnable.checked ? 'on' : 'off',
                smtp_host: smtpHost ? smtpHost.value : '',
                smtp_port: smtpPort ? smtpPort.value : 465,
                smtp_auto_tls: smtpAutoTls && smtpAutoTls.checked ? 'on' : 'off',
                smtp_auth: smtpAuth && smtpAuth.checked ? 'on' : 'off',
                smtp_user: smtpUser ? smtpUser.value : '',
                smtp_pass: smtpPass ? smtpPass.value : '',
                smtp_from_email: smtpFromEmail ? smtpFromEmail.value : '',
                smtp_force_from_email: smtpForceFromEmail && smtpForceFromEmail.checked ? 'on' : 'off',
                smtp_from_name: smtpFromName ? smtpFromName.value : '',
                smtp_encryption: smtpEncRadio ? smtpEncRadio.value : 'ssl'
            };

            const formData = new FormData();
            formData.append('action', 'wpef_save_general');
            formData.append('nonce', wpefAdminData.nonce_validation);
            formData.append('general_settings', JSON.stringify(generalSettings));

            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                spinner.classList.remove('is-active');
                if (res.success) {
                    saveMsg.textContent = res.data;
                    saveMsg.style.display = 'inline-block';
                    setTimeout(() => saveMsg.style.display = 'none', 3000);
                    wpefAdminData.general_settings = generalSettings;
                } else {
                    alert(wpefAdminData.i18n.errSave + res.data);
                }
            })
            .catch(err => {
                spinner.classList.remove('is-active');
                alert(wpefAdminData.i18n.errNetwork);
            });
            return;
        }

        // --- СОХРАНЕНИЕ ГЛОБАЛЬНОЙ ВАЛИДАЦИИ ---
        if (tabName === 'validation') {
            const fieldBlocks = validationContainer.querySelectorAll('.wpef-global-val-field-block');
            const rulesData = [];
            let hasValidationError = false;

            fieldBlocks.forEach(fieldBlock => {
                if (hasValidationError) return;

                const selector = fieldBlock.querySelector('[name="val_field_selector"]').value.trim();
                if (!selector) {
                    alert(wpefAdminData.i18n.errValSelector);
                    hasValidationError = true;
                    return;
                }

                const ruleBlocks = fieldBlock.querySelectorAll('.wpef-val-rule-block');
                const rules = [];

                ruleBlocks.forEach(ruleBlock => {
                    rules.push({
                        type: ruleBlock.querySelector('[name="val_rule_type"]').value,
                        value: ruleBlock.querySelector('[name="val_rule_value"]').value,
                        error: ruleBlock.querySelector('[name="val_rule_error"]').value
                    });
                });

                rulesData.push({
                    selector: selector,
                    rules: rules
                });
            });

            if (hasValidationError) return;

            spinner.classList.add('is-active');
            saveMsg.style.display = 'none';

            const formData = new FormData();
            formData.append('action', 'wpef_save_validation');
            formData.append('nonce', wpefAdminData.nonce_validation);
            formData.append('rules', JSON.stringify(rulesData));

            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                spinner.classList.remove('is-active');
                if (res.success) {
                    saveMsg.textContent = res.data;
                    saveMsg.style.display = 'inline-block';
                    setTimeout(() => saveMsg.style.display = 'none', 3000);
                    wpefAdminData.validation_rules = rulesData;
                } else {
                    alert(wpefAdminData.i18n.errSave + res.data);
                }
            })
            .catch(err => {
                spinner.classList.remove('is-active');
                alert(wpefAdminData.i18n.errNetwork);
            });
        }
    });

    function addConfigBlock(data) {
        const div = document.createElement('div');
        div.innerHTML = configTemplate;
        const block = div.firstElementChild;
        
        block.dataset.id = data.id || generateId();
        
        const nameInput = block.querySelector('[name="name"]');
        nameInput.value = data.name || wpefAdminData.i18n.allForms;
        block.querySelector('.wpef-config-title').textContent = nameInput.value;

        // Если есть ID, выводим дашборд статистики (CR %)
        const statsBlock = block.querySelector('.wpef-config-stats');
        if (statsBlock) {
            if (data.id && wpefAdminData.stats && wpefAdminData.stats[data.id]) {
                const stat = wpefAdminData.stats[data.id];
                const views = parseInt(stat.views) || 0;
                const submits = parseInt(stat.submits) || 0;
                const cr = views > 0 ? ((submits / views) * 100).toFixed(1) : '0.0';
                
                statsBlock.style.display = 'flex';
                statsBlock.innerHTML = `
                    <span title="${wpefAdminData.i18n.statsViews}"><span class="dashicons dashicons-visibility" style="font-size: 16px; width: 16px; height: 16px; margin-right: 4px; color: #8c8f94; vertical-align: text-bottom;"></span> ${views}</span>
                    <span title="${wpefAdminData.i18n.statsSubmits}"><span class="dashicons dashicons-yes-alt" style="font-size: 16px; width: 16px; height: 16px; margin-right: 4px; color: #8c8f94; vertical-align: text-bottom;"></span> ${submits}</span>
                    <span title="${wpefAdminData.i18n.statsCR}" style="font-weight: 600; color: #2271b1; background: #e0f0fa; padding: 2px 6px; border-radius: 3px;">CR: ${cr}%</span>
                `;
            } else if (data.id) {
                statsBlock.style.display = 'flex';
                statsBlock.innerHTML = `
                    <span title="${wpefAdminData.i18n.statsViews}"><span class="dashicons dashicons-visibility" style="font-size: 16px; width: 16px; height: 16px; margin-right: 4px; color: #8c8f94; vertical-align: text-bottom;"></span> 0</span>
                    <span title="${wpefAdminData.i18n.statsSubmits}"><span class="dashicons dashicons-yes-alt" style="font-size: 16px; width: 16px; height: 16px; margin-right: 4px; color: #8c8f94; vertical-align: text-bottom;"></span> 0</span>
                    <span title="${wpefAdminData.i18n.statsCR}" style="font-weight: 600; color: #2271b1; background: #e0f0fa; padding: 2px 6px; border-radius: 3px;">CR: 0.0%</span>
                `;
            }
        }

        // Динамическое обновление заголовка при вводе названия
        nameInput.addEventListener('input', function() {
            block.querySelector('.wpef-config-title').textContent = this.value || wpefAdminData.i18n.untitled;
        });

        // Добавляем логику аккордеона (Сворачивание/Разворачивание блоков)
        const header = block.querySelector('.wpef-config-header');
        const body = block.querySelector('.wpef-config-body');
        const toggleIcon = document.createElement('span');
        toggleIcon.className = 'wpef-toggle-icon dashicons dashicons-arrow-up-alt2';
        toggleIcon.style.marginRight = '15px';
        toggleIcon.style.color = '#2271b1';
        
        // Вставляем иконку перед заголовком
        const titleEl = block.querySelector('.wpef-config-title');
        titleEl.parentNode.insertBefore(toggleIcon, titleEl);
        titleEl.style.display = 'inline-block';
        titleEl.style.verticalAlign = 'middle';
        
        header.style.cursor = 'pointer';
        header.addEventListener('click', function(e) {
            // Игнорируем клик по кнопке удаления
            if (e.target.closest('.wpef-remove-config')) return;
            
            if (body.style.display === 'none') {
                body.style.display = 'block';
                block.classList.remove('wpef-collapsed');
                toggleIcon.classList.remove('dashicons-arrow-down-alt2');
                toggleIcon.classList.add('dashicons-arrow-up-alt2');
                
                // Лечим баг CodeMirror при разворачивании
                if (block.cmInstance) {
                    setTimeout(() => block.cmInstance.refresh(), 50);
                }
            } else {
                body.style.display = 'none';
                block.classList.add('wpef-collapsed');
                toggleIcon.classList.remove('dashicons-arrow-up-alt2');
                toggleIcon.classList.add('dashicons-arrow-down-alt2');
            }
        });

        // Сворачиваем блок по умолчанию, если это не только что созданный новый блок
        if (data.id) {
            body.style.display = 'none';
            block.classList.add('wpef-collapsed');
            toggleIcon.classList.remove('dashicons-arrow-up-alt2');
            toggleIcon.classList.add('dashicons-arrow-down-alt2');
        }

        block.querySelector('[name="selector"]').value = data.selector || 'form';
        block.querySelector('[name="email_to"]').value = data.email_to || '';
        block.querySelector('[name="email_subject"]').value = data.email_subject !== undefined ? data.email_subject : wpefAdminData.i18n.defaultEmailSubject;
        block.querySelector('[name="email_body"]').value = data.email_body !== undefined ? data.email_body : wpefAdminData.i18n.defaultEmailBody;

        if (data.reply_enabled !== undefined) block.querySelector('[name="reply_enabled"]').checked = data.reply_enabled;
        block.querySelector('[name="reply_email_field"]').value = data.reply_email_field || '';
        block.querySelector('[name="reply_subject"]').value = data.reply_subject || '';
        block.querySelector('[name="reply_body"]').value = data.reply_body || '';

        // Отрисовка списка файлов для автоответа
        const replyFilesContainer = block.querySelector('.wpef-reply-files-container');
        if (replyFilesContainer) {
            replyFilesContainer.innerHTML = ''; // Очищаем перед отрисовкой
            
            // Если файлов нет, добавляем одно пустое поле по умолчанию
            const filesList = Array.isArray(data.reply_files) && data.reply_files.length > 0 ? data.reply_files : [''];
            
            filesList.forEach(fileUrl => {
                addReplyFileRow(replyFilesContainer, fileUrl);
            });
        }

        // Webhook Mapping
        const mappingContainer = block.querySelector('.wpef-webhook-mapping-container');
        const addMappingBtn = block.querySelector('.wpef-add-mapping-btn');
        
        function addMappingRow(formField = '', jsonKey = '') {
            if (!mappingContainer) return;
            const row = document.createElement('div');
            row.className = 'wpef-mapping-row';
            row.style.display = 'flex';
            row.style.gap = '10px';
            row.innerHTML = `
                <input type="text" name="map_form_field" placeholder="${wpefAdminData.i18n.placeholderFormField}" value="${formField}" style="flex: 1;">
                <span style="display: flex; align-items: center; color: #64748b;">&rarr;</span>
                <input type="text" name="map_json_key" placeholder="${wpefAdminData.i18n.placeholderJsonKey}" value="${jsonKey}" style="flex: 1;">
                <button type="button" class="button wpef-remove-mapping-btn">&times;</button>
            `;
            row.querySelector('.wpef-remove-mapping-btn').addEventListener('click', () => row.remove());
            mappingContainer.appendChild(row);
        }

        if (addMappingBtn) {
            addMappingBtn.addEventListener('click', () => addMappingRow());
        }

        if (mappingContainer) {
            mappingContainer.innerHTML = '';
            if (Array.isArray(data.webhook_mapping) && data.webhook_mapping.length > 0) {
                data.webhook_mapping.forEach(map => addMappingRow(map.form_field, map.json_key));
            }
        }

        // Conditions Logic
        const conditionsContainer = block.querySelector('.wpef-conditions-container');
        const addConditionBtn = block.querySelector('.wpef-add-condition-btn');
        const conditionTemplateNode = document.getElementById('wpef-condition-template');
        const conditionTemplate = conditionTemplateNode ? conditionTemplateNode.innerHTML : '';

        function addConditionRow(condData = {}) {
            if (!conditionsContainer || !conditionTemplate) return;
            const div = document.createElement('div');
            div.innerHTML = conditionTemplate;
            const condBlock = div.firstElementChild;

            condBlock.querySelector('[name="cond_trigger_name"]').value = condData.trigger_name || '';
            condBlock.querySelector('[name="cond_operator"]').value = condData.operator || 'equals';
            condBlock.querySelector('[name="cond_trigger_value"]').value = condData.trigger_value || '';
            condBlock.querySelector('[name="cond_target_selector"]').value = condData.target_selector || '';
            condBlock.querySelector('[name="cond_action"]').value = condData.action || 'show';
            
            const effectEl = condBlock.querySelector('[name="cond_effect"]');
            if (effectEl) effectEl.value = condData.effect || 'slide';
            
            const durationEl = condBlock.querySelector('[name="cond_duration"]');
            if (durationEl) durationEl.value = condData.duration !== undefined ? condData.duration : '300';

            const delayEl = condBlock.querySelector('[name="cond_delay"]');
            if (delayEl) delayEl.value = condData.delay !== undefined ? condData.delay : '0';

            const operatorSelect = condBlock.querySelector('[name="cond_operator"]');
            const valueWrapper = condBlock.querySelector('.wpef-cond-value-wrapper');

            function toggleValueInput() {
                if (operatorSelect.value === 'checked' || operatorSelect.value === 'not_checked') {
                    valueWrapper.style.display = 'none';
                } else {
                    valueWrapper.style.display = 'block';
                }
            }

            operatorSelect.addEventListener('change', toggleValueInput);
            toggleValueInput();

            condBlock.querySelector('.wpef-remove-condition-btn').addEventListener('click', () => condBlock.remove());

            conditionsContainer.appendChild(condBlock);
        }

        if (addConditionBtn) {
            addConditionBtn.addEventListener('click', () => addConditionRow());
        }

        if (conditionsContainer) {
            conditionsContainer.innerHTML = '';
            if (Array.isArray(data.conditions) && data.conditions.length > 0) {
                data.conditions.forEach(cond => addConditionRow(cond));
            }
        }

        // Smart Routing Logic
        const routeEnable = block.querySelector('[name="route_enable"]');
        const routeFields = block.querySelector('.wpef-route-fields');
        const routesContainer = block.querySelector('.wpef-routes-container');
        const addRouteBtn = block.querySelector('.wpef-add-route-btn');
        const routeTemplateNode = document.getElementById('wpef-route-template');
        const routeTemplate = routeTemplateNode ? routeTemplateNode.innerHTML : '';

        if (routeEnable && routeFields) {
            routeEnable.checked = data.route_enable || false;
            routeEnable.addEventListener('change', (e) => {
                routeFields.style.display = e.target.checked ? 'block' : 'none';
            });
            routeFields.style.display = routeEnable.checked ? 'block' : 'none';
        }

        function addRouteRow(routeData = {}) {
            if (!routesContainer || !routeTemplate) return;
            const div = document.createElement('div');
            div.innerHTML = routeTemplate;
            const routeBlock = div.firstElementChild;

            routeBlock.querySelector('[name="route_trigger_name"]').value = routeData.trigger_name || '';
            routeBlock.querySelector('[name="route_operator"]').value = routeData.operator || 'equals';
            routeBlock.querySelector('[name="route_trigger_value"]').value = routeData.trigger_value || '';
            routeBlock.querySelector('[name="route_target_email"]').value = routeData.target_email || '';

            routeBlock.querySelector('.wpef-remove-route-btn').addEventListener('click', () => routeBlock.remove());

            routesContainer.appendChild(routeBlock);
        }

        if (addRouteBtn) {
            addRouteBtn.addEventListener('click', () => addRouteRow());
        }

        if (routesContainer) {
            routesContainer.innerHTML = '';
            if (Array.isArray(data.routes) && data.routes.length > 0) {
                data.routes.forEach(route => addRouteRow(route));
            }
        }

        block.querySelector('[name="redirect_url"]').value = data.redirect_url || '';
        if (data.redirect_blank !== undefined) block.querySelector('[name="redirect_blank"]').checked = data.redirect_blank;

        const hideBlockEnable = block.querySelector('[name="hide_block_enable"]');
        const hideBlockFields = block.querySelector('.wpef-hide-block-fields');
        if (hideBlockEnable && hideBlockFields) {
            hideBlockEnable.checked = data.hide_block_enable || false;
            hideBlockEnable.addEventListener('change', (e) => {
                hideBlockFields.style.display = e.target.checked ? 'block' : 'none';
            });
            hideBlockFields.style.display = hideBlockEnable.checked ? 'block' : 'none';
        }
        if (block.querySelector('[name="hide_block_selector"]')) block.querySelector('[name="hide_block_selector"]').value = data.hide_block_selector || '';
        if (block.querySelector('[name="hide_block_effect"]')) block.querySelector('[name="hide_block_effect"]').value = data.hide_block_effect || 'fadeOut';
        if (block.querySelector('[name="hide_block_duration"]')) block.querySelector('[name="hide_block_duration"]').value = data.hide_block_duration || '500';
        if (block.querySelector('[name="hide_block_delay"]')) block.querySelector('[name="hide_block_delay"]').value = data.hide_block_delay || '0';

        const showBlockEnable = block.querySelector('[name="show_block_enable"]');
        const showBlockFields = block.querySelector('.wpef-show-block-fields');
        if (showBlockEnable && showBlockFields) {
            showBlockEnable.checked = data.show_block_enable || false;
            showBlockEnable.addEventListener('change', (e) => {
                showBlockFields.style.display = e.target.checked ? 'block' : 'none';
            });
            showBlockFields.style.display = showBlockEnable.checked ? 'block' : 'none';
        }
        if (block.querySelector('[name="show_block_selector"]')) block.querySelector('[name="show_block_selector"]').value = data.show_block_selector || '';
        if (block.querySelector('[name="show_block_effect"]')) block.querySelector('[name="show_block_effect"]').value = data.show_block_effect || 'fadeIn';
        if (block.querySelector('[name="show_block_duration"]')) block.querySelector('[name="show_block_duration"]').value = data.show_block_duration || '500';
        if (block.querySelector('[name="show_block_delay"]')) block.querySelector('[name="show_block_delay"]').value = data.show_block_delay || '0';

        if (block.querySelector('[name="loader_enable"]')) block.querySelector('[name="loader_enable"]').checked = data.loader_enable !== undefined ? data.loader_enable : true;
        if (block.querySelector('[name="loader_color"]')) block.querySelector('[name="loader_color"]').value = data.loader_color || '#4f46e5';
        if (block.querySelector('[name="loader_bg"]')) block.querySelector('[name="loader_bg"]').value = data.loader_bg || 'rgba(255, 255, 255, 0.7)';

        // Loader Toggle
        const loaderEnable = block.querySelector('[name="loader_enable"]');
        const loaderSettings = block.querySelector('.wpef-loader-settings');
        if (loaderEnable && loaderSettings) {
            loaderEnable.addEventListener('change', (e) => {
                loaderSettings.style.display = e.target.checked ? 'flex' : 'none';
            });
            loaderSettings.style.display = loaderEnable.checked ? 'flex' : 'none';
        }
        
        // Webhook Migration from old webhook_type
        if (data.webhook_type) {
            if (data.webhook_type === 'telegram') data.webhook_tg_enable = true;
            if (data.webhook_type === 'bitrix24') data.webhook_bx_enable = true;
            if (data.webhook_type === 'custom') data.webhook_custom_enable = true;
        }

        // Webhook Multiple
        const tgEnable = block.querySelector('[name="webhook_tg_enable"]');
        const bxEnable = block.querySelector('[name="webhook_bx_enable"]');
        const customEnable = block.querySelector('[name="webhook_custom_enable"]');

        if (tgEnable) tgEnable.checked = data.webhook_tg_enable || false;
        if (bxEnable) bxEnable.checked = data.webhook_bx_enable || false;
        if (customEnable) customEnable.checked = data.webhook_custom_enable || false;

        const tgToken = block.querySelector('[name="webhook_tg_token"]');
        if (tgToken) tgToken.value = data.webhook_tg_token || '';

        const tgChat = block.querySelector('[name="webhook_tg_chat"]');
        if (tgChat) tgChat.value = data.webhook_tg_chat || '';

        const bxUrl = block.querySelector('[name="webhook_bx_url"]');
        if (bxUrl) bxUrl.value = data.webhook_bx_url || '';

        const customUrl = block.querySelector('[name="webhook_url"]');
        if (customUrl) customUrl.value = data.webhook_url || '';

        const analyticsYmId = block.querySelector('[name="analytics_ym_id"]');
        if (analyticsYmId) analyticsYmId.value = data.analytics_ym_id || '';
        
        const analyticsYmGoal = block.querySelector('[name="analytics_ym_goal"]');
        if (analyticsYmGoal) analyticsYmGoal.value = data.analytics_ym_goal || '';
        
        const analyticsGaEvent = block.querySelector('[name="analytics_ga_event"]');
        if (analyticsGaEvent) analyticsGaEvent.value = data.analytics_ga_event || '';
        
        const analyticsVkEvent = block.querySelector('[name="analytics_vk_event"]');
        if (analyticsVkEvent) analyticsVkEvent.value = data.analytics_vk_event || '';

        // Toggles visibility
        const bindWebhookToggle = (checkbox, targetClass) => {
            if (!checkbox) return;
            const target = block.querySelector(targetClass);
            if (!target) return;
            
            checkbox.addEventListener('change', (e) => {
                target.style.display = e.target.checked ? 'block' : 'none';
            });
            target.style.display = checkbox.checked ? 'block' : 'none';
        };

        bindWebhookToggle(tgEnable, '.wpef-webhook-telegram');
        bindWebhookToggle(bxEnable, '.wpef-webhook-bitrix24');
        bindWebhookToggle(customEnable, '.wpef-webhook-custom');

        if (data.enabled !== undefined) {
            block.querySelector('[name="enabled"]').checked = data.enabled;
        }

        // Custom JS Textarea & CodeMirror
        const jsArea = block.querySelector('[name="custom_js"]');
        jsArea.value = data.custom_js || '';
        jsArea.id = 'wpef_js_' + generateId();
        
        configsContainer.appendChild(block);

        // Заполняем маппинги Bitrix24
        const bxMappingContainer = block.querySelector('.wpef-bx-mapping-container');
        if (bxMappingContainer && data.webhook_bx_mapping && data.webhook_bx_mapping.length > 0) {
            data.webhook_bx_mapping.forEach(map => {
                addBxMappingRow(bxMappingContainer, map.form_field, map.json_key);
            });
        }

        const addBxMappingBtn = block.querySelector('.wpef-add-bx-mapping-btn');
        if (addBxMappingBtn && bxMappingContainer) {
            addBxMappingBtn.addEventListener('click', function() {
                addBxMappingRow(bxMappingContainer);
            });
        }

        if (typeof wp !== 'undefined' && wp.codeEditor && wpefAdminData.cm_settings) {
            let cmSettings = Object.assign({}, wpefAdminData.cm_settings);
            
            // Важно: чтобы CodeMirror нормально инициализировался в скрытом (или только что добавленном) блоке,
            // нужно дать браузеру микросекунду на рендер DOM.
            setTimeout(() => {
                let editor = wp.codeEditor.initialize(jsArea.id, cmSettings);
                block.cmInstance = editor.codemirror;
                
                // CodeMirror может криво считать размеры при display:none у табов
                // Эта команда заставит его пересчитать свои габариты
                editor.codemirror.refresh();
            }, 10);
        }

        nameInput.addEventListener('input', function(e) {
            block.querySelector('.wpef-config-title').textContent = e.target.value || wpefAdminData.i18n.newConfig;
        });

        block.querySelector('.wpef-remove-config').addEventListener('click', function() {
            if(confirm(wpefAdminData.i18n.confirmDelConfig)) {
                block.remove();
            }
        });
    }

    // Функция добавления строки для файла автоответа
    function addReplyFileRow(container, value = '') {
        const row = document.createElement('div');
        row.className = 'wpef-reply-file-row';
        row.style.display = 'flex';
        row.style.gap = '10px';
        row.style.marginBottom = '5px';
        
        row.innerHTML = `
            <input type="text" name="reply_files[]" class="regular-text" style="flex: 1;" value="${value}" readonly>
            <button type="button" class="button wpef-upload-btn">${wpefAdminData.i18n.btnSelect}</button>
            <button type="button" class="wpef-btn-delete-icon wpef-remove-reply-file" title="${wpefAdminData.i18n.btnDeleteFile}">&times;</button>
        `;
        
        container.appendChild(row);
    }

    function addMappingRow(container, field = '', key = '') {
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'wpef-mapping-row';
        row.style.display = 'flex';
        row.style.gap = '10px';
        row.style.marginBottom = '10px';
        row.style.alignItems = 'center';
        row.innerHTML = `
            <input type="text" name="map_form_field" placeholder="${wpefAdminData.i18n.placeholderNameFieldPhone}" class="regular-text" value="${field}">
            <span class="dashicons dashicons-arrow-right-alt"></span>
            <input type="text" name="map_json_key" placeholder="${wpefAdminData.i18n.placeholderJsonKeyExample}" class="regular-text" value="${key}">
            <button type="button" class="wpef-btn-delete-icon wpef-remove-mapping">&times;</button>
        `;
        row.querySelector('.wpef-remove-mapping').addEventListener('click', () => row.remove());
        container.appendChild(row);
    }

    function addBxMappingRow(container, field = '', key = '') {
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'wpef-bx-mapping-row';
        row.style.display = 'flex';
        row.style.gap = '10px';
        row.style.marginBottom = '10px';
        row.style.alignItems = 'center';
        row.innerHTML = `
            <input type="text" name="bx_map_form_field" placeholder="${wpefAdminData.i18n.placeholderNameFieldUserPhone}" class="regular-text" value="${field}">
            <span class="dashicons dashicons-arrow-right-alt"></span>
            <input type="text" name="bx_map_json_key" placeholder="${wpefAdminData.i18n.placeholderB24Key}" class="regular-text" value="${key}">
            <button type="button" class="wpef-btn-delete-icon wpef-remove-mapping">&times;</button>
        `;
        row.querySelector('.wpef-remove-mapping').addEventListener('click', () => row.remove());
        container.appendChild(row);
    }

    // Делегированный слушатель для кнопок файлов автоответа
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('wpef-upload-btn')) {
            e.preventDefault();
            const input = e.target.previousElementSibling;
            const custom_uploader = wp.media({
                title: wpefAdminData.i18n.selectReplyFile,
                button: { text: wpefAdminData.i18n.btnSelect },
                multiple: false
            }).on('select', function() {
                const attachment = custom_uploader.state().get('selection').first().toJSON();
                if (input) {
                    input.value = attachment.url;
                }
            }).open();
        }
        
        if (e.target.classList.contains('wpef-add-reply-file-btn')) {
            e.preventDefault();
            const container = e.target.previousElementSibling;
            if (container && container.classList.contains('wpef-reply-files-container')) {
                addReplyFileRow(container);
            }
        }
        
        if (e.target.classList.contains('wpef-remove-reply-file')) {
            e.preventDefault();
            const row = e.target.closest('.wpef-reply-file-row');
            const container = row.parentElement;
            row.remove();
            // Если удалили последний файл, добавляем пустую строку, чтобы всегда была хотя бы одна
            if (container.children.length === 0) {
                addReplyFileRow(container);
            }
        }
    });

    if (wpefAdminData.configs && wpefAdminData.configs.length > 0) {
        wpefAdminData.configs.forEach(config => addConfigBlock(config));
    } else {
        addConfigBlock({});
    }

    addConfigBtn.addEventListener('click', function() {
        addConfigBlock({});
    });



    // ==========================================
    // Вкладка: Глобальная валидация (Validation)
    // ==========================================
    const validationContainer = document.getElementById('wpef-global-validation-container');
    const ruleTemplateNode = document.getElementById('wpef-val-rule-template');
    const valFieldTemplate = document.getElementById('wpef-global-val-field-template');
    
    const ruleTemplate = ruleTemplateNode ? ruleTemplateNode.innerHTML : '';

    const addFieldBtn = document.getElementById('wpef-add-global-val-field');


    function renderValRule(container, ruleData = {}) {
        if (!container) return;
        
        const div = document.createElement('div');
        div.innerHTML = ruleTemplate;
        const ruleBlock = div.firstElementChild;

        ruleBlock.querySelector('[name="val_rule_type"]').value = ruleData.type || 'required';
        ruleBlock.querySelector('[name="val_rule_value"]').value = ruleData.value || '';
        ruleBlock.querySelector('[name="val_rule_error"]').value = ruleData.error || '';

        const typeSelect = ruleBlock.querySelector('[name="val_rule_type"]');
        const valueWrap = ruleBlock.querySelector('.wpef-val-rule-value-wrap');
        const valueInput = ruleBlock.querySelector('[name="val_rule_value"]');
        
        function toggleValueField() {
            const t = typeSelect.value;
            if (['required', 'email', 'password', 'strongPassword', 'number', 'integer'].includes(t)) {
                valueWrap.style.display = 'none';
            } else {
                valueWrap.style.display = 'flex';
            }
        }
        function updatePlaceholder() {
            const t = typeSelect.value;
            if (t === 'acceptTypes') {
                valueInput.placeholder = wpefAdminData.i18n.exAcceptTypes;
            } else if (t === 'customRegexp') {
                valueInput.placeholder = wpefAdminData.i18n.exRegexp;
            } else if (t === 'maxTotalSize') {
                valueInput.placeholder = wpefAdminData.i18n.exMaxTotalSize;
            } else {
                valueInput.placeholder = wpefAdminData.i18n.exNumber10;
            }
        }

        typeSelect.addEventListener('change', function() {
            toggleValueField();
            updatePlaceholder();
        });
        
        toggleValueField();
        updatePlaceholder();

        ruleBlock.querySelector('.wpef-remove-val-rule').addEventListener('click', function() {
            ruleBlock.remove();
        });

        container.appendChild(ruleBlock);
    }

    function renderValField(data = {}) {
        if (!valFieldTemplate) return;
        
        const clone = valFieldTemplate.content.cloneNode(true);
        const fieldBlock = clone.querySelector('.wpef-global-val-field-block');

        fieldBlock.querySelector('[name="val_field_selector"]').value = data.selector || '';

        const rulesContainer = fieldBlock.querySelector('.wpef-val-rules-container');
        if (data.rules && data.rules.length > 0) {
            data.rules.forEach(rule => renderValRule(rulesContainer, rule));
        }

        fieldBlock.querySelector('.wpef-add-val-rule-btn').addEventListener('click', function() {
            renderValRule(rulesContainer, {});
        });

        // Обработка кнопки удаления поля
        fieldBlock.querySelector('.wpef-remove-val-field').addEventListener('click', function() {
            if(confirm(wpefAdminData.i18n.confirmDelValField)) {
                fieldBlock.remove();
            }
        });

        validationContainer.appendChild(fieldBlock);
    }

    if (wpefAdminData.validation_rules && wpefAdminData.validation_rules.length > 0) {
        wpefAdminData.validation_rules.forEach(field => renderValField(field));
    } else {
        renderValField({});
    }

    // Инициализация общих настроек
    const disableAutoRequired = document.getElementById('wpef-disable-auto-required');
    const loadJustValidate = document.getElementById('wpef-load-just-validate');
    const loadImask = document.getElementById('wpef-load-imask');
    const imaskJsRow = document.getElementById('wpef-imask-js-row');
    const imaskJsField = document.getElementById('wpef-imask-js');

    const enableHoneypot = document.getElementById('wpef-enable-honeypot');
    const enableRateLimit = document.getElementById('wpef-enable-rate-limit');
    const rateLimitSettings = document.getElementById('wpef-rate-limit-settings');
    const rateLimitCount = document.getElementById('wpef-rate-limit-count');
    const rateLimitTime = document.getElementById('wpef-rate-limit-time');

    if (wpefAdminData.general_settings) {
        if (disableAutoRequired) disableAutoRequired.checked = !!wpefAdminData.general_settings.disable_auto_required;
        if (loadJustValidate) loadJustValidate.checked = wpefAdminData.general_settings.load_just_validate !== false; // true по дефолту
        if (loadImask) {
            loadImask.checked = !!wpefAdminData.general_settings.load_imask;
            if (loadImask.checked) imaskJsRow.style.display = 'block';
        }
        if (imaskJsField) {
            imaskJsField.value = wpefAdminData.general_settings.imask_js || `const phoneInputs = document.querySelectorAll('[type="tel"]');\nphoneInputs.forEach((phoneInput) => {\n    IMask(phoneInput, {\n        mask: '+{7} (000) 000-00-00',\n        prepare: function (appended, masked) {\n          if (appended === '8' && masked.value === '') {\n            return '7 ';\n          }\n          return appended;\n        },\n    });\n});`;
        }

        if (enableHoneypot) enableHoneypot.checked = !!wpefAdminData.general_settings.enable_honeypot;
        if (enableRateLimit) {
            enableRateLimit.checked = !!wpefAdminData.general_settings.enable_rate_limit;
            if (enableRateLimit.checked && rateLimitSettings) {
                rateLimitSettings.style.display = 'block';
            }
        }
        if (wpefAdminData.general_settings.rate_limit_count && rateLimitCount) {
            rateLimitCount.value = wpefAdminData.general_settings.rate_limit_count;
        }
        if (wpefAdminData.general_settings.rate_limit_time && rateLimitTime) {
            rateLimitTime.value = wpefAdminData.general_settings.rate_limit_time;
        }

        // Новые настройки
        const enableUtm = document.getElementById('wpef-enable-utm');
        const enableDeviceInfo = document.getElementById('wpef-enable-device-info');
        const enableBasicTechInfo = document.getElementById('wpef-enable-basic-tech-info');
        const usePageTitle = document.getElementById('wpef-use-page-title');
        const enableSendEmptyFields = document.getElementById('wpef-enable-send-empty-fields');
        const saveToMedia = document.getElementById('wpef-save-to-media');
        const maxAttachmentSize = document.getElementById('wpef-max-attachment-size');

        if (enableUtm) enableUtm.checked = !!wpefAdminData.general_settings.enable_utm;
        if (enableDeviceInfo) enableDeviceInfo.checked = !!wpefAdminData.general_settings.enable_device_info;
        if (enableBasicTechInfo) enableBasicTechInfo.checked = wpefAdminData.general_settings.enable_basic_tech_info !== false; // true по умолчанию
        if (usePageTitle) usePageTitle.checked = !!wpefAdminData.general_settings.use_page_title;
        if (enableSendEmptyFields) enableSendEmptyFields.checked = !!wpefAdminData.general_settings.enable_send_empty_fields;
        if (saveToMedia) saveToMedia.checked = !!wpefAdminData.general_settings.save_to_media;
        if (maxAttachmentSize && wpefAdminData.general_settings.max_attachment_size) {
            maxAttachmentSize.value = wpefAdminData.general_settings.max_attachment_size;
        }

        const cleanupLeadsDays = document.getElementById('wpef-cleanup-leads-days');
        if (cleanupLeadsDays) cleanupLeadsDays.value = wpefAdminData.general_settings.cleanup_leads_days || 0;

        const cleanupLogsDays = document.getElementById('wpef-cleanup-logs-days');
        if (cleanupLogsDays) cleanupLogsDays.value = wpefAdminData.general_settings.cleanup_logs_days || 30;

        const enableCacheCompat = document.getElementById('wpef-enable-cache-compat');
        if (enableCacheCompat) enableCacheCompat.checked = wpefAdminData.general_settings.enable_cache_compat === 'on';

        if (document.getElementById('wpef-smtp-enable')) {
            document.getElementById('wpef-smtp-enable').checked = wpefAdminData.general_settings.smtp_enable === 'on';
            document.getElementById('wpef-smtp-host').value = wpefAdminData.general_settings.smtp_host || '';
            document.getElementById('wpef-smtp-port').value = wpefAdminData.general_settings.smtp_port || 465;
            document.getElementById('wpef-smtp-auto-tls').checked = wpefAdminData.general_settings.smtp_auto_tls !== 'off';
            document.getElementById('wpef-smtp-auth').checked = wpefAdminData.general_settings.smtp_auth !== 'off';
            document.getElementById('wpef-smtp-user').value = wpefAdminData.general_settings.smtp_user || '';
            document.getElementById('wpef-smtp-pass').value = wpefAdminData.general_settings.smtp_pass || '';
            document.getElementById('wpef-smtp-from-email').value = wpefAdminData.general_settings.smtp_from_email || '';
            document.getElementById('wpef-smtp-force-from-email').checked = wpefAdminData.general_settings.smtp_force_from_email === 'on';
            document.getElementById('wpef-smtp-from-name').value = wpefAdminData.general_settings.smtp_from_name || '';
            
            const enc = wpefAdminData.general_settings.smtp_encryption || 'ssl';
            const encRadio = document.querySelector('input[name="wpef_smtp_encryption"][value="' + enc + '"]');
            if (encRadio) encRadio.checked = true;
            
            // Toggle SMTP settings wrapper
            const smtpEnable = document.getElementById('wpef-smtp-enable');
            const smtpWrapper = document.getElementById('wpef-smtp-settings-wrapper');
            if (smtpEnable && smtpWrapper) {
                smtpWrapper.style.display = smtpEnable.checked ? 'block' : 'none';
                smtpEnable.addEventListener('change', function() {
                    smtpWrapper.style.display = this.checked ? 'block' : 'none';
                });
            }
            
            // Toggle SMTP Auth fields
            const smtpAuth = document.getElementById('wpef-smtp-auth');
            const smtpAuthWrapper = document.getElementById('wpef-smtp-auth-wrapper');
            if (smtpAuth && smtpAuthWrapper) {
                smtpAuthWrapper.style.display = smtpAuth.checked ? 'block' : 'none';
                smtpAuth.addEventListener('change', function() {
                    smtpAuthWrapper.style.display = this.checked ? 'block' : 'none';
                });
            }

            // Test Email Toggle
            const testToggleHeader = document.getElementById('wpef-smtp-test-toggle-header');
            const testBody = document.getElementById('wpef-smtp-test-body');
            const testIcon = document.getElementById('wpef-smtp-test-icon');
            if (testToggleHeader && testBody && testIcon) {
                testToggleHeader.addEventListener('click', function() {
                    if (testBody.style.display === 'none') {
                        testBody.style.display = 'block';
                        testIcon.classList.replace('dashicons-arrow-down-alt2', 'dashicons-arrow-up-alt2');
                    } else {
                        testBody.style.display = 'none';
                        testIcon.classList.replace('dashicons-arrow-up-alt2', 'dashicons-arrow-down-alt2');
                    }
                });
            }

            // Test Email Toggles
            const testCustomToggle = document.getElementById('wpef-smtp-test-custom');
            const testCustomLabel = document.getElementById('wpef-smtp-test-custom-label');
            const testCustomFields = document.getElementById('wpef-smtp-test-custom-fields');
            if (testCustomToggle && testCustomLabel && testCustomFields) {
                testCustomToggle.addEventListener('change', function() {
                    testCustomLabel.textContent = this.checked ? wpefAdminData.i18n.on : wpefAdminData.i18n.off;
                    testCustomFields.style.display = this.checked ? 'block' : 'none';
                });
            }

            // Test Email Submit
            const testBtn = document.getElementById('wpef-smtp-test-btn');
            if (testBtn) {
                testBtn.addEventListener('click', function() {
                    const email = document.getElementById('wpef-smtp-test-email').value.trim();
                    if (!email) {
                        alert(wpefAdminData.i18n.errTestEmail);
                        return;
                    }

                    const isCustom = testCustomToggle ? testCustomToggle.checked : false;
                    const subject = document.getElementById('wpef-smtp-test-subject') ? document.getElementById('wpef-smtp-test-subject').value : '';
                    const message = document.getElementById('wpef-smtp-test-message') ? document.getElementById('wpef-smtp-test-message').value : '';

                    const spinner = document.getElementById('wpef-smtp-test-spinner');
                    const resultBox = document.getElementById('wpef-smtp-test-result');

                    spinner.classList.add('is-active');
                    resultBox.style.display = 'none';
                    testBtn.disabled = true;

                    const formData = new FormData();
                    formData.append('action', 'wpef_send_test_email');
                    formData.append('nonce', wpefAdminData.nonce_validation);
                    formData.append('email', email);
                    formData.append('is_custom', isCustom);
                    formData.append('subject', subject);
                    formData.append('message', message);

                    fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(res => {
                        spinner.classList.remove('is-active');
                        testBtn.disabled = false;
                        resultBox.style.display = 'block';
                        if (res.success) {
                            resultBox.style.borderColor = '#10b981';
                            resultBox.style.color = '#059669';
                            resultBox.innerHTML = wpefAdminData.i18n.successStrong + res.data;
                        } else {
                            resultBox.style.borderColor = '#ef4444';
                            resultBox.style.color = '#dc2626';
                            resultBox.innerHTML = wpefAdminData.i18n.errorStrong + res.data;
                        }
                    })
                    .catch(err => {
                        spinner.classList.remove('is-active');
                        testBtn.disabled = false;
                        resultBox.style.display = 'block';
                        resultBox.style.borderColor = '#ef4444';
                        resultBox.style.color = '#dc2626';
                        resultBox.innerHTML = wpefAdminData.i18n.errorNetworkStrong;
                    });
                });
            }
        }

    } else {
        if (imaskJsField) {
            imaskJsField.value = `const phoneInputs = document.querySelectorAll('[type="tel"]');\nphoneInputs.forEach((phoneInput) => {\n    IMask(phoneInput, {\n        mask: '+{7} (000) 000-00-00',\n        prepare: function (appended, masked) {\n          if (appended === '8' && masked.value === '') {\n            return '7 ';\n          }\n          return appended;\n        },\n    });\n});`;
        }
    }

    if (enableRateLimit) {
        enableRateLimit.addEventListener('change', function() {
            if (rateLimitSettings) {
                rateLimitSettings.style.display = this.checked ? 'block' : 'none';
            }
        });
    }

    if (loadImask) {
        loadImask.addEventListener('change', function() {
            imaskJsRow.style.display = this.checked ? 'block' : 'none';
            if (this.checked && window.wpefImaskCmInstance) {
                setTimeout(() => window.wpefImaskCmInstance.refresh(), 10);
            }
        });
    }

    if (typeof wp !== 'undefined' && wp.codeEditor && wpefAdminData.cm_settings && imaskJsField) {
        setTimeout(() => {
            let editor = wp.codeEditor.initialize(imaskJsField.id, wpefAdminData.cm_settings);
            window.wpefImaskCmInstance = editor.codemirror;
        }, 100);
    }

    addFieldBtn.addEventListener('click', function() {
        renderValField({});
    });



    // --- ЛОГИКА ЭКСПОРТА / ИМПОРТА ---
    const btnExport = document.getElementById('wpef-btn-export');
    const exportDataField = document.getElementById('wpef-export-data');
    const btnImport = document.getElementById('wpef-btn-import');
    const importDataField = document.getElementById('wpef-import-data');

    if (btnExport && exportDataField) {
        btnExport.addEventListener('click', function() {
            const exportData = {
                configs: wpefAdminData.configs,
                validation_rules: wpefAdminData.validation_rules,
                general_settings: wpefAdminData.general_settings
            };
            exportDataField.value = JSON.stringify(exportData, null, 2);
            exportDataField.select();
            document.execCommand('copy');
            alert(wpefAdminData.i18n.configCopied);
        });
    }

    if (btnImport && importDataField) {
        btnImport.addEventListener('click', function() {
            const importString = importDataField.value.trim();
            if (!importString) {
                alert(wpefAdminData.i18n.errEmptyImport);
                return;
            }

            try {
                JSON.parse(importString); // Проверка валидности JSON
            } catch (e) {
                alert(wpefAdminData.i18n.errInvalidImportFormat);
                return;
            }

            if (!confirm(wpefAdminData.i18n.confirmImport)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'wpef_import_settings');
            formData.append('nonce', wpefAdminData.nonce_validation);
            formData.append('import_data', importString);

            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert(`${res.data}. ${wpefAdminData.i18n.pageWillReload}`);
                    window.location.reload();
                } else {
                    alert(wpefAdminData.i18n.errImport + res.data);
                }
            })
            .catch(err => {
                alert(wpefAdminData.i18n.errNetworkImport);
            });
        });
    }

    // --- ЛОГИКА ТАБЛИЦ (ЗАЯВКИ И ЛОГИ) ---
    function loadTableData(action, containerId, page = 1) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = `<p>${wpefAdminData.i18n.loadingData}</p>`;

        const formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', wpefAdminData.nonce_validation);
        formData.append('paged', page);

        if (action === 'wpef_get_leads') {
            const searchInput = document.getElementById('wpef-leads-search');
            if (searchInput && searchInput.value.trim() !== '') {
                formData.append('search', searchInput.value.trim());
            }
        }

        fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(res => {
            if (res.success) {
                let html = res.data.html;
                
                // Добавляем пагинацию
                if (res.data.total_pages > 1) {
                    html += '<div class="tablenav-pages" style="margin-top: 15px;">';
                    html += `<span class="displaying-num">${wpefAdminData.i18n.page}${res.data.current_page}${wpefAdminData.i18n.of}${res.data.total_pages}</span>`;
                    html += '<span class="pagination-links" style="margin-left: 10px;">';
                    
                    if (res.data.current_page > 1) {
                        html += `<a class="button wpef-page-link" href="#" data-action="${action}" data-container="${containerId}" data-page="${res.data.current_page - 1}">&laquo; ${wpefAdminData.i18n.btnBack}</a> `;
                    }
                    if (res.data.current_page < res.data.total_pages) {
                        html += `<a class="button wpef-page-link" href="#" data-action="${action}" data-container="${containerId}" data-page="${parseInt(res.data.current_page) + 1}">${wpefAdminData.i18n.btnForward} &raquo;</a>`;
                    }
                    
                    html += '</span></div>';
                }
                
                container.innerHTML = html;
            } else {
                console.error('Ошибка AJAX:', res);
                container.innerHTML = `<p style="color:red;">${wpefAdminData.i18n.errLoadData}${res.data || wpefAdminData.i18n.errUnknown}</p>`;
            }
        })
        .catch(err => {
            console.error('Сетевая ошибка AJAX:', err);
            container.innerHTML = `<p style="color:red;">${wpefAdminData.i18n.errNetworkLoadData}</p>`;
        });
    }

    // Первичная настройка видимости сайдбара
    const initialActiveTab = document.querySelector('.wpef-tabs .nav-tab-active');
    if (initialActiveTab) {
        const tabName = initialActiveTab.getAttribute('data-tab');
        const sidebar = document.querySelector('.wpef-sidebar');
        if (sidebar && (tabName === 'dashboard' || tabName === 'leads' || tabName === 'logs')) {
            sidebar.style.display = 'none';
        }
        
        if (tabName === 'leads') {
            loadTableData('wpef_get_leads', 'wpef-leads-container', 1);
        } else if (tabName === 'logs') {
            loadTableData('wpef_get_logs', 'wpef-logs-container', 1);
        }
    }

    // Первичная загрузка при открытии вкладок
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            const tabName = this.getAttribute('data-tab');
            if (tabName === 'leads') {
                loadTableData('wpef_get_leads', 'wpef-leads-container', 1);
            } else if (tabName === 'logs') {
                loadTableData('wpef_get_logs', 'wpef-logs-container', 1);
            }
        });
    });

    // Обработка поиска по заявкам
    const searchBtn = document.getElementById('wpef-leads-search-btn');
    const searchInput = document.getElementById('wpef-leads-search');
    
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function() {
            loadTableData('wpef_get_leads', 'wpef-leads-container', 1);
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loadTableData('wpef_get_leads', 'wpef-leads-container', 1);
            }
        });
    }

    // Обработка кликов по пагинации и кнопкам удаления
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('wpef-page-link')) {
            e.preventDefault();
            const action = e.target.getAttribute('data-action');
            const containerId = e.target.getAttribute('data-container');
            const page = e.target.getAttribute('data-page');
            loadTableData(action, containerId, page);
        }

        // Удаление одной заявки
        if (e.target.classList.contains('wpef-delete-lead')) {
            e.preventDefault();
            if (!confirm(wpefAdminData.i18n.confirmDelLead)) return;
            const id = e.target.getAttribute('data-id');
            const formData = new FormData();
            formData.append('action', 'wpef_delete_lead');
            formData.append('nonce', wpefAdminData.nonce_validation);
            formData.append('id', id);

            e.target.disabled = true;
            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    loadTableData('wpef_get_leads', 'wpef-leads-container', 1);
                } else {
                    alert(wpefAdminData.i18n.errDelete);
                    e.target.disabled = false;
                }
            });
        }

        // Удаление одного лога
        if (e.target.classList.contains('wpef-delete-log')) {
            e.preventDefault();
            if (!confirm(wpefAdminData.i18n.confirmDelLog)) return;
            const id = e.target.getAttribute('data-id');
            const formData = new FormData();
            formData.append('action', 'wpef_delete_log');
            formData.append('nonce', wpefAdminData.nonce_validation);
            formData.append('id', id);

            e.target.disabled = true;
            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    loadTableData('wpef_get_logs', 'wpef-logs-container', 1);
                } else {
                    alert(wpefAdminData.i18n.errDelete);
                    e.target.disabled = false;
                }
            });
        }

        // Очистка всех заявок
        if (e.target.classList.contains('wpef-clear-leads')) {
            e.preventDefault();
            if (!confirm(wpefAdminData.i18n.confirmClearLds)) return;
            const formData = new FormData();
            formData.append('action', 'wpef_clear_leads');
            formData.append('nonce', wpefAdminData.nonce_validation);

            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    loadTableData('wpef_get_leads', 'wpef-leads-container', 1);
                } else {
                    alert(wpefAdminData.i18n.errClearLds);
                }
            });
        }

        // Очистка всех логов
        if (e.target.classList.contains('wpef-clear-logs')) {
            e.preventDefault();
            if (!confirm(wpefAdminData.i18n.confirmClearLgs)) return;
            const formData = new FormData();
            formData.append('action', 'wpef_clear_logs');
            formData.append('nonce', wpefAdminData.nonce_validation);

            fetch(wpefAdminData.ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    loadTableData('wpef_get_logs', 'wpef-logs-container', 1);
                } else {
                    alert(wpefAdminData.i18n.errClearLgs);
                }
            });
        }
    });

    // Предпросмотр JSON
    document.addEventListener('click', function(e) {
        function escapeHtml(unsafe) {
            return (unsafe || '').toString()
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        if (e.target.closest('.wpef-view-data')) {
            const btn = e.target.closest('.wpef-view-data');
            const rawJson = btn.getAttribute('data-json');
            if (!rawJson) return;

            let parsedJson = {};
            try {
                parsedJson = JSON.parse(rawJson);
            } catch (err) {}

            let tableHtml = '<table class="wpef-data-table"><tbody>';
            let copyText = '';
            
            const techFields = ['wpef_website_url', 'wpef_client_user_email', 'wpef_user_agent', 'wpef_screen_res', 'wpef_device_type'];

            if (parsedJson && typeof parsedJson === 'object') {
                for (const [key, value] of Object.entries(parsedJson)) {
                    if (techFields.includes(key)) continue;
                    if (key.startsWith('wpef_utm_') && !value) continue;

                    let displayKey = key.replace(/^wpef_/, '');
                    displayKey = displayKey.replace(/[_-]/g, ' ');
                    displayKey = displayKey.charAt(0).toUpperCase() + displayKey.slice(1);

                    let displayValue = String(value);
                    if (displayValue.trim().toLowerCase() === 'on') {
                        displayValue = wpefAdminData.i18n.yesReceived || 'Да / Получено';
                    }

                    tableHtml += `
                        <tr>
                            <th>${escapeHtml(displayKey)}</th>
                            <td>${escapeHtml(displayValue)}</td>
                        </tr>
                    `;
                    copyText += `${displayKey}: ${displayValue}\n`;
                }
            } else {
                tableHtml += `<tr><td>${wpefAdminData.i18n.noData}</td></tr>`;
            }
            tableHtml += '</tbody></table>';

            const modal = document.createElement('div');
            modal.className = 'wpef-modal-overlay';
            modal.innerHTML = `
                <div class="wpef-modal-content wpef-modal-content-data">
                    <div class="wpef-modal-header">
                        <h3>${wpefAdminData.i18n.leadData}</h3>
                        <button type="button" class="wpef-modal-close">&times;</button>
                    </div>
                    <div class="wpef-modal-body">
                        ${tableHtml}
                        <div style="margin-top: 20px; text-align: right;">
                            <button type="button" class="button button-primary wpef-copy-data" data-text="${escapeHtml(copyText)}">${wpefAdminData.i18n.copyToClipboard}</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            modal.querySelector('.wpef-copy-data').addEventListener('click', function(btnEvent) {
                const textToCopy = btnEvent.target.getAttribute('data-text');
                
                // Fallback для старых браузеров или если не работает Clipboard API в iframe/админке
                const copyFallback = (text) => {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');
                    } catch (err) {
                        console.error('Ошибка копирования:', err);
                    }
                    document.body.removeChild(textarea);
                };

                const showSuccess = () => {
                    const originalText = btnEvent.target.innerText;
                    btnEvent.target.innerText = wpefAdminData.i18n.copied;
                    setTimeout(() => {
                        btnEvent.target.innerText = originalText;
                    }, 2000);
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textToCopy)
                        .then(showSuccess)
                        .catch(() => {
                            copyFallback(textToCopy);
                            showSuccess();
                        });
                } else {
                    copyFallback(textToCopy);
                    showSuccess();
                }
            });

            modal.querySelector('.wpef-modal-close').addEventListener('click', function() {
                modal.remove();
            });
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.remove();
                }
            });
        }

        if (e.target.classList.contains('wpef-view-json')) {
            const rawJson = e.target.getAttribute('data-json');
            if (!rawJson) return;

            let parsedJson = '';
            try {
                parsedJson = JSON.parse(rawJson);
            } catch (err) {
                // Если не парсится, выводим как есть
                parsedJson = rawJson;
            }

            const highlightedJson = syntaxHighlightJSON(parsedJson);

            // Создаем модальное окно
            const modal = document.createElement('div');
            modal.className = 'wpef-modal-overlay';
            modal.innerHTML = `
                <div class="wpef-modal-content">
                    <div class="wpef-modal-header">
                        <h3>${wpefAdminData.i18n.reqDetails}</h3>
                        <button type="button" class="wpef-modal-close">&times;</button>
                    </div>
                    <div class="wpef-modal-body">
                        <pre class="wpef-json-preview">${highlightedJson}</pre>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Закрытие
            modal.querySelector('.wpef-modal-close').addEventListener('click', function() {
                modal.remove();
            });
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.remove();
                }
            });
        }
    });

    // Функция для подсветки синтаксиса JSON (с темной темой а-ля VS Code)
    function syntaxHighlightJSON(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 4);
        } else {
            try {
                json = JSON.stringify(JSON.parse(json), undefined, 4);
            } catch(e) {}
        }
        
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            let cls = 'wpef-json-number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'wpef-json-key';
                } else {
                    cls = 'wpef-json-string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'wpef-json-boolean';
            } else if (/null/.test(match)) {
                cls = 'wpef-json-null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

});