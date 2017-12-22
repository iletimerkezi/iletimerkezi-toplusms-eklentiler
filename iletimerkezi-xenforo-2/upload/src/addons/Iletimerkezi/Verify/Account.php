<?php
namespace Iletimerkezi\Verify;

class Account extends XFCP_Account {

    public function actionAccountDetails() {

        if ($this->isPost())
        {
            $session   = $this->app()->session();
            $vcode     = $session->get('vcode');
            $userField = $this->repository('XF:UserField');
            $uFV       = $userField->getUserFieldValues($session->get('userId'));

            if($uFV['iletimerkezi_gsm'] != $_POST['custom_fields']['iletimerkezi_gsm']) {
                if($_POST['vcode'] != $vcode) {
                    return $this->error(\XF::phrase('iletimerkezi_verify_error'));
                }
            }

            $options = \XF::options();
            $UGC     = $this->app()->service('XF:User\UserGroupChange');
            $UGC->addUserGroupChange($session->get('userId'), rand(100000, 999999), [$options->iletimerkezi_verify_group]);
        }

        return parent::actionAccountDetails();
    }
}