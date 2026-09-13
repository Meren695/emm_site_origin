<?php
/*
Template name: Страница не найдена
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf427" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body meta="Страница 404">
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

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
		<section id="third-section" class="testimonial-stack">
			<div class="container-2">
				<div class="testimonial-card-three"><img width="360" alt src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac7a0_170b00dac56ba29d2f58d9e6bf8f11539d32842e.webp" loading="lazy" class="image-2-3 hide-tablet-mob"><img width="360" alt src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac7ae_d6939c560c97518c186f388331a0da4cfa007487.webp" loading="lazy" class="image-2-4 hide-tablet-mob"><img width="540" alt src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac7a9_77a875632dd8ab7bba84be8e055e602061233677.webp" loading="lazy" class="image-2-2 hide-tablet-mob"><img width="540" alt src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac7a4_3e597dbc7496e547d5e3936cbfd491d4d48eba4c.webp" loading="lazy" class="image-2 hide-tablet-mob">
					<div class="testimonial-info-four">
						<div class="wave-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac767_emmaus-logo.svg" alt class="testimonial-image"></div>
						<div class="about-600">
							<h4 class="preim-text">Упс!</h4>
							<h2 class="h2">Эммаусс Волга Клаб</h2>
							<p class="preim-text">К сожалению такой страницы нет</p>
						</div>
					</div>
					<div class="testimonial-card-content"><a href="/" class="main-button-ht green w-button">На главную</a></div>
				</div>
			</div>
			<div class="perehod_vodi"></div>
		</section>
		<div>
			<?php if( have_rows('sekciya_bronirovaniya_nomerov', 'options') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция Бронирования номеров"; $loop_field = "sekciya_bronirovaniya_nomerov"; while( have_rows('sekciya_bronirovaniya_nomerov', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="bronirovanie-section" class="feedback-section">
				<div class="container-xl testimonias">
					<div class="catalog-box reservation">
						<h2 id="w-node-_011d8967-9774-32ea-4980-075b39131981-3913197d" class="h2 marg-30 b"><?php echo get_sub_field('zagolovok_n2') ?></h2>
						<p class="preim-text reservation"><?php echo get_sub_field('opisanie_poz_zagolovkom') ?></p>
					</div>
					<div>
						<div class="block-slidera">
							<div class="sam-slider swiper-first-reserv">
								<?php if( have_rows('slajder_bronirovaniya_nomerov', 'options') ){ ?><div class="wrapper-slider swiper-wrapper"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Слайдер Бронирования номеров"; $loop_field = "slajder_bronirovaniya_nomerov"; while( have_rows('slajder_bronirovaniya_nomerov', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
									<div class="slide-home swiper-slide">
										<div class="img-bg_slider" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_slajda'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
											<div class="inside-slide">
												<?php if( have_rows('harakteristiki', 'options') ){ ?><div><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Характеристики"; $loop_field = "harakteristiki"; while( have_rows('harakteristiki', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
													<div class="haracterisctic"><?php echo get_sub_field('harakteristika_1') ?></div>
												<?php } ?></div><?php } ?>
												<div class="text_block w">
													<h4 class="text-block-50"><?php echo get_sub_field('zagolovok_nomera') ?></h4>
													<div class="text-block-300-14-3"><?php echo get_sub_field('tekst') ?></div>
													<?php if( have_rows('knopka', 'options') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="btn-flex-reservation"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?><?php } ?>
												</div>
											</div>
										</div>
									</div>
									
									
									
									
									
									
									
									
								<?php } ?></div><?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div><?php } ?><?php } ?>
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
		
		
		
		
		
	
<!-- FOOTER CODE --><?php get_template_part("footer_block", ""); ?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/404.js?ver=1789049048"></script></body>
</html>
