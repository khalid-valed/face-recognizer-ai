<?php
use Slim\Http\Request;
use Slim\Http\Response;

/**
   * @apiDefine BadRequest
   *
   * @apiError BadRequest Пожалуйста проверте данных правильность Обычно этот ощибка происходит из за не правилного ввода данных либо запись не найдено
   *
   * @apiErrorExample Error-Response:
   *     HTTP/1.1 400 Bad Request
   *    {
   *        "msg": "Сервер обнаружил в запросе клиента синтаксическую ошибку или не достаточно параметров"
   *    }
   */
  /**
   * @apiDefine BasicAuth
   * @apiError BasicAuth Стандарт подразумевает добавление в header каждого запроса к серверу строки вида Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l, где хеш - это логин и пароль без пробелов, разделенный двоеточием и закодированный алгоритмом Base64.
   * @apiErrorExample Error-Response:
   *     HTTP/1.1 401 Unauthorized
   *    {
   *      msg:"No Authorization header was found"
   *      header: "Authorization: 'Basic [required]'"
   *    }
   */
  /**
   * @apiDefine Conflict
   * @apiError Conflict Обычно происходит из-за повторного операция , например повторного транзакция с той же транзакции
   * @apiErrorExample Error-Response:
   *     HTTP/1.1 409 Conflict
   *    {
   *      "msg": "Платёж с номером 5cae26a4d316ab400ef23801 уже был проведён"
   *    }
   */

// Routes
$app->post('/api/image-upload/{codeOrg}', \ImageController::class . ':imageUploadAction');
$app->post('/api/image-upload-face/{codeOrg}', \ImageController::class . ':imageFaceSaveAction');
$app->get('/api/image-list/{codeOrg}', \ImageController::class . ':showUsersAction');
$app->get('/api/image-show/{codeOrg}/{userId}', \ImageController::class . ':showUserAction');
$app->post('/api/image-check/{codeOrg}', \ImageController::class . ':imageCheckAction');
$app->delete('/api/image-delete/{codeOrg}/{userId}', \ImageController::class . ':imageDeleteAction');
