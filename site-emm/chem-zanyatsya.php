<?php
/*
Template name: Чем заняться
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf3b3" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body>
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('bloki_stranicy_chem_zanyatsya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блоки страницы Чем заняться"; $loop_field = "bloki_stranicy_chem_zanyatsya"; while( have_rows('bloki_stranicy_chem_zanyatsya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
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
			<?php if( have_rows('pervaya_sekciya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Первая секция"; $loop_field = "pervaya_sekciya"; while( have_rows('pervaya_sekciya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
				<section class="section_same-page" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_verhnej_sekcii'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
					<div>
						<div class="container-xl same-page">
							<div class="same-box">
								<h1 class="h2 trip b"><?php echo get_sub_field('zagolovok_n2') ?></h1>
								<p class="preim-text"><?php echo get_sub_field('opisanie_pt') ?></p>
								<?php if( have_rows('knopka_sinyaya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка синяя"; $loop_field = "knopka_sinyaya"; while( have_rows('knopka_sinyaya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-center"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?><?php } ?>
							</div>
							<div class="reservation_block">
								<div class="fiil_block"><?php echo get_field('kod_paneli_bronirovaniya', 'options') ?></div>
								<?php if(empty(get_field('skryt_blok_pod_bronirovaniem', 'options'))) { ?><div class="news-block-leto"><?php if (!wtw_device_is("phone")) : ?><img class="image-52" src="<?php echo get_template_directory_uri() ?>/images/6a1714c8c47ba0e1d11810d2_banner-leto.webp" alt loading="eager"><?php endif; unset($detect); ?><?php if (wtw_device_is("mobile")) : ?><img class="image-52-mob" src="<?php echo get_template_directory_uri() ?>/images/699ed53710251e237d2cf49a_parr.webp" alt loading="eager"><?php endif; unset($detect); ?>
									<div></div>
									<div class="text-block-52"><?php echo get_field('tekst_ng', 'options') ?></div>
									<div></div>
									<div class="button-group top-mob jfn">
										<?php if(empty(get_field('skryt_knopku_2', 'options'))) { ?><div><a href="<?php echo get_field('ssylka_knopki_ng', 'options') ?>" class="main-button-ht ksncjsdc w-button"><?php echo get_field('knopka_ng', 'options') ?></a></div><?php } ?>
									</div>
								</div><?php } ?>
							</div>
						</div>
					</div>
				</section>
			</div><?php } ?><?php } ?>
			<?php if( have_rows('sekciya_my_predlagaem') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция Мы предлагаем"; $loop_field = "sekciya_my_predlagaem"; while( have_rows('sekciya_my_predlagaem') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="feedback-section">
				<div class="container-padding-20">
					<div class="container-xl same-page-cont">
						<div class="catalog-box reservation">
							<h2 id="w-node-_5ec44309-c7d9-7601-c13d-0e62ce7f7ea3-d6fcf3b3" class="h2 same-page-h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
							<p class="preim-text reservation"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
						</div>
						<?php if( have_rows('blok') ){ ?><div class="block-twith_offer_page"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок"; $loop_field = "blok"; while( have_rows('blok') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
							<div class="offer_block"><img class="image-30" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="70" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" id="w-node-_6e13ac81-744e-6ffe-21f9-904d1f78b820-d6fcf3b3" loading="lazy">
								<div class="offer_text_block">
									<div class="text-block-39"><?php echo get_sub_field('nomer') ?></div>
									<h5 class="text-block-40"><?php echo get_sub_field('tekst') ?></h5>
								</div>
							</div>
							
							
							
							
							
						<?php } ?></div><?php } ?>
					</div>
				</div>
			</div><?php } ?><?php } ?>
			<?php if( have_rows('sekciya_dosuga') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция Досуга"; $loop_field = "sekciya_dosuga"; while( have_rows('sekciya_dosuga') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_layout408">
				<div class="padding-global">
					<div class="container-large">
						<div class="padding-section-large">
							<div class="margin-bottom margin-xxlarge">
								<div class="about-600">
									<p class="preim-text"><?php echo get_sub_field('opisanie_mini') ?></p>
									<h2 class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
								</div>
							</div>
							<?php if( have_rows('bloki_dosuga') ){ ?><div data-w-id="f9486c11-8ce3-bb1a-b7a1-efbfe239d953" class="w-layout-grid layout408_component"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блоки досуга"; $loop_field = "bloki_dosuga"; while( have_rows('bloki_dosuga') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
								<div class="layout408_card <?php echo get_sub_field('dobavit_klass') ?>">
									<div class="layout408_card-content">
										<div class="layout408_card-content-top">
											<div class="margin-bottom margin-small">
												<h3><?php echo get_sub_field('zagolovok_n3') ?></h3>
											</div>
											<p><?php echo get_sub_field('opisanie') ?></p>
											<?php if( have_rows('spisok') ){ ?><ul role="list"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Список"; $loop_field = "spisok"; while( have_rows('spisok') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
												<li><?php echo get_sub_field('tekst') ?></li><?php } ?></ul><?php } ?>
										</div>
										<div class="margin-top margin-medium">
											<div class="button-group">
												<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
												<?php if(empty(get_sub_field('skryt_knopku_2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" class="main-button-ht w-button"><?php echo get_sub_field('knopka_2') ?></a></div><?php } ?>
											</div>
										</div>
									</div>
									<div class="layout408_image-wrapper"><img class="layout408_image" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
								</div>
								
								
								
							<?php } ?></div><?php } ?>
						</div>
						<div class="padding-section-large">
							<div class="margin-bottom margin-xxlarge">
								<div class="about-600">
									<h2 class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
								</div>
							</div>
							<?php if( have_rows('bloki_aktivnyj_otdyh') ){ ?><div data-w-id="9e27c9a0-123e-3119-d962-967e62ef8005" class="w-layout-grid layout408_component"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блоки Активный отдых"; $loop_field = "bloki_aktivnyj_otdyh"; while( have_rows('bloki_aktivnyj_otdyh') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
								<div class="layout408_card <?php echo get_sub_field('dobavit_klass') ?>">
									<div class="layout408_card-content">
										<div class="layout408_card-content-top">
											<div class="margin-bottom margin-small">
												<h3><?php echo get_sub_field('zagolovok_n3') ?></h3>
											</div>
											<p><?php echo get_sub_field('opisanie') ?></p>
											<?php if( have_rows('spisok') ){ ?><ul role="list"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Список"; $loop_field = "spisok"; while( have_rows('spisok') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
												<li><?php echo get_sub_field('tekst') ?></li><?php } ?></ul><?php } ?>
										</div>
										<div class="margin-top margin-medium">
											<div class="button-group">
												<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
												<?php if(empty(get_sub_field('skryt_knopku_2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" class="main-button-ht w-button"><?php echo get_sub_field('knopka_2') ?></a></div><?php } ?>
											</div>
										</div>
									</div>
									<div class="layout408_image-wrapper"><img width="151.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="layout408_image"></div>
								</div>
								
								
							<?php } ?></div><?php } ?>
						</div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('sekcii_aktivnyj_otdyh') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секции Активный отдых"; $loop_field = "sekcii_aktivnyj_otdyh"; while( have_rows('sekcii_aktivnyj_otdyh') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
				<div class="container-padding-20">
					<div class="container-xl green-cont _23">
						<div class="div-preim-copy">
							<div class="catalog-box_black">
								<div class="text-block-35"><?php echo get_sub_field('tekst_mini') ?></div>
								<h2 id="w-node-_251413a3-917a-92cb-0d9b-954add8c0b25-d6fcf3b3" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<div id="w-node-_251413a3-917a-92cb-0d9b-954add8c0b27-d6fcf3b3" class="headin-button">
									<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
								</div>
							</div>
						</div>
						<?php if( have_rows('blok_otdyh_i_razvlecheniya') ){ ?><div><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок Отдых и развлечения"; $loop_field = "blok_otdyh_i_razvlecheniya"; while( have_rows('blok_otdyh_i_razvlecheniya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
							<div class="what-to-do-page_grid"><img class="image-31" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<div class="what-to-do_text-block">
									<div class="text_block_info">
										<h3 class="h3-1 down-0"><?php echo get_sub_field('zagolovok_n3') ?></h3>
										<p class="preim-text red-18 b"><?php echo get_sub_field('opisanie_mini') ?></p>
										<?php if( have_rows('spisok') ){ ?><ul role="list" class="list"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Список"; $loop_field = "spisok"; while( have_rows('spisok') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
											<li class="list-item"><?php echo get_sub_field('punkt') ?></li>
											
											
											
											
											
											
										<?php } ?></ul><?php } ?>
										<div class="margin-top margin-medium">
											<div class="button-group">
												<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
												<?php if(empty(get_sub_field('skryt_knopku_2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" class="main-button-ht w-button"><?php echo get_sub_field('knopka_2') ?></a></div><?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							
							
							
							
						<?php } ?></div><?php } ?>
					</div>
				</div>
			</section><?php } ?><?php } ?>
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
			<?php if( have_rows('blok_statej') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок статей"; $loop_field = "blok_statej"; while( have_rows('blok_statej') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="meropri-section" class="section_food">
				<div class="container-padding-20">
					<div class="container-xl green-cont">
						<div class="rich-text-block w-richtext"><?php echo get_sub_field('dopolnitelnyj_tekst') ?></div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if(empty(get_sub_field('skryt_blok_seo'))) { ?><div>
				<?php if( have_rows('blok_s_statyami') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок с статьями"; $loop_field = "blok_s_statyami"; while( have_rows('blok_s_statyami') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="meropri-section" class="section_food">
					<div class="container-padding-20">
						<div class="container-xl green-cont">
							<div class="div-preim-copy">
								<div class="catalog-box_black">
									<div class="text-block-35"><?php echo get_sub_field('mini_tekst') ?></div>
									<h2 id="w-node-_15fa0982-f232-c70f-7e4d-8a564a63f648-d6fcf3b3" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<div id="w-node-_15fa0982-f232-c70f-7e4d-8a564a63f64a-d6fcf3b3" class="headin-button">
										<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
									</div>
								</div>
							</div>
							<div class="gallery23_component">
								<div class="gallery23_slider swiper-first-reserv">
									<?php $query = new WP_Query('cat=27'); if($query->have_posts()) : ?>
<div class="gallery23_mask main-page swiper-wrapper">
<?php $rotation = 0; $group = 0; $post_index = 0; while($query->have_posts()) : $query->the_post();
        $rotation === 0 ? $rotation = 1 : $rotation++;
        $group === 0 ? $group = 1 : $group++; $post_index++; ?>
<?php if ( class_exists( "WooCommerce" )) $product = wc_get_product(get_the_ID()); ?><a href="<?php the_permalink(); ?>" class="gallery23_slide swiper-slide w-inline-block" data-content="query_item"><img class="gallery23_image" src="<?php $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); echo $img[0]; ?>" width="500" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) ?>" loading="lazy" title="<?php echo get_the_title(get_post_thumbnail_id()) ?>"><h4 class="heading-7"><?php the_title(); ?></h4></a>
<?php endwhile; ?></div>
<?php else : ?><?php endif; unset($query_args); wp_reset_postdata(); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="margin-footer-100"></div>
				</section><?php } ?><?php } ?>
			</div><?php } ?>
			<div class="margin-footer-100"></div>
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
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/chem-zanyatsya.js?ver=1789049048"></script></body>
</html>
