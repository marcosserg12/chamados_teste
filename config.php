<?php
if (!defined('TWILIO_SID')) {
    define('TWILIO_SID', 'AC48ef3e13fbbb859457bd4d30410f30cd');
}

if (!defined('TWILIO_TOKEN')) {
    define('TWILIO_TOKEN', '5d2ec6b13b658fb01a9a13e287424d34');
}

if (!defined('TWILIO_WHATSAPP_NUMBER')) {
    define('TWILIO_WHATSAPP_NUMBER', 'whatsapp:+14155238886');
}

return [
    'mailer' => [
        'from' => [
            'name' => 'Ibranutro',
            'address' => 'no-reply@ibranutro.com.br'
        ],
        'host' => 'smtp-relay.sendinblue.com',
        'port' => '587',
        'user' => 'matheuscabral@ibranutro.com.br',
        'password' => 'h4nBTDyJvaQNsFCK',
        'debug' => false
    ],
    // 'database' => [
    //     'host' => 'ibranutrodb.postgresql.dbaas.com.br',
    //     'dbname' => 'ibranutrodb',
    //     'user' => 'ibranutrodb',
    //     'password' => 'Senha@123',
    //     'port' => '5432'
    // ],
    'database' => [
        'host' => '145.223.26.225',
        'port' => '3306',
        'dbname' => 'chamado',
        'user' => 'marcos',
        'password' => 'M@rcos648209'
    ],
    'debug' => false
];
