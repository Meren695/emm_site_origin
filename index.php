<?php
/*
Template name: Отель Эммаусс Волга Клаб
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf387" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body class="body">
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('bloki_glavnoj_stranicy') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блоки Главной страницы"; $loop_field = "bloki_glavnoj_stranicy"; while( have_rows('bloki_glavnoj_stranicy') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
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
					<nav class="container-xxl-nav"><a href="/" aria-current="page" class="logo w-nav-brand w--current"><div class="logo-block"><img loading="eager" src="<?php echo get_template_directory_uri() ?>/images/694938646b904ed971a56b41_emmauss-logo.svg" alt class="image-logo"></div></a>
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
			<?php if( have_rows('pervaya_sekciya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Первая Секция"; $loop_field = "pervaya_sekciya"; while( have_rows('pervaya_sekciya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
				<section id="first-section" class="section-hero">
					<?php if (!wtw_device_is("phone")) : ?><div><img class="image-51" src="<?php $field = get_sub_field('izobrazhenie_dlya_pk'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="eager"></div><?php endif; unset($detect); ?>
					<?php if (wtw_device_is("mobile")) : ?><div><img class="image-51-mob" src="<?php $field = get_sub_field('izobrazhenie_dlya_mob'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="eager"></div><?php endif; unset($detect); ?>
					<div class="fil-bg-hero">
						<div class="container-xl img-main">
							<div class="div-preim-copy">
								<div class="main-text-block">
									<div class="main-text"><?php echo get_sub_field('mini_tekst_vyshe_zagolovka') ?></div>
									<h1 class="h1"><?php echo get_sub_field('zagolovok_h1') ?></h1>
									<div class="text-block-34"><?php echo get_sub_field('mini_tekst_nizhe_zagolovka') ?></div>
									<?php if( have_rows('knopka_na_pervom_ekrane') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка на первом экране"; $loop_field = "knopka_na_pervom_ekrane"; while( have_rows('knopka_na_pervom_ekrane') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons">
										<div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht w-button"><?php echo get_sub_field('knopka') ?></a></div>
									</div><?php } ?><?php } ?>
								</div>
							</div>
							<div class="div-preim-copy">
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
					</div>
				</section>
			</div><?php } ?><?php } ?>
			<div class="fill-white">
				<?php if( have_rows('sekciya_predlozhenie_gostyam') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция предложение гостям"; $loop_field = "sekciya_predlozhenie_gostyam"; while( have_rows('sekciya_predlozhenie_gostyam') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="second-section" class="section_green-akcii">
					<div class="container-padding-20">
						<div class="container-xl green-cont">
							<div class="div-preim-copy">
								<div class="catalog-box green">
									<div id="w-node-d4818119-45b4-0348-e995-201f1357169a-d6fcf387" class="div-block-468-copy">
										<div class="text-block-35"><?php echo get_sub_field('mini_tekst_pered_zagolovkom') ?></div>
									</div>
									<h2 id="w-node-d4818119-45b4-0348-e995-201f1357169d-d6fcf387" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<div class="div-block-468">
										<div class="headin-button">
											<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
										</div>
									</div>
								</div>
							</div>
							<div class="what-to-do_grid"><a id="w-node-d4818119-45b4-0348-e995-201f135716a4-d6fcf387" href="<?php echo get_sub_field('ssylka_1') ?>" class="link-svadb w-inline-block"><div class="bg_img" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_1') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_1') ?></p></div></div></a><a href="<?php echo get_sub_field('ssylka_2') ?>" class="link-svadb w-inline-block"><div class="bg_img small" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item small"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_2') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_2') ?></p></div></div></a><a href="<?php echo get_sub_field('ssylka_3') ?>" class="link-svadb w-inline-block"><div class="bg_img bg-img-lodki" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item small"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_3') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_3') ?></p></div></div></a><a id="w-node-d4818119-45b4-0348-e995-201f135716b9-d6fcf387" href="<?php echo get_sub_field('ssylka_4') ?>" class="link-svadb w-inline-block"><div class="bg_img small" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_4'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item small"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_4') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_4') ?></p></div></div></a><a id="w-node-d4818119-45b4-0348-e995-201f135716c0-d6fcf387" href="<?php echo get_sub_field('ssylka_5') ?>" class="link-svadb w-inline-block"><div class="bg_img" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_5'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_5') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_5') ?></p></div></div></a><a id="w-node-d4818119-45b4-0348-e995-201f135716c7-d6fcf387" href="<?php echo get_sub_field('ssylka_6') ?>" class="link-svadb w-inline-block"><div class="bg_img small" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_6'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item small"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_6') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_6') ?></p></div></div></a><a id="w-node-d4818119-45b4-0348-e995-201f135716ce-d6fcf387" href="<?php echo get_sub_field('ssylka_7') ?>" class="link-svadb w-inline-block"><div class="bg_img" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_7'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_7') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_7') ?></p></div></div></a><a id="w-node-_40440970-23fe-205c-f1dd-a8f6949a94ca-d6fcf387" href="<?php echo get_sub_field('ssylka_8') ?>" class="link-svadb w-inline-block"><div class="bg_img small" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_8'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"><div class="what-to-do_item small"><h3 class="h3"><?php echo get_sub_field('zagolovok_n3_8') ?></h3><p class="preim-text-white"><?php echo get_sub_field('opisanie_8') ?></p></div></div></a></div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
			</div>
			<?php if( have_rows('sekciya_o_nas') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция О нас"; $loop_field = "sekciya_o_nas"; while( have_rows('sekciya_o_nas') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="third-section" class="testimonial-stack">
				<div class="container-2">
					<div class="testimonial-card-three"><img class="image-2-3 hide-tablet-mob" src="<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="360" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"><img class="image-2-4 hide-tablet-mob" src="<?php $field = get_sub_field('izobrazhenie_4'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="360" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"><img class="image-2-2 hide-tablet-mob" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="540" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"><img class="image-2 hide-tablet-mob" src="<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="540" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
						<div class="testimonial-info-four">
							<div class="wave-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac767_emmaus-logo.svg" alt class="testimonial-image"></div>
							<div class="about-600">
								<h4 class="preim-text"><?php echo get_sub_field('opisanie_mini') ?></h4>
								<h2 class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
							</div>
						</div>
						<div class="testimonial-card-content"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div>
					</div>
				</div>
				<div class="perehod_vodi"></div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('sekciya_s_kartochkami') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция с карточками"; $loop_field = "sekciya_s_kartochkami"; while( have_rows('sekciya_s_kartochkami') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="fourth-section" class="section-2">
				<div class="container-xl abuot">
					<div class="about-cards_grid">
						<?php if( have_rows('kartochka_1') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 1"; $loop_field = "kartochka_1"; while( have_rows('kartochka_1') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-b0392195-9303-3bc8-160b-90c5732c9bb6-d6fcf387">
							<div class="abuot-item beach" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
								<h3 class="h3 about"><?php echo get_sub_field('zagolovok_n3') ?></h3>
								<p class="preim-text b"><?php echo get_sub_field('opisanie_pt') ?></p>
							</div>
						</div><?php } ?><?php } ?>
						<?php if( have_rows('kartochka_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 2"; $loop_field = "kartochka_2"; while( have_rows('kartochka_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_691dc082-c779-e032-a0d5-45aead9298cf-d6fcf387">
							<div class="abuot-item sauna" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
								<h3 class="h3 about"><?php echo get_sub_field('zagolovok_n3') ?></h3>
								<p class="preim-text b"><?php echo get_sub_field('opisanie_pt') ?></p>
							</div>
						</div><?php } ?><?php } ?>
						<?php if( have_rows('kartochka_3') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 3"; $loop_field = "kartochka_3"; while( have_rows('kartochka_3') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_8de18eab-6c06-1f42-3116-362f6db8a5bb-d6fcf387">
							<div class="abuot-item hunting" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
								<h3 class="h3 about w"><?php echo get_sub_field('zagolovok_n3') ?></h3>
								<p class="preim-text b"><?php echo get_sub_field('opisanie_pt') ?></p>
							</div>
						</div><?php } ?><?php } ?>
						<?php if( have_rows('kartochka_4') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 4"; $loop_field = "kartochka_4"; while( have_rows('kartochka_4') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-b903020f-c73c-a076-5087-f3315722372d-d6fcf387">
							<div class="abuot-item volga" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_4'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
								<h3 class="h3 about"><?php echo get_sub_field('zagolovok_n3') ?></h3>
								<p class="preim-text b"><?php echo get_sub_field('opisanie_pt') ?></p>
							</div>
						</div><?php } ?><?php } ?>
						<?php if( have_rows('kartochka_5') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 5"; $loop_field = "kartochka_5"; while( have_rows('kartochka_5') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_3130e567-27ed-64e8-58ca-12c85541f2f5-d6fcf387">
							<div class="abuot-item history" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_5'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
								<h3 class="h3 about w"><?php echo get_sub_field('zagolovok_n3') ?></h3>
								<p class="preim-text b"><?php echo get_sub_field('opisanie_pt') ?></p>
							</div>
						</div><?php } ?><?php } ?>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('sekciya_s_bronyu') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция с бронью"; $loop_field = "sekciya_s_bronyu"; while( have_rows('sekciya_s_bronyu') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner-section" class="banner-section">
				<div class="container-xl-2 img-prom-banner">
					<div class="banner_block home">
						<div class="prom-text-block">
							<h2 class="h2-2 banner"><?php echo get_sub_field('zagolovok_n2') ?></h2>
							<div class="hide_fo_empty"></div>
							<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-2"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?><?php } ?>
						</div><img id="w-node-_74284cdb-da2e-7996-0d3b-1128da004e90-d6fcf387" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-3-1"></div>
					<?php if(empty(get_sub_field('skryt_skidku'))) { ?><div>
						<div class="sale-10"><?php echo get_sub_field('skidka') ?></div>
					</div><?php } ?>
				</div>
			</div><?php } ?><?php } ?>
			<div>
				<div id="bronirovanie-section" class="feedback-section">
					<?php if( have_rows('sekciya_bronirovaniya_nomerov', 'options') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция Бронирования номеров"; $loop_field = "sekciya_bronirovaniya_nomerov"; while( have_rows('sekciya_bronirovaniya_nomerov', 'options') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="container-xl testimonias">
						<div class="catalog-box reservation">
							<h2 id="w-node-b900733f-2f27-c1c5-840c-f658d0534548-d6fcf387" class="h2 marg-30 b"><?php echo get_sub_field('zagolovok_n2') ?></h2>
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
					</div><?php } ?><?php } ?>
					<?php if( have_rows('knopka_pod_blokom_bronirovaniya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка под блоком бронирования"; $loop_field = "knopka_pod_blokom_bronirovaniya"; while( have_rows('knopka_pod_blokom_bronirovaniya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="testimonial-card-content-cost bbbb"><a href="<?php echo get_sub_field('ssylka_knopki__br') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka__br') ?></a></div><?php } ?><?php } ?>
				</div>
			</div>
			<?php if( have_rows('bannyj_kompleks') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Банный комплекс"; $loop_field = "bannyj_kompleks"; while( have_rows('bannyj_kompleks') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="red-section">
				<div class="container-xl">
					<div class="testimonial-info-four black">
						<div class="wave-icon"><img width="7.5" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/686aab1d2fa7d0770b2c30d9_emmaus-white-home.svg" class="testimonial-image-bana"></div>
						<div class="width-text">
							<h2 class="h2 trip"><?php echo get_sub_field('zagolovok_n2') ?></h2>
							<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
						</div>
					</div>
					<div class="bana-page_grid">
						<div class="bana_text-block">
							<div class="text_block_bana">
								<div class="block_gap-065">
									<h3 class="h3-1 down-0"><?php echo get_sub_field('zagolovok_n3') ?></h3>
									<p class="preim-text"><?php echo get_sub_field('opisanie_mini') ?></p>
								</div>
								<?php if( have_rows('harakteristiki') ){ ?><div class="characteristics_block-bana"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Характеристики"; $loop_field = "harakteristiki"; while( have_rows('harakteristiki') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
									<div class="haracteristic3"><?php echo get_sub_field('harakteristika') ?></div>
									
									
									
									
									
								<?php } ?></div><?php } ?>
								<div class="margin-top margin-medium">
									<div class="button-group">
										<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
										<?php if(empty(get_sub_field('skryt_knopku_2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" class="main-button-ht w-button"><?php echo get_sub_field('knopka_2') ?></a></div><?php } ?>
									</div>
								</div>
							</div>
						</div>
						<div class="bana_slider swiper-first-reserv">
							<?php if( have_rows('dobavit_slajdy_bani') ){ ?><div class="slider_wrapper swiper-wrapper"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Добавить слайды бани"; $loop_field = "dobavit_slajdy_bani"; while( have_rows('dobavit_slajdy_bani') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
								<div class="slide_bana swiper-slide"><img class="image-31" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
								
								
							<?php } ?></div><?php } ?>
						</div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('verhovaya_ezda') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Верховая езда"; $loop_field = "verhovaya_ezda"; while( have_rows('verhovaya_ezda') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section">
				<div class="container-xl-2 img-prom-banner2">
					<div class="banner_block">
						<div class="prom-text-block">
							<div class="width_icon_label">
								<div class="wave-icon_banner3"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac75f_emmaus-green-bron.svg" alt class="testimonial-image"></div>
								<div class="text-block-36"><?php echo get_sub_field('mini_tekst') ?></div>
							</div>
							<div class="block_gap-065">
								<h2 class="h2-2 banner2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<p class="preim-text banner3 k"><?php echo get_sub_field('opisanie') ?></p>
							</div>
							<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-2"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?><?php } ?>
						</div><img width="210" id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0557-d6fcf387" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-3"></div>
				</div>
			</div><?php } ?><?php } ?>
			<?php if( have_rows('pitanie') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Питание"; $loop_field = "pitanie"; while( have_rows('pitanie') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="food-section" class="section_food">
				<div class="container-padding-20">
					<div class="container-xl green-cont">
						<div class="div-preim-copy">
							<div class="catalog-box_black">
								<div class="text-block-35"><?php echo get_sub_field('mini_tekst') ?></div>
								<h2 id="w-node-_8f30da29-d58d-d98f-7589-bebda7be055e-d6fcf387" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0560-d6fcf387" class="headin-button">
									<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
								</div>
							</div>
						</div>
						<div class="food_grid">
							<?php if( have_rows('kartochka_1') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 1"; $loop_field = "kartochka_1"; while( have_rows('kartochka_1') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0564-d6fcf387" class="food_item">
								<div>
									<h4 class="h4 food"><?php echo get_sub_field('zagolovok_n4') ?></h4>
									<p class="preim-text_black"><?php echo get_sub_field('opisanie') ?></p>
								</div>
							</div><?php } ?><?php } ?>
							<div id="w-node-f3c1eb72-b182-34cf-39b6-3f019aa7959b-d6fcf387" class="hide_fo_empty"></div>
							<?php if( have_rows('kartochka_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 2"; $loop_field = "kartochka_2"; while( have_rows('kartochka_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0569-d6fcf387" class="food_item _2">
								<div>
									<h4 class="h3 food w"><?php echo get_sub_field('zagolovok_n4') ?></h4>
									<p class="preim-text_black"><?php echo get_sub_field('opisanie') ?></p>
								</div>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('kartochka_3') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 3"; $loop_field = "kartochka_3"; while( have_rows('kartochka_3') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be056e-d6fcf387" class="food_item _33">
								<div>
									<h4 class="h3 food"><?php echo get_sub_field('zagolovok_n4') ?></h4>
									<p class="preim-text_black"><?php echo get_sub_field('opisanie') ?></p>
								</div>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('kartochka_4') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 4"; $loop_field = "kartochka_4"; while( have_rows('kartochka_4') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0573-d6fcf387" class="food_item _44">
								<div>
									<h4 class="h3 food"><?php echo get_sub_field('zagolovok_n4') ?></h4>
									<p class="preim-text_black"><?php echo get_sub_field('opisanie') ?></p>
								</div>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('kartochka_5') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Карточка 5"; $loop_field = "kartochka_5"; while( have_rows('kartochka_5') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0578-d6fcf387" class="food_item _55">
								<div>
									<h4 class="h3 food w"><?php echo get_sub_field('zagolovok_n4') ?></h4>
									<p class="preim-text_black"><?php echo get_sub_field('opisanie') ?></p>
								</div>
							</div><?php } ?><?php } ?>
						</div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('blok_s_statyami') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок с статьями"; $loop_field = "blok_s_statyami"; while( have_rows('blok_s_statyami') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="meropri-section" class="section_food">
				<?php if(empty(get_sub_field('skryt_blok_so_slajderom'))) { ?><div>
					<div class="container-padding-20">
						<div class="container-xl green-cont">
							<div class="div-preim-copy">
								<div class="catalog-box_black">
									<div class="text-block-35"><?php echo get_sub_field('mini_tekst') ?></div>
									<h2 id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0583-d6fcf387" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0585-d6fcf387" class="headin-button">
										<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
									</div>
								</div>
							</div>
							<div class="gallery23_component">
								<div class="gallery23_slider swiper-first-reserv">
									<?php $query = new WP_Query('cat=26'); if($query->have_posts()) : ?>
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
				</div><?php } ?>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('ekskursii') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Экскурсии"; $loop_field = "ekskursii"; while( have_rows('ekskursii') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="red-section" class="section_red">
				<div class="container-padding-20">
					<div class="container-xl red-cont">
						<div class="testimonial-info-four">
							<div class="wave-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac767_emmaus-logo.svg" alt class="testimonial-image"></div>
							<div class="width-text">
								<h2 class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<p class="preim-text red-18"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
							</div>
						</div>
						<div class="trip_grid">
							<div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be05bb-d6fcf387" class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
							</div>
							<div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be05c1-d6fcf387" class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_2') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_2') ?></p>
							</div>
							<div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be05c7-d6fcf387" class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_3') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_3') ?></p>
							</div>
						</div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('informaciya_dlya_gostej') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Информация для гостей"; $loop_field = "informaciya_dlya_gostej"; while( have_rows('informaciya_dlya_gostej') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
				<section id="info-section" class="section_info" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_dlya_gostej'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
					<div class="container-xl info-block">
						<div id="w-node-_8f30da29-d58d-d98f-7589-bebda7be05cf-d6fcf387" class="info-text-block">
							<div class="text_block_info">
								<h2 class="h2 down-0"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<p class="preim-text red-18 b"><?php echo get_sub_field('opisanie') ?></p>
								<?php if( have_rows('spisok') ){ ?><ul role="list" class="list"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Список"; $loop_field = "spisok"; while( have_rows('spisok') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
									<li class="list-item"><?php echo get_sub_field('tekst') ?></li>
									
									
									
									
									
									
									
									
								<?php } ?></ul><?php } ?>
							</div>
						</div>
						<div class="empty-div-block" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_dlya_gostej'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"></div>
					</div>
				</section>
			</div><?php } ?><?php } ?>
			<?php if( have_rows('shema') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Схема"; $loop_field = "shema"; while( have_rows('shema') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
				<section id="scheme-section" class="section_info cart" style="background-image:url('<?php $field = get_sub_field('shema_proezda'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
					<div class="container-xl info-block card">
						<div class="empty-div-block map-tab-img" style="background-image:url('<?php $field = get_sub_field('shema_proezda'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"></div>
						<?php if( have_rows('shema_proezda_v_tekste') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Схема проезда в тексте"; $loop_field = "shema_proezda_v_tekste"; while( have_rows('shema_proezda_v_tekste') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="info-text-block left">
							<div class="text_block_info">
								<h2 class="h2 down-0"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<?php if( have_rows('shema_proezda') ){ ?><div class="scheme_block"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Схема проезда"; $loop_field = "shema_proezda"; while( have_rows('shema_proezda') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
									<div class="div2-stag014">
										<div class="liner_block">
											<div class="round">
												<div class="green-round"></div>
											</div>
											<div class="sep-stag014"></div>
										</div>
										<div class="text_scheme_block">
											<div class="title-stag014"><?php echo get_sub_field('tekst') ?></div>
											<p class="p2-stag014"><?php echo get_sub_field('opisanie') ?></p>
										</div>
									</div>
									
									
									
								<?php } ?></div><?php } ?>
								<div>
									<div class="div2-stag014">
										<div class="liner_block">
											<div class="round _3232">
												<div class="end_icon_scheme"></div>
											</div>
										</div>
										<div class="text_scheme_block">
											<div class="title-stag014 end"><?php echo get_sub_field('poslednij_punkt') ?></div>
										</div>
									</div>
								</div>
								<?php if( have_rows('blok_transfer') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок трансфер"; $loop_field = "blok_transfer"; while( have_rows('blok_transfer') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="transfer-block"><img width="60" id="w-node-_8f30da29-d58d-d98f-7589-bebda7be0622-d6fcf387" alt src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac7f0_Mask20group.webp" loading="lazy" class="image-5">
									<div class="text_block_transfer">
										<div class="title-stag014"><?php echo get_sub_field('test') ?></div>
										<p class="preim-text_black"><?php echo get_sub_field('opisanie_transfer') ?></p>
									</div>
								</div><?php } ?><?php } ?>
							</div>
						</div><?php } ?><?php } ?>
					</div>
				</section>
			</div><?php } ?><?php } ?>
			<?php if( have_rows('sekciya_otzyvov') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция отзывов"; $loop_field = "sekciya_otzyvov"; while( have_rows('sekciya_otzyvov') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="testumonies-section" class="feedback-section">
				<div class="container-xl testimonias">
					<div class="marg-auto">
						<div class="preim-text testimonies"><?php echo get_sub_field('opisanie') ?></div>
						<h2 class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
						<div class="div-block-448">
							<div class="code-embed-6 w-embed">
								<iframe src="https://yandex.ru/sprav/widget/rating-badge/1182641831?type=rating" width="150" height="50" frameborder="0"></iframe>
							</div>
							<div class="preim-text b centered"><?php echo get_sub_field('ocenka_v_yad') ?></div>
						</div>
					</div>
					<div class="block-slidera otzivi">
						<div class="sam-slider swiper-first-reserv">
							<?php if( have_rows('dobavit_otzyv') ){ ?><div class="wrapper-slider swiper-wrapper"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Добавить отзыв"; $loop_field = "dobavit_otzyv"; while( have_rows('dobavit_otzyv') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
								<div class="slide-block_otziv swiper-slide">
									<div class="otz-fon">
										<div class="w-layout-grid grid-otz">
											<div class="otz-ava"><img src="<?php $field = get_sub_field('avatvr'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" class="image-24"></div>
											<div class="otz-text-fon">
												<div class="stars-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6868d4995f13d52624276183_Star.svg" alt class="star-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6868d4995f13d52624276183_Star.svg" alt class="star-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6868d4995f13d52624276183_Star.svg" alt class="star-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6868d4995f13d52624276183_Star.svg" alt class="star-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6868d4995f13d52624276183_Star.svg" alt class="star-icon"></div>
												<div class="text-block-4"><?php echo get_sub_field('imya_napisavshego,_familiya,_nik') ?></div>
												<div class="text-block-4-copy"><strong><?php echo get_sub_field('data') ?></strong></div>
											</div>
										</div>
										<div style="height:12.5rem" class="text_block">
											<div class="text-block-5"><?php echo get_sub_field('otzyv') ?></div>
										</div>
										<div data-w-id="af000654-c974-17c4-f770-c22bdfc2965f" class="text_btn_open">‍Читать полностью</div>
										<div style="display:none;opacity:0" class="text_btn_close">Скрыть</div><a href="<?php echo get_sub_field('ssylka_na_otzyv') ?>" target="_blank" class="link">Отзыв из Яндекса</a></div>
								</div>
								
								
								
								
								
								
								
							<?php } ?></div><?php } ?>
						</div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if(empty(get_sub_field('skryt_blok'))) { ?><div>
				<?php if( have_rows('sekciya_raboty') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция работы"; $loop_field = "sekciya_raboty"; while( have_rows('sekciya_raboty') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="worck-section">
					<div class="container-xl worck">
						<div class="testimonial-info-four-auto">
							<div class="about-auto">
								<div class="width-text_worck">
									<div class="group-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac7fd_Rectangle205.webp" alt class="group-image"></div>
									<div class="group-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac7ff_Rectangle206.webp" alt class="group-image"></div>
									<div class="group-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac7f9_Rectangle207.webp" alt class="group-image"></div>
									<div class="group-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac803_Rectangle208.webp" alt class="group-image"></div>
									<div class="group-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac801_Rectangle209.webp" alt class="group-image"></div>
								</div>
								<p class="preim-text"><?php echo get_sub_field('opisanie_mini') ?></p>
								<h2 class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
								<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-center"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?><?php } ?>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
			</div><?php } ?>
			<?php if(empty(get_sub_field('skryt_blok_seo'))) { ?><div>
				<?php if( have_rows('sekciya_ceo') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция CEO"; $loop_field = "sekciya_ceo"; while( have_rows('sekciya_ceo') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="worck-section" class="worck-section">
					<div class="container-xl green-cont">
						<div class="rich-text-block w-richtext"><?php echo get_sub_field('dopolnitelnyj_tekst') ?></div>
					</div>
				</section><?php } ?><?php } ?>
				<?php if( have_rows('blok_s_statyami') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок с статьями"; $loop_field = "blok_s_statyami"; while( have_rows('blok_s_statyami') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="meropri-section" class="section_food">
					<div class="container-padding-20">
						<div class="container-xl green-cont">
							<?php if(empty(get_sub_field('skryt_tekst'))) { ?><div>
								<div class="catalog-box_black">
									<div class="text-block-35"><?php echo get_sub_field('mini_tekst') ?></div>
									<h2 id="w-node-_57c92d43-27ff-b61c-b8c8-be8a47e12916-d6fcf387" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<div id="w-node-_57c92d43-27ff-b61c-b8c8-be8a47e12918-d6fcf387" class="headin-button">
										<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
									</div>
								</div>
							</div><?php } ?>
							<div class="gallery23_component">
								<div class="gallery23_slider swiper-first-reserv">
									<?php $query = new WP_Query('cat=23'); if($query->have_posts()) : ?>
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
			<div>
				<section class="footer-dark"><a href="#" class="footer-brand w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac805_logo-footer-white.svg" alt></a>
					<div class="footer-divider"></div>
					<div class="container-xl">
						<div class="footer-wrapper">
							<div class="footer-content">
								<div id="w-node-d2510c3f-abe0-7c21-3baa-0e3c6dbef737-6dbef72c" class="footer-block">
									<div class="title-small"><?php echo get_field('zagolovok_menyu', 'options') ?></div><a href="/" aria-current="page" class="footer-link w--current">Главная</a>
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
		</div><?php } ?><?php } ?>
		
		
		
		
		
		
		
	
<!-- FOOTER CODE --><?php get_template_part("footer_block", ""); ?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/index.js?ver=1789049048"></script></body>
</html>
