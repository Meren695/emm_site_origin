<?php
/*
Template name: Контакты
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf41b" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body>
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('bloki_stranicy_kontakty') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блоки страницы Контакты"; $loop_field = "bloki_stranicy_kontakty"; while( have_rows('bloki_stranicy_kontakty') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
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
			<?php if( have_rows('kontakty') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Контакты"; $loop_field = "kontakty"; while( have_rows('kontakty') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
				<div class="container-xl contact-cont">
					<div class="contact_grid">
						<div class="what-to-do_text-block">
							<div class="aboute-us_text-block-contacts">
								<?php if( have_rows('kontaktnaya_inf.') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Контактная инф."; $loop_field = "kontaktnaya_inf."; while( have_rows('kontaktnaya_inf.') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="w-layout-grid grid-2">
									<h1 id="w-node-_0541955f-b702-f791-c628-dee0c9276fc0-d6fcf41b" class="h1 contact"><?php echo get_sub_field('zagolovok_n1') ?></h1>
									<p id="w-node-a3bddfbe-86f5-556b-89ba-44548473b101-d6fcf41b" class="preim-text-cotact"><?php echo get_sub_field('adres_otelya') ?></p><a href="<?php echo get_sub_field('ssylka_na_yandeks_karty') ?>" target="_blank" class="link-phone-contacts"><?php echo get_sub_field('adres') ?></a>
									<p class="preim-text-cotact"><?php echo get_sub_field('gps_koordinaty') ?></p><a href="<?php echo get_sub_field('ssylka_na_yandeks_karty_gps') ?>" class="link-phone-contacts"><?php echo get_sub_field('koordinaty') ?></a>
									<p class="preim-text-cotact"><?php echo get_sub_field('telefon') ?></p>
									<div class="phone_block"><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_field('telefon_1', 'options')) ?>" class="link-phone-contacts"><?php echo get_field('telefon_1', 'options') ?></a><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_field('telefon_2', 'options')) ?>" class="link-phone-contacts"><?php echo get_field('telefon_2', 'options') ?></a></div>
									<p class="preim-text-cotact"><?php echo get_sub_field('pochta') ?></p><a href="mailto:<?php echo get_sub_field('email') ?>" class="link-mail-contacts"><?php echo get_sub_field('email') ?></a></div><?php } ?><?php } ?>
								<?php if( have_rows('rekvizity_inf.') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Реквизиты инф."; $loop_field = "rekvizity_inf."; while( have_rows('rekvizity_inf.') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="w-layout-grid grid-2">
									<h3 class="heading-3"><?php echo get_sub_field('zagolovok_n3') ?></h3>
									<div></div>
									<p id="w-node-da36bf39-133a-330c-662d-4f4c4a697cb1-d6fcf41b" class="preim-text-cotact"><?php echo get_sub_field('yur._infa') ?></p>
									<div class="link-phone-contacts terser"><?php echo get_sub_field('tekst_1') ?></div>
									<p class="preim-text-cotact"><?php echo get_sub_field('inn') ?></p>
									<div class="link-phone-contacts terser"><?php echo get_sub_field('tekst_2') ?></div>
									<p class="preim-text-cotact"><?php echo get_sub_field('ogrn') ?></p>
									<div class="link-phone-contacts terser"><?php echo get_sub_field('tekst_3') ?></div>
									<p class="preim-text-cotact"><?php echo get_sub_field('yur._adres') ?></p>
									<div class="link-phone-contacts terser"><?php echo get_sub_field('tekst_4') ?></div>
								</div><?php } ?><?php } ?>
							</div>
						</div><img class="image-31" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('blok_s_kartoj') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок с картой"; $loop_field = "blok_s_kartoj"; while( have_rows('blok_s_kartoj') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
				<div class="container-xl contact-cont">
					<div id="ya-map" class="map_block w-node-_9f693d4f-4d7e-a512-5ae1-535a6d782c2c-d6fcf41b">
						<div class="map_text_block">
							<div class="wave-icon about-us"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac767_emmaus-logo.svg" alt class="testimonial-image"></div>
							<div><?php echo get_sub_field('tekst') ?></div>
						</div>
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
			<div>
				<address class="call-to-action">
					<div class="trigger">
						<div data-is-ix2-target="1" class="trigger-lottie" data-w-id="6c2dad21-436f-9358-e1e9-4c7534bdb1f3" data-animation-type="lottie" data-src="https://cdn.prod.website-files.com/5f9b359e6ddc81e427ceab3a/5fa17bb235bfbbc33afaad4e_contact-cta.json" data-loop="0" data-direction="1" data-autoplay="0" data-renderer="svg" data-default-duration="2.0020019204587935" data-duration="2" data-loading="eager" data-ix2-initial-state="0"></div>
					</div><a href="<?php echo get_field('ssylka_na_votcap_futer', 'options') ?>" target="_blank" class="whatsapp w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/69a83d8fafb695c599a13167_D0BCD0B0D0BAD18120D0B1D0B5D0BB.svg" alt class="call-to-action-icon"></a><a href="<?php echo get_field('ssylka_na_telegram_futer', 'options') ?>" class="email w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/686931998f03c728d6fcf3ac_Telegram-footer.svg" alt class="call-to-action-icon"></a><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_field('telefon_1', 'options')) ?>" class="mobile w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/62ffe65faad4eedcbcf0ebdb_call.svg" alt class="call-to-action-icon"></a></address>
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
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/contacts.js?ver=1789049048"></script></body>
</html>
