<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Block_Adminhtml_UserSettings_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'nortoneo_twofactorauth';
        $this->_controller = 'adminhtml_userSettings';
        $this->_mode = 'edit';

        $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
            Mage::helper('core')->__('Are you sure? If you are using mobile app you will have to rescan QR code. For safety reasons you will have to enable protection again.')
        );
        $onclickJs = 'deleteConfirm(\'' . $confirmationMessage . '\', \'' . $this->getUrl('*/*/regenerateSecret') . '\');';
        $this->_addButton('regenerate_secret', array(
            'label'   => Mage::helper('sales')->__('Regenerate secret key'),
            'onclick' => $onclickJs,
        ));

        parent::__construct();
        $this->removeButton('back');
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    public function getHeaderText()
    {
        return Mage::helper('nortoneo_twofactorauth')->__('Two Factor Authentication Settings');
    }
}
