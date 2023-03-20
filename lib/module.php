<?php
namespace X\Postman;

\Bitrix\Main\Loader::includeModule('x.module');
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Module extends \X\Module\Module
{
	function __construct(array $dctEnvModule=[])
	{
		if(!count($dctEnvModule)) $dctEnvModule = include(dirname(__DIR__).'/.env.php');
		return parent::__construct($dctEnvModule);
	}
}