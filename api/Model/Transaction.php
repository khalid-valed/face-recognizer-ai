<?php

namespace Api\Model;

class Transaction
{
    // private $id;
    // private $transactionId;
    // private $sum;
    // private $vendor;
    // private $account;
    // private $comment;
    // private $paymentType;
    // private $status;
    // private $codeOrg;
    // private $createdAt;
    
    private $db;//data base instance

    public function __construct($dbInstance)
    {
        $this->db = $dbInstance->payment->transaction;
    }
    
    // public function exchangeArray(array $data)
    // {
    //     $this->id = (!empty($data['id'])) ? $data['id'] : '';
    //     $this->transactionId = (!empty($data['transactionId'])) ? $data['transactionId'] : '';
    //     $this->sum = (!empty($data['sum'])) ? $data['sum'] : '';
    //     $this->vendor = (!empty($data['vendor'])) ? $data['vendor'] : '';
    //     $this->account = (!empty($data['account'])) ? $data['account'] : '';
    //     $this->comment = (!empty($data['comment'])) ? $data['comment'] : '';
    //     $this->paymentType = (!empty($data['paymentType'])) ? $data['paymentType'] : '';
    //     $this->status = (!empty($data['status'])) ? $data['status'] : '';
    //     $this->codeOrg = (!empty($data['codeOrg'])) ? $data['codeOrg'] : '';
    //     $this->createdAt = (!empty($data['createdAt'])) ? $data['createdAt'] : '';
    // }
    public function findOne(array $criteria)
    {
        $result = $this->db->findOne($criteria,['projection'=>['_id' => 0]]);
        return $result;
    }
  
    public function find(array $criteria)
    {
        $result = $this->db->find($criteria,['projection' =>['_id'=>0]]);
        return $result;
    }
}
