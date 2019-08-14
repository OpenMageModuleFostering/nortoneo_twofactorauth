<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Adminhtml_LoginController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if ($code = $this->getRequest()->getPost('code')) {
            $this->processCode($code);

            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function resendAction()
    {
        /** @var Nortoneo_TwoFactorAuth_Model_UserSettings $authSettings */
        $authSettings = Mage::helper('nortoneo_twofactorauth')->getCurrentUserSettingsModel();
        $authSettings->provideCurrentCode();

        $this->_redirect('nortoneo_twofactorauth/login/index');
    }

    protected function processCode($code)
    {
        $code = str_replace(' ', '', $code);
        /** @var Nortoneo_TwoFactorAuth_Model_UserSettings $authSettings */
        $authSettings = Mage::helper('nortoneo_twofactorauth')->getCurrentUserSettingsModel();
        $isCodeValid = $authSettings->verifyCode($code);
        if ($isCodeValid) {
            Mage::helper('nortoneo_twofactorauth')->setCurrentUserValidated(true);
            $currentIp = Mage::helper('core/http')->getRemoteAddr();
            $authSettings->setLastIp($currentIp);
            $authSettings->save();
            $this->_redirect('adminhtml');
            return;
        }

        Mage::getSingleton('admin/session')->unsetAll();
        $this->_redirect('adminhtml/index/logout');
    }

    protected function _isAllowed()
    {
        return true;
    }

}