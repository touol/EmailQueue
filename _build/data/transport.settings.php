<?php
/** @var modX $modx */
/** @var array $sources */

$settings = array();

$tmp = array(
    'limit' => array(
        'xtype' => 'textfield',
        'value' => '50',
        'area' => 'emailqueue_main',
    ),
	'store_days' => array(
        'xtype' => 'textfield',
        'value' => '30',
        'area' => 'emailqueue_main',
    ),
);

foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'emailqueue_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}
unset($tmp);

return $settings;
