<?php

class Emarka_Storesms_Block_Adminhtml_Storesms_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        /* Emarka Block */
        parent::__construct();
        $this->setId('storesms_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(false);
       
    }




    protected function _prepareCollection()
    {   
        
        //$reports = Mage::getModel('storesms/storesms')->getSmsReports(Mage::app()->getRequest()->getParam('page'));
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connection->select()->from(array("storesms"),array('*'));

        $collection_of_reports = new Varien_Data_Collection_Db();

       
        //$res = $connection->query($query);
        
        //die(print_r($connection->select(),1)."<br><br><br><br><br><br>");
        
        $collection_of_reports->setConnection($connection);
        $collection_of_reports->getSelect()->from("storesms");

        //$collection_of_reports->addBindParam("from","storesms");
        //die("CCC ".print_r($collection_of_reports->getSelect(),1)."<br><br><br><br><br><br>");
        //$results = $connection->fetchAll("SELECT * FROM storesms");

        //die("DDD".print_r($res,1));
        
        /*
        foreach ($reports as $key => $report) {
            $report_temp = new Varien_Object();
            $report_temp->setId($report['id']);
            $report_temp->setNumber($report['number']);
            $report_temp->setMessage($report['message']); 
            $report_temp->setResponse($report['response']); 

            if($report['status']==0)
                $status = "Tekrar Eden Numara";
            elseif($report['status']==1)
                $status = "Gönderiliyor";
            elseif($report['status']==2)
                $status = "Gönderildi";
            elseif($report['status']==3) 
                $status = "Gönderilemedi";
            else
                $status = "";

            $report_temp->setStatus($status); 
            $report_temp->setCreated($report['created']); 
            //die(var_export($report_temp));
            $collection_of_reports->addItem($report_temp);
            unset($report_temp);
        }
        
       // $collection_of_reports->setCurPage(2);
        //$collection_of_reports->setPageSize(4); 
        //$collection_of_reports->setSize(40);
        $collection_of_reports->load();
        die("SELECT ".$collection_of_reports->getSelect());


       
        $this->setTotals("40");
        
        
        $this->setPagerVisibility(true);
        $this->setDefaultLimit(10);
        $this->setDefaultPage(2);
        */
        $this->setFilterVisibility(false);
        $this->setCollection($collection_of_reports);
        return parent::_prepareCollection();
        
    }


    

    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header'    => Mage::helper('storesms')->__('ID'),
            'align'     =>'left',
            'width'     => '40px',
            'index'     => 'id',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('number', array(
            'header'    => Mage::helper('storesms')->__('Alıcı Numarası'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'number',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('message', array(
            'header'    => Mage::helper('storesms')->__('Mesaj'),
            'align'     =>'left',
            'width'     => '250px',
            'index'     => 'message',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('response', array(
            'header'    => Mage::helper('storesms')->__('Rapor'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'response',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('storesms')->__('Durumu'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'status',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('created', array(
            'header'    => Mage::helper('storesms')->__('Oluşturulma Tarihi'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'created',
            'type'      => 'date',
            'filter'    => false,
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

}