<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $currentUserSettingsModel;

    /**
     * @return Nortoneo_TwoFactorAuth_Model_UserSettings
     */
    public function getCurrentUserSettingsModel()
    {
        if ($this->currentUserSettingsModel === null) {
            $user = Mage::getSingleton('admin/session')->getUser();
            if (!$user) {
                Mage::throwException('Cant load user model.');
            }
            $userId = $user->getId();
            /** @var Nortoneo_TwoFactorAuth_Model_UserSettings $userSettingsModel */
            $userSettingsModel = Mage::getResourceModel('nortoneo_twofactorauth/userSettings_collection')
                ->addFieldToFilter('user_id', $userId)
                ->getFirstItem();
            $userSettingsModel->afterLoad();
            if (!$userSettingsModel->getId()) {
                $data = array(
                    'is_active'     => 0,
                    'secret'        => $userSettingsModel->generateSecret(),
                    'user_id'       => $userId,
                    'method'        => Nortoneo_TwoFactorAuth_Model_UserSettings::TWO_FACTOR_AUTH_METHOD_EMAIL,
                    'trust_last_ip' => Nortoneo_TwoFactorAuth_Model_UserSettings::TWO_FACTOR_AUTH_TRUST_LAST_IP_NO,
                    'discrepancy'   => 4, //2 minutes
                    'last_ip'       => Mage::helper('core/http')->getRemoteAddr()
                );
                $userSettingsModel->setData($data);
                $userSettingsModel->save();
            }
            $this->currentUserSettingsModel = $userSettingsModel;
        }

        return $this->currentUserSettingsModel;
    }

    /**
     * @return bool
     */
    public function isCurrentUserForAuthentication()
    {
        $session = Mage::getSingleton('admin/session');
        if (!$session->isLoggedIn()) {
            return false;
        }

        $authSettings = $this->getCurrentUserSettingsModel();
        if (!$authSettings->getIsActive()) {
            return false;
        }

        $currentIp = Mage::helper('core/http')->getRemoteAddr();
        if ($authSettings->getTrustLastIp() && $authSettings->getLastIp() == $currentIp) {
            return false;
        }

        if ($this->isCurrentUserValidated()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isCurrentUserValidated()
    {
        $session = Mage::getSingleton('admin/session');
        if (!$session->isLoggedIn()) {
            return false;
        }

        return (bool)$session->getData('two_factor_auth_validated');
    }

    /**
     * @param bool $status
     */
    public function setCurrentUserValidated($status)
    {
        $session = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn()) {
            $session->setData('two_factor_auth_validated', (bool)$status);
        }
    }
}