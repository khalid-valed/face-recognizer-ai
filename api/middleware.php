<?php
// Application middleware
 class isFormValidMiddleware
 {
     public function __invoke($request, $response, $next)
     {
         //$request->getBody()->write('Before');
         return $next($request, $response);
     }
 }
//$app->add(new isFormValidMiddleware());


$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
    'users' => [
        'admin'=>'admin',
        'yaros' => 'yaros',
    ],
    'secure' => false,

    'before' => function ($request, $arguments) {
        return $request->withAttribute('vendor', $arguments['user']);
    },
    'after' => function ($response, $arguments) {
        return $response->withHeader('X-powered-by', 'Yaros');
    },
    'error' => function ($response, $args) {
        return $response->withJson(['msg'=>'please check your credentials user or password can not be found']);
    }
]));
