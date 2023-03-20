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
        
        
        \Bitrix\Main\Mail\Event::send(array(
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
        
        return ['result' => true];
    }
}

