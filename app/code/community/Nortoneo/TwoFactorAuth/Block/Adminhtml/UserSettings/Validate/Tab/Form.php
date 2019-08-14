<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Block_Adminhtml_UserSettings_Validate_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('nortoneo_twofactorauth')->__('User Settings');
    }

    /**
     * Prepare title for tab
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $helper = Mage::helper('nortoneo_twofactorauth');
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('nortoneo_twofactorauth_usersettings', array('legend' => $helper->__('Two Factor Authentication Validation')));

        $fieldset->addField('code', 'text', array(
            'label'    => $helper->__('Authentication Code'),
            'title'    => $helper->__('Authentication Code'),
            'name'     => 'code',
            'required' => true,
        ));

        return parent::_prepareForm();
    }
}
