<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
class Nortoneo_TwoFactorAuth_Adminhtml_UserSettingsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('nortoneo_twofactorauth/adminhtml_userSettings_edit'))
            ->_addLeft($this->getLayout()->createBlock('nortoneo_twofactorauth/adminhtml_userSettings_edit_tabs'));
        $this->renderLayout();
    }

    public function validateAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('nortoneo_twofactorauth/adminhtml_userSettings_validate'))
            ->_addLeft($this->getLayout()->createBlock('nortoneo_twofactorauth/adminhtml_userSettings_validate_tabs'));
        $this->renderLayout();
    }

    public function validatePostAction()
    {
        if ($code = $this->getRequest()->getPost('code')) {
            $code = str_replace(' ', '', $code);
            try {
                $model = Mage::helper('nortoneo_twofactorauth')->getCurrentUserSettingsModel();
                if (!$model->verifyCode($code)) {
                    $model->setData('is_active', 0);
                    $this->_getSession()->addError(Mage::helper('nortoneo_twofactorauth')->__('Code not validated. Protection disabled.'));
                } else {
                    $this->_getSession()->addSuccess(Mage::helper('nortoneo_twofactorauth')->__('Code validated. Protection enabled.'));
                    $model->setData('is_active', 1);
                    Mage::helper('nortoneo_twofactorauth')->setCurrentUserValidated(true);
                }
                $model->save();
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('nortoneo_twofactorauth')->__('Unable to save settings.'));
                Mage::logException($e);
            }
        }

        $this->_redirect('*/*/index');
    }

    public function regenerateSecretAction()
    {
        $userSettingsModel = Mage::helper('nortoneo_twofactorauth')->getCurrentUserSettingsModel();
        $userSettingsModel->setData('is_active', 0);
        $userSettingsModel->setSecret($userSettingsModel->generateSecret());
        $userSettingsModel->save();

        $this->_redirect('*/*/index');
    }

    public function saveAction()
    {
        $requireValidation = false;
        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = Mage::helper('nortoneo_twofactorauth')->getCurrentUserSettingsModel();
                if ($data['is_active'] && (!$model->getIsActive() || $model->getMethod() != $data['method'])) {
                    $requireValidation = true;
                    $model->setData('is_active', 0);
                } else {
                    $model->setData('is_active', $data['is_active']);
                }
                $model->setData('trust_last_ip', $data['trust_last_ip']);
                $model->setData('method', $data['method']);
                $model->setData('discrepancy', $data['discrepancy']);
                $model->save();

                if ($requireValidation) {
                    $model->provideCurrentCode();
                }

                $this->_getSession()->addSuccess(
                    Mage::helper('nortoneo_twofactorauth')->__('Settings has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('nortoneo_twofactorauth')->__('Unable to save settings.'));
                Mage::logException($e);
            }
        }
        if ($requireValidation) {
            $this->_redirect('*/*/validate');
        } else {
            $this->_redirect('*/*/index');
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/nortoneo_twofactor_auth');
    }

}