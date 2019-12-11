define({ "api": [
  {
    "type": "get",
    "url": "/api/payment/client-info/:account/:codeOrg",
    "title": "Получение данных о клиента",
    "name": "_________________________",
    "group": "LTS_API",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "user",
            "description": "<p>Basic Auth user login Который получены при согласовании</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Basic Auth password Который получены при согласовании</p>"
          }
        ]
      }
    },
    "description": "<p>Метод позволяет проверить регистрацию клиента в указанном организации по лицевому счету и получить его данные, такие как ФИО и наименование организации</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "account",
            "description": "<p>Номер лицевого счета водителя (как правило номер телефона водителя)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "codeOrg",
            "description": "<p>Код организации. Код организации выдается при регистрации в системе и будет предоставлен в процессе интеграции с платежной системой. . Также можно получить список доступных таксопарков с их кодами отдельным запросом (смотреть ниже).</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"Full Name\": \"[R_1001] Ярославцев Александр Васильевич\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "api/Controller/AbstractController.php",
    "groupTitle": "LTS_API",
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "BasicAuth",
            "description": "<p>Стандарт подразумевает добавление в header каждого запроса к серверу строки вида Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l, где хеш - это логин и пароль без пробелов, разделенный двоеточием и закодированный алгоритмом Base64.</p>"
          },
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "BadRequest",
            "description": "<p>Пожалуйста проверте данных правильность Обычно этот ощибка происходит из за не правилного ввода данных либо запись не найдено</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 401 Unauthorized\n{\n  msg:\"No Authorization header was found\"\n  header: \"Authorization: 'Basic [required]'\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 400 Bad Request\n{\n    \"msg\": \"Сервер обнаружил в запросе клиента синтаксическую ошибку или не достаточно параметров\"\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "get",
    "url": "/api/payment/transaction",
    "title": "Получение список транзакции",
    "name": "___________________________",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "transactionId",
            "description": "<p>Код транзакции.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "account",
            "description": "<p>номер аккаунта.</p>"
          }
        ]
      }
    },
    "group": "LTS_API",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n\n[\n {\n     \"id\": \"5cae1ccff8eed21bb21a8aea\",\n     \"transactionId\": \"5cae1ccef8eed21bb21a8ae9\",\n      \"sum\": \"1\",\n      \"account\": \"555992211\",\n      \"comment\": \"testing \",\n      \"paymentType\": \"2\",\n      \"createdAt\": \"2019-04-10T16:41:51.645Z\"\n\n },\n {\n     \"id\": \"5cae1d9ff8eed21bb21a8aec\",\n     \"transactionId\": \"5cae1d9ff8eed21bb21a8aeb\",\n     \"sum\": \"1\",\n     \"account\": \"555992211\",\n     \"comment\": \"testing \",\n     \"paymentType\": \"2\",\n     \"createdAt\": \"2019-04-10T16:45:19.619Z\"\n\n }\n]",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "api/Controller/AbstractController.php",
    "groupTitle": "LTS_API",
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "BasicAuth",
            "description": "<p>Стандарт подразумевает добавление в header каждого запроса к серверу строки вида Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l, где хеш - это логин и пароль без пробелов, разделенный двоеточием и закодированный алгоритмом Base64.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 401 Unauthorized\n{\n  msg:\"No Authorization header was found\"\n  header: \"Authorization: 'Basic [required]'\"\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "get",
    "url": "/api/payment/organization",
    "title": "Получение список организации.",
    "name": "_____________________________",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "codeOrg",
            "description": "<p>Код организации. Код организации выдается при регистрации в системе и будет предоставлен в процессе интеграции с платежной системой. получить список доступных таксопарков с их кодами отдельным запросом (смотреть ниже).</p>"
          }
        ]
      }
    },
    "group": "LTS_API",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n[\n   {\n     \"id\": \"5cadd708b5d7570b954f46df\",\n     \"nameOfOrganization\": \"Такси №1 Яндекс\",\n     \"codeOrg\": 1001\n   },\n   {\n     \"id\": \"5cadd708b5d7570b954f46df\",\n     \"nameOfOrganization\": \"my organization\",\n     \"codeOrg\": 1040\n   },\n]",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "api/Controller/AbstractController.php",
    "groupTitle": "LTS_API",
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "BasicAuth",
            "description": "<p>Стандарт подразумевает добавление в header каждого запроса к серверу строки вида Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l, где хеш - это логин и пароль без пробелов, разделенный двоеточием и закодированный алгоритмом Base64.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 401 Unauthorized\n{\n  msg:\"No Authorization header was found\"\n  header: \"Authorization: 'Basic [required]'\"\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "post",
    "url": "/api/payment/transaction/status",
    "title": "Метод позволяет изменить статус транзакции по данному transaction_id.",
    "name": "update_transaction____________________________________________",
    "group": "LTS_API",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "user",
            "description": "<p>Basic Auth user login Который получены при согласовании</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Basic Auth password Который получены при согласовании</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Request body": [
          {
            "group": "Request body",
            "type": "String",
            "optional": false,
            "field": "transaction_id",
            "description": "<p>id</p>"
          },
          {
            "group": "Request body",
            "type": "String",
            "optional": false,
            "field": "state",
            "description": "<p>статус</p>"
          },
          {
            "group": "Request body",
            "type": "json",
            "optional": true,
            "field": "details",
            "description": "<p>Произвольная обьект.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "api/Controller/AbstractController.php",
    "groupTitle": "LTS_API",
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "BasicAuth",
            "description": "<p>Стандарт подразумевает добавление в header каждого запроса к серверу строки вида Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l, где хеш - это логин и пароль без пробелов, разделенный двоеточием и закодированный алгоритмом Base64.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 401 Unauthorized\n{\n  msg:\"No Authorization header was found\"\n  header: \"Authorization: 'Basic [required]'\"\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "post",
    "url": "/api/payment/transaction/status",
    "title": "Метод позволяет изменить статус транзакции по данному transaction_id.",
    "name": "update_transaction____________________________________________",
    "group": "LTS_API",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "user",
            "description": "<p>Basic Auth user login Который получены при согласовании</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Basic Auth password Который получены при согласовании</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Request body": [
          {
            "group": "Request body",
            "type": "String",
            "optional": false,
            "field": "transaction_id",
            "description": "<p>id</p>"
          },
          {
            "group": "Request body",
            "type": "String",
            "optional": false,
            "field": "state",
            "description": "<p>статус</p>"
          },
          {
            "group": "Request body",
            "type": "json",
            "optional": true,
            "field": "details",
            "description": "<p>Произвольная обьект.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "api/Controller/PaymentController.php",
    "groupTitle": "LTS_API",
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "BasicAuth",
            "description": "<p>Стандарт подразумевает добавление в header каждого запроса к серверу строки вида Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l, где хеш - это логин и пароль без пробелов, разделенный двоеточием и закодированный алгоритмом Base64.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 401 Unauthorized\n{\n  msg:\"No Authorization header was found\"\n  header: \"Authorization: 'Basic [required]'\"\n}",
          "type": "json"
        }
      ]
    }
  }
] });
