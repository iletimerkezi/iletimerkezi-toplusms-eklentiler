<?php
if (!defined('_PS_VERSION_'))
    exit;

include _PS_MODULE_DIR_."iletimerkezi/libs/IMSender.php";

class Iletimerkezi extends Module
{
    const MODULE_NAME     = 'Iletimerkezi Sms New';
    const MODULE_DESC     = 'Müşterilerinize, siparişinin kargo durumlarını sms ile bildirin.';
    const MODULE_UNINS    = 'Are you sure you want to uninstall?';
    const DEFAULT_SENDER  = 'ILETI MRKZI';
    const ERROR_USER_INFO = 'Kullanıcı adınız veya şifreniz hatalı.';
    const WARNING_SENDER  = 'Lütfen onaylı başlıklarınızdan birini yazın. ';
    const SUCCESS_CONFIG  = 'Settings updated';
    const LABEL_ORDER_STS = 'Siparişin durumu %s olduğunda aşağıdaki mesaj gönderilsin';

    public function __construct()
    {
        $this->name          = 'iletimerkezi';
        $this->tab           = 'advertising_marketing';
        $this->version       = 1.1;
        $this->author        = 'www.iletimerkezi.com';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName            = $this->l(self::MODULE_NAME);
        $this->description            = $this->l(self::MODULE_DESC);
        $this->confirmUninstall       = $this->l(self::MODULE_UNINS);
        $this->bootstrap              = true;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->menu_controller        = 'AdminIM';
        $this->menu_name              = 'Iletimerkezi Sms';
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if(
            parent::install() == false OR
            !$this->registerHook('actionObjectAddAfter') OR
            !$this->registerHook('actionOrderStatusPostUpdate') OR
            !$this->registerHook('actionAdminOrdersTrackingNumberUpdate')
        ){
            return false;
        }

        if (!$this->installModuleTab($this->menu_controller, 'AdminParentModulesSf')) {
            return false;
        }

        $this->installSmsReportTable();
        return true;
    }

    public function installModuleTab($tabClassName, $TabParentName)
    {
        $tab     = new Tab();
        $tabName = [];
        $langues = Language::getLanguages(false);
        foreach ($langues as $langue) {
            $tabName[$langue['id_lang']] = $this->menu_name;
        }

        $tab_parent_id   = self::getIdTab($TabParentName);
        $tab->name       = $tabName;
        $tab->class_name = $tabClassName;
        $tab->module     = $this->name;
        $tab->id_parent  = $tab_parent_id;
        $id_tab          = $tab->save();
        if (!$id_tab) {
            return false;
        }

        return true;
    }

    protected static function getIdTab($tabClassName)
    {
        return (int) \Db::getInstance()->getValue(
            'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = \''.pSQL($tabClassName).'\''
        );
    }

