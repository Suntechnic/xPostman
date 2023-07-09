<?php
/*
                                {class}{method}
BX.ajax.runAction('x:postman.api.mail.send')
    .then(function(responce) {
        console.log(responce);
    });
*/
namespace X\Postman\Api;

class Mail extends \Bitrix\Main\Engine\Controller
{
    
    private $actionsConfig = [
            'send' => [
                    '-prefilters' => [
                            '\Bitrix\Main\Engine\ActionFilter\Authentication'
                        ]
                ],
        ];
    
    protected function init()
	{
        parent::init();
        foreach ($this->actionsConfig as $name=>$arConfig) $this->setActionConfig($name, $arConfig);
	}
    
    public function sendAction (string $EventName, array $dctFields, string $HlBlock='')
    {
        
        $selfModule = new \X\Postman\Module();
        // тут должна быть допустимых событий

        $dctResponse = [
                'result' => false,
                'errors' => []
            ];
        
        ////////////////////////////////// контроль капчи /////////////////////////////////////////////
        //https://dev.1c-bitrix.ru/user_help/settings/settings/captcha.php
        if ($selfModule->getOption('captcha') == 'Y') {

            include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php"); 
            $cptcha = new \CCaptcha(); 


            if(!strlen($dctFields["captcha_word"])>0){ 
                $dctResponse['errors'][] = ['text' => "Не введен защитный код"]; 
            } elseif(!$cptcha->CheckCode($dctFields["captcha_word"],$dctFields["captcha_sid"])){ 
                $dctResponse['errors'][] = ['text' => "Не правильный защитны код"];
            } 
            if(count($dctResponse['errors']) >0) {
                return $dctResponse;
            }
        }
        ////////////////////////////////// контроль капчи /////////////////////////////////////////////

        ////////////////////////////////// контроль скртытого поля /////////////////////////////////////////////
        if ($selfModule->getOption('hidefield') && $dctFields[$selfModule->getOption('hidefield')]) {
            $dctResponse['result'] = true;
            return $dctResponse;
        }
        ////////////////////////////////// контроль скртытого поля /////////////////////////////////////////////

        $dctResponse['result'] = \Bitrix\Main\Mail\Event::send(array(
                'EVENT_NAME' => $EventName,
                'LID' => SITE_ID,
                'C_FIELDS' => $dctFields,
            ));

        // фиксация в хайлойд блоке:
        if ($HlBlock) {
            \Bitrix\Main\Loader::includeModule('highloadblock');
            $res = \Bitrix\Highloadblock\HighloadBlockTable::getList([
                    'filter' => ['NAME' => 'Postman'.$HlBlock]
                ]);
            if ($hlBlockData = $res->fetch()) {
                // такой блок есть
                $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlBlockData);
                $class = $entity->getDataClass();
                $fields = $entity->getFields();

                $dctField4add = [];
                foreach ($fields as $field) {
                    $FieldName = $field->getColumnName();
                    $EmailFieldsName = substr($FieldName,3);
                    if ($EmailFieldsName && $dctFields[$EmailFieldsName]) {
                        $dctField4add[$FieldName] = $dctFields[$EmailFieldsName];
                    }
                }

                if (count($dctField4add)) {
                    $class::add($dctField4add);
                }
            }
        }


        if (defined('APPLICATION_ENV') && APPLICATION_ENV != 'production') {
            $dctResponse['debug'] = [
                    '$dctFields' => $dctFields,
                    '$EventName' => $EventName
                ];
        }

        return $dctResponse;
    }
}

