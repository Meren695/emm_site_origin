
<?php wp_footer(); ?>
<script type="text/javascript"
  src="<?php echo get_template_directory_uri() ?>/js/front.js?ver=1789049048"></script>
<?php if(file_exists(dirname( __FILE__ ).'/mailer.php') && !function_exists("wtw_forms_extentions") && (!function_exists('wtw_is_legacy_mailer_disabled') ? !(function_exists('get_field') && !empty((get_field('wtw_site_settings', 'options')['disable_legacy_mailer'] ?? false))) : !wtw_is_legacy_mailer_disabled())){ include_once 'mailer.php'; } ?>
<?php if(function_exists('get_field')) { echo get_field('footer_code', 'option'); } ?>
<?php if(file_exists(dirname( __FILE__ ).'/footer_code.php')){ include_once 'footer_code.php'; } ?>
<script type="text/javascript"
  src="<?php echo get_template_directory_uri() ?>/js/shop.js?ver=1789049048"></script>
<style type="text/css">
  .w-webflow-badge {
    display: none !important;
  }
		</style><script>
$(document).ready(function() {
$('.clonblockfirst').last().append($('.clonblock').clone());
})
		</script><script>
$(document).ready(function(){
$(window).scroll(function () {
if ($(this).scrollTop() > 30) {
$('#button-up').fadeIn();
} else {
$('#button-up').fadeOut();
}
});
$('#button-up').click(function () {
$('body,html').animate({
scrollTop: 0
}, 500);
return false;
});
});
		</script>