<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Block_Adminhtml_UserSettings_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

    /**
     * @return Nortoneo_TwoFactorAuth_Model_UserSettings
     */
    protected function getUserSettingsModel()
    {
        return Mage::helper('nortoneo_twofactorauth')->getCurrentUserSettingsModel();
    }

    protected function _prepareForm()
    {
        $helper = Mage::helper('nortoneo_twofactorauth');
        $userSettingsModel = $this->getUserSettingsModel();
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('nortoneo_twofactorauth_usersettings', array('legend' => $helper->__('Two Factor Authentication Settings')));

        $fieldset->addField('is_active', 'select', array(
            'label'    => $helper->__('Status'),
            'title'    => $helper->__('Status'),
            'name'     => 'is_active',
            'required' => true,
            'options'  => array(0 => $helper->__('Disabled'), 1 => $helper->__('Enabled')),
        ));

        $fieldset->addField('trust_last_ip', 'select', array(
            'label'    => $helper->__('Trust last IP address'),
            'title'    => $helper->__('Trust last IP address'),
            'name'     => 'trust_last_ip',
            'required' => true,
            'options'  => $userSettingsModel->getTrustLastIpOptions(),
            'note'     => $helper->__('Ask for code only if IP has changed since last successful login?'),
        ));

        $fieldset->addField('discrepancy', 'select', array(
            'label'    => $helper->__('Discrepancy'),
            'title'    => $helper->__('Discrepancy'),
            'name'     => 'discrepancy',
            'required' => true,
            'options'  => $userSettingsModel->getDiscrepancyOptions(),
            'note'     => $helper->__('How long your code should be valid.'),
        ));

        $fieldset->addField('method', 'select', array(
            'label'    => $helper->__('Authentication Method'),
            'title'    => $helper->__('Authentication Method'),
            'name'     => 'method',
            'required' => true,
            'options'  => $userSettingsModel->getMethodOptions(),
            'style'    => 'width:400px;',
        ));

        $form->setValues($userSettingsModel);

        return parent::_prepareForm();
    }
}
