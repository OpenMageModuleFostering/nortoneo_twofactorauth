<?php

/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Model_Observer
{
    /**
     * @param $observer
     */
    public function processAuthentication($observer)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = Mage::app()->getRequest();
        if ($this->isRequestAlwaysAllowed($request)) {
            return;
        }

        $helper = Mage::helper('nortoneo_twofactorauth');
        if (!$helper->isCurrentUserForAuthentication()) {
            return;
        }

        /** @var Nortoneo_TwoFactorAuth_Model_UserSettings $authSettings */
        $authSettings = Mage::helper('nortoneo_twofactorauth')->getCurrentUserSettingsModel();
        if ($authSettings->isCodeByEmailEnabled()) {
            $url = Mage::helper("adminhtml")->getUrl('nortoneo_twofactorauth/login/resend');
        } else {
            $url = Mage::helper("adminhtml")->getUrl('nortoneo_twofactorauth/login');
        }

        $response = Mage::app()->getResponse();
        $response->setRedirect($url);
        $response->sendResponse();
        /** @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $observer->getData('controller_action');
        $controllerAction->setFlag(
            $controllerAction->getRequest()->getActionName(),
            Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,
            true
        );
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    protected function isRequestAlwaysAllowed(Mage_Core_Controller_Request_Http $request)
    {
        $controller = $request->getControllerName();
        $module = $request->getModuleName();
        $action = $request->getActionName();

        if ($module == 'nortoneo_twofactorauth' && $controller == 'login') {
            return true;
        } elseif ($module == 'admin' && $controller == 'index' && in_array($action, array(
                'login',
                'logout',
                'forgotpassword',
                'resetPassword',
                'resetPasswordPost'
            ))
        ) {
            return true;
        }

        return false;
    }
}