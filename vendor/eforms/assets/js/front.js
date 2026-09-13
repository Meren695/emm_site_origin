const JustValidate = window.JustValidate;

const fadeOut = (el, timeout) => { 
    el.style.opacity = 1; 
    el.style.transition = `opacity ${timeout}ms`; 
    el.style.opacity = 0; 
    setTimeout(() => { 
        el.style.display = 'none'; 
    }, timeout); 
}; 

const slideUp = (target, duration = 500) => { 
    target.style.transitionProperty = 'height, margin, padding'; 
    target.style.transitionDuration = duration + 'ms'; 
    target.style.boxSizing = 'border-box'; 
    target.style.height = target.offsetHeight + 'px'; 
    target.offsetHeight; 
    target.style.overflow = 'hidden'; 
    target.style.height = 0; 
    target.style.paddingTop = 0; 
    target.style.paddingBottom = 0; 
    target.style.marginTop = 0; 
    target.style.marginBottom = 0; 
    window.setTimeout(() => { 
        target.style.display = 'none'; 
        target.style.removeProperty('height'); 
        target.style.removeProperty('padding-top'); 
        target.style.removeProperty('padding-bottom'); 
        target.style.removeProperty('margin-top'); 
        target.style.removeProperty('margin-bottom'); 
        target.style.removeProperty('overflow'); 
        target.style.removeProperty('transition-duration'); 
        target.style.removeProperty('transition-property'); 
    }, duration); 
};

const fadeIn = (el, timeout, display) => { 
    el.style.opacity = 0; 
    el.style.display = display || 'block'; 
    el.style.transition = `opacity ${timeout}ms`; 
    setTimeout(() => { 
        el.style.opacity = 1; 
    }, 10); 
}; 

const slideDown = (target, duration = 500, displayValue = 'block') => { 
    target.style.removeProperty('display'); 
    let currentDisplay = window.getComputedStyle(target).display; 
    if (currentDisplay === 'none') currentDisplay = displayValue; 
    target.style.display = currentDisplay; 
    let height = target.offsetHeight; 
    target.style.overflow = 'hidden'; 
    target.style.height = 0; 
    target.style.paddingTop = 0; 
    target.style.paddingBottom = 0; 
    target.style.marginTop = 0; 
    target.style.marginBottom = 0; 
    target.offsetHeight; 
    target.style.boxSizing = 'border-box'; 
    target.style.transitionProperty = 'height, margin, padding'; 
    target.style.transitionDuration = duration + 'ms'; 
    target.style.height = height + 'px'; 
    target.style.removeProperty('padding-top'); 
    target.style.removeProperty('padding-bottom'); 
    target.style.removeProperty('margin-top'); 
    target.style.removeProperty('margin-bottom'); 
    window.setTimeout(() => { 
        target.style.removeProperty('height'); 
        target.style.removeProperty('overflow'); 
        target.style.removeProperty('transition-duration'); 
        target.style.removeProperty('transition-property'); 
    }, duration); 
};

