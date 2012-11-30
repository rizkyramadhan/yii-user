<?php

class UWfile {

    /**
     * @var array
     * @name widget parametrs
     */
    public $params = array('path' => 'assets');

    /**
     * Widget initialization
     * @return array
     */
    public function init() {
        return array(
            'name' => __CLASS__,
            'label' => UserModule::t('File field'),
            'fieldType' => array('VARCHAR'),
            'params' => $this->params,
            'paramsLabels' => array(
                'path' => UserModule::t('Upload path'),
            ),
            'other_validator' => array(
                'file' => array(
                    'allowEmpty' => array('', 'false', 'true'),
                    'maxFiles' => '',
                    'maxSize' => '',
                    'minSize' => '',
                    'tooLarge' => '',
                    'tooMany' => '',
                    'tooSmall' => '',
                    'types' => '',
                    'wrongType' => '',
                ),
            ),
        );
    }

    private $style = 'height:130px';

    private function isImage($file) {
        $f = explode(".", $file);
        $ext = $f[count($f) - 1];
        if ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext = "png")
            return true;
        else
            return false;
    }

    /**
     * @param $value
     * @param $model
     * @param $field_varname
     * @return string
     */
    public function setAttributes($value, $model, $field_varname) {
        $value = CUploadedFile::getInstance($model, $field_varname);

        if ($value) {
            $old_file = $model->getAttribute($field_varname);
            $file_name = $this->params['path'] . '/' . $value->name;
            if (file_exists($file_name)) {
                $file_name = str_replace('.' . $value->extensionName, '-' . time() . '.' . $value->extensionName, $file_name);
            }
            $model->validate();
            $errors = $model->getErrors();
            if (!isset($errors[$field_varname])) {
                if ($old_file && file_exists($old_file))
                    unlink($old_file);
                $value->saveAs($file_name);
            }

            $value = $file_name;
        } else {
            if (isset($_POST[get_class($model)]['uwfdel'][$field_varname]) && $_POST[get_class($model)]['uwfdel'][$field_varname]) {
                $old_file = $model->getAttribute($field_varname);
                if ($old_file && file_exists($old_file))
                    unlink($old_file);
                $value = '';
            } else {
                $value = $model->getAttribute($field_varname);
            }
        }
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    public function viewAttribute($model, $field) {
        $file = $model->getAttribute($field->varname);
        if ($file) {
            $file = Yii::app()->baseUrl . '/' . $file;
            if ($this->isImage($file)) {
                return CHtml::link(CHtml::image($file, '', array('style' => $this->style)), $file);
            } else {
                return CHtml::link($file, $file);
            }
        } else
            return '';
    }

    /**
     * @param $value
     * @return string
     */
    public function editAttribute($model, $field, $params = array()) {
        if (!isset($params['options']))
            $params['options'] = array();
        unset($params['options']);

        $file = Yii::app()->request->baseUrl . '/' . $model->getAttribute($field->varname);
        
        /*
        $delete = CHtml::activeCheckBox($model, '[uwfdel]' . $field->varname, $params)
                . ' ' .
                CHtml::activeLabelEx($model, '[uwfdel]' . $field->varname, array('label' => UserModule::t('Delete file'), 'style' => 'display:inline;'));
         * 
         */
        
        return CHtml::activeFileField($model, $field->varname, $params)
                . (($model->getAttribute($field->varname)) ?
                        '<br/>'
                        . CHtml::link(CHtml::image($file, '', array('style' => $this->style)), $file) : '');
    }

}