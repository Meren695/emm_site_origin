<?php
/*
Template name: Детские
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf432" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body>
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('sekcii_stranicy_detskie') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секции Страницы Детские"; $loop_field = "sekcii_stranicy_detskie"; while( have_rows('sekcii_stranicy_detskie') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
			<div>
				<div class="w-embed">
					<style>.w-nav-overlay {
	margin-top: 60px;
}
					</style>
				</div>
				<div class="w-embed">
					<style>.nav-button {
  position: relative;
  padding-bottom: 5px;
}

.nav-button::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: #229F5C;
  transition: width 0.3s ease;
}

.nav-button:hover::after {
  width: 100%;
}
					</style>
				</div>
				<div data-animation="over-right" data-collapse="medium" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="navbar-2 w-nav">
					<nav class="container-xxl-nav"><a href="/" class="logo w-nav-brand"><div class="logo-block"><img loading="eager" src="<?php echo get_template_directory_uri() ?>/images/694938646b904ed971a56b41_emmauss-logo.svg" alt class="image-logo"></div></a>
						<nav role="navigation" class="nav-menu w-nav-menu">
							<div id="mainNav" class="nav-buttons"><?php $menu_tree = wtw_get_menu_tree("Главное меню"); if(is_array($menu_tree)){ foreach($menu_tree as $menu_item){ ?>
								<div data-hover="true" data-delay="0" class="dropdown-5 w-dropdown" data-content="menu-level">
									<div class="nav-button w-dropdown-toggle">
										<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="link-3"><?php echo $menu_item->title ?></a></div>
									<?php if(count($menu_item->children)){ ?><nav class="dropdown-list-2 w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?>
										<div data-hover="true" data-delay="0" class="dropdown-4 w-dropdown" data-content="menu-level">
											<div class="dropdown-toggle-2 w-dropdown-toggle">
												<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="nav-button"><?php echo $menu_item->title ?></a></div>
											<?php if(count($menu_item->children)){ ?><nav class="dropdown-list-3 w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?>
												<div data-hover="true" data-delay="0" class="dropdown-4 w-dropdown" data-content="menu-level">
													<div class="dropdown-toggle-2 w-dropdown-toggle">
														<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="nav-button"><?php echo $menu_item->title ?></a></div>
													<?php if(count($menu_item->children)){ ?><nav class="w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?>
														<div data-hover="true" data-delay="0" class="dropdown-4 w-dropdown" data-content="menu-level">
															<div class="dropdown-toggle-2 w-dropdown-toggle">
																<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="nav-button"><?php echo $menu_item->title ?></a></div>
															<?php if(count($menu_item->children)){ ?><nav class="w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?><a href="#" class="nav-button-2 w-dropdown-link" data-content="menu-level">Link 1</a><?php } ?></nav><?php } ?>
														</div><?php } ?></nav><?php } ?>
												</div><?php } ?></nav><?php } ?>
										</div><?php } ?></nav><?php } ?>
								</div><?php }} ?></div>
						</nav>
						<div class="nav-soc-tel">
							<div class="phone-nav-block"><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_field('telefon_1', 'options')) ?>" class="link-phone w-nav-link"><?php echo get_field('telefon_1', 'options') ?></a></div><a href="<?php echo get_field('ssylka_knopki', 'options') ?>" class="main-button-ht top-btn w-button"><?php echo get_field('knopka', 'options') ?></a>
							<div class="bvi-open"></div>
						</div>
						<div class="menu-button-2 w-nav-button">
							<div data-w-id="ee73380d-1027-de24-dc9c-33949dbfa886" data-is-ix2-target="1" class="lottie-animation" data-animation-type="lottie" data-src="https://cdn.prod.website-files.com/67d0865dbdaf088294d91f45/67d0865dbdaf088294d920b2_58235-hamburger-menu.json" data-loop="0" data-direction="1" data-autoplay="0" data-renderer="svg" data-default-duration="1.8333333333333333" data-duration="0.5" data-loading="eager"></div>
						</div>
					</nav>
				</div>
			</div>
			<div class="korp-block">
				<div>
					<?php if( have_rows('pervaya_sekciya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Первая секция"; $loop_field = "pervaya_sekciya"; while( have_rows('pervaya_sekciya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
						<section id="first-section" class="section-detskie" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_pervoj_sekcii'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
							<div class="fil-bg-korporat">
								<div class="container-xl img-main">
									<div class="div-preim-copy header">
										<div class="main-text-block">
											<div class="main-text"><?php echo get_sub_field('mini_tekst_vyshe_zagolovka') ?></div>
											<h1 class="h1"><?php echo get_sub_field('zagolovok_h1') ?></h1>
											<div class="text-block-34 hide-mob"><?php echo get_sub_field('mini_tekst_nizhe_zagolovka') ?></div>
											<?php if( have_rows('knopka_na_pervom_ekrane') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка на первом экране"; $loop_field = "knopka_na_pervom_ekrane"; while( have_rows('knopka_na_pervom_ekrane') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons">
												<div><a data-w-id="fe6a7a0c-3203-4b46-3bfb-f1a76c7622f9" href="#" class="main-button-ht w-button">Заказать мероприятие</a></div>
											</div><?php } ?><?php } ?>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div><?php } ?><?php } ?>
					<?php if( have_rows('sekciya_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 2"; $loop_field = "sekciya_2"; while( have_rows('sekciya_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="second-section" class="section_brown">
						<div class="container-padding-20">
							<div class="container-xl green-cont-60">
								<div class="about-us_grid end-d">
									<div class="what-to-do_text-block">
										<div class="aboute-us_text-block-korp">
											<h2 id="w-node-_15b36a85-58bc-1d62-d459-1e2b6ddcfb4b-d6fcf432" class="h2 same-page-h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
											<div>
												<p class="preim-text red-18 b korp">Мероприятие пройдёт на берегу Волги, в окружении соснового леса. Свежий воздух, пение птиц и завораживающие виды создадут неповторимую атмосферу. Дети смогут вдоволь набегаться на просторной территории, исследовать окружающий мир и зарядиться энергией природы.<br><br><strong>Форматы мероприятий:</strong></p>
												<ul role="list" class="w-list-unstyled">
													<li class="preim-text-korp">Детские дни рождения</li>
													<li class="preim-text-korp">Выпускные</li>
													<li class="preim-text-korp">Детские квесты</li>
													<li class="preim-text-korp">Гендер-пати</li>
												</ul>
												<p class="preim-text red-18 b korp">Организовать яркий, безопасный и запоминающийся праздник для детей непросто. «Эммаус Волга Клаб» решает эту задачу комплексно: здесь есть всё необходимое, чтобы и малыши, и родители провели день с удовольствием.<br></p>
											</div>
										</div>
									</div><img class="image-31-korp" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
							</div>
						</div>
					</section><?php } ?><?php } ?>
					<?php if( have_rows('banner_big_1') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Баннер биг 1"; $loop_field = "banner_big_1"; while( have_rows('banner_big_1') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section">
						<div class="container-padding-20">
							<div class="container-xl-2 img-prom-banner-deti">
								<div class="banner_block korp-grid">
									<div class="prom-text-block">
										<div class="width_icon_label">
											<div class="wave-icon winter-icon"><img width="8" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac76a_emmaus-logo-home.svg" class="testimonial-image"></div>
											<div class="text-block-36"><?php echo get_sub_field('mini_tekst') ?></div>
										</div>
										<h2 class="h2-2 banner2 korp"><?php echo get_sub_field('zagolovok_n2') ?></h2>
										<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-2">
											<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div><a data-w-id="842ca4fe-7b6d-285b-5577-de596a35f8ef" href="#" class="main-button-ht green w-button">Забронировать</a></div><?php } ?>
										</div><?php } ?><?php } ?>
									</div><img class="image-3" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="210" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" id="w-node-fe6a7a0c-3203-4b46-3bfb-f1a76c762450-d6fcf432" loading="lazy"></div>
							</div>
						</div>
					</div><?php } ?><?php } ?>
					<?php if( have_rows('sekciya_4') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 4"; $loop_field = "sekciya_4"; while( have_rows('sekciya_4') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="second-section" class="section_brown sekt-40">
						<div class="container-padding-20">
							<div class="container-xl green-cont-60">
								<div class="about-us_grid end-d"><img class="image-31-korp" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" id="w-node-fe6a7a0c-3203-4b46-3bfb-f1a76c762440-d6fcf432" loading="lazy">
									<div class="what-to-do_text-block">
										<div class="aboute-us_text-block-korp">
											<div>
												<p class="preim-text red-18 b korp">В «Эммаусс Волга Клаб для» детей предусмотрены:</p>
												<ul role="list" class="w-list-unstyled">
													<li class="preim-text-korp">Благоустроенная детская площадка</li>
													<li class="preim-text-korp">Детская комната</li>
													<li class="preim-text-korp">Прокат велосипедов и другого инвентаря</li>
													<li class="preim-text-korp">Настольный теннис</li>
												</ul>
											</div>
											<div>
												<p class="preim-text red-18 b korp">Наша команда возьмёт на себя все хлопоты по организации детского праздника:<br></p>
												<ul role="list" class="w-list-unstyled">
													<li class="preim-text-korp">Поможем с оформлением площадки</li>
													<li class="preim-text-korp">Предложим сценарии развлечений с аниматорами</li>
													<li class="preim-text-korp">Обеспечим техническое сопровождение (музыка, микрофоны)</li>
												</ul>
											</div>
											<div>
												<p class="preim-text red-18 b korp">Пока дети веселятся, взрослые могут:</p>
												<ul role="list" class="w-list-unstyled">
													<li class="preim-text-korp">Отдохнуть в беседках с видом на Волгу</li>
													<li class="preim-text-korp">Посетить банный комплекс для релаксации</li>
													<li class="preim-text-korp">Организовать фуршет или банкет в ресторане</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section><?php } ?><?php } ?>
					<?php if( have_rows('banner_big_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Баннер биг 2"; $loop_field = "banner_big_2"; while( have_rows('banner_big_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section hide">
						<div class="container-padding-20">
							<div class="container-xl-2 img-prom-banner-deti">
								<div class="banner_block korp-grid">
									<div class="prom-text-block">
										<div class="width_icon_label">
											<div class="wave-icon winter-icon"><img width="8" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac76a_emmaus-logo-home.svg" class="testimonial-image"></div>
											<div class="text-block-36"><?php echo get_sub_field('mini_tekst') ?></div>
										</div>
										<h2 class="h2-2 banner2 korp"><?php echo get_sub_field('zagolovok_n2') ?></h2>
										<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-2"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?><?php } ?>
									</div><img class="image-3" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="210" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" id="w-node-_073c4f31-281e-ba34-1b36-8753139f229f-d6fcf432" loading="lazy"></div>
							</div>
						</div>
					</div><?php } ?><?php } ?>
					<?php if( have_rows('blok_s_kartoj') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок с картой"; $loop_field = "blok_s_kartoj"; while( have_rows('blok_s_kartoj') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
						<div class="container-xl green-cont-60 end">
							<div id="ya-map" class="map_block w-node-c408fca7-81ef-7fb6-2f3b-0fe1438e784d-d6fcf432">
								<div class="map_text_block">
									<div class="wave-icon about-us"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac76a_emmaus-logo-home.svg" alt class="testimonial-image"></div>
									<div><?php echo get_sub_field('tekst') ?></div><a href="tel:+79588685593" class="text-block-58">+7 (958) 868-55-93</a><a href="mailto:sales@emmausvolgaclub.ru" class="text-block-58-copy">sales@emmausvolgaclub.ru</a></div>
								<div id="wrapMap"></div>
								<div class="map"><?php echo get_sub_field('kod_karty') ?></div>
								<div class="w-embed">
									<style>
#wrapMap {
    z-index: 3;
    position: absolute;
    inset: 0%;
}
.mapTitle {
    position: absolute;
    z-index: 1000;
    box-shadow: rgba(0, 0, 0, 0.25) 0px 0px 5px;
    display: none;
    padding: 5px 20px;
    border-radius: 5px;
    background: rgb(255, 255, 255);
    border-width: 1px;
    border-style: solid;
    border-color: rgb(204, 204, 204);
    border-image: initial;
}
									</style>
								</div>
								<div class="w-embed">
									<script>
const mapTitle = document.createElement('div'); mapTitle.className = 'mapTitle';
mapTitle.textContent = 'Кликните для активации карты';
wrapMap.appendChild(mapTitle);
wrapMap.onclick = function() {
    this.removeAttribute('style'); // Удаляем inline-стили
    this.removeAttribute('id'); 
    mapTitle.parentElement.removeChild(mapTitle);
}

wrapMap.onmousemove = function(event) {
    mapTitle.style.display = 'block';
    if(event.offsetY > 10) mapTitle.style.top = event.offsetY + 20 + 'px';
    if(event.offsetX > 10) mapTitle.style.left = event.offsetX + 20 + 'px';
}
wrapMap.onmouseleave = function() {
    mapTitle.style.display = 'none';
}
									</script>
								</div>
							</div>
						</div>
					</section><?php } ?><?php } ?>
				</div>
				<div class="div-block-110">
					<div>
						<div class="w-embed">
							<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
						</div>
						<div class="w-embed">
							<script>
const swiper_first_reserv = new Swiper('.swiper-first-reserv', {
  speed: 500,          
  freeMode: true,      
  freeModeMomentum: true, 
  freeModeMomentumRatio: 2,
  freeModeMomentumBounce: true, 
  freeModeMomentumVelocityRatio: 1,
  direction: "horizontal",
  effect: "slide",
  disableOnInteraction: true,
  slidesPerView: "auto",
  mousewheel: {
  enabled: true,
  releaseOnEdges: true, },
  keyboard: true,
  centeredSlides: false,
  autoplay: {
    delay: 3000,
  },
});
							</script>
						</div>
					</div>
				</div>
				<div>
					<div class="contact-modal1_component">
						<div class="contact-modal1_content-wrapper">
							<div class="margin-bottom margin-large">
								<div class="text-align-center">
									<div class="max-width-large align-center">
										<div class="margin-bottom margin-xsmall">
											<h2>Забронировать мероприятие</h2>
										</div>
										<p class="text-size-medium-2">Оставьте свои контактные данные.</p>
									</div>
								</div>
							</div>
							<div class="contact-modal1_form-block w-form">
								<form id="wf-form-Contact-1-Form" name="wf-form-Contact-1-Form" method="get" data-name="Contact 1 Form" class="contact-modal1_form" data-wf-page-id="686931998f03c728d6fcf432" data-wf-element-id="b5321e1b-2509-421d-4eb2-6b2106229abf">
									<div class="form_field-wrapper"><label for="Contact-1-Name" class="form_field-label">Ваше ФИО</label><input class="form_input w-input" maxlength="256" name="field" data-name="ФИО" placeholder type="text" id="field" required></div>
									<div class="form_field-wrapper"><label for="field" class="form_field-label">Телефон</label><input class="form_input w-input" maxlength="256" name="field" data-name="Телефон" placeholder type="tel" id="field" required></div>
									<div class="form_field-wrapper"><label for="Email" class="form_field-label">Email</label><input class="form_input w-input" maxlength="256" name="Email" data-name="Email" placeholder type="email" id="Email"></div>
									<div class="margin-bottom margin-xsmall"><label id="Contact-1-Checkbox" class="w-checkbox form_checkbox"><div class="w-checkbox-input w-checkbox-input--inputType-custom form_checkbox-icon w--redirected-checked" for="Checkbox-1"></div><input type="checkbox" name="Checkbox-1" id="Checkbox-1" data-name="Checkbox 1" required style="opacity:0;position:absolute;z-index:-1" checked><span for="Checkbox-1" class="form_checkbox-label text-size-small w-form-label">Согласие на <a href="/polzovatelskoe-soglashenie/" class="text-style-link">Пользовательское соглашение</a></span></label><label id="Contact-1-Checkbox" class="w-checkbox form_checkbox"><div class="w-checkbox-input w-checkbox-input--inputType-custom form_checkbox-icon w--redirected-checked" for="Checkbox-2"></div><input type="checkbox" name="Checkbox-2" id="Checkbox-2" data-name="Checkbox 2" required style="opacity:0;position:absolute;z-index:-1" checked><span for="Checkbox-2" class="form_checkbox-label text-size-small w-form-label">Согласие на <a href="/privacy-policy/" class="text-style-link">Политика конфиденциальности</a></span></label></div>
									<div class="div-block-446"><input type="submit" data-wait="Подождите..." class="main-button-modal top-btn w-button" value="Забронировать"></div>
								</form>
								<div class="success-message w-form-done">
									<div class="success-text">Thank you! Your submission has been received!</div>
								</div>
								<div class="error-message w-form-fail">
									<div class="error-text">Oops! Something went wrong while submitting the form.</div>
								</div>
							</div><a data-w-id="b5321e1b-2509-421d-4eb2-6b2106229ae2" href="#" class="contact-modal1_close-button w-inline-block"><div class="icon-embed-small hide-mobile-landscape w-embed"><svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M24.056 23.5004L23.5004 24.056C23.1935 24.3628 22.696 24.3628 22.3892 24.056L16 17.6668L9.61078 24.056C9.30394 24.3628 8.80645 24.3628 8.49961 24.056L7.94403 23.5004C7.63719 23.1936 7.63719 22.6961 7.94403 22.3892L14.3332 16L7.94403 9.61081C7.63719 9.30397 7.63719 8.80648 7.94403 8.49964L8.49961 7.94406C8.80645 7.63721 9.30394 7.63721 9.61078 7.94406L16 14.3333L22.3892 7.94404C22.6961 7.6372 23.1935 7.6372 23.5004 7.94404L24.056 8.49963C24.3628 8.80647 24.3628 9.30395 24.056 9.61079L17.6667 16L24.056 22.3892C24.3628 22.6961 24.3628 23.1936 24.056 23.5004Z" fill="currentColor"></path></svg></div><img alt src="<?php echo get_template_directory_uri() ?>/images/6932f5b29a97cdbf7bbad570_icon_close-modal.svg" loading="lazy" class="show-mobile-landscape"></a></div>
						<div data-w-id="b5321e1b-2509-421d-4eb2-6b2106229ae5" class="contact-modal1_background-overlay"></div>
					</div>
				</div>
				<div>
					<address class="call-to-action">
						<div class="trigger">
							<div data-is-ix2-target="1" class="trigger-lottie" data-w-id="6c2dad21-436f-9358-e1e9-4c7534bdb1f3" data-animation-type="lottie" data-src="https://cdn.prod.website-files.com/5f9b359e6ddc81e427ceab3a/5fa17bb235bfbbc33afaad4e_contact-cta.json" data-loop="0" data-direction="1" data-autoplay="0" data-renderer="svg" data-default-duration="2.0020019204587935" data-duration="2" data-loading="eager" data-ix2-initial-state="0"></div>
						</div><a href="<?php echo get_field('ssylka_na_votcap_futer', 'options') ?>" target="_blank" class="whatsapp w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/69a83d8fafb695c599a13167_D0BCD0B0D0BAD18120D0B1D0B5D0BB.svg" alt class="call-to-action-icon"></a><a href="<?php echo get_field('ssylka_na_telegram_futer', 'options') ?>" class="email w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/686931998f03c728d6fcf3ac_Telegram-footer.svg" alt class="call-to-action-icon"></a><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_field('telefon_1', 'options')) ?>" class="mobile w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/62ffe65faad4eedcbcf0ebdb_call.svg" alt class="call-to-action-icon"></a></address>
				</div>
			</div>
			<div>
				<section class="footer-dark"><a href="#" class="footer-brand w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac805_logo-footer-white.svg" alt></a>
					<div class="footer-divider"></div>
					<div class="container-xl">
						<div class="footer-wrapper">
							<div class="footer-content">
								<div id="w-node-d2510c3f-abe0-7c21-3baa-0e3c6dbef737-6dbef72c" class="footer-block">
									<div class="title-small"><?php echo get_field('zagolovok_menyu', 'options') ?></div><a href="/" class="footer-link">Главная</a>
									<div class="footer-menu-block"><?php $menu_tree = wtw_get_menu_tree("Главное меню"); if(is_array($menu_tree)){ foreach($menu_tree as $menu_item){ ?>
										<div data-hover="false" data-delay="0" class="dropdown-2 w-dropdown" data-content="menu-level">
											<div class="dropdown-toggle-2 footer w-dropdown-toggle">
												<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="footer-link footer"><?php echo $menu_item->title ?></a></div>
											<?php if(count($menu_item->children)){ ?><nav class="w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?>
												<div data-hover="false" data-delay="0" class="dropdown-3 w-dropdown" data-content="menu-level">
													<div class="dropdown-toggle-2 w-dropdown-toggle">
														<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="nav-button"><?php echo $menu_item->title ?></a></div>
													<?php if(count($menu_item->children)){ ?><nav class="w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?>
														<div data-hover="false" data-delay="0" class="w-dropdown" data-content="menu-level">
															<div class="dropdown-toggle-2 w-dropdown-toggle">
																<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="nav-button"><?php echo $menu_item->title ?></a></div>
															<?php if(count($menu_item->children)){ ?><nav class="w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?>
																<div data-hover="false" data-delay="0" class="w-dropdown" data-content="menu-level">
																	<div class="dropdown-toggle-2 w-dropdown-toggle">
																		<?php if( count($menu_item->children) ){ ?><div class="icon-6 w-icon-dropdown-toggle"></div><?php } ?><a href="<?php echo $menu_item->url ?>" class="nav-button"><?php echo $menu_item->title ?></a></div>
																	<?php if(count($menu_item->children)){ ?><nav class="w-dropdown-list"><?php foreach($menu_item->children as $menu_item){ ?><a href="#" class="w-dropdown-link" data-content="menu-level">Link 1</a><?php } ?></nav><?php } ?>
																</div><?php } ?></nav><?php } ?>
														</div><?php } ?></nav><?php } ?>
												</div><?php } ?></nav><?php } ?>
										</div>
										
										
									<?php }} ?></div>
								</div>
								<?php if( have_rows('poleznoe', 'options') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Полезное"; $loop_field = "poleznoe"; while( have_rows('poleznoe', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-d2510c3f-abe0-7c21-3baa-0e3c6dbef744-6dbef72c" class="footer-block">
									<div class="title-small"><?php echo get_sub_field('zagolovok_poleznoe') ?></div><a href="<?php echo get_sub_field('ssylka_1') ?>" class="footer-link"><?php echo get_sub_field('tekst_ssylki_1') ?></a><a href="<?php echo get_sub_field('ssylka_2') ?>" class="footer-link"><?php echo get_sub_field('tekst_ssylki_2') ?></a><a href="<?php echo get_sub_field('ssylka_3') ?>" class="footer-link"><?php echo get_sub_field('tekst_ssylki_3') ?></a><a href="<?php echo get_sub_field('ssylka_4') ?>" class="footer-link"><?php echo get_sub_field('tekst_ssylki_4') ?></a>
									<div class="footer-link bvi-open footer-link-tnd"><?php echo get_sub_field('rezhim_dlya_slabovidyaschih') ?></div>
								</div><?php } ?><?php } ?>
								<?php if( have_rows('rekvizity', 'options') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Реквизиты"; $loop_field = "rekvizity"; while( have_rows('rekvizity', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-d2510c3f-abe0-7c21-3baa-0e3c6dbef74b-6dbef72c" class="footer-block">
									<div class="title-small"><?php echo get_sub_field('zagolovok_rekvizity') ?></div>
									<div class="footer-link"><?php echo get_sub_field('tekst_1') ?></div>
									<div class="footer-link"><?php echo get_sub_field('tekst_2') ?></div>
									<div class="footer-link"><?php echo get_sub_field('tekst_3') ?></div>
									<div class="footer-link"><?php echo get_sub_field('tekst_4') ?></div>
									<div class="footer-link"><?php echo get_sub_field('tekst_5') ?></div>
								</div><?php } ?><?php } ?>
								<?php if( have_rows('kontakty', 'options') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Контакты"; $loop_field = "kontakty"; while( have_rows('kontakty', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-d2fe8e9e-9867-b243-9a26-f4cf8298f222-6dbef72c" class="footer-block">
									<div class="title-small"><?php echo get_sub_field('zagolovok_kontakty') ?></div><a href="<?php echo get_sub_field('ssylka_na_kartu') ?>" class="footer-link"><?php echo get_sub_field('adres') ?></a><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_sub_field('telefon_2')) ?>" class="footer-link"><?php echo get_sub_field('telefon_2') ?></a><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_sub_field('telefon_1')) ?>" class="footer-link"><?php echo get_sub_field('telefon_1') ?></a><a href="mailto:<?php echo get_sub_field('email') ?>" class="footer-link"><?php echo get_sub_field('email') ?></a>
									<?php if( have_rows('knopka_sinyaya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка синяя"; $loop_field = "knopka_sinyaya"; while( have_rows('knopka_sinyaya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
										<div class="nav-soc"><a href="<?php echo get_sub_field('ssylka_na_socet_1') ?>" target="_blank" class="soc-link w-inline-block"><img width="0" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('cocset_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="wa-icon"></a><a href="<?php echo get_sub_field('ssylka_na_socet_2') ?>" target="_blank" class="soc-link w-inline-block"><img width="10" height="24" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('cocset_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="tg-icon"></a><a href="<?php echo get_sub_field('ssylka_na_socet_3') ?>" target="_blank" class="soc-link w-inline-block"><img width="10" height="24" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('cocset_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="call-to-action-icon"></a></div>
									</div><?php } ?><?php } ?>
								</div><?php } ?><?php } ?>
							</div>
						</div>
					</div>
					<div class="footer-divider"></div>
					<div class="footer_end_block">
						<div class="text-block-38">© 2026 Эммаусс Волга Клаб</div><a href="https://sladki.site/" target="_blank" class="text-block-38">Разработка сайта</a></div>
				</section>
			</div>
			<?php if(empty(get_field('skryt_popap', 'options'))) { ?><div data-w-id="2c516348-a310-29f6-6b1b-90800d70acbd" class="center-bg">
				<div data-w-id="2c516348-a310-29f6-6b1b-90800d70acbe" class="form-bg"></div>
				<div class="light-box-form"><a data-w-id="2c516348-a310-29f6-6b1b-90800d70acc0" href="#" class="close-light-box-form w-inline-block"><div class="circle-form-box"><div class="close-fo-m-line"></div><div class="close-fo-m-line2"></div></div></a>
					<div class="about-us_grid popap-grid">
						<div class="what-to-do_text-block">
							<div class="aboute-us_text-block popap-m-0">
								<div class="wave-icon about-us"><img width="8" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6926b231e3f6bdd943088940_comfort.svg" class="testimonial-image pankaces"></div>
								<h6 class="h2 same-page-popap"><?php echo get_field('zagolovok_popap', 'options') ?></h6>
								<div class="div-block-431">
									<p class="popap-text"><?php echo get_field('tekst_popap', 'options') ?></p>
								</div>
								<?php if( have_rows('knopki_na_popap', 'options') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопки на ПОПАП"; $loop_field = "knopki_na_popap"; while( have_rows('knopki_na_popap', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons popap">
									<?php if(empty(get_sub_field('skryt_knopku_1'))) { ?><div><a data-w-id="2c516348-a310-29f6-6b1b-90800d70acf0" href="<?php echo get_sub_field('ssylka_knopki_popap_1') ?>" class="winter-button home popap-btn-m w-button"><?php echo get_sub_field('knopka_popap_1') ?></a></div><?php } ?>
								</div><?php } ?><?php } ?>
							</div>
						</div><img class="image-31" src="<?php $field = get_field('izobrazhenie_popap', 'options'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
				</div>
			</div><?php } ?>
		</div><?php } ?><?php } ?>
		
		
		
		
		
	
<!-- FOOTER CODE --><?php get_template_part("footer_block", ""); ?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/detskie.js?ver=1789049048"></script></body>
</html>
