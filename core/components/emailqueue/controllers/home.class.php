<?php

/**
 * The home manager controller for EmailQueue.
 *
 */
class EmailQueueHomeManagerController extends modExtraManagerController
{
    /** @var EmailQueue $EmailQueue */
    public $EmailQueue;


    /**
     *
     */
    public function initialize()
    {
        $path = $this->modx->getOption('emailqueue_core_path', null,
                $this->modx->getOption('core_path') . 'components/emailqueue/') . 'model/emailqueue/';
        $this->EmailQueue = $this->modx->getService('emailqueue', 'EmailQueue', $path);
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('emailqueue:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('emailqueue');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->EmailQueue->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->EmailQueue->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->EmailQueue->config['jsUrl'] . 'mgr/emailqueue.js');
        $this->addJavascript($this->EmailQueue->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->EmailQueue->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->EmailQueue->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->EmailQueue->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->EmailQueue->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->EmailQueue->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        EmailQueue.config = ' . json_encode($this->EmailQueue->config) . ';
        EmailQueue.config.connector_url = "' . $this->EmailQueue->config['connectorUrl'] . '";
        Ext.onReady(function() {
            MODx.load({ xtype: "emailqueue-page-home"});
        });
        </script>
        ');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->EmailQueue->config['templatesPath'] . 'home.tpl';
    }
}