<?php
/*
Template name: Скрытая страница
*/
?>
    <!DOCTYPE html>
<html data-wf-page="686931998f03c728d6fcf438" data-wf-site="686931998f03c728d6fcf388" lang="ru">
	<?php get_template_part("header_block", ""); ?>
	<body>
<?php if(function_exists('get_field')) { echo get_field('body_code', 'option'); } ?>

		<?php if( have_rows('sekcii_skrytoj_stranicy') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секции Скрытой страницы"; $loop_field = "sekcii_skrytoj_stranicy"; while( have_rows('sekcii_skrytoj_stranicy') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="div-block-467">
			<?php if( have_rows('pervaya_sekciya') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Первая Секция"; $loop_field = "pervaya_sekciya"; while( have_rows('pervaya_sekciya') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div>
				<section id="first-section" class="section-hero hide-page" style="background-image:url('<?php $field = get_sub_field('izobrazhenie_pervoj_sekcii'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>');">
					<div class="fil-bg-hero">
						<div class="container-xl img-main">
							<div class="div-preim-copy">
								<div class="main-text-block">
									<h1 class="h1 contact"><?php echo get_sub_field('zagolovok_h1') ?></h1>
									<div class="text-block-34"><?php echo get_sub_field('mini_tekst_nizhe_zagolovka') ?></div>
									<?php if( have_rows('knopka_na_pervom_ekrane') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Кнопка на первом экране"; $loop_field = "knopka_na_pervom_ekrane"; while( have_rows('knopka_na_pervom_ekrane') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><div class="main-buttons">
										<div><a href="<?php echo get_sub_field('ssylka_knopki') ?>" class="main-button-ht w-button"><?php echo get_sub_field('knopka') ?></a></div>
									</div><?php } ?><?php } ?>
								</div>
							</div>
							<div class="div-preim-copy"></div>
						</div>
					</div>
				</section>
			</div><?php } ?><?php } ?>
			<?php if( have_rows('sekciya_dosuga') ){ ?><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Секция Досуга"; $loop_field = "sekciya_dosuga"; while( have_rows('sekciya_dosuga') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?><section class="section_layout408">
				<div class="padding-global">
					<div class="container-large">
						<div class="padding-section-large">
							<div class="layout408_card-copy sfd fdvghj fdg dbgrwfs <?php echo get_sub_field('dobavit_klass') ?>">
								<div class="layout408_image-wrapper"></div>
								<div class="layout408_card-content">
									<div class="layout408_card-content-top vhjbk">
										<div class="margin-bottom margin-small">
											<h3><?php echo get_sub_field('zagolovok_n3') ?></h3>
										</div>
									</div>
									<div class="margin-top margin-medium">
										<div class="button-group">
											<?php if(empty(get_sub_field('skryt_knopku_2'))) { ?><div></div><?php } ?>
										</div>
									</div>
								</div>
							</div>
							<?php if( have_rows('bloki_dosuga') ){ ?><div data-w-id="1183cb5e-5c5a-6d7a-9249-c01230d8ea75" class="w-layout-grid layout408_component"><?php global $parent_id; if(isset($loop_id)) $parent_id = $loop_id; $loop_index = 0; $loop_title="Блоки досуга"; $loop_field = "bloki_dosuga"; while( have_rows('bloki_dosuga') ){ global $loop_id; $loop_index++; $loop_id++; the_row(); ?>
								<div class="layout408_card-copy <?php echo get_sub_field('dobavit_klass') ?>">
									<div class="layout408_card-content">
										<div class="layout408_card-content-top vhjbk">
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
												<?php if(empty(get_sub_field('skryt_knopku_2'))) { ?><div></div><?php } ?>
											</div>
										</div>
									</div>
									<div class="layout408_image-wrapper"><img class="layout408_image" src="<?php $field = get_sub_field('izobrazhenie'); if(isset($field['url'])){ echo($field['url']); }elseif(is_numeric($field)){ echo(wp_get_attachment_image_url($field, 'full')); }else{ echo($field); } ?>" width="500.5" alt="<?php echo !empty($field['alt']) ? esc_attr($field['alt']) : ''; ?>" loading="lazy"></div>
								</div>
								
								
								
								
								
							<?php } ?></div><?php } ?>
						</div>
					</div>
				</div>
			</section><?php } ?><?php } ?>
			<div>
				<address class="call-to-action">
					<div class="trigger">
						<div data-is-ix2-target="1" class="trigger-lottie" data-w-id="6c2dad21-436f-9358-e1e9-4c7534bdb1f3" data-animation-type="lottie" data-src="https://cdn.prod.website-files.com/5f9b359e6ddc81e427ceab3a/5fa17bb235bfbbc33afaad4e_contact-cta.json" data-loop="0" data-direction="1" data-autoplay="0" data-renderer="svg" data-default-duration="2.0020019204587935" data-duration="2" data-loading="eager" data-ix2-initial-state="0"></div>
					</div><a href="<?php echo get_field('ssylka_na_votcap_futer', 'options') ?>" target="_blank" class="whatsapp w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/69a83d8fafb695c599a13167_D0BCD0B0D0BAD18120D0B1D0B5D0BB.svg" alt class="call-to-action-icon"></a><a href="<?php echo get_field('ssylka_na_telegram_futer', 'options') ?>" class="email w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/686931998f03c728d6fcf3ac_Telegram-footer.svg" alt class="call-to-action-icon"></a><a href="tel:+<?php echo preg_replace("/(\D)/", "", get_field('telefon_1', 'options')) ?>" class="mobile w-inline-block"><img loading="lazy" src="<?php echo get_template_directory_uri() ?>/images/62ffe65faad4eedcbcf0ebdb_call.svg" alt class="call-to-action-icon"></a></address>
			</div>
			<div class="div-block-460">
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
			</div>
		</div><?php } ?><?php } ?>
		
		
		
		
		
	
<!-- FOOTER CODE --><?php get_template_part("footer_block", ""); ?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/skrytaya-stranica.js?ver=1789049048"></script></body>
</html>
