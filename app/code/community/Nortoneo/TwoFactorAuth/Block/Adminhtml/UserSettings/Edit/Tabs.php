<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Block_Adminhtml_UserSettings_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('userSettings_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('nortoneo_twofactorauth')->__('Settings'));
    }

    protected function _beforeToHtml()
    {
        $settingsFormBlock = $this->getLayout()->createBlock('nortoneo_twofactorauth/adminhtml_userSettings_edit_tab_form');
        $settingsFormBlock->setTemplate('nortoneo/twofactorauth/usersettings.phtml');

        $this->addTab('form_section', array(
            'label'   => Mage::helper('nortoneo_twofactorauth')->__('User Settings'),
            'title'   => Mage::helper('nortoneo_twofactorauth')->__('User Settings'),
            'content' => $settingsFormBlock->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
