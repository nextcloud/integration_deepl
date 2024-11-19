<?php
/**
 * Nextcloud - DeepL
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Anupam Kumar <kyteinsky@gmail.com>
 * @copyright Anupam Kumar 2023
 */

namespace OCA\IntegrationDeepl\Controller;

use OCA\IntegrationDeepl\AppInfo\Application;
use OCA\IntegrationDeepl\Service\UtilsService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;

class ConfigController extends Controller {

	public function __construct(
		string   $appName,
		IRequest $request,
		private IConfig  $config,
		private UtilsService $utilsService,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Set admin config values
	 *
	 * @param array $values key/value pairs to store in app config
	 * @return DataResponse
	 */
	#[PasswordConfirmationRequired]
	public function setAdminConfig(array $values): DataResponse {
		foreach ($values as $key => $value) {
			if ($key === 'apikey' && $value !== '') {
				$this->utilsService->setEncryptedAppValue($key, $value);
			} else {
				$this->config->setAppValue(Application::APP_ID, $key, $value);
			}
		}
		return new DataResponse(1);
	}
}
