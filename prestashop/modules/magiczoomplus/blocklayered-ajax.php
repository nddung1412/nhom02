<?php

chdir(dirname(__FILE__).'/../blocklayered');

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$magiczoomplusInstance = Module::getInstanceByName('magiczoomplus');

if($magiczoomplusInstance && $magiczoomplusInstance->active) {
    $magiczoomplusTool = $magiczoomplusInstance->loadTool();
    $magiczoomplusFilter = 'parseTemplate'.($magiczoomplusTool->type == 'standard' ? 'Standard' : 'Category');
    if($magiczoomplusInstance->isSmarty3) {
        //Smarty v3 template engine
        $smarty->registerFilter('output', array($magiczoomplusInstance, $magiczoomplusFilter));
    } else {
        //Smarty v2 template engine
        $smarty->register_outputfilter(array($magiczoomplusInstance, $magiczoomplusFilter));
    }
    if(!isset($GLOBALS['magictoolbox']['filters'])) {
        $GLOBALS['magictoolbox']['filters'] = array();
    }
    $GLOBALS['magictoolbox']['filters']['magiczoomplus'] = $magiczoomplusFilter;
}

include(dirname(__FILE__).'/../blocklayered/blocklayered.php');

Context::getContext()->controller->php_self = 'category';
$blockLayered = new BlockLayered();
echo $blockLayered->ajaxCall();