function initWpefForms() {
	if (typeof wpefData === 'undefined' || !wpefData.configs) {
		return;
	}

	// Инициализация IMask (если включено в настройках)
	if (wpefData.general_settings && wpefData.general_settings.load_imask && wpefData.general_settings.imask_js) {
		try {
			var imaskFn = new Function(wpefData.general_settings.imask_js);
			imaskFn();
		} catch (e) {
			console.error('WP Easy Forms IMask Error:', e);
		}
	}

	// Проходим по всем активным конфигам
	let freshNonce = null;
	let isFetchingNonce = false;

	wpefData.configs.forEach(function (config, index) {
		if (!config.selector) return;

		var forms = document.querySelectorAll(config.selector);
		if (!forms || !forms.length) return;

		forms.forEach(function (formEl, formIndex) {
			// Встроенная защита от перехвата системных форм WordPress и WooCommerce
			// Если пользователь указал слишком общий селектор (например, 'form'), мы игнорируем важные формы
			var isSystemForm = formEl.closest('.woocommerce') || 
							   formEl.classList.contains('woocommerce-checkout') ||
							   formEl.classList.contains('woocommerce-cart-form') ||
							   formEl.getAttribute('id') === 'loginform' || 
							   formEl.getAttribute('id') === 'registerform' || 
							   formEl.getAttribute('id') === 'lostpasswordform' || 
							   formEl.getAttribute('id') === 'commentform' || 
							   formEl.classList.contains('search-form') || 
							   formEl.getAttribute('id') === 'searchform' ||
							   formEl.getAttribute('name') === 'search' ||
							   formEl.getAttribute('role') === 'search' ||
							   formEl.classList.contains('admin-email-confirm-form') ||
							   formEl.getAttribute('name') === 'admin-email-confirm-form' ||
							   formEl.getAttribute('id') === 'admin-email-confirm-form';
			
			// Мы пропускаем системные формы только если селектор слишком общий
			// И ТОЛЬКО если это не включенная "Только валидация"
			// Поисковые формы исключаем всегда
			var isSearchForm = formEl.getAttribute('name') === 'search' || formEl.getAttribute('id') === 'searchform' || formEl.classList.contains('search-form') || formEl.getAttribute('role') === 'search';
			
			if ((isSystemForm && (config.selector === 'form' || config.selector === '*') && !config.validation_only) || isSearchForm) {
				return; // Пропускаем инициализацию для этой формы
			}

			// Режим совместимости с кэшем (Обновление Nonce)
			if (wpefData.general_settings && wpefData.general_settings.enable_cache_compat === 'on') {
				['mouseenter', 'touchstart', 'focusin'].forEach(evt => {
					formEl.addEventListener(evt, function() {
						if (!freshNonce && !isFetchingNonce) {
							isFetchingNonce = true;
							fetch(wpefData.ajaxUrl + '?action=wpef_get_nonce', { credentials: 'same-origin' })
								.then(r => r.json())
								.then(res => {
									if(res.success) freshNonce = res.data;
								}).catch(() => {})
								.finally(() => isFetchingNonce = false);
						}
					}, {once: true});
				});
			}

			// Аналитика: Отслеживание показов формы (View)
			if ('IntersectionObserver' in window) {
				let viewTracked = false;
				let observer = new IntersectionObserver(function(entries) {
					if (entries[0].isIntersecting && !viewTracked) {
						viewTracked = true;
						observer.disconnect();
						
						var formData = new FormData();
						formData.append('action', 'wpef_track_view');
						formData.append('config_id', config.id);
						
						fetch(wpefData.ajaxUrl, {
							method: 'POST',
							body: formData,
							credentials: 'same-origin'
						}).catch(function(){});
					}
				}, { threshold: 0.1 });
				observer.observe(formEl);
			}

			initUniversalForm(formEl, formIndex, config, () => freshNonce);
		});
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initWpefForms);
} else {
	initWpefForms();
}

function initUniversalForm(formEl, formIndex, config, getFreshNonceFn) {
	if (!formEl) return;

	// Отключаем нативные обработчики Webflow (и другие делегированные),
	// блокируя всплытие события submit до document.
	formEl.addEventListener('submit', function(event) {
		if (!config.validation_only) {
			event.preventDefault();
			event.stopPropagation();
		}
	});

	// Если на странице есть jQuery, отвязываем прямые обработчики Webflow с самой формы
	if (typeof jQuery !== 'undefined' && !config.validation_only) {
		jQuery(formEl).off('submit');
	}

	var formId = formEl.getAttribute('id');
	if (!formId) {
		formId = 'wpef-form-' + config.id + '-' + formIndex;
		formEl.setAttribute('id', formId);
	}

	// Внедряем скрытое поле с ID конфигурации
	var configInput = formEl.querySelector('input[name="wpef_config_id"]');
	if (!configInput) {
		configInput = document.createElement('input');
		configInput.type = 'hidden';
		configInput.name = 'wpef_config_id';
		configInput.value = config.id;
		formEl.appendChild(configInput);
	}

	// Условная логика (Отображение полей)
	if (config.conditions && config.conditions.length > 0) {
		config.conditions.forEach(function(rule) {
			if (!rule.trigger_name || !rule.target_selector) return;

			var triggerName = rule.trigger_name.trim();
			// Очищаем значение, если пользователь случайно ввел [name="..."] вместо простого имени
			var nameMatch = triggerName.match(/^\[name=['"]?(.*?)['"]?\]$/);
			if (nameMatch) {
				triggerName = nameMatch[1];
			}

			// Ищем элементы. Если имя содержит кириллицу или спецсимволы, 
			// CSS.escape помогает избежать ошибки синтаксиса в querySelector,
			// но для атрибутов в кавычках (например, [name="..."]) это обычно не обязательно.
			// Для максимальной надежности используем безопасный поиск:
			var triggers;
			try {
				triggers = formEl.querySelectorAll('[name="' + triggerName + '"]');
				// Поддержка поиска по ID или классу, если ввели селектор вместо имени
				if ((!triggers || !triggers.length) && (triggerName.charAt(0) === '#' || triggerName.charAt(0) === '.')) {
					triggers = formEl.querySelectorAll(triggerName);
				}
			} catch(e) {
				// Fallback, если querySelector упал из-за сложного имени (очень редкий кейс для атрибутов в кавычках)
				triggers = Array.from(formEl.elements).filter(function(el) {
					return el.name === triggerName;
				});
			}
			if (!triggers || !triggers.length) return;

			var targetEl = formEl.querySelector(rule.target_selector);
			// Если блок не найден внутри формы (например, он находится где-то в другом месте на странице),
			// ищем его глобально по всему документу
			if (!targetEl) {
				targetEl = document.querySelector(rule.target_selector);
			}
			if (!targetEl) return;

			var checkCondition = function() {
				var triggerValue = '';
				var triggerText = '';
				var isChecked = false;
				var isTriggerRadioOrCheckbox = false;

				// Ищем выбранное значение среди триггеров
				triggers.forEach(function(t) {
					if (t.type === 'radio' || t.type === 'checkbox') {
						isTriggerRadioOrCheckbox = true;
						if (t.checked) {
							triggerValue = t.value;
							isChecked = true;
						}
					} else if (t.tagName === 'SELECT') {
						triggerValue = t.value;
						if (t.selectedIndex >= 0) {
							triggerText = t.options[t.selectedIndex].text;
						}
					} else {
						triggerValue = t.value;
					}
				});

				var conditionMet = false;
				
				// Приводим к нижнему регистру для нечувствительности к регистру
				var valToCompare = (triggerValue || '').toString().trim().toLowerCase();
				var textToCompare = (triggerText || '').toString().trim().toLowerCase();
				var ruleValToCompare = (rule.trigger_value || '').toString().trim().toLowerCase();

				switch (rule.operator) {
					case 'equals':
						conditionMet = (valToCompare === ruleValToCompare) || (textToCompare === ruleValToCompare && textToCompare !== '');
						break;
					case 'not_equals':
						conditionMet = (valToCompare !== ruleValToCompare) && (textToCompare !== ruleValToCompare || textToCompare === '');
						break;
					case 'contains':
						conditionMet = (valToCompare.indexOf(ruleValToCompare) !== -1) || (textToCompare.indexOf(ruleValToCompare) !== -1 && textToCompare !== '');
						break;
					case 'checked':
						conditionMet = isTriggerRadioOrCheckbox ? isChecked : valToCompare !== '';
						break;
					case 'not_checked':
						conditionMet = isTriggerRadioOrCheckbox ? !isChecked : valToCompare === '';
						break;
				}

				var shouldShow = (rule.action === 'show' && conditionMet) || (rule.action === 'hide' && !conditionMet);

				var inputsInside = targetEl.querySelectorAll('input, select, textarea');
				
				var effect = rule.effect || 'slide';
				var duration = rule.duration ? parseInt(rule.duration, 10) : 300;
				var delay = rule.delay ? parseInt(rule.delay, 10) : 0;

				if (shouldShow) {
					// Показываем блок
					if (window.getComputedStyle(targetEl).display === 'none') {
						setTimeout(function() {
							if (effect === 'fade') {
								fadeIn(targetEl, duration);
							} else {
								slideDown(targetEl, duration);
							}
						}, delay);
					}
					// Включаем поля обратно
					inputsInside.forEach(function(inp) {
						if (inp.hasAttribute('data-wpef-disabled-by-logic')) {
							inp.disabled = false;
							inp.removeAttribute('data-wpef-disabled-by-logic');
						}
					});
				} else {
					// Скрываем блок
					if (window.getComputedStyle(targetEl).display !== 'none') {
						setTimeout(function() {
							if (effect === 'fade') {
								fadeOut(targetEl, duration);
							} else {
								slideUp(targetEl, duration);
							}
						}, delay);
					}
					// Отключаем поля, чтобы они не валидировались и не отправлялись
					inputsInside.forEach(function(inp) {
						if (!inp.disabled) {
							inp.disabled = true;
							inp.setAttribute('data-wpef-disabled-by-logic', 'true');
						}
						// Очищаем значение, чтобы не сохранялся мусор (кроме радио/чекбоксов)
						if (inp.type !== 'radio' && inp.type !== 'checkbox') {
							inp.value = '';
						}
					});
				}
			};

			// Запускаем при инициализации для установки дефолтного состояния
			checkCondition();

			// Навешиваем слушатели на изменение (change и input для мгновенной реакции)
			triggers.forEach(function(t) {
				t.addEventListener('change', checkCondition);
				if (t.type === 'text' || t.type === 'email' || t.type === 'number' || t.type === 'password' || t.tagName === 'TEXTAREA') {
					t.addEventListener('input', checkCondition);
				}
			});
		});
	}

	// Сбор UTM меток
	if (wpefData.general_settings && (wpefData.general_settings.enable_utm === '1' || wpefData.general_settings.enable_utm === true || wpefData.general_settings.enable_utm)) {
		var urlParams = new URLSearchParams(window.location.search);
		
		var setCookie = function(name, value, days) {
			var d = new Date();
			d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
			document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
		};
		var getCookie = function(name) {
			var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
			return v ? decodeURIComponent(v[2]) : null;
		};

		['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(utm) {
			var cookieName = 'wpef_' + utm;
			
			// Ищем в URL
			var val = urlParams.get(utm);
			// Если нет в URL, ищем в Cookies (Cross-session tracking)
			if (!val) {
				val = getCookie(cookieName);
			}
			
			// Если значение есть (из URL или старой сессии)
			if (val) {
				// Продлеваем/сохраняем куки на 30 дней
				setCookie(cookieName, val, 30);
				
				// Добавляем в форму
				var utmInput = formEl.querySelector('input[name="' + cookieName + '"]');
				if (!utmInput) {
					utmInput = document.createElement('input');
					utmInput.type = 'hidden';
					utmInput.name = cookieName;
					formEl.appendChild(utmInput);
				}
				utmInput.value = val;
			}
		});
	}

	// Сбор данных об устройстве (Client Fingerprint)
	if (wpefData.general_settings && (wpefData.general_settings.enable_device_info === '1' || wpefData.general_settings.enable_device_info === true || wpefData.general_settings.enable_device_info)) {
		var deviceInfo = {
			'wpef_user_agent': navigator.userAgent,
			'wpef_screen_res': screen.width + 'x' + screen.height,
			'wpef_device_type': /Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(navigator.userAgent) ? 'Mobile' : 'Desktop'
		};
		
		for (var key in deviceInfo) {
			var devInput = formEl.querySelector('input[name="' + key + '"]');
			if (!devInput) {
				devInput = document.createElement('input');
				devInput.type = 'hidden';
				devInput.name = key;
				formEl.appendChild(devInput);
			}
			devInput.value = deviceInfo[key];
		}
	}

	// Внедряем Honeypot (если включен)
	if (wpefData.general_settings && wpefData.general_settings.enable_honeypot) {
		var hpInput = formEl.querySelector('input[name="wpef_website_url"]');
		if (!hpInput) {
			hpInput = document.createElement('input');
			hpInput.type = 'text';
			hpInput.name = 'wpef_website_url';
			// Скрываем от обычных пользователей
			hpInput.style.display = 'none';
			hpInput.style.opacity = '0';
			hpInput.style.position = 'absolute';
			hpInput.style.top = '-9999px';
			hpInput.style.left = '-9999px';
			hpInput.tabIndex = -1;
			hpInput.autocomplete = 'off';
			formEl.appendChild(hpInput);
		}
	}

	var dropZone = formEl.querySelector('.file-drop-zone');
	var fileInput = dropZone ? dropZone.querySelector('.file__input') : null;
	var fileListEl = formEl.querySelector('.file-list');
	var submitBtn = formEl.querySelector('.submit-btn') || formEl.querySelector('[type="submit"]') || formEl.querySelector('input[type="submit"]');

	// Fallbacks для файлов, если нет глобальных правил
	var acceptTypes = fileInput ? (fileInput.getAttribute('accept') || '') : '';
	var selectedFiles = [];

	var validation = new JustValidate(formEl, {
		errorFieldCssClass: 'is-invalid',
		errorLabelCssClass: 'form__error-label',
		focusInvalidField: true,
	});

	// Применяем ГЛОБАЛЬНЫЕ правила валидации
	if (wpefData.validation_rules && wpefData.validation_rules.length > 0) {
		wpefData.validation_rules.forEach(function(fieldConfig) {
			if (!fieldConfig.selector || !fieldConfig.rules || fieldConfig.rules.length === 0) return;

			var fieldExists = formEl.querySelector(fieldConfig.selector);
			if (!fieldExists) return;

			var rulesForJustValidate = [];

			if (fieldExists.type === 'file') {
				var fileRules = { minFiles: 0, maxFiles: 0, maxTotalSize: 0, acceptTypes: '' };
				var isRequired = false, requiredError = wpefData.i18n.reqField;

				fieldConfig.rules.forEach(function(rule) {
					if (rule.type === 'required') {
						isRequired = true;
						if (rule.error) requiredError = rule.error;
					} else if (rule.type === 'minFiles') {
						fileRules.minFiles = parseInt(rule.value, 10) || 0;
						if (rule.error) fileRules.minFilesError = rule.error;
					} else if (rule.type === 'maxFiles') {
						fileRules.maxFiles = parseInt(rule.value, 10) || 0;
						if (rule.error) fileRules.maxFilesError = rule.error;
					} else if (rule.type === 'maxTotalSize') {
						fileRules.maxTotalSize = parseInt(rule.value, 10) || 0;
						if (rule.error) fileRules.maxTotalSizeError = rule.error;
					} else if (rule.type === 'acceptTypes') {
						fileRules.acceptTypes = rule.value || '';
					}
				});

				if (isRequired) {
					rulesForJustValidate.push({
						validator: function() { return selectedFiles.length > 0; },
						errorMessage: requiredError
					});
				}

				if (fileRules.minFiles > 0) {
					rulesForJustValidate.push({
						validator: function() { return selectedFiles.length >= fileRules.minFiles; },
						errorMessage: fileRules.minFilesError || (wpefData.i18n.attachMin + fileRules.minFiles)
					});
				}

				if (fileRules.maxFiles > 0) {
					rulesForJustValidate.push({
						validator: function() { return selectedFiles.length <= fileRules.maxFiles; },
						errorMessage: fileRules.maxFilesError || (wpefData.i18n.maxFiles + fileRules.maxFiles)
					});
				}

				if (fileRules.maxTotalSize > 0) {
					rulesForJustValidate.push({
						validator: function() { 
							var totalSize = selectedFiles.reduce(function (sum, file) { return sum + file.size; }, 0);
							return totalSize <= fileRules.maxTotalSize; 
						},
						errorMessage: fileRules.maxTotalSizeError || (wpefData.i18n.maxSizeExceeded + formatFileSize(fileRules.maxTotalSize))
					});
				}

				if (fileRules.acceptTypes) {
					acceptTypes = fileRules.acceptTypes;
				}

			} else {
				fieldConfig.rules.forEach(function(rule) {
					// Игнорируем файловые правила для обычных полей
					if (['minFiles', 'maxFiles', 'maxTotalSize', 'acceptTypes'].includes(rule.type)) return;

					var ruleObj = { rule: rule.type };
					
					if (rule.error) {
						ruleObj.errorMessage = rule.error;
					} else {
						var defaultErrors = {
							required: wpefData.i18n.reqField,
							email: wpefData.i18n.errEmail,
							minLength: wpefData.i18n.errMinLength,
							maxLength: wpefData.i18n.errMaxLength,
							password: wpefData.i18n.errPassword,
							strongPassword: wpefData.i18n.errStrongPass,
							number: wpefData.i18n.errNumber,
							integer: wpefData.i18n.errInteger,
							minNumber: wpefData.i18n.errMinNumber,
							maxNumber: wpefData.i18n.errMaxNumber,
							customRegexp: wpefData.i18n.errCustomRegexp
						};
						ruleObj.errorMessage = defaultErrors[rule.type] || wpefData.i18n.errInvalid;
					}
					
					if (rule.value) {
						if (rule.type === 'minLength' || rule.type === 'maxLength') {
							ruleObj.value = parseInt(rule.value, 10);
						} else if (rule.type === 'minNumber' || rule.type === 'maxNumber') {
							ruleObj.value = parseFloat(rule.value);
						} else if (rule.type === 'customRegexp') {
							try {
								ruleObj.value = new RegExp(rule.value);
							} catch(e) {
								console.error('Invalid RegExp:', rule.value);
							}
						} else {
							ruleObj.value = rule.value;
						}
					}
					rulesForJustValidate.push(ruleObj);
				});
			}

			if (rulesForJustValidate.length > 0) {
				try {
					if (fieldExists.type === 'radio' || fieldExists.type === 'checkbox') {
						for (var i = 0; i < rulesForJustValidate.length; i++) {
							if (rulesForJustValidate[i].rule === 'required') {
								delete rulesForJustValidate[i].rule;
								rulesForJustValidate[i].validator = function() {
									var elements = formEl.querySelectorAll('[name="' + fieldExists.name + '"]');
									for (var j = 0; j < elements.length; j++) {
										if (elements[j].checked) return true;
									}
									return false;
								};
							}
						}
					}

					var uniqueIdGlobal = fieldExists.getAttribute('data-wpef-id');
					if (!uniqueIdGlobal) {
						uniqueIdGlobal = 'wpef_tmp_' + Math.random().toString(36).substr(2, 9);
						fieldExists.setAttribute('data-wpef-id', uniqueIdGlobal);
					}
					var finalSelectorGlobal = '[data-wpef-id="' + uniqueIdGlobal + '"]';
					validation.addField(finalSelectorGlobal, rulesForJustValidate);

					if (fieldExists.type === 'radio' || fieldExists.type === 'checkbox') {
						var groupElements = formEl.querySelectorAll('[name="' + fieldExists.name + '"]');
						var isRevalidatingGlobal = false;
						for (var k = 0; k < groupElements.length; k++) {
							groupElements[k].addEventListener('change', function() {
								if (isRevalidatingGlobal) return;
								isRevalidatingGlobal = true;
								var firstEl = formEl.querySelector(finalSelectorGlobal);
								if (firstEl) {
									if (typeof validation.revalidateField === 'function') {
										validation.revalidateField(finalSelectorGlobal);
									} else {
										firstEl.dispatchEvent(new Event('change', { bubbles: true }));
									}
								}
								isRevalidatingGlobal = false;
							});
						}
					}
				} catch(e) {
					console.error('Ошибка добавления глобального правила', fieldExists, e);
				}
			}
		});
	}

	// Дефолтные required (если не переопределены глобальными правилами и не отключены)
	var disableAutoRequired = wpefData.general_settings && wpefData.general_settings.disable_auto_required;
	
	if (!disableAutoRequired) {
		var requiredFields = formEl.querySelectorAll('[required]');
		requiredFields.forEach(function (field) {
			if (field.type === 'file') return;

			var uniqueIdAuto = field.getAttribute('data-wpef-id');
			if (!uniqueIdAuto) {
				uniqueIdAuto = 'wpef_tmp_' + Math.random().toString(36).substr(2, 9);
				field.setAttribute('data-wpef-id', uniqueIdAuto);
			}
			var fieldSelector = '[data-wpef-id="' + uniqueIdAuto + '"]';
			
			// Защита от двойного добавления правил для любых полей
			if (field.getAttribute('data-wpef-processed')) return;
			field.setAttribute('data-wpef-processed', '1');

			// Если поле уже добавлено в валидатор через глобальные правила, пропускаем
			if (validation.fields && validation.fields[fieldSelector]) return;

			// Если поле - радио или чекбокс, проверяем, не добавляли ли мы уже правило для этой группы (по имени)
			if (field.type === 'radio' || field.type === 'checkbox') {
				if (field.name) {
					var groupSelector = 'wpef_group_' + field.name;
					// Проверяем, есть ли уже правило для этой группы в JustValidate
					var groupAlreadyAdded = false;
					if (validation.fields) {
						for (var key in validation.fields) {
							if (validation.fields[key] && validation.fields[key].elem && validation.fields[key].elem.name === field.name) {
								groupAlreadyAdded = true;
								break;
							}
						}
					}
					if (groupAlreadyAdded) return;
				}
			}

			try {
				if (field.type === 'radio' || field.type === 'checkbox') {
					validation.addField(fieldSelector, [
						{
							validator: function() {
								var elements = formEl.querySelectorAll('[name="' + field.name + '"]');
								for (var i = 0; i < elements.length; i++) {
									if (elements[i].checked) return true;
								}
								return false;
							},
							errorMessage: wpefData.i18n.reqField
						}
					]);

					var groupElementsAuto = formEl.querySelectorAll('[name="' + field.name + '"]');
					var isRevalidatingAuto = false;
					for (var k = 0; k < groupElementsAuto.length; k++) {
						groupElementsAuto[k].addEventListener('change', function() {
							if (isRevalidatingAuto) return;
							isRevalidatingAuto = true;
							var firstEl = formEl.querySelector(fieldSelector);
							if (firstEl) {
								if (typeof validation.revalidateField === 'function') {
									validation.revalidateField(fieldSelector);
								} else {
									firstEl.dispatchEvent(new Event('change', { bubbles: true }));
								}
							}
							isRevalidatingAuto = false;
						});
					}
				} else {
					validation.addField(fieldSelector, [
						{
							rule: 'required',
							errorMessage: wpefData.i18n.reqField,
						},
					]);
				}
			} catch (e) {
				console.error('Ошибка добавления правила required для поля', field, e);
			}
		});
	}



	if (dropZone && fileInput) {
		var uniqueIdFile = fileInput.getAttribute('data-wpef-id');
		if (!uniqueIdFile) {
			uniqueIdFile = 'wpef_tmp_' + Math.random().toString(36).substr(2, 9);
			fileInput.setAttribute('data-wpef-id', uniqueIdFile);
		}
		var fileSelector = '[data-wpef-id="' + uniqueIdFile + '"]';

		// Дефолтная проверка required для файла, если нет глобального правила
		if (!disableAutoRequired && fileInput.hasAttribute('required') && (!validation.fields || !validation.fields[fileSelector])) {
			validation.addField(fileSelector, [
				{
					validator: function () { return selectedFiles.length > 0; },
					errorMessage: wpefData.i18n.reqField,
				}
			]);
		}

		initFileDropZone(dropZone, fileInput, fileListEl, acceptTypes, function (files) {
			selectedFiles = files;
			if (validation.fields && validation.fields[fileSelector]) {
				if (typeof validation.revalidateField === 'function') {
					validation.revalidateField(fileSelector);
				} else {
					fileInput.dispatchEvent(new Event('change', { bubbles: true }));
				}
			}
		});
	}

	validation.onSuccess(function (event) {
		if (event && event.preventDefault) {
			event.preventDefault();
		}
		
		// Если включена только валидация (нативная отправка)
		if (config.validation_only) {
			// Вызываем нативный сабмит, обходя возможные конфликты с name="submit"
			HTMLFormElement.prototype.submit.call(formEl);
			return;
		}

		submitUniversalForm(formEl, selectedFiles, submitBtn, config, getFreshNonceFn ? getFreshNonceFn() : null);
	});
}

function initFileDropZone(dropZone, fileInput, fileListEl, acceptTypes, onChange) {
	if (!dropZone || !fileInput) return;
	var files = [];

	dropZone.addEventListener('click', function (event) {
		if (event.target === fileInput) return;
		fileInput.click();
	});

	dropZone.addEventListener('dragover', function (event) {
		event.preventDefault();
		dropZone.classList.add('is-dragover');
	});

	dropZone.addEventListener('dragleave', function () {
		dropZone.classList.remove('is-dragover');
	});

	dropZone.addEventListener('drop', function (event) {
		event.preventDefault();
		dropZone.classList.remove('is-dragover');
		if (!event.dataTransfer || !event.dataTransfer.files) return;
		addFiles(Array.from(event.dataTransfer.files));
	});

	fileInput.addEventListener('change', function () {
		if (!fileInput.files || !fileInput.files.length) return;
		addFiles(Array.from(fileInput.files));
		fileInput.value = '';
	});

	function addFiles(newFiles) {
		newFiles.forEach(function (file) {
			if (!isFileTypeAllowed(file, acceptTypes)) {
				showFormMessage(dropZone.closest('form'), wpefData.i18n.unsupportedFormat1 + file.name + wpefData.i18n.unsupportedFormat2, 'error');
				return;
			}
			files.push(file);
		});
		renderFileList();
		onChange(files);
	}

	function removeFile(index) {
		files.splice(index, 1);
		renderFileList();
		onChange(files);
	}

	function renderFileList() {
		if (!fileListEl) return;
		fileListEl.innerHTML = '';
		fileListEl.style.display = files.length > 0 ? 'flex' : 'none';

		files.forEach(function (file, index) {
			var item = document.createElement('div');
			item.className = 'file__item';
			item.innerHTML = '<div class="filename">' + file.name + '</div>' +
							 '<div>' + formatFileSize(file.size) + '</div>' +
							 '<svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 16 16" fill="none" aria-label="' + wpefData.i18n.deleteFile + '" class="file__del" style="cursor:pointer;">' +
							 '<rect x="3.01465" y="12.6006" width="13.5539" height="0.564747" rx="0.282374" transform="rotate(-45 3.01465 12.6006)" fill="white"></rect>' +
							 '<rect x="3.58008" y="3" width="13.5539" height="0.564747" rx="0.282374" transform="rotate(45 3.58008 3)" fill="white"></rect>' +
							 '</svg>';
			item.querySelector('.file__del').addEventListener('click', function () { removeFile(index); });
			fileListEl.appendChild(item);
		});
	}
}

function isFileTypeAllowed(file, acceptTypes) {
	if (!acceptTypes) return true;
	var allowedList = acceptTypes.split(',').map(function (item) { return item.trim().toLowerCase(); });
	var fileType = (file.type || '').toLowerCase();
	var fileExt = '.' + (file.name.split('.').pop() || '').toLowerCase();
	return allowedList.some(function (allowed) { return allowed === fileType || allowed === fileExt; });
}

function formatFileSize(bytes) {
	if (!bytes || bytes <= 0) return '0 MB';
	return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

function showFormMessage(formEl, text, type) {
	if (!formEl) return;
	
	// Поиск блока с классом w-form-fail (для ошибок) или w-form-done/success (для успеха) 
	// рядом с формой (Webflow структура)
	var parentContainer = formEl.closest('.w-form') || formEl.parentElement;
	var isWebflow = false;
	
	if (parentContainer) {
		var failBlock = parentContainer.querySelector('.w-form-fail');
		var successBlock = parentContainer.querySelector('.w-form-done, .success');
		
		if (failBlock || successBlock) {
			isWebflow = true;
			
			if (type === 'error' && failBlock) {
				failBlock.style.display = 'block';
				if (successBlock) successBlock.style.display = 'none';
				
				var msgDiv = failBlock.querySelector('div');
				if (msgDiv) msgDiv.innerHTML = text;
			} else if (type === 'success' && successBlock) {
				successBlock.style.display = 'block';
				if (failBlock) failBlock.style.display = 'none';
				
				var msgDiv = successBlock.querySelector('div');
				if (msgDiv) msgDiv.innerHTML = text;
			}
		}
	}
	
	// Стандартное поведение (если не Webflow или если нет нужных блоков)
	if (!isWebflow) {
		var messageEl = formEl.querySelector('.form__message');
		if (!messageEl) {
			messageEl = document.createElement('div');
			messageEl.className = 'form__message';
			formEl.appendChild(messageEl);
		}
		messageEl.innerHTML = text;
		messageEl.classList.remove('is-success', 'is-error');
		messageEl.classList.add(type === 'success' ? 'is-success' : 'is-error');
	}
}

function submitUniversalForm(formEl, files, submitBtn, config, freshNonce) {
	if (!formEl || typeof wpefData === 'undefined' || !wpefData.ajaxUrl) {
		showFormMessage(formEl, wpefData.i18n.formUnavailable, 'error');
		return;
	}

	// Фикс для чекбоксов-массивов и радио-кнопок: если value не задан или равен "on",
	// браузер отправляет "on". Для групп опций мы заменяем его на текст связанного label,
	// чтобы на почту приходило название опции, а не просто "Да / Получено".
	var checkboxesAndRadios = formEl.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked');
	checkboxesAndRadios.forEach(function(cb) {
		if (!cb.hasAttribute('value') || cb.value === 'on') {
			var labelText = '';
			var id = cb.getAttribute('id');
			
			if (id && id !== '[]') {
				try {
					var labelEl = formEl.querySelector('label[for="' + CSS.escape(id) + '"]');
					if (labelEl) labelText = labelEl.textContent;
				} catch(e) {}
			}
			
			if (!labelText) {
				var parentLabel = cb.closest('label');
				if (parentLabel) labelText = parentLabel.textContent;
			}

			if (!labelText) {
				var next = cb.nextElementSibling;
				if (next && (next.tagName === 'SPAN' || next.tagName === 'DIV')) {
					labelText = next.textContent;
				}
			}
			
			if (labelText) {
				labelText = labelText.replace(/\s+/g, ' ').trim();
				if (labelText) {
					cb.value = labelText;
				}
			}
		}
	});

	var formData = new FormData(formEl);
	formData.append('action', 'wpef_submit_form'); // Экшен плагина
	formData.append('wpef_nonce', freshNonce || wpefData.nonce); // Глобальный Nonce плагина

	// Удаляем нативные поля файлов из FormData, чтобы избежать дублирования
	// файлов на сервере (мы отправляем их вручную ниже)
	var fileInputs = formEl.querySelectorAll('input[type="file"]');
	for (var i = 0; i < fileInputs.length; i++) {
		if (fileInputs[i].name) {
			formData.delete(fileInputs[i].name);
		}
	}

	files.forEach(function (file) {
		formData.append('attachments[]', file);
	});

	var actualFormName = formEl.getAttribute('name');
	if (!actualFormName) {
		actualFormName = formEl.querySelector('[name="form_name"]') ? formEl.querySelector('[name="form_name"]').value : (config ? config.name : '');
	}
	if (actualFormName) {
		formData.append('form_name', actualFormName);
	}

	// Явный захват title страницы для функции "Использовать имя страницы в заголовке"
	if (!formData.has('__title')) {
		formData.append('__title', document.title);
	}
	if (!formData.has('__page')) {
		formData.append('__page', window.location.href);
	}

	if (submitBtn) {
		submitBtn.disabled = true;
	}

	var useLoader = config.loader_enable !== false && config.loader_enable !== 'false' && config.loader_enable !== '0';
	
	if (useLoader) {
		formEl.classList.add('wpef-form-is-loading');
		
		// Добавляем спиннер-оверлей поверх формы, если его еще нет
		var overlay = formEl.querySelector('.wpef-form-loader-overlay');
		if (!overlay) {
			overlay = document.createElement('div');
			overlay.className = 'wpef-form-loader-overlay';
			overlay.innerHTML = '<div class="wpef-form-spinner"><svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg></div>';
			formEl.appendChild(overlay);
		}

		var loaderBg = config.loader_bg || 'rgba(255, 255, 255, 0.7)';
		var loaderColor = config.loader_color || '#4f46e5';

		overlay.style.background = loaderBg;
		var circle = overlay.querySelector('circle');
		if (circle) circle.style.stroke = loaderColor;

		// Инжектим стили для оверлея один раз
		if (!document.getElementById('wpef-front-styles')) {
			var style = document.createElement('style');
			style.id = 'wpef-front-styles';
			style.innerHTML = `
				.wpef-form-is-loading {
					position: relative !important;
					pointer-events: none !important;
				}
				.wpef-form-loader-overlay {
					position: absolute;
					top: 0; left: 0; right: 0; bottom: 0;
					backdrop-filter: blur(2px);
					z-index: 9999;
					display: flex;
					align-items: center;
					justify-content: center;
					border-radius: inherit;
					opacity: 0;
					visibility: hidden;
					transition: opacity 0.3s ease, visibility 0.3s ease;
				}
				.wpef-form-is-loading .wpef-form-loader-overlay {
					opacity: 1;
					visibility: visible;
				}
				.wpef-form-spinner {
					width: 40px;
					height: 40px;
				}
				.wpef-form-spinner svg {
					animation: wpef-rotate 2s linear infinite;
					width: 100%;
					height: 100%;
				}
				.wpef-form-spinner circle {
					stroke-dasharray: 1, 150;
					stroke-dashoffset: 0;
					animation: wpef-dash 1.5s ease-in-out infinite;
					stroke-linecap: round;
				}
				@keyframes wpef-rotate { 100% { transform: rotate(360deg); } }
				@keyframes wpef-dash {
					0% { stroke-dasharray: 1, 150; stroke-dashoffset: 0; }
					50% { stroke-dasharray: 90, 150; stroke-dashoffset: -35; }
					100% { stroke-dasharray: 90, 150; stroke-dashoffset: -124; }
				}
			`;
			document.head.appendChild(style);
		}
	}

	// Сбор дополнительных данных для автоответа на клиенте, если настроен селектор
		var replyEmailSelector = config.reply_email_field;
		if (replyEmailSelector) {
			var emailInput = formEl.querySelector(replyEmailSelector);
			if (emailInput && emailInput.value) {
				formData.append('wpef_client_user_email', emailInput.value);
			}
		}

		fetch(wpefData.ajaxUrl, {
		method: 'POST',
		body: formData,
		credentials: 'same-origin',
	})
	.then(function (response) { return response.json(); })
	.then(function (result) {
		if (result && result.success) {
			var existingMsg = formEl.querySelector('.form__message');
			if (existingMsg) existingMsg.remove();
			
			formEl.reset();
			var fileListEl = formEl.querySelector('.file-list');
			if (fileListEl) {
				fileListEl.innerHTML = '';
				fileListEl.style.display = 'none';
			}

			// Выполнение Custom JS из настроек админки
			if (config.custom_js) {
				try {
					var customFn = new Function('form', 'response', config.custom_js);
					customFn(formEl, result);
				} catch (e) {
					console.error('WP Easy Forms Custom JS Error:', e);
				}
			}

			// Редирект
			if (config.redirect_url) {
				if (config.redirect_blank) {
					window.open(config.redirect_url, '_blank');
				} else {
					window.location.href = config.redirect_url;
				}
			}

			// Скрытие блока
			if (config.hide_block_enable) {
				var targetEl = formEl;
				if (config.hide_block_selector) {
					var foundEl = document.querySelector(config.hide_block_selector);
					if (foundEl) targetEl = foundEl;
				}
				
				var duration = config.hide_block_duration ? parseInt(config.hide_block_duration, 10) : 500;
				var delay = config.hide_block_delay ? parseInt(config.hide_block_delay, 10) : 0;
				
				setTimeout(function() {
					if (config.hide_block_effect === 'slideUp') {
						slideUp(targetEl, duration);
					} else {
						fadeOut(targetEl, duration);
					}
				}, delay);
			}

			// Отображение блока
			if (config.show_block_enable && config.show_block_selector) {
				var showTargetEl = document.querySelector(config.show_block_selector);
				if (showTargetEl) {
					var showDuration = config.show_block_duration ? parseInt(config.show_block_duration, 10) : 500;
					var showDelay = config.show_block_delay ? parseInt(config.show_block_delay, 10) : 0;
					
					setTimeout(function() {
						if (config.show_block_effect === 'slideDown') {
							slideDown(showTargetEl, showDuration);
						} else {
							fadeIn(showTargetEl, showDuration);
						}
					}, showDelay);
				}
			}

			var successEvent = new CustomEvent('wpefFormSuccess', {
				bubbles: true,
				detail: { form: formEl, response: result }
			});
			formEl.dispatchEvent(successEvent);

			// Интеграция с системами аналитики
			if (config.analytics_ym_id && config.analytics_ym_goal) {
				if (typeof ym !== 'undefined') {
					ym(config.analytics_ym_id, 'reachGoal', config.analytics_ym_goal);
				} else if (typeof window['yaCounter' + config.analytics_ym_id] !== 'undefined') {
					window['yaCounter' + config.analytics_ym_id].reachGoal(config.analytics_ym_goal);
				}
			}

			if (config.analytics_ga_event) {
				if (typeof gtag !== 'undefined') {
					gtag('event', config.analytics_ga_event);
				} else if (typeof dataLayer !== 'undefined') {
					dataLayer.push({'event': config.analytics_ga_event});
				} else if (typeof ga !== 'undefined') {
					ga('send', 'event', 'wpef_form', 'submit', config.analytics_ga_event);
				}
			}

			if (config.analytics_vk_event) {
				if (typeof VK !== 'undefined' && VK.Retargeting) {
					VK.Retargeting.Event(config.analytics_vk_event);
				}
			}
		} else {
			var errorMessage = result && result.data && result.data.message ? result.data.message : wpefData.i18n.submitFailed;
			
			// Выводим технические детали ошибки только в консоль для разработчика
			if (result && result.data && result.data.debug_error) {
				console.error('Детали ошибки отправки почты (WP Easy Forms):', result.data.debug_error);
			}

			showFormMessage(formEl, errorMessage, 'error');
		}
	})
	.catch(function () {
		showFormMessage(formEl, wpefData.i18n.networkError, 'error');
	})
	.finally(function () {
		if (submitBtn) {
			submitBtn.disabled = false;
		}
		formEl.classList.remove('wpef-form-is-loading');
	});
}