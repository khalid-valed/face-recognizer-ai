<?php

namespace Api\Controller;

abstract class AbstractController
{
    protected $container;

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }
  
    public function getOrganization($request, $response, array $args)
    {
        /**
        * @api {get} /api/payment/organization Получение список организации.
        * @apiName Получение список организации.
        * @apiParam {String} [codeOrg] Код организации. Код организации выдается при регистрации в системе и будет предоставлен в процессе интеграции с платежной системой. получить список доступных таксопарков с их кодами отдельным запросом (смотреть ниже).
        * @apiUse BasicAuth
        * @apiGroup LTS API
        * @apiSuccessExample Success-Response:
        *     HTTP/1.1 200 OK
        * [
        *    {
        *      "id": "5cadd708b5d7570b954f46df",
        *      "nameOfOrganization": "Такси №1 Яндекс",
        *      "codeOrg": 1001
        *    },
        *    {
        *      "id": "5cadd708b5d7570b954f46df",
        *      "nameOfOrganization": "my organization",
        *      "codeOrg": 1040
        *    },
        * ]
        *
        */
        $model = $this->container->get('organization');
        $result = !empty($args['codeOrg']) ? $model->findOne($args) : $model->find()->toArray();
        return $response->withJson($result);
    }
    
    public function getTransaction($request, $response, array $args)
    {
        /**
        * @api {get} /api/payment/transaction Получение список транзакции
        * @apiName Получение список транзакции
        * @apiParam {String} [transactionId] Код транзакции.
        * @apiParam {Number} [account] номер аккаунта.
        * @apiUse BasicAuth
        * @apiGroup LTS API
        * @apiSuccessExample Success-Response:
        *     HTTP/1.1 200 OK
        *
        *[
        *  {
        *      "id": "5cae1ccff8eed21bb21a8aea",
        *      "transactionId": "5cae1ccef8eed21bb21a8ae9",
        *       "sum": "1",
        *       "account": "555992211",
        *       "comment": "testing ",
        *       "paymentType": "2",
        *       "createdAt": "2019-04-10T16:41:51.645Z"
        *
        *  },
        *  {
        *      "id": "5cae1d9ff8eed21bb21a8aec",
        *      "transactionId": "5cae1d9ff8eed21bb21a8aeb",
        *      "sum": "1",
        *      "account": "555992211",
        *      "comment": "testing ",
        *      "paymentType": "2",
        *      "createdAt": "2019-04-10T16:45:19.619Z"
        *
        *  }
        *]
        *
        */

        $model = $this->container->get('transaction');
        $args['vendor'] = $request->getAttribute('vendor');
        if (!empty($args['_id'])) {
            $args['_id'] = new \MongoDB\BSON\ObjectId($args['_id']);
            $result =  $model->findOne($args);
            return $response->withJson($result);
        }
        $result = $model->find($args)->toArray();
        return $response->withJson($result);
    }

    public function getClientInfo($request, $response, $args)
    {
        /**
        * @api {get} /api/payment/client-info/:account/:codeOrg Получение данных о клиента
        * @apiName Получение данных водителя
        * @apiGroup LTS API
        * @apiUse BasicAuth
        * @apiHeader {String} user Basic Auth user login Который получены при согласовании
        * @apiHeader {String} password Basic Auth password Который получены при согласовании
        * @apiDescription Метод позволяет проверить регистрацию клиента в указанном организации по лицевому счету и получить его данные, такие как ФИО и наименование организации
        * @apiParam {String} account Номер лицевого счета водителя (как правило номер телефона водителя)
        * @apiParam {String} codeOrg Код организации. Код организации выдается при регистрации в системе и будет предоставлен в процессе интеграции с платежной системой. . Также можно получить список доступных таксопарков с их кодами отдельным запросом (смотреть ниже).
        * @apiSuccessExample Success-Response:
        *     HTTP/1.1 200 OK
        *     {
        *       "Full Name": "[R_1001] Ярославцев Александр Васильевич"
        *     }
        *
        * @apiUse BadRequest
        */
        $model = $this->container->get('mongo')->payment->organization;
        $model = $model->findOne(array_slice($args, 1, 1));
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://taximeter.yandex.rostaxi.org/pay/system/mobilnik/".$model['apiKey']."?command=check&account=".$args['account'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "Host: taximeter.yandex.rostaxi.org",
         ),
        ));
        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "Server Error";
        } else {
            $array = json_decode(json_encode((array)simplexml_load_string($res)), true);
            $data["FullName"] =$array['info']['extra'];
            $data["Organization"] =$model['nameOfOrganization'];
            return  $response->withJson($data);
        }
    }
    public function sendTelegram($request, $response)
    {
        /**
          * @api {post} /api/payment/transaction/status Метод позволяет изменить статус транзакции по данному transaction_id.
          * @apiName  update transaction Метод позволяет изменить статус транзакции.
          * @apiGroup LTS API
          * @apiUse BasicAuth
          * @apiHeader {String} user Basic Auth user login Который получены при согласовании
          * @apiHeader {String} password Basic Auth password Который получены при согласовании
          * @apiParam (Request body) {String} transaction_id id
          * @apiParam (Request body) {String} state статус
          * @apiParam (Request body) {json} [details] Произвольная обьект.
          *@apiSuccessExample Success-Response:
          *     HTTP/1.1 200 OK
          */
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }
}
