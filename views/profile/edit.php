
<?php
ob_start();include('tabs.css');$css = ob_get_clean();
ob_start();include('tabs.js');$js = ob_get_clean();
Yii::app()->getClientScript()->registerCoreScript('jquery');
Yii::app()->getClientScript()->registerScript('user-tabs', $js);
Yii::app()->getClientScript()->registerCss('user-tabs', $css);
?>

<?php
$this->pageTitle = Yii::app()->name . ' - ' . UserModule::t("Profile");
$this->breadcrumbs = array(
    UserModule::t("Profile") => array('profile'),
    UserModule::t("Edit"),
);
$this->menu = array(
    ((UserModule::isAdmin()) ? array('label' => UserModule::t('Manage Users'), 'url' => array('/user/admin')) : array()),
    array('label' => UserModule::t('Profile'), 'url' => array('/user/profile')),
    array('label' => UserModule::t('Change password'), 'url' => array('changepassword')),
    array('label' => UserModule::t('Logout'), 'url' => array('/user/logout')),
);
?><h1><?php echo UserModule::t('Edit Profile'); ?></h1>

<?php if (Yii::app()->user->hasFlash('profileMessage')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('profileMessage'); ?>
    </div>
<?php endif; ?>
<div class="form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'profile-form',
        'enableAjaxValidation' => true,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
            ));
    ?>

    <?php echo $form->errorSummary(array($model, $profile)); ?>

    <?php
    $profileFields = $profile->getFields();
    $profileGroups = Profile::extractGroups($profileFields);
    ?>

    <div class="tabs"> 
        <ul class="tabs-nav"> 
            <?php foreach ($profileGroups as $k => $g): ?>
                <li <?php if ($k == 0): ?>class='active'<?php endif; ?>>
                    <a href="#tab_<?php echo $g; ?>">
                        <?php echo str_replace('_', ' ', $g); ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li><a href="#tab_Login_Information">Login Information</a></li>
            <div class="clearfix"></div>
        </ul>
        <div class="tabs-container">
            <?php
            foreach ($profileGroups as $k => $g):
                $subgroup = Profile::extractSubGroups($profileFields, $g);
                ?>
                <div id="tab_<?php echo $g; ?>" class="tabs-content <?php if ($k == 0): ?>active<?php endif; ?>">
                    <?php
                    $es = array();
                    foreach ($profileFields as $field) {
                        foreach ($subgroup as $s) {
                            if ($field->group == $g && $field->subgroup == $s) {
                                ?>


                                <?php
                                if (!in_array($field->subgroup, $es)) {
                                    $es[] = $field->subgroup;
                                    if ($s != ""):
                                        ?>
                                        <div class="tabs-row-outer">
                                            <div class="tabs-row-inner">
                                                <?php echo $s; ?>
                                            </div>
                                        </div>
                                        <?php
                                    endif;
                                }
                                ?>

                                <div class="row">
                                    <?php
                                    echo $form->labelEx($profile, $field->varname);
                                    $widgetEdit = $field->widgetEdit($profile);
                                    if ($widgetEdit) {
                                        echo $widgetEdit;
                                    } elseif ($field->range) {
                                        echo $form->dropDownList($profile, $field->varname, Profile::range($field->range));
                                    } elseif ($field->field_type == "TEXT") {
                                        echo $form->textArea($profile, $field->varname, array('rows' => 6, 'cols' => 50));
                                    } else {
                                        echo $form->textField($profile, $field->varname, array('size' => 60, 'maxlength' => (($field->field_size) ? $field->field_size : 255)));
                                    }
                                    echo $form->error($profile, $field->varname);
                                    ?>
                                </div>	
                                <?php
                            }
                        }
                    }
                    ?>

                </div>
            <?php endforeach; ?>
            <div id="tab_Login_Information" class="tabs-content">

                <?php if (!Yii::app()->getModule('user')->disableUsername): ?>
                    <tr>
                        <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?></th>
                        <td><?php echo CHtml::encode($model->username); ?></td>
                    </tr>
                <?php endif; ?>
                <div class="row">
                    <?php echo $form->labelEx($model, 'email'); ?>
                    <?php echo $form->textField($model, 'email', array('size' => 60, 'maxlength' => 128)); ?>
                    <?php echo $form->error($model, 'email'); ?>
                    <div class="hint">Your e-mail is used for login to <?php echo Yii::app()->name; ?>.</div><br/>
                </div>

                <div class="row">
                    <?php echo $form->labelEx($model, 'password'); ?>
                    <?php echo Chtml::link('Click here to change', array('changepassword')); ?>
                    <br/><br/>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $tab = window.location.hash;
            if ($tab != "") {
                $('.tabs-nav li.active').removeClass('active');
                $('.tabs-content.active').removeClass('active');
	
                $("a[href="+$tab+"]").parent().addClass('active');
                $($tab).addClass('active');
            }
            $('.tabs-nav li a').click(function() {
                $('.tabs-nav li.active').removeClass('active');
                $('.tabs-content.active').removeClass('active');
	
                $(this).parent().addClass('active');
                $($(this).attr('href')).addClass('active');
	 
                window.location.hash = $(this).attr('href');
                $("html").scrollTop(0);
	
                return false;
            });
        });
    </script>

    <div class="row buttons tabs-button">
        <?php echo CHtml::submitButton($model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
