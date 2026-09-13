<?php
/*
Template name: Акции и спец предложения
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf439" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body>
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('sekcii_stranicy_akcij') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секции страницы акций"; $loop_field = "sekcii_stranicy_akcij"; while( have_rows('sekcii_stranicy_akcij') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
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
					<div class="fill-bg-sect">
						<div class="container-xl same-page">
							<div class="same-box o-nas_top-txt">
								<h1 class="h2 trip"><?php echo get_sub_field('zagolovok_n2') ?></h1>
								<p class="preim-text"><?php echo get_sub_field('opisanie_stranicy') ?></p>
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
			<section id="second-section" class="section-paiding-60">
				<div class="container-padding-20">
					<div class="container-xl green-cont">
						<?php $current_id = get_queried_object_id(); $parent_id  = wp_get_post_parent_id( $current_id ); ?>
						<?php if( !isset($query_args)) $query_args = array('post_type'      => 'page', 'post_status'    => 'publish', 'post_parent'    => $current_id, 'post__not_in'   => array( $current_id ), 'posts_per_page' => 12, 'orderby'        => 'date', 'order'          => 'DESC', 'paged'          => $paged, 'no_found_rows'  => false,); if( isset($args['paged']) && $args['paged']){ $query_args['paged'] = $args['paged']; }else{ $query_args['paged'] = get_query_var('page') ? get_query_var('page') : get_query_var('paged'); }$query = new WP_Query($query_args); if($query->have_posts()) : ?>
<div class="what-to-do_grid-akcii-vse">
<?php $rotation = 0; $group = 0; $post_index = 0; while($query->have_posts()) : $query->the_post();
        $rotation === 0 ? $rotation = 1 : $rotation++;
        $group === 0 ? $group = 1 : $group++; $post_index++; ?>
<?php if ( class_exists( "WooCommerce" )) $product = wc_get_product(get_the_ID()); ?><a href="<?php the_permalink(); ?>" class="link-svadb w-inline-block" data-content="query_item"><div class="bg_img-akcii" style="background-image: url('<?php $img = wp_get_attachment_image_src(get_post_thumbnail_id(), "full"); echo $img[0]; ?>');"><div class="what-to-do_item-akcii"><?php if( have_rows('stranica_akcij') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Страница Акций"; $loop_field = "stranica_akcij"; while( have_rows('stranica_akcij') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div><?php if( have_rows('sekciya_pervaya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция первая"; $loop_field = "sekciya_pervaya"; while( have_rows('sekciya_pervaya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div><h3 class="h3"><?php the_title(); ?></h3><p class="preim-text-white"><?php echo get_sub_field('mini_opisanie_akcii') ?></p></div><?php } ?><?php } ?></div><?php } ?><?php } ?><div class="div-block-463"><div class="main-button-ht green"><div>Подробнее</div></div></div></div></div></a>
<?php endwhile; ?></div>
<?php else : ?><?php endif; unset($query_args); wp_reset_postdata(); ?>
						<div class="pagination1_component"><?php echo paginate_links( array( 'base' => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ), 'current' => max( 1, get_query_var('page') ? get_query_var('page') : get_query_var('paged') ), 'total' => $query->max_num_pages,  'mid_size' => 5, 'prev_text' => 'Назад', 'next_text' => 'Вперед',  )); ?></div>
					</div>
				</div>
			</section>
			<?php if( have_rows('sekciya_ceo') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция CEO"; $loop_field = "sekciya_ceo"; while( have_rows('sekciya_ceo') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section id="worck-section">
				<div class="container-padding-20">
					<div class="container-xl">
						<div class="rich-text-block w-richtext"><?php echo get_sub_field('dopolnitelnyj_tekst') ?></div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
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
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/akcii.js?ver=1789049048"></script></body>
</html>
