<?php

function wherefrom_create_settings_field($id, $title, $args, $registerOptions = array()) {
  $args["id"] = $id;
  add_settings_field(
    $id,
    $title,
    'wherefrom_render_settings_field',
    'wherefrom_general_settings',
    'wherefrom_general_section',
    $args
  );

  register_setting(
    'wherefrom_general_settings',
    $id,
    $registerOptions
  );
}
/**
 * renders a settings field
 */
function wherefrom_render_settings_field($args) {
  $type = $args['type'];
  $subtype = $args['subtype'];
  $id = $args['id'];
  $disabled = $args['disabled'];
  $prepend_value = $args['prepend_value'];

  $prependStart = (isset($prepend_value)) ? '<div class="input-prepend"> <span class="add-on">'.esc_html($prepend_value).'</span>' : '';
  $prependEnd = (isset($prepend_value)) ? '</div>' : '';

  $value = get_option($id);
  $size = $args["size"];

  $html = '';

  switch ($type) {
    case 'input':

      $min = $args["min"];
      $max = $args["max"];
      $step = $args["step"];

      if($subtype != 'checkbox'){
        if(isset($disabled)){
          // hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
          $html = input([
            a('type', $subtype),
            a('id', $id.(isset($disabled) ? '_disabled' : '')),
            a('name', $id.(isset($disabled) ? '_disabled' : '')),
            a('value', $value),
            a('size', isset($size) ? $size : '30'),
            a('min', $min, true),
            a('max', $max, true),
            a('step', $step, true),
            'disabled'
          ]).input([
            a('type', 'hidden'),
            a('id', $id),
            a('name', $id),
            a('value', $value)
          ]);

        } else {
          $html = input([
            a('type', $subtype),
            a('id', $id),
            a('name', $id),
            a('value', $value),
            a('size', isset($size) ? $size : '30'),
            a('min', $min, true),
            a('max', $max, true),
            a('step', $step, true)
          ]);
        }
      } else {
        $html = input([
          a('type', $subtype),
          a('id', $id),
          a('name', $id),
          a('value', '1'),
          (($value === 1 || $value === '1' || $value === true) ? 'checked' : null)
        ]);
      }
    break;
    case 'select':

      $multiselect = $args["multiselect"];

      if(isset($disabled)){
        // hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
        $html = select([
          a('id', $id.(isset($disabled) ? '_disabled' : '')),
          a('name', $id.(isset($disabled) ? '_disabled' : '')),
          a('size', isset($size) ? $size : null),
          a('value', $value),
          'disabled'
        ]).input([
          a('type', 'hidden'),
          a('id', $id),
          a('name', $id),
          a('value', $value)
        ]);
      } else {
        $optionsList = [];
        if (is_array($args['options'])) {
          foreach($args['options'] as $key => $val) {
            $optionsList[] = option([
              a('value', $key),
              $value === $val || ( isset($multiselect) && is_array($value) && in_array($key, $value) ) ? a('selected', 'selected') : null
            ], $val);
          }
        }
        $html = select([
          a('id', $id),
          a('name', isset($multiselect) ? $id.'[]': $id),
          isset($size) ? a('size', $size) : null,
          a('value', $value),
          isset($multiselect) ? 'multiple' : null
        ], $optionsList);
      }
    break;
    default:
      # code...
    break;
  }

  echo $prependStart.$html.$prependEnd;
}

function a($name, $value, $onlyRenderIfIsSet = false) {
	if ($onlyRenderIfIsSet && !isset($value)) return '';

	return $name.'="'.esc_attr($value).'" ';
}

function input($attributes = array()) {
	return node('input', $attributes);
}

function select($attributes = array(), $options = array()) {
	return node('select', $attributes, $options);
}

function option($attributes = array(), $label) {
	return node('option', $attributes, [$label]);
}

function node($type, $attributes = array(), $children = false) {
	if ($children) return '<'.$type.' '.implode(array_filter($attributes), ' ').'>'.implode(array_filter($children)).'</'.$type.'>';

	return '<'.$type.' '.implode(array_filter($attributes), ' ').'/>';
}
?>