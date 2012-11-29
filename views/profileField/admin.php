<?php
$this->breadcrumbs = array(
    UserModule::t('Profile Fields') => array('admin'),
    UserModule::t('Manage'),
);
$this->menu = array(
    array('label' => UserModule::t('Create Profile Field'), 'url' => array('create')),
    array('label' => UserModule::t('Manage Profile Field'), 'url' => array('admin')),
    array('label' => UserModule::t('Manage Users'), 'url' => array('/user/admin')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('profile-field-grid', {
        data: $(this).serialize()
    });
    return false;
});
");
?>
<h1><?php echo UserModule::t('Manage Profile Fields'); ?></h1>

<p><?php echo UserModule::t("You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done."); ?></p>

<?php echo CHtml::link(UserModule::t('Advanced Search'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
    ));
    ?>
</div><!-- search-form -->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            'header' => '# ',
            'value' => '$data->position',
        ),
        array(
            'name' => 'varname',
            'type' => 'raw',
            'value' => 'UHtml::markSearch($data,"varname")',
        ),
        array(
            'name' => 'title',
            'value' => 'UserModule::t($data->title)',
        ),
        array(
            'name' => 'field_type',
            'value' => '$data->field_type',
            'filter' => ProfileField::itemAlias("field_type"),
        ),
        //'field_size_min',
        //'match',
        //'range',
        //'error_message',
        //'other_validator',
        //'default',
        array(
            'name' => 'visible',
            'value' => 'ProfileField::itemAlias("visible",$data->visible)',
            'filter' => ProfileField::itemAlias("visible"),
        ), 'visible_for_role',
        //*/
        array(
            'class' => 'CButtonColumn',
        ),
    ),
));
?>

<?php
Yii::app()->clientScript->registerCoreScript('jquery.ui');
?>
<script type="text/javascript">
    $(function() {
        $( "table.items tbody" ).sortable({ 
            items: 'tr',
            update : function () { 
                var order = [];
                $('table.items tbody tr').each(function(i,item) {
                    order.push($(item).find('td:eq(0)').text() * 1);
                    $(item).find('td:eq(0)').text(i + 1);
                });
                $.get('?update_sort='+order.join(',')+'&ajax=yw0');
            } 
        });
        $("#yw0_c0").hide();
        $( "table.items tbody").attr('style','cursor:pointer');
    });
</script>