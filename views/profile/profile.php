
<?php
ob_start(); include('tabs.css'); $css = ob_get_clean();
Yii::app()->getClientScript()->registerCoreScript('jquery');
Yii::app()->getClientScript()->registerCss('user-tabs', $css);
?>

<?php
$this->pageTitle = Yii::app()->name . ' - ' . UserModule::t("Profile");
$this->breadcrumbs = array(
    UserModule::t("Profile"),
);
$this->menu = array(
    ((UserModule::isAdmin()) ? array('label' => UserModule::t('Manage Users'), 'url' => array('/user/admin')) : array()),
    array('label' => UserModule::t('Edit'), 'url' => array('edit')),
    array('label' => UserModule::t('Change password'), 'url' => array('changepassword')),
    array('label' => UserModule::t('Logout'), 'url' => array('/user/logout')),
);
?><h1><?php echo UserModule::t('Your profile'); ?></h1>

<?php if (Yii::app()->user->hasFlash('profileMessage')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('profileMessage'); ?>
    </div>
<?php endif; ?>

<?php
$profileFields = ProfileField::model()->forRole()->forOwner()->sort()->findAll();
$profileGroups = Profile::extractGroups($profileFields);
?>

<div class="tabs"> 
    <ul class="tabs-nav"> 
        <?php foreach ($profileGroups as $k => $g): ?>
            <li <?php if ($k == 0): ?>class='active'<?php endif; ?>><a href="#tab_<?php echo $g; ?>"><?php echo $g; ?></a></li>
        <?php endforeach; ?>
        <li><a href="#tab_Activity">Activity</a></li>
        <div class="clearfix"></div>
    </ul>
    <div class="tabs-container">
        <?php
        foreach ($profileGroups as $k => $g):
            $subgroup = Profile::extractSubGroups($profileFields, $g);
            ?>
            <div id="tab_<?php echo $g; ?>" class="tabs-content <?php if ($k == 0): ?>active<?php endif; ?>">
                <table class="dataGrid">
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
                                        <tr>
                                            <td colspan="2">
                                                <div class="tabs-row-outer">
                                                    <div class="tabs-row-inner">
                                                        <?php echo $s; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    endif;
                                }
                                ?>
                                <tr>
                                    <th class="label">
                                        <?php echo CHtml::encode(UserModule::t($field->title)); ?>
                                    </th>
                                    <td>
                                        <?php echo (($field->widgetView($profile)) ? $field->widgetView($profile) : CHtml::encode((($field->range) ? Profile::range($field->range, $profile->getAttribute($field->varname)) : $profile->getAttribute($field->varname)))); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>

                    <?php if ($k == 0): ?>

                        <tr>
                            <td colspan="2">
                                <div class="tabs-row-outer">
                                    <div class="tabs-row-inner">
                                        Site Information
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php if (!Yii::app()->getModule('user')->disableUsername): ?>
                            <tr>
                                <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?></th>
                                <td><?php echo CHtml::encode($model->username); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th class="label" style="width:150px;"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?></th>
                            <td><?php echo CHtml::encode($model->email); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        <?php endforeach; ?>
        <div id="tab_Activity" class="tabs-content">
            <table class="dataGrid">
                <tr>
                    <th class="label" colspan="2"> &mdash; User Activity &mdash;</th>
                </tr>
                <tr>
                    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('createtime')); ?></th>
                    <td><?php echo date("d.m.Y H:i:s", $model->createtime); ?></td>
                </tr>
                <tr>
                    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('lastvisit')); ?></th>
                    <td><?php echo date("d.m.Y H:i:s", $model->lastvisit); ?></td>
                </tr>
                <tr>
                    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></th>
                    <td><?php echo CHtml::encode(User::itemAlias("UserStatus", $model->status)); ?></td>
                </tr>
            </table>
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
