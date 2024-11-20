<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\IntegrationDeepl\Migration;

use Closure;
use OCA\IntegrationDeepl\AppInfo\Application;
use OCP\IConfig;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;
use OCP\Security\ICrypto;

class Version010301Date20241119004002 extends SimpleMigrationStep {

	public function __construct(
		private ICrypto $crypto,
		private IConfig $config,
	) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
		foreach (['apikey'] as $key) {
			$value = $this->config->getAppValue(Application::APP_ID, $key);
			if ($value !== '') {
				$encryptedValue = $this->crypto->encrypt($value);
				$this->config->setAppValue(Application::APP_ID, $key, $encryptedValue);
			}
		}
	}
}
