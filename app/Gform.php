<?php

namespace App;

class Gform {

    private $form_normal;
    private $form_refined;
    private $form_values;
    public $form_types_orange = array(
        'text', 'email', 'password', 'hidden', 'url', 'auto_complete', 'number',
        'checkbox', 'radio', 'select', 'select-multiple', 'boolean_checkbox',
        'textarea', 'date', 'file', 'ckeditor', 'autocomplete', 'min_max', 'ng_location_tag', 'I_agree');
    public $form_types_green = array('heading', 'paragraph', 'clearfix', 'country-state-city');

    function __construct() {
        
    }

    function set_form($form, $form_values = null) {


        //$this->form_normal = $this->get_sample_form();
        $this->form_normal = $form;
        $this->form_name = $this->form_normal['form_name'];
        $this->form_values = $form_values;
        $this->form_refined = $this->prepare_form();



        return $this->form_refined;
    }

    function prepare_form() {
        $form = $this->form_normal;
        $new_fields = array();
        $count = 1000;

        foreach ($form['fields'] as $field) {

            if (in_array($field['type'], $this->form_types_orange)) {
                $key = $field['name'];
                $new_fields[$key] = $field;
                $new_fields[$key]['id'] = $this->form_name . '_' . $key;

                $new_fields[$key]['placeholder'] = (isset($field['placeholder'])) ? $field['placeholder'] : '';
                $new_fields[$key]['value'] = $this->get_field_value($field);
            }

            if (in_array($field['type'], $this->form_types_green)) {
                $key = $field['type'] . '_' . $count++;
                $new_fields[$key] = $field;
                $new_fields[$key]['id'] = $this->form_name . '_' . $key;
            }
        }
        $form['fields'] = $new_fields;

        $form['submit'] = (isset($form['submit'])) ? $form['submit'] : 'Submit';

        return $form;
    }

    function get_field_value($field) {

        $field_value = '';
        $key = $field['name'];

        if ($field['type'] == 'hidden') {
            return $field['value'];
        }


        if (!empty($this->form_values)) {

            if (isset($this->form_values[$key])) {
                $field_value = $this->form_values[$key];
            } else {
                $field_value = '';
            }
        } elseif (isset($field['default'])) {
            $field_value = $field['default'];
        } else {
            $field_value = '';
        }

        if ($field['type'] == 'checkbox' && $field_value == '') {
            $field_value = array();
        }
        if ($field['type'] == 'select-multiple' && $field_value == '') {
            $field_value = array();
        }

        return $field_value;
    }

    function make_list($form) {

        $list = array();
        foreach ($form['fields'] as $field) {

            if ($field['type'] == 'hidden') {
                continue;
            }

            $dot['label'] = $field['label'];

            if (!isset($field['value'])) {
                if (isset($field['options'])) {
                    $dot['value'] = array();
                } else {
                    $dot['value'] = '';
                }
            } else {
                if (isset($field['options'])) {
                    if (is_array($field['value'])) {
                        // Checkbox or multiple select
                        $opt_val = array();
                        foreach ($field['value'] as $val) {
                            if (isset($field['options'][$val])) {
                                $opt_val[] = $field['options'][$val];
                            }
                        }
                        $dot['value'] = implode(', ', $opt_val);
                    } else {
                        // non-multiple-select or radio
                        $dot['value'] = (isset($field['options'][$field['value']])) ? $field['options'][$field['value']] : '';
                    }
                } else {
                    $dot['value'] = $field['value'];
                }
            }

            $list[$field['name']] = $dot;
        }

        return $list;
    }

    /**
     * 
     * Remove all non required fields from the array
     * @param array $fields
     * @param array $not_required_fields
     * @return array
     */
    function set_not_required_fields($fields, $not_required_fields) {
        $not_required_fields = array_fill_keys($not_required_fields, '');
        return array_diff_key($fields, $not_required_fields);
    }

    /**
     * 
     * Generate a new array with only required fields
     * @param array $fields
     * @param array $required_fields
     * @return array
     */
    function set_required_fields($fields, $required_fields) {
        $result = array();
        foreach ($required_fields as $value) {
            if (isset($fields[$value])) {
                $result[$value] = $fields[$value];
            }
        }
        return $result;
    }

    function get_sample_form() {

        return array(
            'form_name' => 'sample_form',
            'submit' => 'Save',
            'fields' => array(
                array(
                    'type' => 'heading',
                    'content' => 'Register Form',
                    'tag' => 'h4',
                    'description' => 'Sample heading content',
                ),
                array(
                    'type' => 'paragraph',
                    'content' => 'Lorem ipsum dolor',
                ),
                array(
                    'type' => 'text',
                    'label' => 'Name',
                    'name' => 'full_name',
                ),
                array(
                    'type' => 'email',
                    'label' => 'Email',
                    'name' => 'email',
                    'placeholder' => 'Enter your email address',
                ),
                array(
                    'type' => 'select',
                    'label' => 'Category',
                    'name' => 'category',
                    'options' => array(
                        'red' => 'Acting',
                        'dancer' => 'Dancer',
                        'singer' => 'Singer',
                        'anchor' => 'Anchor',
                    ),
                    'default' => 'singer'
                ),
                array(
                    'type' => 'radio',
                    'label' => 'Gender',
                    'name' => 'gender',
                    'options' => array(
                        'male' => 'Male',
                        'female' => 'Female',
                    ),
                    'default' => 'male'
                ),
                array(
                    'type' => 'checkbox',
                    'label' => 'Hobbies',
                    'name' => 'hobbies',
                    'options' => array(
                        'swimming' => 'Swimming',
                        'reading' => 'Reading',
                        'traveling' => 'Traveling',
                    ),
                    'default' => array(
                        'swimming', 'reading'
                    ),
                ),
            ),
        );
    }

}
