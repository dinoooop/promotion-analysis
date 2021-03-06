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
            case 'url':

                
                $description = (isset($description) && $description != '') ? $description : '';
                $placeholder = (isset($placeholder) && $placeholder != '') ? $placeholder : '';
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
                        placeholder="<?php echo $placeholder ?>">
                    <p class="help-description"><?php echo $description; ?></p>
                </div>
                <?php echo '</div>'; ?>

                <?php
                break;

            case 'textarea':

                $description = (isset($description) && $description != '') ? $description : '';
                $placeholder = (isset($placeholder) && $placeholder != '') ? $placeholder : '';
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
                <p class="help-description"><?php echo $description; ?></p>
                <?php echo '</div>'; ?>

                <?php
                break;



            case 'select':

                
                if (!isset($options)) {
                    return false;
                }
                $description = (isset($description) && $description != '') ? $description : '';
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
                    <p class="help-description"><?php echo $description; ?></p>
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
                    <p class="help-description"><?php echo $description; ?></p>
                </div>
                <?php echo '</div>'; ?>
                <?php
                break;

            case 'hidden':
                ?><input type="hidden" id="<?php echo $id ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>"><?php
                break;
            
            case 'file':
                $description = (isset($description) && $description != '') ? $description : '';
                $placeholder = (isset($placeholder)) ? $placeholder : 'Upload';
                echo Temp::upload_file($field);
                ?>
                <?php echo $col; ?>
                <div class="form-group">
                    <label><?php echo $label; ?></label>
                    <p class="help-description"><?php echo $description; ?></p>

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

}
