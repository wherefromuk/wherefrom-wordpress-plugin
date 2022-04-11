<?php
class Wherefrom_Shortcodes {
	
  public function wherefrom_brand_widget_stacked( $atts ) {
		$params = shortcode_atts( array(
			'width' => '220px',
			'height' => '130px',
			'backgroundColor' => 'transparent'
		), $atts );
	
		$seoName = get_option('wherefrom_seo_name', false );

		if (! $seoName || $seoName === '') return '<div style="background-color: red; color: white; padding: 20px;">To display your Wherefrom Score, please set your SEO NAME in the Wherefrom\'s settings within your Admin area.</div>';
	
		$wfPluginOutput = '
			<iframe
				class="wherefrom-widget wherefrom-brand-stacked wherefrom-brand-'.esc_attr($seoName).'"
				src="https://wherefrom.org/widget/brand/'.esc_attr($seoName).'"
				frameborder="0"
				scrolling="no"
				allowtransparency="true"
				style="background-color: '.esc_attr($params['backgroundColor']).';"
				width="'.esc_attr($params['width']).'"
				height="'.esc_attr($params['height']).'"
			>
			</iframe>
		';
	
		return $wfPluginOutput;
	}
	
	public function wherefrom_brand_widget_banner( $atts ) {
		$params = shortcode_atts( array(
			'width' => '100%',
			'maxWidth' => '1024px',
			'height' => '50px',
			'backgroundColor' => 'transparent'
		), $atts );
	
		$seoName = get_option('wherefrom_seo_name', false );

		if (! $seoName || $seoName === '') return '<div style="background-color: red; color: white; padding: 20px;">To display your Wherefrom Score, please set your SEO NAME in the Wherefrom\'s settings within your Admin area.</div>';
	
		$wfPluginOutput = '
			<iframe
				class="wherefrom-widget wherefrom-brand-banner wherefrom-brand-'.esc_attr($seoName).'"
				src="https://wherefrom.org/widget/brand/'.esc_attr($seoName).'/banner"
				frameborder="0"
				scrolling="no"
				allowtransparency="true"
				style="background-color: '.esc_attr($params['backgroundColor']).'; maxWidth: '.esc_attr($params['maxWidth']).';"
				width="'.esc_attr($params['width']).'"
				height="'.esc_attr($params['height']).'"
			></iframe>
		';
	
		return $wfPluginOutput;
	}

	public function wherefrom_product_widget( $atts ) {
		$params = shortcode_atts( array(
			'id_type' => 'SID',
			'id' => null,
			'width' => '100%',
			'height' => '130px',
			'backgroundColor' => 'transparent',
			'align' => 'center',
			'theme' => 'coloured'
		), $atts );
	
		$seoName = get_option('wherefrom_seo_name', false );

		if (! $seoName || $seoName === '') return '<div style="background-color: red; color: white; padding: 20px;">To display your Wherefrom Products Score, please set your SEO NAME in the Wherefrom\'s settings within your Admin area.</div>';

		if (! $params['id'] || $params['id'] === '') return '<div style="background-color: red; color: white; padding: 20px;"><b>No product id found!</b> <br /> Please make sure the shortcode is correctly added to your woocommerce template.</div>';
	
		$url = 'https://wherefrom.org/widget/'.esc_attr($seoName).'/embed/'.esc_attr($params['id']);
		
		if ($params['id_type'] === 'WFID') {
			$url .= '/WFID';
		} else if ($params['id_type'] === 'SID') {
			$url .= '/SID';
		}

		if ($params['align'] !== 'center' || $params['theme'] !== 'coloured') {
			$wfParamsArray = array();

			if ($params['align'] !== 'center') {
				$wfParamsArray[] = 'align='.esc_attr($params['align']);
			}

			if ($params['theme'] !== 'coloured') {
				$wfParamsArray[] = 'theme='.esc_attr($params['theme']);
			}

			$url .= '?'.implode('&', $wfParamsArray);
		}

		$wfPluginOutput = '
			<iframe
				class="wherefrom-widget wherefrom-product-widget"
				src="'.$url.'"
				frameborder="0"
				scrolling="no"
				allowtransparency="true"
				style="background-color: transparent; maxWidth: 1024px;"
				width="'.esc_attr($params['width']).'"
				height="'.esc_attr($params['height']).'"
			></iframe>
		';
	
		return $wfPluginOutput;
	}

  public function register() {
    add_shortcode( 'wherefrom_brand_widget_stacked', array($this, 'wherefrom_brand_widget_stacked' ));
		add_shortcode( 'wherefrom_brand_widget_banner', array($this, 'wherefrom_brand_widget_banner' ));
		add_shortcode( 'wherefrom_product_widget', array($this, 'wherefrom_product_widget' ));
  }
}