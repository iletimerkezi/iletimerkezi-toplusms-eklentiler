<?php
namespace Iletimerkezi\Verify;

class Account extends XFCP_Account {

    public function actionAccountDetails() {

        if ($this->isPost())
        {
            $session   = $this->app()->session();
            $userField = $this->repository('XF:UserField');
            $uFV       = $userField->getUserFieldValues($session->get('userId'));

            if($uFV['iletimerkezi_gsm'] != $_POST['custom_fields']['iletimerkezi_gsm']) {
                if(!$this->isVerified()) {
                    return $this->error(\XF::phrase('iletimerkezi_verify_error'));
                }
            }

            if($this->isVerified()) {
                $options = \XF::options();
                $UGC     = $this->app()->service('XF:User\UserGroupChange');
                $UGC->addUserGroupChange($session->get('userId'), rand(100000, 999999), [$options->iletimerkezi_verify_group]);
            }
        }

        return parent::actionAccountDetails();
    }

    private function isVerified()
    {

        $session = $this->app()->session();
        $vcode   = $session->get('vcode');

        if(is_null($vcode) || empty($vcode))
            return false;

        if(is_null($_POST['vcode']) || empty($_POST['vcode']))
            return false;

        if(strlen($_POST['vcode']) != 6 || strlen($vcode) != 6)
            return false;

        if($_POST['vcode'] == $vcode) {
            return true;
        } else {
            return false;
        }
    }

}