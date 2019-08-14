<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Block_Adminhtml_UserSettings_Validate extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'nortoneo_twofactorauth';
        $this->_controller = 'adminhtml_userSettings';
        $this->_mode = 'validate';

        parent::__construct();
        $this->removeButton('reset');
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/validatePost');
    }

    public function getHeaderText()
    {
        return Mage::helper('nortoneo_twofactorauth')->__('Two Factor Authentication Settings');
    }
}
