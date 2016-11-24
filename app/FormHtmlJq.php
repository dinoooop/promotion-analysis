<?php

namespace App;

class FormHtmlJq {

    private $gform;
    private $form;

    function __construct() {
        $this->gform = new Gform();
    }

    function create_form($form) {

        $this->form = $form;

        $html = '';

        foreach ($this->form['fields'] as $key => $field) {
            $html .= $this->form_group_html($field);
        }

        $html .= $this->get_submit_button();

        return $html;
    }

    function form_group_html($field) {

        extract($field);

        ob_start();

        $col = (isset($col) && $col == 6) ? '<div class="col-sm-6 col-md-6">' : '<div class="col-sm-12 col-md-12">';

        switch ($type):


            case 'email':
            case 'password':
            case 'phone':
            case 'text':
            case 'number':
            case 'url':



                //For number
                $step = (isset($step) && $step != '') ? ' step="' . $step . '" ' : '';
                ?>

                <?php echo $col; ?>
                <div class="form-group">

                    <label class="control-label" for="<?php echo $id ?>"><?php echo $label ?></label>
                    <input 
                        id="<?php echo $id ?>"
                        type="<?php echo $type; ?>" 
                        name="<?php echo $name; ?>" 
                        class="form-control"
                        value="<?php echo $value; ?>"
                        placeholder="<?php echo $placeholder ?>"
                        <?php echo $step; ?>>

                    <?php if ($description != ''): ?>
                        <p class="help-description"><?php echo $description; ?></p>
                    <?php endif; ?>
                </div>
                <?php echo '</div>'; ?>

                <?php
                break;

            case 'textarea':
                ?>

                <?php echo $col; ?>
                <div class="form-group">

                    <label class="control-label" for="<?php echo $id ?>"><?php echo $label ?></label>
                    <textarea 
                        id="<?php echo $id ?>"
                        type="<?php echo $type; ?>" 
                        name="<?php echo $name; ?>" 
                        class="form-control" 
                        placeholder="<?php echo $placeholder ?>"><?php echo $value; ?></textarea>
                </div>
                <?php if ($description != ''): ?>
                    <p class="help-description"><?php echo $description; ?></p>
                <?php endif; ?>
                <?php echo '</div>'; ?>

                <?php
                break;



            case 'select':


                if (!isset($options)) {
                    return false;
                }
                ?>
                <?php echo $col; ?>
                <div class="form-group">
                    <label class="control-label" for="<?php echo $id ?>"><?php echo $label ?></label>
                    <select class="form-control selectpicker" name="<?php echo $name; ?>" id="<?php echo $id ?>">
                        <?php
                        foreach ($options as $key => $option_value):
                            $selected = ($key == $value) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>>
                                <?php echo $option_value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($description != ''): ?><p class="help-description"><?php echo $description; ?></p><?php endif; ?>
                </div>
                <?php echo '</div>'; ?>
                <?php
                break;

            case 'select-multiple':


                $description = (isset($description) && $description != '') ? $description : '';
                ?>
                <?php echo $col; ?>
                <div class="form-group">

                    <select class="selectpicker" multiple 
                            name="<?php echo $name; ?>"
                            title="<?php echo $label; ?>">
                                <?php
                                foreach ($options as $key => $option_value):
                                    $selected = ($key == $value) ? 'selected' : '';
                                    ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>>
                                <?php echo $option_value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($description != ''): ?><p class="help-description"><?php echo $description; ?></p><?php endif; ?>
                </div>
                <?php echo '</div>'; ?>
                <?php
                break;

            case 'boolean_checkbox':
                $checked = (isset($value) && $value == 1) ? 'checked' : '';
                ?>
                <?php echo $col; ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox"  value="1" name="<?php echo $name; ?>" <?php echo $checked; ?>> <?php echo $label ?>
                    </label>
                </div>
                <?php echo '</div>'; ?>
                <?php
                break;

            case 'hidden':
                ?><input type="hidden" id="<?php echo $id ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>"><?php
                break;

            case 'file':
                
                $placeholder = ($placeholder != '') ? $placeholder : 'Upload';
                echo Temp::upload_file($field);
                ?>
                <?php echo $col; ?>
                <div class="form-group">
                    <label><?php echo $label; ?></label>
                    <?php if ($description != ''): ?><p class="help-description"><?php echo $description; ?></p><?php endif; ?>

                    <a href="#" class="btn btn-default" data-toggle="modal" data-target="#<?php echo $id; ?>"><i class="fa fa-picture-o"></i>&nbsp; <?php echo $placeholder; ?></a>
                    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
                    <br>
                    <div class="upload-file-preview">

                        <?php if (isset($value) && $value != ''): ?>
                            <?php echo Temp::get_media_html($value); ?>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
                <?php
                break;

            case 'clearfix':
                echo '<div class="clearfix"></div>';
                break;

            case 'date':
                $value = (!isset($value) || $value == '') ? date('m/d/Y') : date('m/d/Y', strtotime($value));
                
                ?>
                <?php echo $col; ?>
                <div class="form-group">
                    <label><?php echo $label; ?></label>
                    <p class="help-description"><?php echo $description; ?></p>
                    <input type="text" name="<?php echo $name; ?>" class="form-control" value="<?php echo $value; ?>" />
                </div>
                <?php echo '</div>'; ?>

                <script type="text/javascript">
                    $(function () {
                        $('input[name="<?php echo $name; ?>"]').daterangepicker({
                            singleDatePicker: true,
                            showDropdowns: true
                        },
                                function (start, end, label) {
                                    var years = moment().diff(start, 'years');
                                });
                    });
                </script>
                <?php
                break;

            case 'auto_complete':
                $list = $this->create_array_string($list)
                ?>
                <?php echo $col; ?>
                <div class="form-group">
                    <label><?php echo $label; ?></label>
                    <input id="<?php echo $id ?>" class="form-control" value="<?php echo $value; ?>" name="<?php echo $name; ?>">
                </div>
                <script>
                    $(function () {
                        var availableTags = [<?php echo $list; ?>];
                        $("#<?php echo $id ?>").autocomplete({
                            source: availableTags
                        });
                    });
                </script>
                <?php echo '</div>'; ?>
                <?php
                break;

        endswitch;
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    function get_submit_button() {
        $label = (isset($this->form['submit'])) ? $this->form['submit'] : 'Submit';
        ob_start();
        ?>
        <div class="col-sm-12 col-md-12"> 
            <button type="submit" class="btn btn-primary"><?php echo $label; ?></button>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * 
     * Create comma separated list from array
     * e.g 'amazone', 'flipkart', 'walmart'
     * @param array $array single dimensional array
     * @return string
     */
    function create_array_string($array) {
        $new = [];
        foreach ($array as $value) {
            $new[] = "\"{$value}\"";
        }

        return implode(', ', $new);
    }

}
