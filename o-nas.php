<?php
/*
Template name: О нас
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf3e7" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body>
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('bloki_stranicy_o_nas') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блоки страницы О нас"; $loop_field = "bloki_stranicy_o_nas"; while( have_rows('bloki_stranicy_o_nas') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
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
			<section class="section_same-page o-nas" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_verhnej_sekcii'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
				<div class="fill-bg-sect">
					<div class="container-xl same-page">
						<div class="same-box o-nas_top-txt">
							<h1 class="h2 trip b" style="color: <?php echo get_sub_field('cvet_zagolovka') ?>;"><?php echo get_sub_field('zagolovok_n2') ?></h1>
							<p class="preim-text" style="color: <?php echo get_sub_field('cvet_teksta') ?>;"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
							<?php if(empty(get_sub_field('skryt_knopku'))) { ?><div class="main-buttons-center"><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht green w-button"><?php echo get_sub_field('knopka') ?></a></div><?php } ?>
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
			<?php if( have_rows('my_predlagaem') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Мы предлагаем"; $loop_field = "my_predlagaem"; while( have_rows('my_predlagaem') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
				<div class="container-padding-20">
					<div class="container-xl green-cont-60 first">
						<div class="about-us_grid-1">
							<div class="what-to-do_text-block">
								<div class="aboute-us_text-block">
									<div class="wave-icon about-us"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac767_emmaus-logo.svg" alt class="testimonial-image"></div>
									<h2 id="w-node-_9ff8ead5-6f84-8fd1-a640-f156f9f0706a-d6fcf3e7" class="h2 same-page-h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<p class="preim-text red-18 b"><?php echo get_sub_field('opisanie_mini') ?></p>
								</div>
							</div><img class="image-31" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<?php if( have_rows('o_nas') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="О нас"; $loop_field = "o_nas"; while( have_rows('o_nas') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
				<div class="container-padding-20">
					<div class="container-xl green-cont-60">
						<div class="w-layout-grid grid">
							<div id="w-node-ad631d4e-7345-79e6-d96c-d5942ed44cf3-d6fcf3e7" class="about-us_block-tekst img-bg" style="background-image:url('<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');"></div>
							<?php if( have_rows('blok_s_tekstom') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок с текстом"; $loop_field = "blok_s_tekstom"; while( have_rows('blok_s_tekstom') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="about-us_block-tekst">
								<div>
									<div class="home-icon-grey"><img width="7.5" loading="lazy" alt src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac76a_emmaus-logo-home.svg" class="testimonial-image"></div>
									<h3 class="heading"><?php echo get_sub_field('zagolovok_n2') ?></h3>
									<p class="preim-text red-18 b"><?php echo get_sub_field('opisanie') ?></p>
								</div>
								<?php if( have_rows('harakteristiki') ){ ?><div class="characteristics_block"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Характеристики"; $loop_field = "harakteristiki"; while( have_rows('harakteristiki') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
									<div class="haracteristic2"><?php echo get_sub_field('harakteristika') ?></div>
									
									
									
									
								<?php } ?></div><?php } ?>
							</div><?php } ?><?php } ?>
							<?php if( have_rows('blok_s_tekstom_2') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блок с текстом 2"; $loop_field = "blok_s_tekstom_2"; while( have_rows('blok_s_tekstom_2') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="about-us_block-tekst">
								<div>
									<div class="wave-icon-grey"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/6869365a5499af0f5aeac767_emmaus-logo.svg" alt class="testimonial-image"></div>
									<h3 class="heading"><?php echo get_sub_field('zagolovok_n2') ?></h3>
									<p class="preim-text red-18 b"><?php echo get_sub_field('opisanie') ?></p>
								</div>
								<?php if( have_rows('harakteristiki') ){ ?><div class="characteristics_block"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Характеристики"; $loop_field = "harakteristiki"; while( have_rows('harakteristiki') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
									<div class="haracteristic2"><?php echo get_sub_field('harakteristika_1') ?></div>
									
									
									
									
									
									
								<?php } ?></div><?php } ?>
							</div><?php } ?><?php } ?>
						</div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<div>
				<?php if( have_rows('video') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Видео"; $loop_field = "video"; while( have_rows('video') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
					<div class="container-xl green-cont-60">
						<div class="heading_block center">
							<h2 id="w-node-d0f7b788-affa-5b40-8463-ab5a89069e5d-d6fcf3e7" class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
						</div>
						<div class="gallery23_component">
							<div class="gallery23_slider-video swiper video swiper-first-reserv">
								<?php if( have_rows('zagruzit_video') ){ ?><div class="gallery23_mask main-page swiper-wrapper"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Загрузить видео"; $loop_field = "zagruzit_video"; while( have_rows('zagruzit_video') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
									<div class="gallery23_slide-video swiper-slide"><a href="#" class="hero014-lbox w-inline-block w-lightbox"><div class="video-orig"><?php echo get_sub_field('video') ?></div><script type="application/json" class="w-json"><?php
$item = get_sub_field('video');
$items = Array();
if( is_array($item) ){
  $video = $item['video'];
  $poster = $item['poster'];
  if( isset($poster['url']) ){
    $image_url = $poster['url'];
  }
  elseif ( is_numeric($poster) ){
    $image_url = wp_get_attachment_image_url($poster, 'full');
  }
  else {
    $image_url = $poster;
  }
} else {
  $video = $item;
  $image_url = '';
}
$items[] = [
  'html' => $video,
  'thumbnailUrl' => $image_url,
  'width' => 940,
  'height' => 528,
  'type' => 'video'
];
echo json_encode([
  'group' => '',
  'items' => $items
], JSON_UNESCAPED_SLASHES);
?></script></a>
										<div class="video_block"><img class="hero014-img" src="<?php $field = get_sub_field('oblozhka'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"><img src="<?php echo get_template_directory_uri() ?>/images/686fbb62d660b35affd2d4fd_Vector20(8).svg" loading="lazy" width="34" alt class="hero014-video__ico"></div>
									</div>
									
									
									
								<?php } ?></div><?php } ?>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
				<?php if( have_rows('foto') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Фото"; $loop_field = "foto"; while( have_rows('foto') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
					<div class="container-padding-20">
						<div class="container-xl green-cont-60">
							<div class="heading_block center">
								<h2 id="w-node-c435d567-69c7-9411-18fe-b7b821fe6cf7-d6fcf3e7" class="h2">Галерея фотографий</h2>
							</div>
							<div class="form-block w-form">
								<form id="search_filter_ajax" name="search_filter_ajax" data-name="search_filter_ajax" method="get" class="filter-controls" data-wf-page-id="686931998f03c728d6fcf3e7" data-wf-element-id="c435d567-69c7-9411-18fe-b7b821fe6d07" action="<?php echo get_home_url() ?>/index.php#results" data-action="search_filter_ajax"><input type="hidden" name="post_type" value="attachment">

									<div class="filters-block_gallery"><?php echo do_shortcode('[facetwp facet="foto_gallery_par1"]'); ?></div>
								</form>
								<div class="w-form-done">
									<div>Thank you! Your submission has been received!</div>
								</div>
								<div class="w-form-fail">
									<div>Oops! Something went wrong while submitting the form.</div>
								</div>
							</div>
							<div class="div-block-108">
								<?php if( !isset($query_args)) $query_args = array('post_type' => 'attachment' , 'post_mime_type' => 'image', 'post_status' => 'inherit','facetwp' => true, 'tax_query' => array( array( 'taxonomy' => 'Mediafiles_par_1', 'operator' => 'EXISTS' ) ) ); if( isset($args['paged']) && $args['paged']){ $query_args['paged'] = $args['paged']; }else{ $query_args['paged'] = get_query_var('page') ? get_query_var('page') : get_query_var('paged'); }$query = new WP_Query($query_args); if($query->have_posts()) : ?>
<div class="gallery23_component-gallery facetwp-template">
<?php $rotation = 0; $group = 0; $post_index = 0; while($query->have_posts()) : $query->the_post();
        $rotation === 0 ? $rotation = 1 : $rotation++;
        $group === 0 ? $group = 1 : $group++; $post_index++; ?>
<?php if ( class_exists( "WooCommerce" )) $product = wc_get_product(get_the_ID()); ?><a href="#" class="gallery23_lightbox-link w-inline-block w-lightbox" data-content="query_item"><div class="gallery23_image-wrapper"><img class="gallery23_image" src="<?php $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); echo $img[0]; ?>" width="500" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) ?>" loading="lazy" title="<?php echo get_the_title(get_post_thumbnail_id()) ?>"></div><script type="application/json" class="w-json"><?php
$item = wp_get_attachment_image_src(get_post_thumbnail_id(), "full");
if( isset($item[0]) ){
  $image_url = $item[0];
} else {
  $image_url = '';
}
$items = Array();
$items[] = [
  'url' => $image_url,
  'type' => 'image',
  'caption' => isset($item['caption']) ? $item['caption'] : ''
];
echo json_encode([
  'group' => $loop_field.$loop_id,
  'items' => $items
], JSON_UNESCAPED_SLASHES);
?></script></a>
<?php endwhile; ?></div>
<?php else : ?><?php endif; unset($query_args); wp_reset_postdata(); ?>
								<div class="div-block-107"><?php echo do_shortcode('[facetwp facet="load_more"]'); ?></div>
							</div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
				<?php if( have_rows('foto') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Фото"; $loop_field = "foto"; while( have_rows('foto') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_food">
					<div class="container-padding-20">
						<div class="container-xl green-cont-60 end">
							<div class="heading_block">
								<h2 id="w-node-bc061041-a5e2-1652-6ee7-6cebb2f0ddf7-d6fcf3e7" class="h2"><?php echo get_sub_field('zagolovok_n2') ?></h2>
							</div>
							<div class="gallery23_component-gallery"><?php $gallery = get_sub_field('galereya_zagruzh_fotogalereya'); if(!empty($gallery)){ foreach($gallery as $item){ ?><a href="#" class="gallery23_lightbox-link w-inline-block w-lightbox"><div class="gallery23_image-wrapper"><img class="gallery23_image" src="<?php $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); echo $img[0]; ?>" width="500" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) ?>" loading="lazy" title="<?php echo get_the_title(get_post_thumbnail_id()) ?>"></div><script type="application/json" class="w-json"><?php
$item = wp_get_attachment_image_src(get_post_thumbnail_id(), "full");
if( isset($item[0]) ){
  $image_url = $item[0];
} else {
  $image_url = '';
}
$items = Array();
$items[] = [
  'url' => $image_url,
  'type' => 'image',
  'caption' => isset($item['caption']) ? $item['caption'] : ''
];
echo json_encode([
  'group' => '',
  'items' => $items
], JSON_UNESCAPED_SLASHES);
?></script></a><?php }} ?></div>
						</div>
					</div>
				</section><?php } ?><?php } ?>
			</div>
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
					<div class="container-xl">
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
									<h2 id="w-node-_8a00430c-fc37-0bef-162e-3fcfe1609895-d6fcf3e7" class="h2 marg-30"><?php echo get_sub_field('zagolovok_n2') ?></h2>
									<div id="w-node-_8a00430c-fc37-0bef-162e-3fcfe1609897-d6fcf3e7" class="headin-button">
										<p class="preim-text"><?php echo get_sub_field('opisanie_pod_zagolovkom') ?></p>
									</div>
								</div>
							</div>
							<div class="gallery23_component">
								<div class="gallery23_slider swiper-first-reserv">
									<?php $query = new WP_Query('cat=28'); if($query->have_posts()) : ?>
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
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/o-nas.js?ver=1789049048"></script></body>
</html>
