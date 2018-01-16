<?php

class AdminIMController extends ModuleAdminController
{

    public function __construct()
    {
        $this->lang      = true;
        $this->bootstrap = true;
        $this->context   = Context::getContext();
        $this->name      = 'iletimerkezi';

        parent::__construct();
    }

    public function renderList() {

        $info_html = '';
        if (Tools::isSubmit('submit'.$this->name)) {

            $result = $this->saveForm();
            if($result[0]) {
                $info_html = $this->module->displayConfirmation($this->module->l('Settings updated'));
            } else {
                $info_html = $this->module->displayWarning($this->module->l('We cant send because of '.$result[1]));
            }
        }

        $imSender       = new IMSender();
        $is_user_exists = $imSender->isUserExists(Configuration::get('IM_USERNAME'), Configuration::get('IM_PASSWORD'));
        if($is_user_exists[0]) {
            $balance = $is_user_exists[1].' SMS <a href="https://www.iletimerkezi.com/user" target="_blank">Buy Credits</a>';
        } else {
            $balance = $this->module->l('Firstly, you have to configure the module.');
        }

        $this->context->smarty->assign([
            'im_img'         => _MODULE_DIR_.'iletimerkezi/views/img/',
            'bulk_send_form' => $this->bulkSendForm(),
            'balance'        => $balance,
            'reports'         => $this->module->getLastMessages()
        ]);

        $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.'iletimerkezi/views/templates/admin/iletimerkezi-layout.tpl');

        return $info_html.$html;
    }

    private function saveForm() {

        $group           = new Group(Tools::getValue('IM_BULK_GROUP'));
        $customers_count = $group->getCustomers(true);
        $msg_template    = Tools::getValue('IM_BULK_MSG');
        $payload         = [];

        if($customers_count > 0) {
            $customers = $group->getCustomers();

            foreach ($customers as $customer) {

                $address_id = Address::getFirstCustomerAddressId($customer['id_customer'], true);
                if($address_id > 0) {
                    $address        = new Address($address_id);
                    $customer_phone = $address->phone_mobile;
                    if(empty($customer_phone))
                        $customer_phone = $address->phone;


                    $tmp_variables = [
                        '[firstname]' => $customer['firstname'],
                        '[lastname]'  => $customer['lastname']
                    ];

                    $text      = $this->module->parseSmsTemplate($tmp_variables, $msg_template);
                    $payload[] = ['gsm' => $customer_phone, 'text' => $text];
                }
            }
            // Send Sms To Payload
            return [true, ''];
        }

        return [false, ''];
    }

    private function bulkSendForm() {

        $helper                  = new HelperForm();
        $helper->module          = $this;
        $helper->name_controller = $this->name;
        $helper->token           = Tools::getAdminTokenLite('AdminIM');
        $helper->currentIndex    = self::$currentIndex;

        $default_lang                     = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language    = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->title          = 'Iletimerkezi SMS';
        $helper->show_toolbar   = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action  = 'submit'.$this->name;
        $helper->toolbar_btn    = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => self::$currentIndex.'&save'.$this->name.'&token='.$helper->token,
            ],
        ];

        $helper->fields_value['IM_BULK_GROUP'] = 1;
        $helper->fields_value['IM_BULK_MSG']   = '';

        return $helper->generateForm($this->getFormFields());
    }

    private function getFormFields() {

        $groups = Group::getGroups($this->context->language->id, true);
        foreach ($groups as $group) {

            $options[] = [
                'id_option' => $group['id_group'],
                'name'      => $group['name']
            ];
        }

        $fields_form[0]['form'] =
        [
            'legend' => [
                'title' => $this->l('Send Bulk SMS'),
            ],
            'input' => [
                [
                    'type'     => 'select',
                    'label'    => $this->l('Customer Groups'),
                    'desc'     => '',
                    'name'     => 'IM_BULK_GROUP',
                    'options' => [
                        'query' => $options,
                        'id'    => 'id_option',
                        'name'  => 'name'
                    ]
                ],
                [
                    'type'     => 'textarea',
                    'label'    => $this->l('Message'),
                    'name'     => 'IM_BULK_MSG',
                    'size'     => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        return $fields_form;
    }

    // private function parseSmsTemplate($tmp_variables, $template) {
    //     return str_replace(array_keys($tmp_variables), array_values($tmp_variables), $template);
    // }
}