<?php

declare(strict_types=1);

namespace OCA\IntegrationDeepl\Service;

use Exception;
use OCA\IntegrationDeepl\AppInfo\Application;
use OCP\IConfig;
use OCP\Security\ICrypto;

class UtilsService {
	/**
	 * Service providing storage, circles and tags tools
	 */
	public function __construct(
		private IConfig $config,
		private ICrypto $crypto,
	) {
	}

	/**
	 * Get decrypted app value
	 *
	 * @param string $key
	 * @return string
	 * @throws Exception
	 */
	public function getEncryptedAppValue(string $key): string {
		$storedValue = $this->config->getAppValue(Application::APP_ID, $key);
		if ($storedValue === '') {
			return '';
		}
		return $this->crypto->decrypt($storedValue);
	}

	/**
	 * Store encrypted app secret
	 *
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function setEncryptedAppValue(string $key, string $value): void {
		if ($value === '') {
			$this->config->setAppValue(Application::APP_ID, $key, '');
		} else {
			$encryptedClientSecret = $this->crypto->encrypt($value);
			$this->config->setAppValue(Application::APP_ID, $key, $encryptedClientSecret);
		}
	}
}
