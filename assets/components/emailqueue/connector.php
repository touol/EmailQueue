<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var EmailQueue $EmailQueue */
$EmailQueue = $modx->getService('emailqueue', 'EmailQueue', $modx->getOption('emailqueue_core_path', null,
        $modx->getOption('core_path') . 'components/emailqueue/') . 'model/emailqueue/'
);
$modx->lexicon->load('emailqueue:default');

// handle request
$corePath = $modx->getOption('emailqueue_core_path', null, $modx->getOption('core_path') . 'components/emailqueue/');
$path = $modx->getOption('processorsPath', $EmailQueue->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));