    private function installSmsReportTable() {
        Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'iletimerkezisms`');
        Db::getInstance()->execute('
        CREATE TABLE `'._DB_PREFIX_.'iletimerkezisms` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `report_id` int(11) NOT NULL,
          `number` varchar(55) NOT NULL,
          `message` text NOT NULL,
          `status` tinyint(1) NOT NULL,
          `error` text NOT NULL,
          `date_send` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `report_id` (`report_id`)
        ) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;');
    }

    public function uninstall()
    {
        parent::uninstall();
        Configuration::deleteByName('iletimerkezisms');
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'iletimerkezisms');

        Configuration::deleteByName('IM_USERNAME');
        Configuration::deleteByName('IM_PASSWORD');
        Configuration::deleteByName('IM_SENDER');
        Configuration::deleteByName('IM_ADMIN_PHONES');
        Configuration::deleteByName('IM_ORDER_ADMIN');
        Configuration::deleteByName('IM_TRACKING');
        Configuration::deleteByName('IM_ADMIN_MSG');

        foreach($this->getAvailableOrderStatuses() as $status) {
            Configuration::deleteByName('IM_ORDER_'.$status['id_order_state']);
        }

        $id_tab = self::getIdTab($this->menu_controller);
        if ($id_tab != 0) {
            $tab = new \Tab($id_tab);
            $tab->delete();
        }

        return true;
    }

    public function getContent() {

        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $result = $this->saveForm();

            if(isset($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $output .= $this->displayError($error);
                }
            }

            if(isset($result['warnings'])) {
                foreach ($result['warnings'] as $warning) {
                    $output .= $this->displayWarning($warning);
                }
            }

            if(isset($result['success']))
                $output .= $this->displayConfirmation($result['success']);
        }

        return $output.$this->getSettingsForm();
    }

    private function saveForm() {

        $result         = [];
        $imSender       = new IMSender();
        $is_user_exists = $imSender->isUserExists(Tools::getValue('IM_USERNAME'), Tools::getValue('IM_PASSWORD'));
        if(!$is_user_exists[0]) {
            $result['errors'][] = $this->l(self::ERROR_USER_INFO);
        }

        $is_sender_exists = $imSender->isSenderExists(
            Tools::getValue('IM_USERNAME'),
            Tools::getValue('IM_PASSWORD'),
            Tools::getValue('IM_SENDER')
        );

        if(!$is_sender_exists[0]) {
            $result['warnings'][] = $this->l(self::WARNING_SENDER).implode(", ", $is_sender_exists[1]);
        }

        Configuration::updateValue('IM_USERNAME', Tools::getValue('IM_USERNAME'));
        Configuration::updateValue('IM_PASSWORD', Tools::getValue('IM_PASSWORD'));
        Configuration::updateValue('IM_SENDER', $is_sender_exists[0]?Tools::getValue('IM_SENDER'):self::DEFAULT_SENDER);
        Configuration::updateValue('IM_ADMIN_PHONES', Tools::getValue('IM_ADMIN_PHONES'));
        Configuration::updateValue('IM_ORDER_ADMIN', Tools::getValue('IM_ORDER_ADMIN'));
        Configuration::updateValue('IM_TRACKING', Tools::getValue('IM_TRACKING'));
        Configuration::updateValue('IM_ADMIN_MSG', Tools::getValue('IM_ADMIN_MSG'));

        foreach($this->getAvailableOrderStatuses() as $status) {
            Configuration::updateValue('IM_ORDER_'.$status['id_order_state'], Tools::getValue('IM_ORDER_'.$status['id_order_state']));
        }

        $result['success'] = $this->l(self::SUCCESS_CONFIG);

        return $result;
    }

    private function getSettingsForm() {

        $helper                  = new HelperForm();
        $helper->module          = $this;
        $helper->name_controller = $this->name;
        $helper->token           = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex    = AdminController::$currentIndex.'&configure='.$this->name;

        $default_lang                     = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language    = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->title          = $this->displayName;
        $helper->show_toolbar   = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action  = 'submit'.$this->name;
        $helper->toolbar_btn    = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper = $this->setFormFieldsValues($helper);

        return $helper->generateForm($this->getFormFields());
    }

    private function setFormFieldsValues($helper) {

        $imSender       = new IMSender();
        $is_user_exists = $imSender->isUserExists(Configuration::get('IM_USERNAME'), Configuration::get('IM_PASSWORD'));
        if($is_user_exists[0])
            $helper->fields_value['IM_BALANCE'] = $is_user_exists[1];
        else
            $helper->fields_value['IM_BALANCE'] = 0;

        $helper->fields_value['IM_USERNAME']     = Configuration::get('IM_USERNAME');
        $helper->fields_value['IM_PASSWORD']     = Configuration::get('IM_PASSWORD');
        $helper->fields_value['IM_SENDER']       = Configuration::get('IM_SENDER');
        $helper->fields_value['IM_ADMIN_PHONES'] = Configuration::get('IM_ADMIN_PHONES');
        $helper->fields_value['IM_ORDER_ADMIN']  = Configuration::get('IM_ORDER_ADMIN');
        $helper->fields_value['IM_TRACKING']     = Configuration::get('IM_TRACKING');
        $helper->fields_value['IM_ADMIN_MSG']    = Configuration::get('IM_ADMIN_MSG');

        foreach($this->getAvailableOrderStatuses() as $status) {
            $helper->fields_value['IM_ORDER_'.$status['id_order_state']] = Configuration::get('IM_ORDER_'.$status['id_order_state']);
        }

        return $helper;
    }

    public function getFormFields()
    {
        $fields_form[0]['form'] = $this->getConnectionSettings();
        $fields_form[1]['form'] = $this->getTextSettings();

        return $fields_form;
    }

    private function getConnectionSettings() {

        $connection = [
            'legend' => [
                'title' => $this->l('Connection'),
            ],
            'input' => [
                [
                    'type'     => 'text',
                    'label'    => $this->l('Username'),
                    'desc'     => $this->l('Click here to create free account.'),
                    'name'     => 'IM_USERNAME',
                    'size'     => 20,
                    'required' => true
                ],
                [
                    'type'     => 'text',
                    'label'    => $this->l('Password'),
                    'name'     => 'IM_PASSWORD',
                    'size'     => 20,
                    'required' => true
                ],
                [
                    'type'     => 'text',
                    'label'    => $this->l('Sender'),
                    'name'     => 'IM_SENDER',
                    'size'     => 20,
                    'required' => true
                ],
                [
                    'type'     => 'text',
                    'label'    => $this->l('Admin Phones'),
                    'name'     => 'IM_ADMIN_PHONES',
                    'desc'     => $this->l('If you want to add more than one, please use comma.'),
                    'size'     => 20,
                    'required' => false
                ],
                [
                    'type'    => 'radio',
                    'label'   => $this->l('Send message with SMS'),
                    'name'    => 'IM_ADMIN_MSG',
                    'desc'    => '',
                    'size'    => 20,
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'active_on',
                            'value' => '1',
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => '0',
                            'label' => $this->l('No'),
                        ],

                    ]
                ],
                [
                    'type'     => 'text',
                    'label'    => $this->l('Available Balance'),
                    'desc'     => '',
                    'name'     => 'IM_BALANCE',
                    'size'     => 20,
                    'required' => false,
                    'suffix'   => 'SMS'
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        return $connection;
    }

    private function getTextSettings() {

        $text_settings = [
            'legend' => [
                'title' => $this->l('Text Settings'),
            ],
            'input' => [],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $text_settings['input'][] = [
            'type'     => 'textarea',
            'label'    => $this->l('Order created message for admin'),
            'name'     => 'IM_ORDER_ADMIN',
            'desc'     => $this->l('Available shortcodes:').'[order_id], [customer_id], [order_sum]',
            'size'     => 20,
            'required' => false
        ];

        $text_settings['input'][] = [
            'type'     => 'textarea',
            'label'    => $this->l('Shipping code updated message'),
            'name'     => 'IM_TRACKING',
            'desc'     => $this->l('Available shortcodes:').'[order_id], [firstname], [lastname], [telephone], [order_sum], [shipping_number]',
            'size'     => 20,
            'required' => false
        ];

        foreach($this->getAvailableOrderStatuses() as $status) {

            $text_settings['input'][] = [
                'type'     => 'textarea',
                'label'    => $this->l(str_replace('%s', $status['name'], self::LABEL_ORDER_STS)),
                'name'     => 'IM_ORDER_'.$status['id_order_state'],
                'desc'     => $this->l('Available shortcodes:').'[order_id], [firstname], [lastname], [telephone], [status_name], [order_sum], [shipping_number]',
                'size'     => 20,
                'required' => false
            ];
        }

        return $text_settings;
    }

    private function getAvailableOrderStatuses() {

        $order_state  = new OrderState();
        $db_id_order  = Tools::getValue('id_order_state');
        $order_status = $order_state->getOrderStates(empty($db_id_order)?1:$id_order_state);

        return $order_status;
    }

    // If Order Status Changed
    public function hookActionOrderStatusPostUpdate($params) {

        $orderStatus = $params['newOrderStatus'];
        $order       = new Order($params['id_order']);
        $customer    = new Customer($order->id_customer);
        $address     = new Address(intval($order->id_address_invoice));

        $customer_phone = $address->phone_mobile;
        if(empty($customer_phone))
            $customer_phone = $address->phone;

        $uname         = Configuration::get('IM_USERNAME');
        $pass          = Configuration::get('IM_PASSWORD');
        $sender        = Configuration::get('IM_SENDER');
        $text_template = Configuration::get('IM_ORDER_'.$orderStatus->id);

        if(!empty($text_template)) {

            $tmp_variables = [
                '[order_id]'        => $params['id_order'],
                '[firstname]'       => $customer->firstname,
                '[lastname]'        => $customer->lastname,
                '[telephone]'       => $customer_phone,
                '[status_name]'     => $orderStatus->name,
                '[order_sum]'       => Tools::ps_round($order->getOrdersTotalPaid(), 2),
                '[shipping_number]' => $order->shipping_number
            ];

            $text     = $this->parseSmsTemplate($tmp_variables, $text_template);
            $imSender = new IMSender();
            $imSender->sendOneSms($uname, $pass, $customer_phone, $text, $sender);
        }
    }

    // If Tracking Code is updated
    public function hookActionAdminOrdersTrackingNumberUpdate($params) {

        $order    = $params['order'];
        $customer = new Customer($order->id_customer);
        $address  = new Address(intval($order->id_address_invoice));

        $customer_phone = $address->phone_mobile;
        if(empty($customer_phone))
            $customer_phone = $address->phone;

        $uname         = Configuration::get('IM_USERNAME');
        $pass          = Configuration::get('IM_PASSWORD');
        $sender        = Configuration::get('IM_SENDER');
        $text_template = Configuration::get('IM_TRACKING');

        if(!empty($text_template)) {

            $tmp_variables = [
                '[order_id]'        => $order->getIdByCartId($order->id_cart),
                '[firstname]'       => $customer->firstname,
                '[lastname]'        => $customer->lastname,
                '[telephone]'       => $customer_phone,
                '[order_sum]'       => Tools::ps_round($order->getOrdersTotalPaid(), 2),
                '[shipping_number]' => $order->shipping_number
            ];

            $text     = $this->parseSmsTemplate($tmp_variables, $text_template);
            $imSender = new IMSender();
            $imSender->sendOneSms($uname, $pass, $customer_phone, $text, $sender);
        }

    }


    public function hookActionObjectAddAfter($params) {

        // If New Order Created
        if(get_class($params['object']) == 'Order') {
            $this->orderCreated($params);
        }
        // If Admin send message
        if(get_class($params['object']) == 'CustomerMessage') {
            $this->adminSendMessage($params);
        }
    }

    private function orderCreated($params) {

        $order         = $params['object'];
        $tmp_variables = [
            '[order_id]'    => $order->getIdByCartId($order->id_cart),
            '[customer_id]' => $order->id_customer,
            '[order_sum]'   => Tools::ps_round($order->getOrdersTotalPaid(), 2)
        ];
        $uname         = Configuration::get('IM_USERNAME');
        $pass          = Configuration::get('IM_PASSWORD');
        $sender        = Configuration::get('IM_SENDER');
        $anumbers      = Configuration::get('IM_ADMIN_PHONES');
        $text_template = Configuration::get('IM_ORDER_ADMIN');
        $text          = $this->parseSmsTemplate($from, $to, $text_template);
        $imSender      = new IMSender();
        $admin         = explode(',', $anumbers);

        foreach ($admin as $to) {

            if(!empty($text))
                $imSender->sendOneSms($uname, $pass, $to, $text, $sender);
        }
    }

    private function adminSendMessage($params) {

        $customerMessage = $params['object'];
        if($customerMessage->id_employee > 0 && $customerMessage->private == 0) {

            if(Configuration::get('IM_ADMIN_MSG') == 1) {
                $ct      = new CustomerThread($customerMessage->id_customer_thread);
                $order   = new Order($ct->id_order);
                $address = new Address(intval($order->id_address_invoice));

                $customer_phone = $address->phone_mobile;
                if(empty($customer_phone))
                    $customer_phone = $address->phone;

                $uname    = Configuration::get('IM_USERNAME');
                $pass     = Configuration::get('IM_PASSWORD');
                $sender   = Configuration::get('IM_SENDER');
                $imSender = new IMSender();
                $is_send  = $imSender->sendOneSms($uname, $pass, $customer_phone, $customerMessage->message, $sender);
                if($is_send[0]) {
                    $this->insertMessage($is_send[1], $customer_phone, $customerMessage->message);
                } else {
                    $this->insertMessage('-1', $customer_phone, $customerMessage->message, $is_send[1]);
                }
            }
        }
    }

    public function parseSmsTemplate($tmp_variables, $template) {
        return str_replace(array_keys($tmp_variables), array_values($tmp_variables), $template);
    }

    public function getLastMessages() {

        $sql     = 'SELECT * FROM '._DB_PREFIX_.'iletimerkezisms ORDER BY id DESC LIMIT 0,30';
        $results = Db::getInstance()->ExecuteS($sql);
        $res     = [];
        foreach ($results as $key => $result) {

            if($result['status'] == 1) {
                $imSender = new IMSender();
                $report   = $imSender->checkReport($result['report_id'], Configuration::get('IM_USERNAME'), Configuration::get('IM_PASSWORD'));

                if($report[0]) {

                    if($report[1]['total'] == $report[1]['delivered']) {
                        $this->updateMessage($result['id'], '2');
                    } else if($report[1]['total'] == $report[1]['undelivered']) {
                        $this->updateMessage($result['id'], '3');
                    } else if($report[1]['total'] == ($report[1]['delivered'] + $report[1]['undelivered']) ) {
                        $this->updateMessage($result['id'], '2');
                    }

                    $res[$key] = $result;

                } else {
                    $res[$key] = $result;
                }

            } else {
                $res[$key] = $result;
            }

        }

        return $res;
    }

    public function insertMessage($id, $number, $message, $error='') {

        $status = 1;
        if(!empty($error)) {
            $status = 3;
        }

        $res = Db::getInstance()->insert('iletimerkezisms', [
            'report_id' => (int)$id,
            'number'    => pSQL($number),
            'message'   => pSQL($message),
            'status'    => (int)$status,
            'error'     => pSQL($error)
        ]);
    }

    public function updateMessage($id, $status) {

        Db::getInstance()->update('iletimerkezisms', ['status' => $status], "id='".$id."'");
    }
}