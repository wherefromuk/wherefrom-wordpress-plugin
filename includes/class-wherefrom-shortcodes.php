<?php
class Wherefrom_Shortcodes {
	
  public function wherefrom_brand_widget_stacked( $atts ) {
		$seoName = get_option('wherefrom_seo_name', false );

		if (! $seoName || $seoName === '') return '<div style="background-color: red; color: white; padding: 20px;">To display your Wherefrom Score, please set your SEO NAME in the Wherefrom\'s settings within your Admin area.</div>';

		$url = 'https://wherefrom.org/widget/brand/'.esc_attr($seoName);

		$logoVariant = get_option('wherefrom_brand_widget_logo_variant', 'black');

		$params = shortcode_atts(array(
			'width' => '220px',
			'height' => '130px',
			'backgroundColor' => 'transparent',
			'logoVariant' => $logoVariant,
			'align' => 'center'
		), $atts );

		if ($params['logoVariant'] !== 'black' || $params['align'] !== 'left') {
			$wfParamsArray = array();

			if ($params['logoVariant'] !== 'black') {
				$wfParamsArray[] = 'logoVariant='.esc_attr($params['logoVariant']);
			}

			if ($params['align'] !== 'left') {
				$wfParamsArray[] = 'align='.esc_attr($params['align']);
			}

			$url .= '?'.implode('&', $wfParamsArray);
		}
	
		$wfPluginOutput = '
			<iframe
				class="wherefrom-widget wherefrom-brand-stacked wherefrom-brand-'.esc_attr($seoName).'"
				src="'.$url.'"
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
		$seoName = get_option('wherefrom_seo_name', false );
		if (! $seoName || $seoName === '') return '<div style="background-color: red; color: white; padding: 20px;">To display your Wherefrom Score, please set your SEO NAME in the Wherefrom\'s settings within your Admin area.</div>';

		$url = 'https://wherefrom.org/widget/brand/'.esc_attr($seoName).'/banner';

		$logoVariant = get_option('wherefrom_brand_widget_logo_variant', 'black');

		$params = shortcode_atts(array(
			'width' => '100%',
			'height' => '50px',
			'backgroundColor' => 'transparent',
			'logoVariant' => $logoVariant
		), $atts );

		if ($params['logoVariant'] !== 'black') {
			$wfParamsArray = array();

			if ($params['logoVariant'] !== 'black') {
				$wfParamsArray[] = 'logoVariant='.esc_attr($params['logoVariant']);
			}

			$url .= '?'.implode('&', $wfParamsArray);
		}
	
		$wfPluginOutput = '
			<iframe
				class="wherefrom-widget wherefrom-brand-banner wherefrom-brand-'.esc_attr($seoName).'"
				src="'.$url.'"
				frameborder="0"
				scrolling="no"
				allowtransparency="true"
				style="background-color: '.esc_attr($params['backgroundColor']).';"
				width="'.esc_attr($params['width']).'"
				height="'.esc_attr($params['height']).'"
			></iframe>
		';
	
		return $wfPluginOutput;
	}

	public function wherefrom_product_widget( $atts ) {
		$seoName = get_option('wherefrom_seo_name', false );

		$layout = get_option('wherefrom_product_widget_layout', 'vertical');
		$align = get_option('wherefrom_product_widget_align', 'center');
		$logoVariant = get_option('wherefrom_product_widget_logo_variant', 'black');
		$condensed = get_option('wherefrom_product_widget_condensed', false);
		
		$params = shortcode_atts( array(
			'id_type' => 'SID',
			'id' => null,
			'width' => '100%',
			'height' => '130px',
			'backgroundColor' => 'transparent',
			'theme' => 'coloured',
			'layout' => $layout,
			'align' => $align,
			'logoVariant' => $logoVariant,
			'condensed' => $condensed
		), $atts );

		if ($params['layout'] === 'vertical') {
			if ($params['condensed'] === 'condensed') {
				$params['height'] = '87px';
			}else{
				$params['height'] = '150px';
			}
		}
	
		$seoName = get_option('wherefrom_seo_name', false );

		if (! $seoName || $seoName === '') return '<div style="background-color: red; color: white; padding: 20px;">To display your Wherefrom Products Score, please set your SEO NAME in the Wherefrom\'s settings within your Admin area.</div>';

		if (! $params['id'] || $params['id'] === '') return '<div style="background-color: red; color: white; padding: 20px;"><b>No product id found!</b> <br /> Please make sure the shortcode is correctly added to your woocommerce template.</div>';
	
		$url = 'https://wherefrom.org/widget/'.esc_attr($seoName).'/embed/'.esc_attr($params['id']);
		
		if ($params['id_type'] === 'WFID') {
			$url .= '/WFID';
		} else if ($params['id_type'] === 'SID') {
			$url .= '/SID';
		}

		$wfParamsArray = array();

		$wfParamsArray[] = 'align='.esc_attr($params['align']);

		if ($params['theme'] !== 'coloured') {
			$wfParamsArray[] = 'theme='.esc_attr($params['theme']);
		}

		if ($params['layout'] !== 'horizontal') {
			$wfParamsArray[] = 'layout='.esc_attr($params['layout']);
		}

		if ($params['logoVariant'] !== 'black') {
			$wfParamsArray[] = 'logoVariant='.esc_attr($params['logoVariant']);
		}

		if ($params['condensed'] !== 'default') {
			$wfParamsArray[] = 'condensed=true';
		}

		$url .= '?'.implode('&', $wfParamsArray);

		$wfPluginOutput = '
			<iframe
				class="wherefrom-widget wherefrom-product-widget"
				src="'.$url.'"
				frameborder="0"
				scrolling="no"
				allowtransparency="true"
				style="background-color: transparent;"
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