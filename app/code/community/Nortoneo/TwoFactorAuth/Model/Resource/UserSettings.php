<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Model_Resource_UserSettings extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $encryptionModel;

    protected function _construct()
    {
        $this->_init('nortoneo_twofactorauth/userSettings', 'settings_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $secret = $object->getSecret();
        $object->setSecret($this->encrypt($secret));
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $secret = $object->getSecret();
        $object->setSecret($this->decrypt($secret));
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $secret = $object->getSecret();
        $object->setSecret($this->decrypt($secret));
    }

    protected function encrypt($value)
    {
        return $this->getEncryptionModel()->encrypt($value);
    }

    protected function decrypt($value)
    {
        return $this->getEncryptionModel()->decrypt($value);
    }

    protected function getEncryptionModel()
    {
        if ($this->encryptionModel === null) {
            $this->encryptionModel = Mage::getModel('core/encryption');
        }

        return $this->encryptionModel;
    }

}