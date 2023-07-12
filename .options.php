<?php
// файл должен возвращать массив параметров модуля
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
return [
        'debug' => [
                'title' => Loc::getMessage('X_MODULE_OPTIONS_DEBUG'),
                'value' => 'Y'
            ],
        'captcha' => [
                'title' => 'Проверять CAPTCHA',
                'value' => 'Y',
                'default' => 'N'
            ],
        'hidefield' => [
                'title' => 'Скрытое поле',
                'default' => 'COMMENT'
            ],
		'csrf' => [
               'title' => 'Отключить проверку csrf',
               'default' => 'Текст',
                'value' => 'Y',
                'default' => 'Y'
           ],
//        'option_checkbox' => [
//                'title' => 'Флажок',
//                'default' => '',
//                'value' => 92
//            ],
//		'option_select' => [
//                'title' => 'Список',
//                'default' => '',
//                'options' => [
//                    1 => 'Один',
//                    2 => 'Два',
//                    3 => 'Три'
//                ]
//            ]
    ];