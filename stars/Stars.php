<?php
/**
 * Plugin Name: Star rating.
 * Description: Star rating.
 * Version: 1.0.0
 * Author: Eugene Slepko MID18-01
 */

    

class StarRating
{
    public function __construct()
    {
        $this->name = 'star_rating_field';
        
        $this->label = __('Star Rating', 'star_rating');
        
        $this->category = 'content';

        $this->defaults = array(
            'max_stars'  => 5,
        );
        
        $this->l10n = array(
            'error' => __('Error! Please enter a higher value', 'star_rating'),
        );

        parent::__construct();
    }
    
    public function render_field_settings($field)
    {
        acf_render_field_setting($field, array(
            'label' => __('Maximum Rating', 'star_rating'),
            'instructions' => __('Maximum number of stars', 'star_rating'),
            'type' => 'number',
            'name' => 'max_stars'
        ));

        acf_render_field_setting($field, array(
            'label' => __('Return Type', 'star_rating'),
            'instructions' => __('What should be returned?', 'star_rating'),
            'type' => 'select',
            'layout' => 'horizontal',
            'choices' => array(
                '0'  => __('Number', 'num'),
                '1' => __('List (unstyled)', 'list_u'),
                '2' => __('List (fa-awesome)', 'list_fa'),
            ),
            'name' => 'return_type'
        ));

        acf_render_field_setting($field, array(
            'label' => __('Allow Half Rating', 'star_rating'),
            'instructions' => __('Allow half ratings?', 'star_rating'),
            'type' => 'true_false',
            'name' => 'allow_half'
        ));
    }
    
    public function render_field($field)
    {
        $dir = plugin_dir_url(__FILE__);
   
        $html = '
            <div class="field_type-star_rating_field">%s</div>
            <a href="#clear-stars" class="button button-small clear-button">%s</a>
            <input type="hidden" id="star-rating" data-allow-half="%s" name="%s" value="%s">
        ';
        
        print sprintf(
            $html,
            $this->make_list(
                $field['max_stars'],
                $field['value'],
                '<li><i class="%s star-%d"></i></li>',
                array('fa fa-star-o', 'fa fa-star-half-o', 'fa fa-star'),
                $field['allow_half']
            ),
            __('Clear', 'star_rating'),
            $field['allow_half'],
            esc_attr($field['name']),
            esc_attr($field['value'])
        );
    }
    
    public function input_admin_enqueue_scripts()
    {
        $dir = plugin_dir_url(__FILE__);
        
        wp_enqueue_script('acf-input-star_rating', "{$dir}js/input.js");
        wp_enqueue_style(
            'font-awesome',
            "//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css"
        );
        wp_enqueue_style('acf-input-star_rating', "{$dir}css/input.css");
    }
    public function load_value($value, $post_id, $field)
    {
        return floatval($value);
    }
    
    public function update_value($value, $post_id, $field)
    {
        return floatval($value);
    }
    public function format_value($value, $post_id, $field)
    {
        if (empty($value)) {
            return $value;
        }

        switch ($field['return_type']) {
            case 0:
                return floatval($value);
                break;
            case 1:
                return $this->make_list(
                    $field['max_stars'],
                    $value,
                    '<li class="%s">%d</li>',
                    array('blank', 'half', 'full'),
                    $field['allow_half']
                );
                break;
            case 2:
                $dir = plugin_dir_url(__FILE__);

                wp_enqueue_style('acf-input-star_rating', "{$dir}css/input.css");
                wp_enqueue_style(
                    'font-awesome',
                    "//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css"
                );
                
                $html = '<div class="field_type-star_rating_field">%s</div>';
                
                return sprintf(
                    $html,
                    $this->make_list(
                        $field['max_stars'],
                        $value,
                        '<li><i class="%s star-%d"></i></li>',
                        array('fa fa-star-o', 'fa fa-star-half-o', 'fa fa-star'),
                        $field['allow_half']
                    )
                );
                break;
        }
    }
    
    public function validate_value($valid, $value, $field, $input)
    {
        if ($value > $field['max_stars']) {
            $valid = __('The value is too large!', 'star_rating');
        }
        
        return $valid;
    }
    
    public function load_field($field)
    {
        return $field;
    }

    public function update_field($field)
    {
        return $field;
    }
    
    public function make_list($maxStars = 5, $currentStar = 0, $li = '', $classes = [], $allowHalf = false)
    {
        $html = '<ul class="star-rating">';
        
        for ($index = 1; $index <= $maxStars; $index++) {
            $class = $classes[0];

            if ($index <= $currentStar) {
                $class = $classes[2];
            }

            if ($allowHalf && ($index - .5 == $currentStar)) {
                $class = $classes[1];
            }

            $html .= sprintf($li, $class, $index);
        }
                
        $html .= "</ul>";
        
        return $html;
    }
}

