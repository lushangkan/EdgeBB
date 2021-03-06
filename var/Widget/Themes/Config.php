<?php
if (!defined('__EDGE_ROOT_DIR__')) exit;
/**
 * EdgeBB Forum
 *
 * @copyright  Copyright (c) 2020 Edge team (http://www.mcedge.ink)
 * @license    MIT License
 * @version    $Id$
 */

/**
 * 皮肤配置组件
 *
 * @author qining
 * @category edge
 * @package Widget
 * @copyright Copyright (c) 2020 Edge team (http://www.mcedge.ink)
 * @license MIT License
 */
class Widget_Themes_Config extends Widget_Abstract_Options
{
    /**
     * 绑定动作
     *
     * @access public
     * @return void
     * @throws Edge_Widget_Exception
     */
    public function execute()
    {
        $this->user->pass('administrator');
        
        if (!self::isExists()) {
            throw new Edge_Widget_Exception(_t('外观配置功能不存在'), 404);
        }
    }

    /**
     * 配置功能是否存在
     * 
     * @access public
     * @return boolean
     */
    public static function isExists()
    {
        $options = Edge_Widget::widget('Widget_Options');
        $configFile = $options->themeFile($options->theme, 'functions.php');

        if (file_exists($configFile)) {
            require_once $configFile;
            
            if (function_exists('themeConfig')) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 配置外观
     *
     * @access public
     * @return Edge_Widget_Helper_Form
     */
    public function config()
    {
        $form = new Edge_Widget_Helper_Form($this->security->getIndex('/action/themes-edit?config'),
            Edge_Widget_Helper_Form::POST_METHOD);
        themeConfig($form);
        $inputs = $form->getInputs();
        
        if (!empty($inputs)) {
            foreach ($inputs as $key => $val) {
                $form->getInput($key)->value($this->options->{$key});
            }
        }

        $submit = new Edge_Widget_Helper_Form_Element_Submit(NULL, NULL, _t('保存设置'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);
        return $form;
    }
}
