<?php
/*
Template name: Новый год Эммаусс Волга Клвб
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf429" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body class="winter-body">
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('sekcii_stranicy_novyj_god') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секции Страницы Новый Год"; $loop_field = "sekcii_stranicy_novyj_god"; while( have_rows('sekcii_stranicy_novyj_god') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
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
					<nav class="container-xxl-nav"><a href="/" class="logo w-nav-brand"><div class="logo-img winter-logo"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac75c_emmaus-logo.svg" alt class="image"></div><div class="logo-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6895f19b74b48c7714523e56_6869365a5499af0f5aeac760_text-logo.svg" alt class="image text"></div></a>
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
							<div class="phone-nav-block"><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_field('telefon_1', 'options')) ?>" class="link-phone w-nav-link"><?php echo get_field('telefon_1', 'options') ?></a></div><a href="<?php echo get_field('ssylka_knopki', 'options') ?>" class="winter-button top-btn w-button"><?php echo get_field('knopka', 'options') ?></a>
							<div class="bvi-open"></div>
						</div>
						<div class="menu-button-2 w-nav-button">
							<div data-w-id="092b6469-176a-560b-b0a1-feca0f86f358" data-is-ix2-target="1" class="lottie-animation" data-animation-type="lottie" data-src="https://cdn.prod.website-files.com/67d0865dbdaf088294d91f45/67d0865dbdaf088294d920b2_58235-hamburger-menu.json" data-loop="0" data-direction="1" data-autoplay="0" data-renderer="svg" data-default-duration="1.8333333333333333" data-duration="0.5" data-loading="eager"></div>
						</div>
					</nav>
				</div>
			</div>
			<div class="div-block-112">
				<?php if( have_rows('pervaya_sekciya_ng') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Первая Секция НГ"; $loop_field = "pervaya_sekciya_ng"; while( have_rows('pervaya_sekciya_ng') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
					<section id="first-section" class="section" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_pervoj_sekcii_ng'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
						<div class="fil-bg">
							<div class="container-xl img-main w">
								<div class="div-preim-copy">
									<div class="main-text-block new-year-text-block">
										<div class="main-text"><?php echo get_sub_field('mini_tekst_vyshe_zagolovka') ?></div>
										<h1 class="h1"><?php echo get_sub_field('zagolovok_h1') ?></h1>
										<div class="text-block-34 new-year-text"><?php echo get_sub_field('mini_tekst_nizhe_zagolovka') ?></div>
										<?php if( have_rows('knopki_na_pervom_ekrane') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопки на первом экране"; $loop_field = "knopki_na_pervom_ekrane"; while( have_rows('knopki_na_pervom_ekrane') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons">
											<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="winter-button home w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
											<?php if(empty(get_sub_field('skryt_knopku_2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" class="winter-button w-button"><?php echo get_sub_field('knopka2') ?></a></div><?php } ?>
										</div><?php } ?><?php } ?>
									</div>
								</div>
								<div class="div-preim-copy">
									<div class="reservation_block hero-block new-year-ko"><?php echo get_sub_field('kod_paneli_bronirovaniya') ?></div>
								</div>
							</div>
						</div>
					</section>
				</div><?php } ?><?php } ?>
				<div class="fill-white">
					<?php if( have_rows('sekciya_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 2"; $loop_field = "sekciya_2"; while( have_rows('sekciya_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="second-section" class="section_whight">
						<div class="container-xl green-cont">
							<div class="about-us_grid">
								<div class="what-to-do_text-block">
									<div class="aboute-us_text-block">
										<div class="wave-icon winter-icon"><img width="8" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" class="testimonial-image"></div>
										<h2 id="w-node-_792b4482-c0c0-74ad-daac-987c2306764d-d6fcf429" class="h2 same-page-h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
										<p class="preim-text red-18 b winter"><?php echo get_sub_field('opisanie_mini') ?></p>
										<p class="preim-text red-18 b winter"><?php echo get_sub_field('opisanie_mini_2') ?></p>
										<p class="preim-text red-18 b winter"><?php echo get_sub_field('opisanie_mini_3') ?></p>
									</div>
								</div><img class="image-31" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
						</div>
					</section><?php } ?><?php } ?>
				</div>
				<?php if( have_rows('sekciya_3') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 3"; $loop_field = "sekciya_3"; while( have_rows('sekciya_3') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="third-section-1" class="testimonial-stack winter-photo-block dgkr snow-section">
					<div class="container-2 winter">
						<div class="w-embed">
							<style>
  .snowflake {
		max-width: 96vw;
    position: absolute;
    top: -10px;
    color: #fff;
    font-size: 1em;
    animation: fall linear infinite;
    pointer-events: none;
    z-index: 1;
  }
  @keyframes fall {
    to {
      transform: translateY(100vh) rotate(360deg);
    }
  }
  
							</style>
							<script>
  document.addEventListener('DOMContentLoaded', function() {
    const section = document.querySelector('.snow-section');
    for (let i = 0; i < 50; i++) {
      const snowflake = document.createElement('div');
      snowflake.className = 'snowflake';
      snowflake.innerHTML = '❄';
      snowflake.style.left = Math.random() * 100 + '%';
      snowflake.style.animationDuration = (5 + Math.random() * 10) + 's';
      snowflake.style.fontSize = (0.5 + Math.random() * 1.5) + 'em';
      snowflake.style.opacity = 0.3 + Math.random() * 0.7;
      snowflake.style.animationDelay = Math.random() * 5 + 's';
      section.appendChild(snowflake);
    }
  });
  
							</script>
						</div>
						<div class="testimonial-card-three-w-45"><img width="82.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-2-3 hide-tablet-mob"><img width="148.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie_4'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-2-4 hide-tablet-mob"><img width="82.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-2-2 hide-tablet-mob"><img width="540" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-2 hide-tablet-mob">
							<div class="testimonial-info-four">
								<div class="wave-icon"><img width="8" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" class="testimonial-image"></div>
								<div class="about-600">
									<h2 class="h2 winter-text"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<div class="div-block-116">
										<div class="div-block-114">
											<p class="preim-text-owin"><?php echo get_sub_field('skidka_1') ?></p>
										</div>
										<?php if( have_rows('knopka_na_pervom_ekrane') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка на первом экране"; $loop_field = "knopka_na_pervom_ekrane"; while( have_rows('knopka_na_pervom_ekrane') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons">
											<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div class="div-block-117"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="winter-button w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
										</div><?php } ?><?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_4') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 4"; $loop_field = "sekciya_4"; while( have_rows('sekciya_4') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section wintren-marg-0">
					<div class="container-xl-2 img-prom-banner-winter winter-up-sugrob">
						<div class="testimonial-info-four black">
							<div class="wave-icon"><img width="7.5" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" class="testimonial-image-bana winter"></div>
							<div class="width-text">
								<p class="preim-text red-18 winter-card-text"><?php echo get_sub_field('opisanie_na_zelenom') ?></p>
							</div>
						</div>
						<div class="main-block-bani w">
							<?php if( have_rows('preimuschestvo_1') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Преимущество 1"; $loop_field = "preimuschestvo_1"; while( have_rows('preimuschestvo_1') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div data-w-id="faa82241-ebdb-e5fa-c030-a9e2f590ee08" style="opacity:0" class="adv018-item">
								<div class="adv018-head__block"><img width="20" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="adv018-img"></div>
								<div class="adv018-descr"><?php echo get_sub_field('opisanie') ?></div>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('preimuschestvo_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Преимущество 2"; $loop_field = "preimuschestvo_2"; while( have_rows('preimuschestvo_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div data-w-id="faa82241-ebdb-e5fa-c030-a9e2f590ee12" style="opacity:0" class="adv018-item">
								<div class="adv018-head__block"><img width="23" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="adv018-img"></div>
								<div class="adv018-descr"><?php echo get_sub_field('opisanie') ?></div>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('preimuschestvo_3') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Преимущество 3"; $loop_field = "preimuschestvo_3"; while( have_rows('preimuschestvo_3') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-faa82241-ebdb-e5fa-c030-a9e2f590ee19-d6fcf429" data-w-id="faa82241-ebdb-e5fa-c030-a9e2f590ee19" style="opacity:0" class="adv018-item">
								<div class="adv018-head__block"><img width="20.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="adv018-img"></div>
								<div class="adv018-descr"><?php echo get_sub_field('opisanie') ?></div>
							</div><?php } ?><?php } ?>
						</div>
						<div class="testimonial-info-four">
							<div class="width-text">
								<p class="preim-text red-18 winter-card-text"><?php echo get_sub_field('opisanie_v_seredine') ?></p>
							</div>
						</div>
						<div class="main-block-bani w">
							<?php if( have_rows('preimuschestvo_4') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Преимущество 4"; $loop_field = "preimuschestvo_4"; while( have_rows('preimuschestvo_4') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div data-w-id="b042b926-0dea-b056-3d8d-429665224229" style="opacity:0" class="adv018-item">
								<div class="adv018-head__block"><img width="20" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="adv018-img"></div>
								<div class="adv018-descr"><?php echo get_sub_field('opisanie') ?></div>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('preimuschestvo_5') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Преимущество 5"; $loop_field = "preimuschestvo_5"; while( have_rows('preimuschestvo_5') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div data-w-id="b042b926-0dea-b056-3d8d-429665224231" style="opacity:0" class="adv018-item">
								<div class="adv018-head__block"><img width="23" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="adv018-img"></div>
								<div class="adv018-descr"><?php echo get_sub_field('opisanie') ?></div>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('preimuschestvo_6') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Преимущество 6"; $loop_field = "preimuschestvo_6"; while( have_rows('preimuschestvo_6') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="w-node-b042b926-0dea-b056-3d8d-429665224236-d6fcf429" data-w-id="b042b926-0dea-b056-3d8d-429665224236" style="opacity:0" class="adv018-item">
								<div class="adv018-head__block"><img width="20.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="adv018-img"></div>
								<div class="adv018-descr"><?php echo get_sub_field('opisanie') ?></div>
							</div><?php } ?><?php } ?>
						</div>
					</div>
				</div><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_5') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 5"; $loop_field = "sekciya_5"; while( have_rows('sekciya_5') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="red-section" class="section-8">
					<div class="container-xl-2 winter-cont-w">
						<div class="winter-info-four-1">
							<div class="wave-icon winter-icon"><img width="8" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" class="testimonial-image"></div>
							<div class="about-600-winter">
								<h2 class="h2 winter-hader"><?php echo get_sub_field('zagolovok_n2') ?></h2>
							</div>
						</div>
						<div class="varianti-razmeshenia">
							<div class="bg_img-winter" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
								<div class="what-to-do_item-winter">
									<h3 class="h3"><?php echo get_sub_field('zagolovok_n3_1') ?></h3>
									<?php if( have_rows('knopka_na_pervom_ekrane') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка на первом экране"; $loop_field = "knopka_na_pervom_ekrane"; while( have_rows('knopka_na_pervom_ekrane') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-w">
										<?php if(empty(get_sub_field('skryt'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="winter-button w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
									</div><?php } ?><?php } ?>
								</div>
							</div>
							<div class="bg_img-winter _1" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
								<div class="what-to-do_item-winter">
									<h3 class="h3"><?php echo get_sub_field('zagolovok_n3_2') ?></h3>
									<?php if( have_rows('knopka_na_pervom_ekrane_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка на первом экране 2"; $loop_field = "knopka_na_pervom_ekrane_2"; while( have_rows('knopka_na_pervom_ekrane_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-w">
										<?php if(empty(get_sub_field('skryt'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="winter-button w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
									</div><?php } ?><?php } ?>
								</div>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_6') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 6"; $loop_field = "sekciya_6"; while( have_rows('sekciya_6') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner-section" class="banner-section winter-secsh-banner">
					<div class="container-xl-2 img-winter-banner">
						<div class="banner_block home">
							<div class="prom-text-block">
								<h2 class="h2-2 banner"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-2-copy">
									<?php if(empty(get_sub_field('skryt'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="winter-button blue w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
									<?php if(empty(get_sub_field('skryt2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" target="_blank" class="winter-button blue w-button"><?php echo get_sub_field('knopka2') ?></a></div><?php } ?>
								</div><?php } ?><?php } ?>
							</div><img id="w-node-c3f27ba0-8f10-a167-a405-6fcba5649452-d6fcf429" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-3-1"></div>
					</div>
				</div><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_7') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 7"; $loop_field = "sekciya_7"; while( have_rows('sekciya_7') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="third-section" class="testimonial-stack winter-photo-block winter-weare">
					<div class="winter-info-four-1 w">
						<div class="wave-icon winter-icon"><img width="8" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" class="testimonial-image"></div>
						<div class="about-600-winter">
							<h2 class="h2 winter-hader"><?php echo get_sub_field('zagolovok_n2') ?></h2>
						</div>
					</div>
					<div class="container-2 winter">
						<div class="testimonial-card-winter-lineup">
							<div>
								<div class="accordion1_component">
									<div data-w-id="9b780f86-67d3-f00c-9805-dc5b53bbbe6a" class="accordion1_top">
										<div>
											<div class="text-block-54">31 декабря</div>
											<h4 class="heading-5">День заезда и Новогодний банкет</h4>
										</div>
										<div data-w-id="6b3e8d8f-f442-5edf-1a70-f7ec04258370" class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<p class="preim-text red-18 b winter"><?php echo get_sub_field('opisanie_mini') ?></p>
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57">12:00</div>
													<div class="text-block-55"><strong>— </strong>Заезд и размещение. Вас встретит уютная welcome-зона с горячим напитком, мандаринами и праздничной атмосферой.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>13:30–18:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Обед — шведский стол с алкоголем.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>17:00–18:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Игровая программа «Снеговик и Снегурочка постигают модные тренды»  5+</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>18:30–19:30</strong></div>
													<div class="text-block-55"><strong>— </strong>Уличные гуляния на «Делянке» у кострища</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>21:30</strong></div>
													<div class="text-block-55"><strong>— </strong>Новогодний банкет 2026<br>‍<br>Грандиозная программа, яркий ведущий, музыка, интерактивы и, конечно, настоящий праздник.<strong><br><br>‍</strong>Welcome: встреча гостей, фотозона, легкие активности от ведущего.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>22:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Старт праздника «Встречаем будущее!»;<br>— торжественное открытие;<br>— первые конкурсы-активности;<br>— лёгкий перекус;<br>— знакомство гостей в игровой форме;</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>22:50–23:35</strong></div>
													<div class="text-block-55"><strong> — </strong>Игровые интерактивы для гостей.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>23:35–23:55</strong></div>
													<div class="text-block-55"><strong> — </strong>Подготовка к Новому году, формирование пожеланий и мечт на следующий год.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>00:00</strong></div>
													<div class="text-block-55"><strong> — </strong>Встреча Нового 2026 года!<br><br>Поздравление Президента, шампанское, объятия, фотографии.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>00:05–00:35</strong></div>
													<div class="text-block-55"><strong> — </strong>Пауза: звонки родным, прогулка, эмоции.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>00:35–01:20</strong></div>
													<div class="text-block-55"><strong> — </strong>Интерактивы, появление главных новогодних героев.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>01:30</strong></div>
													<div class="text-block-55"><strong> — </strong>Кавер-группа — танцы до упаду.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>02:00–02:30</strong></div>
													<div class="text-block-55"><strong> — </strong>Весёлый соревновательный интерактив.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>02:30–03:00</strong></div>
													<div class="text-block-55"><strong> — </strong>Второй выход кавер-группы.<br><br>Ещё больше музыки, энергии и любимых новогодних хитов.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>03:00–03:30</strong></div>
													<div class="text-block-55"><strong> — </strong>Вечер продолжает играть красками и яркими конкурсами.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>03:30–04:30</strong></div>
													<div class="text-block-55"><strong> — </strong>Последний мощный музыкальный аккорд ночи — танцы, которые запомнятся надолго.</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="accordion1_component">
									<div data-w-id="66e591e4-13db-3a94-475c-444b3ff6703b" class="accordion1_top">
										<div>
											<div class="text-block-54">1 января</div>
											<h4 class="heading-5">День отдыха и уличных гуляний</h4>
										</div>
										<div class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<p class="preim-text red-18 b winter">Праздник продолжается! Начните новый день с улыбкой и ароматом свежего кофе.<br>После завтрака вас ждёт весёлая программа на свежем воздухе и вечерний банкет.</p>
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57"><strong>09:00–11:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Завтрак (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>11:00–12:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Игровая программа на улице «Мир Лабубу» 3+.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>13:30–15:30</strong></div>
													<div class="text-block-55"><strong>— </strong>Обед (шведский стол с алкоголем).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>16:00–19:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Большие новогодние гуляния на свежем воздухе:<br>— праздничный квест<br>— встреча с Дедом Морозом и Снегурочкой<br>— чай и глинтвейн<br>— угощения<br>— катание на санях (с лошадью).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>20:00–23:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Вечерний праздничный ужин.<br><br>Ведущий, диджей, атмосферная музыкально-развлекательная программа, новогодние интерактивы, танцы и награждения.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>23:00–03:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Бар «Наутилус»<br>Дискотека, караоке, бильярд (все включено, кроме алкоголя)</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="accordion1_component">
									<div data-w-id="b3d196d1-e572-f6eb-a929-0fdf9b012ab8" class="accordion1_top">
										<div>
											<div class="text-block-54">2 января</div>
											<h4 class="heading-5"><strong>Семейный день</strong></h4>
										</div>
										<div class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<p class="preim-text red-18 b winter">Насладитесь последним днём новогодних каникул в атмосфере покоя и гармонии.<br>Это день для семейных игр, прогулок и отдыха в банном комплексе.</p>
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57"><strong>08:30–10:30</strong></div>
													<div class="text-block-55"><strong>— </strong>Завтрак (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>11:00–12:30</strong></div>
													<div class="text-block-55"><strong>— </strong>Игровая программа в помещении  «Кулинарное безумие» 5+ (в программе не используются продукты питания).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>13:30–15:30</strong></div>
													<div class="text-block-55"><strong>— </strong>Обед (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>15:00–16:30</strong></div>
													<div class="text-block-55"><strong>— </strong>Игровая программа в помещении «Мировые открытия» 5+</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>18:30–21:00</strong></div>
													<div class="text-block-55"><strong>— </strong>Ужин (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57"><strong>21:00–03:00</strong></div>
													<div class="text-block-55"><strong>— </strong>«Наутилус»: музыка, бар, караоке.</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="accordion1_component">
									<div data-w-id="98663848-0ccc-b303-59fd-e5621f6d1b92" class="accordion1_top">
										<div>
											<div class="text-block-54">3 января</div>
											<h4 class="heading-5"><strong>Выезд/новый заезд</strong></h4>
										</div>
										<div class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57">08:30–10:30</div>
													<div class="text-block-55">— Завтрак (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">11:00</div>
													<div class="text-block-55">— Выезд первой группы.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">17:00</div>
													<div class="text-block-55">— Новый заезд.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">18:30–21:00</div>
													<div class="text-block-55">— Ужин (шведский стол с алкоголем).</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="accordion1_component">
									<div data-w-id="f40a3e4d-efde-e008-bb0c-2a57bc5e21dc" class="accordion1_top">
										<div>
											<div class="text-block-54">4 января</div>
											<h4 class="heading-5"><strong>Игры, отдых и семья</strong></h4>
										</div>
										<div class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57">08:30–10:30</div>
													<div class="text-block-55">— Завтрак (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">11:30–13:00</div>
													<div class="text-block-55">— Игровая программа на улице «Танцуют Все!» 3+</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">13:30–15:30</div>
													<div class="text-block-55">— Обед (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">15:30–18:00</div>
													<div class="text-block-55">— Детская анимация<br>Новая программа: мастер-классы, игры, квесты.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">18:30–21:00</div>
													<div class="text-block-55">— Ужин (шведский стол)<br></div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="accordion1_component">
									<div data-w-id="91ff5be1-44c5-7bb2-054a-6b6c5ebdd9c1" class="accordion1_top">
										<div>
											<div class="text-block-54">5 января</div>
											<h4 class="heading-5"><strong>Релакс на берегу Волги</strong></h4>
										</div>
										<div class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57">08:30–10:30</div>
													<div class="text-block-55">— Завтрак (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">11:00–12:00</div>
													<div class="text-block-55">— Детская анимация.<br>Игры, творческие задания, интерактивы в тёплом помещении.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">13:30–15:30</div>
													<div class="text-block-55">— Обед (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">15:00–16:00</div>
													<div class="text-block-55">— Детская анимация<br>Творческий мастер-класс или подвижные игры — программа дня.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">18:30–21:00</div>
													<div class="text-block-55">— Ужин (шведский стол)<br></div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="accordion1_component">
									<div data-w-id="d257156d-62af-631f-fbfb-366be3e73b11" class="accordion1_top">
										<div>
											<div class="text-block-54">6 января</div>
											<h4 class="heading-5"><strong>Рождественский банкет</strong></h4>
										</div>
										<div class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57">08:30–10:30</div>
													<div class="text-block-55">— Завтрак (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">11:30–12:00</div>
													<div class="text-block-55">— Детская анимация.<br>Игры, творческие задания, интерактивы в тёплом помещении.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">13:30–15:30</div>
													<div class="text-block-55">— Обед (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">15:00–16:00</div>
													<div class="text-block-55">— Детская анимация<br>Игры, задания и подготовка к праздничному вечеру.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">20:00–23:00</div>
													<div class="text-block-55">— Праздничный ужин (банкет с алкоголем).<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">20:00</div>
													<div class="text-block-55">— Торжественное открытие вечера.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">20:00–21:00</div>
													<div class="text-block-55">— Знакомство гостей и лёгкие застольные интерактивы.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">21:00–23:00</div>
													<div class="text-block-55">— Музыкальная и танцевальная программа.<br>Танцевальные блоки, тематические интерактивы, рождественские гадания..<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">23:00</div>
													<div class="text-block-55">— Клуб «Наутилус».<br>Дискотека, караоке, бар (алкоголь не входит в стоимость).<br></div>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="accordion1_component">
									<div data-w-id="5d22732f-3370-64b4-82c9-95f50e2d64e0" class="accordion1_top">
										<div>
											<div class="text-block-54">7 января</div>
											<h4 class="heading-5"><strong>Рождественские гуляния</strong></h4>
										</div>
										<div class="accordion1_icon w-embed">
											<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect width="40" height="40" rx="20" transform="matrix(-1 0 0 1 40 0)" fill="#061B3A"></rect>
												<path d="M20 16.5L20.5282 17.0335L25.5 22.1087L24.4435 23.5L20 18.9655L15.5565 23.5L14.5 22.1087L19.4718 17.0335L20 16.5Z" fill="white"></path>
											</svg>
										</div>
									</div>
									<div style="height:0px" class="accordion1_bottom">
										<div class="margin-bottom margin-small">
											<ul role="list" class="list-3">
												<li class="list-item-3">
													<div class="text-block-57">08:30–10:30</div>
													<div class="text-block-55">— Завтрак (шведский стол).</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">11:30–12:00</div>
													<div class="text-block-55">— Детская анимация.<br>Рождественские игры и тематические активности.</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">13:30–15:30</div>
													<div class="text-block-55">— Обед (шведский стол с алкоголем)</div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">16:00–18:00</div>
													<div class="text-block-55">— Рождественские гуляния на улице.<br>Глинтвейн, чай, фрукты, сладости и легкие закуски.<br>Анимационные мини-активности, атмосферная прогулка, фотомоменты.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">19:00–21:00</div>
													<div class="text-block-55">— Ужин (шведский стол с алкоголем).<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">20:00</div>
													<div class="text-block-55">— Торжественное открытие вечера.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">20:00–21:00</div>
													<div class="text-block-55">— Знакомство гостей и лёгкие застольные интерактивы.<br></div>
												</li>
												<li class="list-item-3">
													<div class="text-block-57">21:00</div>
													<div class="text-block-55">— Клуб «Наутилус».<br>Дискотека, бар, караоке (алкоголь не входит в стоимость).<br></div>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_8') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 8"; $loop_field = "sekciya_8"; while( have_rows('sekciya_8') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="meropri-section" class="section_food">
					<div class="container-xl green-cont">
						<div class="div-preim-copy">
							<div class="catalog-box_black">
								<div></div>
								<h2 id="w-node-_0a072d93-6573-72ca-6ef8-08d613366db6-d6fcf429" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<div id="w-node-_0a072d93-6573-72ca-6ef8-08d613366db8-d6fcf429" class="headin-button">
									<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
								</div>
							</div>
						</div>
						<div class="gallery23_component">
							<div class="gallery23_slider swiper-first-reserv">
								<div class="gallery23_mask main-page swiper-wrapper"><a href="#" class="gallery23_slide swiper-slide w-inline-block"><img width="222.5" alt src="<?php echo get_template_directory_uri() ?>/images/690b53209bfa8839d9f7aef7_vkusnaa-ulicnaa-eda-naturmorty.webp" loading="lazy" class="gallery23_image"></a><a href="#" class="gallery23_slide swiper-slide w-inline-block"><img width="222.5" alt src="<?php echo get_template_directory_uri() ?>/images/690c65b9934ab10e2d7b03bc_6908fd7d75bb8177915110a1_event-item600.webp" loading="lazy" class="gallery23_image"></a><a href="#" class="gallery23_slide swiper-slide w-inline-block"><img width="222.5" alt src="<?php echo get_template_directory_uri() ?>/images/690b53208ae9e26e5328cef5_tarelki-s-raznoobraznoi-edoi-na-prazdnicnom-stole.webp" loading="lazy" class="gallery23_image"></a><a href="#" class="gallery23_slide swiper-slide w-inline-block"><img width="222.5" alt src="<?php echo get_template_directory_uri() ?>/images/690b53a8dfa6a4d05133ed9b_kurica-otvarnaa-s-ovosami3.webp" loading="lazy" class="gallery23_image"></a></div>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_9') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 9"; $loop_field = "sekciya_9"; while( have_rows('sekciya_9') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section">
					<div class="container-xl-2 img-winter-banner two">
						<div class="testimonial-info-four">
							<div class="wave-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" alt class="testimonial-image"></div>
							<div class="width-text">
								<h2 class="h2 trip"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<p class="preim-text red-18"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
							</div>
						</div>
						<div class="div-block-113">
							<div id="w-node-_2191d48a-a5d6-a439-f6f9-8aa2905bc8c0-d6fcf429" class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="339" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
							</div>
							<div id="w-node-c8dcea54-ce0f-36a4-4e2f-305f4c4fb407-d6fcf429" class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="339" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
							</div>
						</div>
						<div class="div-block-113">
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
							</div>
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_2') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_2') ?></p>
							</div>
						</div>
						<div class="div-block-113">
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_3') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_3') ?></p>
							</div>
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_4'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_4') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_4') ?></p>
							</div>
						</div>
					</div>
				</div><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_9') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 9"; $loop_field = "sekciya_9"; while( have_rows('sekciya_9') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section">
					<div class="container-xl-2 img-winter-banner two">
						<div class="testimonial-info-four">
							<div class="wave-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" alt class="testimonial-image"></div>
							<div class="width-text">
								<h2 class="h2 trip"><?php echo get_sub_field('zagolovok_n2') ?></h2>
								<p class="preim-text red-18"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
							</div>
						</div>
						<div class="div-block-113">
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
							</div>
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_2') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_2') ?></p>
							</div>
						</div>
						<div class="div-block-113">
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_3') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_3') ?></p>
							</div>
							<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_4'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
								<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_4') ?></h4>
								<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_4') ?></p>
							</div>
						</div>
					</div>
				</div><?php } ?><?php } ?>
				<?php if( have_rows('sekciya_10') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 10"; $loop_field = "sekciya_10"; while( have_rows('sekciya_10') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section">
					<div class="container-xl-2 img-prom-blue-w">
						<div class="banner_block">
							<div class="prom-text-block win">
								<div class="width_icon_label">
									<div class="wave-icon_banner3-w"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6908d7b0ad4271bdadb70c6f_emmaus-logo-winter-wight.svg" alt class="testimonial-image"></div>
									<div class="text-block-36"><?php echo get_sub_field('mini_tekst') ?></div>
								</div>
								<h4 class="heading-6"><?php echo get_sub_field('zagolovok_n4') ?></h4>
								<p class="preim-text banner3"><?php echo get_sub_field('opisanie') ?></p>
								<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-2">
									<?php if(empty(get_sub_field('skryt'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="winter-button blue w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
									<?php if(empty(get_sub_field('skryt2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" class="winter-button blue w-button"><?php echo get_sub_field('knopka2') ?></a></div><?php } ?>
								</div><?php } ?><?php } ?>
							</div><img width="210" id="w-node-_5e50d240-6bef-1e7c-cdf7-f0cb704d0012-d6fcf429" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-3"></div>
					</div>
				</div><?php } ?><?php } ?>
				<?php if( have_rows('shema_proezda_11') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Схема Проезда 11"; $loop_field = "shema_proezda_11"; while( have_rows('shema_proezda_11') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
					<section id="scheme-section" class="section_info cart w" style="background-image:url('<?php $field = get_sub_field('shema_proezda'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
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
									<?php if( have_rows('blok_transfer') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок трансфер"; $loop_field = "blok_transfer"; while( have_rows('blok_transfer') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="transfer-block"><img width="60" id="w-node-c3f27ba0-8f10-a167-a405-6fcba5649545-d6fcf429" alt src="<?php echo get_template_directory_uri() ?>/images/6869365b5499af0f5aeac7f0_Mask20group.webp" loading="lazy" class="image-5">
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
				<?php if( have_rows('sekciya_12') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция 12"; $loop_field = "sekciya_12"; while( have_rows('sekciya_12') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="red-section" class="section-newyear">
					<div class="container-xl-2 winter-cont-w w">
						<div class="winter-info-four-1">
							<div class="about-600-winter">
								<h2 class="h2 winter-hader"><?php echo get_sub_field('zagolovok_n2') ?></h2>
							</div>
						</div>
						<div class="accordion2_component">
							<div data-w-id="0479785a-e89c-b33d-a5ed-948daf92b3c7" class="accordion2_top">
								<div class="text-size-medium text-weight-bold">Что включено в новогодний пакет?</div>
								<div class="accordion2_icon w-embed">
									<svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M25.3333 15.667V16.3336C25.3333 16.7018 25.0349 17.0003 24.6667 17.0003H17V24.667C17 25.0351 16.7015 25.3336 16.3333 25.3336H15.6667C15.2985 25.3336 15 25.0351 15 24.667V17.0003H7.3333C6.96511 17.0003 6.66663 16.7018 6.66663 16.3336V15.667C6.66663 15.2988 6.96511 15.0003 7.3333 15.0003H15V7.33365C15 6.96546 15.2985 6.66699 15.6667 6.66699H16.3333C16.7015 6.66699 17 6.96546 17 7.33365V15.0003H24.6667C25.0349 15.0003 25.3333 15.2988 25.3333 15.667Z" fill="currentColor"></path>
									</svg>
								</div>
							</div>
							<div style="height:0px" class="accordion2_bottom">
								<div class="margin-bottom margin-small">
									<p>При бронировании проживания с 31 декабря 2025 года по 3 января 2026 года вы получаете полностью организованный отдых по системе “всё включено”.<br>В стоимость входят:<br>— проживание в комфортных номерах или коттеджах;<br>— трёхразовое питание по системе “шведский стол”;<br>— праздничный банкет 31 декабря с живой музыкой, ведущим и программой;<br>— анимация и развлечения для всей семьи 1 и 2 января.<br><br>Вам не придётся думать ни о чём — всё уже продумано до мелочей.<br>Просто приезжайте и наслаждайтесь настоящим праздником, где каждый момент наполнен заботой, уютом и атмосферой Нового года.</p>
								</div>
							</div>
						</div>
						<div class="accordion2_component">
							<div data-w-id="151327d4-4c6e-e0dc-6965-3e0bf9f3665f" class="accordion2_top">
								<div class="text-size-medium text-weight-bold">Можно ли приехать с детьми?</div>
								<div class="accordion2_icon w-embed">
									<svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M25.3333 15.667V16.3336C25.3333 16.7018 25.0349 17.0003 24.6667 17.0003H17V24.667C17 25.0351 16.7015 25.3336 16.3333 25.3336H15.6667C15.2985 25.3336 15 25.0351 15 24.667V17.0003H7.3333C6.96511 17.0003 6.66663 16.7018 6.66663 16.3336V15.667C6.66663 15.2988 6.96511 15.0003 7.3333 15.0003H15V7.33365C15 6.96546 15.2985 6.66699 15.6667 6.66699H16.3333C16.7015 6.66699 17 6.96546 17 7.33365V15.0003H24.6667C25.0349 15.0003 25.3333 15.2988 25.3333 15.667Z" fill="currentColor"></path>
									</svg>
								</div>
							</div>
							<div style="height:0px" class="accordion2_bottom">
								<div class="margin-bottom margin-small">
									<p>Конечно! «Эммаусс Волга Клаб» — это семейный формат отдыха, где рады и взрослым, и детям.<br>Для юных гостей подготовлена насыщенная анимационная программа:<br>— игровые активности и мастер-классы в помещении;<br>— весёлые квесты и зимние забавы на свежем воздухе;<br>— встречи с Дедом Морозом и Снегурочкой.<br><br>Пока малыши увлечены праздником, взрослые могут отдохнуть в банном комплексе, насладиться спа или просто прогуляться среди заснеженного соснового леса у реки.<br>Подарите себе и детям настоящее зимнее приключение, где каждому будет уютно и интересно!</p>
								</div>
							</div>
						</div>
						<div class="accordion2_component">
							<div data-w-id="26af3eab-4022-7817-eb92-39c349084467" class="accordion2_top">
								<div class="text-size-medium text-weight-bold">Есть ли скидки на бронирование?</div>
								<div class="accordion2_icon w-embed">
									<svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M25.3333 15.667V16.3336C25.3333 16.7018 25.0349 17.0003 24.6667 17.0003H17V24.667C17 25.0351 16.7015 25.3336 16.3333 25.3336H15.6667C15.2985 25.3336 15 25.0351 15 24.667V17.0003H7.3333C6.96511 17.0003 6.66663 16.7018 6.66663 16.3336V15.667C6.66663 15.2988 6.96511 15.0003 7.3333 15.0003H15V7.33365C15 6.96546 15.2985 6.66699 15.6667 6.66699H16.3333C16.7015 6.66699 17 6.96546 17 7.33365V15.0003H24.6667C25.0349 15.0003 25.3333 15.2988 25.3333 15.667Z" fill="currentColor"></path>
									</svg>
								</div>
							</div>
							<div style="height:0px" class="accordion2_bottom">
								<div class="margin-bottom margin-small">
									<p>Да! Для наших гостей действует система раннего бронирования — забронируйте отдых заранее и получите значительную выгоду.<br>‍<br>— До 15 ноября — скидка 15%<br>— С 16 по 30 ноября — скидка 10%<br>— С 1 по 15 декабря — скидка 5%<br>‍<br>Чем раньше вы бронируете — тем выгоднее ваш отдых!<br>Количество номеров ограничено, поэтому рекомендуем выбрать и забронировать свой вариант уже сейчас, чтобы гарантировать себе место на самом уютном празднике зимы.</p>
								</div>
							</div>
						</div>
						<div class="accordion2_component">
							<div data-w-id="74adda62-f5f6-4a16-802c-052fc78a8400" class="accordion2_top">
								<div class="text-size-medium text-weight-bold">Можно ли приехать на один день?</div>
								<div class="accordion2_icon w-embed">
									<svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M25.3333 15.667V16.3336C25.3333 16.7018 25.0349 17.0003 24.6667 17.0003H17V24.667C17 25.0351 16.7015 25.3336 16.3333 25.3336H15.6667C15.2985 25.3336 15 25.0351 15 24.667V17.0003H7.3333C6.96511 17.0003 6.66663 16.7018 6.66663 16.3336V15.667C6.66663 15.2988 6.96511 15.0003 7.3333 15.0003H15V7.33365C15 6.96546 15.2985 6.66699 15.6667 6.66699H16.3333C16.7015 6.66699 17 6.96546 17 7.33365V15.0003H24.6667C25.0349 15.0003 25.3333 15.2988 25.3333 15.667Z" fill="currentColor"></path>
									</svg>
								</div>
							</div>
							<div style="height:0px" class="accordion2_bottom">
								<div class="margin-bottom margin-small">
									<p>Новогоднее празднование рассчитано на 31.12.2025-03.01.2026. Бронируйте эти даты, чтобы насладиться всей программой и праздничными банкетами! <br>Также мы приглашаем вас с 03.01.2026 (бронирование от 2-ух суток). Продолжаем новогодние гуляния и детскую анимацию, а также отмечаем Рождество в формате банкета 06.01.2026! Ждем вас в удобные даты.</p>
								</div>
							</div>
						</div>
						<div class="accordion2_component">
							<div data-w-id="da887e88-184f-8299-312e-0d48310bf9d5" class="accordion2_top">
								<div class="text-size-medium text-weight-bold">Как проходит новогодний банкет?</div>
								<div class="accordion2_icon w-embed">
									<svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M25.3333 15.667V16.3336C25.3333 16.7018 25.0349 17.0003 24.6667 17.0003H17V24.667C17 25.0351 16.7015 25.3336 16.3333 25.3336H15.6667C15.2985 25.3336 15 25.0351 15 24.667V17.0003H7.3333C6.96511 17.0003 6.66663 16.7018 6.66663 16.3336V15.667C6.66663 15.2988 6.96511 15.0003 7.3333 15.0003H15V7.33365C15 6.96546 15.2985 6.66699 15.6667 6.66699H16.3333C16.7015 6.66699 17 6.96546 17 7.33365V15.0003H24.6667C25.0349 15.0003 25.3333 15.2988 25.3333 15.667Z" fill="currentColor"></path>
									</svg>
								</div>
							</div>
							<div style="height:0px" class="accordion2_bottom">
								<div class="margin-bottom margin-small">
									<p>Вас ждёт незабываемый вечер в тёплой, праздничной атмосфере.<br>Новогодний банкет — это яркое шоу с ведущим, кавер-группой, живой музыкой, конкурсами и поздравлениями Деда Мороза и Снегурочки.<br>На столах — изысканные блюда, праздничное настроение и море положительных эмоций.<br>Банкет станет центральным событием программы и подарит вам настоящую новогоднюю сказку.</p>
								</div>
							</div>
						</div>
						<div class="accordion2_component">
							<div data-w-id="bd1c349d-64e6-037c-fb81-449e62528c4c" class="accordion2_top">
								<div class="text-size-medium text-weight-bold">Какие доп. услуги доступны?</div>
								<div class="accordion2_icon w-embed">
									<svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M25.3333 15.667V16.3336C25.3333 16.7018 25.0349 17.0003 24.6667 17.0003H17V24.667C17 25.0351 16.7015 25.3336 16.3333 25.3336H15.6667C15.2985 25.3336 15 25.0351 15 24.667V17.0003H7.3333C6.96511 17.0003 6.66663 16.7018 6.66663 16.3336V15.667C6.66663 15.2988 6.96511 15.0003 7.3333 15.0003H15V7.33365C15 6.96546 15.2985 6.66699 15.6667 6.66699H16.3333C16.7015 6.66699 17 6.96546 17 7.33365V15.0003H24.6667C25.0349 15.0003 25.3333 15.2988 25.3333 15.667Z" fill="currentColor"></path>
									</svg>
								</div>
							</div>
							<div style="height:0px" class="accordion2_bottom">
								<div class="margin-bottom margin-small">
									<p>Для гостей доступны услуги, которые сделают отдых ещё насыщеннее:<br>— Русская и финская сауна, где можно расслабиться и восстановить силы;<br>— Новый банный комплекс с алтайским чаном прямо на берегу Волги;<br>— Конный клуб, предлагающий прогулки верхом и фотосессии;<br>— Просторная территория отеля для зимних прогулок и отдыха на свежем воздухе.<br><br>⚠️ Эти услуги предоставляются за дополнительную плату и могут быть забронированы заранее или при заселении.</p>
								</div>
							</div>
						</div>
						<div class="accordion2_component">
							<div data-w-id="afc6c64f-bf06-4d8a-286f-d9fc38e7bfe1" class="accordion2_top">
								<div class="text-size-medium text-weight-bold">Как добраться?</div>
								<div class="accordion2_icon w-embed">
									<svg width="100%" height="100%" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M25.3333 15.667V16.3336C25.3333 16.7018 25.0349 17.0003 24.6667 17.0003H17V24.667C17 25.0351 16.7015 25.3336 16.3333 25.3336H15.6667C15.2985 25.3336 15 25.0351 15 24.667V17.0003H7.3333C6.96511 17.0003 6.66663 16.7018 6.66663 16.3336V15.667C6.66663 15.2988 6.96511 15.0003 7.3333 15.0003H15V7.33365C15 6.96546 15.2985 6.66699 15.6667 6.66699H16.3333C16.7015 6.66699 17 6.96546 17 7.33365V15.0003H24.6667C25.0349 15.0003 25.3333 15.2988 25.3333 15.667Z" fill="currentColor"></path>
									</svg>
								</div>
							</div>
							<div style="height:0px" class="accordion2_bottom">
								<div class="margin-bottom margin-small">
									<p>Из Москвы своим ходом<br>Проезд из Москвы, Ленинградский вокзал, Примерное время 3-5 часов, в зависимости от электрички<br>С автовокзала или железнодорожного вокзала Твери автобус №106 <br>Автобус №106 едет до остановки «Эммаусская школа-интернат»<br>От остановки около 800 м пешком до отеля (ориентир — Музей Калининского фронта)<br><br>На машине. <a href="https://yandex.ru/maps/?from=mapframe&ll=36.147089,56.778827&mode=routes&rtext=~56.778827,36.147089&rtt=auto&ruri=~&z=14" target="_blank">Построить маршрут.</a><br></p>
								</div>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
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
		</div><?php } ?><?php } ?>
		<?php if( have_rows('sekciya_novogo_goda') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция Нового Года"; $loop_field = "sekciya_novogo_goda"; while( have_rows('sekciya_novogo_goda') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div id="banner2-section" class="banner-section-ng-40 hide">
			<div class="container-xl green-cont">
				<div class="testimonial-info-four">
					<div class="wave-icon"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6907c4a41b7a708a0d84fe4a_emmaus-logo20(2).svg" alt class="testimonial-image"></div>
					<div class="width-text">
						<h2 class="h2 trip"><?php echo get_sub_field('zagolovok_n2') ?></h2>
						<p class="preim-text red-18 gfd"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
					</div>
				</div>
				<div class="div-block-113">
					<div id="w-node-a80e56cb-0532-ba1e-570d-2849080289ea-d6fcf429" class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="339" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
						<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
						<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
					</div>
					<div id="w-node-a80e56cb-0532-ba1e-570d-2849080289f2-d6fcf429" class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="339" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
						<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
						<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
					</div>
				</div>
				<div class="div-block-113">
					<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_1'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
						<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_1') ?></h4>
						<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_1') ?></p>
					</div>
					<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_2'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
						<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_2') ?></h4>
						<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_2') ?></p>
					</div>
				</div>
				<div class="div-block-113">
					<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_3'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
						<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_3') ?></h4>
						<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_3') ?></p>
					</div>
					<div class="trip_item"><img class="image-4" src="<?php $field = get_sub_field('izobrazhenie_4'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="300" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy">
						<h4 class="h3 red"><?php echo get_sub_field('zagolovok_n4_4') ?></h4>
						<p class="preim-text-white op-50"><?php echo get_sub_field('opisanie_4') ?></p>
					</div>
				</div>
				<div class="banner_block home">
					<div class="prom-text-block">
						<h2 class="h2-2 banner"><?php echo get_sub_field('zagolovok_n2') ?></h2>
						<?php if( have_rows('knopka') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка"; $loop_field = "knopka"; while( have_rows('knopka') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons-2-copy">
							<?php if(empty(get_sub_field('skryt'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="winter-button blue w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
							<?php if(empty(get_sub_field('skryt2'))) { ?><div><a href="<?php echo get_sub_field('ssylka_knopki_2') ?>" target="_blank" class="winter-button blue w-button"><?php echo get_sub_field('knopka2') ?></a></div><?php } ?>
						</div><?php } ?><?php } ?>
					</div><img id="w-node-_377b8053-1738-74db-0188-7151471add07-d6fcf429" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" loading="lazy" class="image-3-1"></div>
			</div>
		</div><?php } ?><?php } ?>
		
		
		
		
		
	
<!-- FOOTER CODE --><?php get_template_part("footer_block", ""); ?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/new-year.js?ver=1789049048"></script></body>
</html>
