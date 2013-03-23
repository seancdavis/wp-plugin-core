<?php
global $mrcf_gs;
$button_bkg = $mrcf_gs->option('button_bkg');
if( $button_bkg == '#000' || $button_bkg == '#000000' ) $button_bkg = $this->lighten($button_bkg, '1');
$button_bkg_gradient = $this->lighten($button_bkg, '20'); ?>
<style type="text/css">
	.rt-mrcf-submit {
		<?php echo $this->gradient( $button_bkg, $button_bkg_gradient ); ?>
	}
	.rt-mrcf-submit:hover {
		background: <?php echo $button_bkg; ?>;
	}
</style>