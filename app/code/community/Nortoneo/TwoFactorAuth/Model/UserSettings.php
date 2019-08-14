<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Model_UserSettings extends Mage_Core_Model_Abstract
{
    const TWO_FACTOR_AUTH_METHOD_EMAIL = 0;
    const TWO_FACTOR_AUTH_METHOD_APP = 1;

    const TWO_FACTOR_AUTH_TRUST_LAST_IP_NO = 0;
    const TWO_FACTOR_AUTH_TRUST_LAST_IP_YES = 1;

    const TWO_FACTOR_AUTH_CODE_EMAIL_TEMPLATE = 'nortoneo_twofactorauth_code';
    const TWO_FACTOR_AUTH_CODE_EMAIL_IDENTITY = 'general';


    protected $authenticator;

    protected function _construct()
    {
        $this->_init('nortoneo_twofactorauth/userSettings');
    }

    /**
     * @param string $code
     * @return bool
     */
    public function verifyCode($code)
    {
        $secret = $this->getSecret();
        if (empty($secret)) {
            return false;
        }

        $discrepancy = (int)$this->getDiscrepancy();

        return $this->getAuthenticator()->verifyCode($secret, $code, $discrepancy);
    }

    /**
     * @return bool|string
     */
    public function getCurrentCode()
    {
        $secret = $this->getSecret();
        if (empty($secret)) {
            return false;
        }

        return $this->getAuthenticator()->getCode($secret);
    }

    /**
     * @param int $width
     * @param int $height
     * @return bool|string
     */
    public function getQRCodeGoogleUrl($width = 250, $height = 250)
    {
        $secret = $this->getSecret();
        if (empty($secret)) {
            return false;
        }
        $title = $this->getTitleForQRCode();
        $name = $this->getNameForQRCode();
        $params = array(
            'width'  => $width,
            'height' => $height
        );

        return $this->getAuthenticator()->getQRCodeGoogleUrl($name, $secret, $title, $params);
    }

    /**
     * @param int $secretLength
     * @return string
     */
    public function generateSecret($secretLength = 16)
    {
        return $this->getAuthenticator()->createSecret($secretLength);
    }

    /**
     * @return array
     */
    public function getMethodOptions()
    {
        $helper = Mage::helper('nortoneo_twofactorauth');
        $userEmail = $this->getUser()->getEmail();

        return array(
            self::TWO_FACTOR_AUTH_METHOD_EMAIL => $helper->__('Send me codes by e-mail (%s)', $userEmail),
            self::TWO_FACTOR_AUTH_METHOD_APP   => $helper->__('I will use mobile application')
        );
    }

    /**
     * @return array
     */
    public function getDiscrepancyOptions()
    {
        $helper = Mage::helper('nortoneo_twofactorauth');

        $options = array();
        for ($i = 1; $i < 31; $i++) {
            $seconds = $i * 30;
            $options[$i] = $helper->__('%s seconds', $seconds);
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getTrustLastIpOptions()
    {
        $helper = Mage::helper('nortoneo_twofactorauth');

        return array(
            self::TWO_FACTOR_AUTH_TRUST_LAST_IP_NO  => $helper->__('No'),
            self::TWO_FACTOR_AUTH_TRUST_LAST_IP_YES => $helper->__('Yes')
        );
    }

    /**
     *
     */
    public function provideCurrentCode()
    {
        if ($this->isCodeByEmailEnabled()) {
            $this->sendCodeByEmail();
        }

        //no other methods implemented
    }

    /**
     * @return bool
     */
    public function isCodeByEmailEnabled()
    {
        return $this->getMethod() == self::TWO_FACTOR_AUTH_METHOD_EMAIL;
    }

    /**
     * @return mixed
     */
    public function sendCodeByEmail()
    {
        $email = $this->getUser()->getEmail();
        $templateId = self::TWO_FACTOR_AUTH_CODE_EMAIL_TEMPLATE;
        $identity = self::TWO_FACTOR_AUTH_CODE_EMAIL_IDENTITY;

        $discrepancyOptions = $this->getDiscrepancyOptions();
        $discrepancyLabel = isset($discrepancyOptions[$this->getDiscrepancy()]) ? $discrepancyOptions[$this->getDiscrepancy()] : '';

        $emailVariables = array(
            'auth_code'         => $this->getCurrentCode(),
            'discrepancy_label' => $discrepancyLabel,
        );

        /* @var $emailTemplate Mage_Core_Model_Email_Template */
        $emailTemplate = Mage::getModel('core/email_template');
        $emailTemplate->sendTransactional($templateId, $identity, $email, null, $emailVariables);

        $sentSuccess = $emailTemplate->getSentSuccess();

        return $sentSuccess;
    }

    /**
     * @return string
     */
    protected function getTitleForQRCode()
    {
        $name = Mage::getStoreConfig('general/store_information/name');

        return $name;
    }

    /**
     * @return string|null
     */
    protected function getNameForQRCode()
    {
        $user = $this->getUser();

        return $user->getUsername();
    }

    /**
     * @return Nortoneo_TwoFactorAuth_Model_Lib_GoogleAuthenticator
     */
    protected function getAuthenticator()
    {
        if ($this->authenticator === null) {
            $this->authenticator = Mage::getModel('nortoneo_twofactorauth/lib_googleAuthenticator');
        }

        return $this->authenticator;
    }

    /**
     * @return Mage_Admin_Model_User
     */
    protected function getUser()
    {
        $userId = $this->getUserId();
        $user = Mage::getModel('admin/user')->load($userId);

        return $user;
    }
}