<?php
namespace App\Exceptions;

/**
 * No Permitted to record exception class
 * @package YetiForce.Exception
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
class NoPermittedToRecord extends NoPermitted
{

	public function __construct($message = '', $code = 0, \Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
		\App\Session::init();

		$request = \App\Request::init();
		$record = $request->getInteger('record', 0);
		$userName = \App\Session::get('full_user_name');
		\App\DB::getInstance('log')->createCommand()->insert('o_#__access_to_record', [
			'username' => empty($userName) ? '-' : $userName,
			'date' => date('Y-m-d H:i:s'),
			'ip' => \App\RequestUtil::getRemoteIP(),
			'record' => $record,
			'module' => $request->getModule(),
			'url' => \App\RequestUtil::getBrowserInfo()->url,
			'agent' => \App\Request::_getServer('HTTP_USER_AGENT', '-'),
			'request' => json_encode($_REQUEST),
			'referer' => \App\Request::_getServer('HTTP_REFERER', '-'),
		])->execute();
	}
}
