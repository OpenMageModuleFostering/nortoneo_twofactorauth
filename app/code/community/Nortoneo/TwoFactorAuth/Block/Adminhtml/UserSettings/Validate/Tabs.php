<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Block_Adminhtml_UserSettings_Validate_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('userSettings_validate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('nortoneo_twofactorauth')->__('Settings'));
    }

    protected function _beforeToHtml()
    {
        $settingsFormBlock = $this->getLayout()->createBlock('nortoneo_twofactorauth/adminhtml_userSettings_validate_tab_form');

        $this->addTab('form_section', array(
            'label'   => Mage::helper('nortoneo_twofactorauth')->__('Code validation'),
            'title'   => Mage::helper('nortoneo_twofactorauth')->__('Code validation'),
            'content' => $settingsFormBlock->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
