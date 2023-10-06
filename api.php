<?php

/** @var Router $router */

$router->group(['middleware' => ['user-authentication']], function (Router $router) {

  $router->group([
      'prefix' => '/accounts',
      'namespace' => '\App\Http\Controllers\Accounts'
    ], function (Router $router) {
      $router->get('/', 'AccountsController@getAccounts');
      $router->post('/', 'AccountsController@createAccount');
      $router->get('/{account_id}', 'AccountsController@getAccount');
      $router->post('/{account_id}', 'AccountsController@updateAccount');
      $router->get('/{account_id}/allowance-remaining', 'AccountsController@getAllowanceRemaining');
    });

    $router->group([
        'prefix' => '/investments',
        'namespace' => '\App\Http\Controllers\Investments'
    ], function (Router $router) {
      $router->get('/', 'InvestmentsController@getInvestments');
      $router->get('/{investment_id}', 'InvestmentsController@getInvestment');
      $router->post('/', 'InvestmentsController@createInvestment');
      $router->post('/{investment_id}', 'InvestmentsController@updateInvestment');  
      $router->patch('/{investment_id}', 'InvestmentsController@endInvestment');        
    });
 
    $router->group([
        'prefix' => '/funds',
        'namespace' => '\App\Http\Controllers\Funds'
    ], function (Router $router) {
        $router->get('/', 'FundsController@getFunds');
    });
         
});
