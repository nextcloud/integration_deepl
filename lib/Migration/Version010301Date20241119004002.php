<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\IntegrationDeepl\Migration;

use Closure;
use OCP\AppFramework\Services\IAppConfig;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;
use OCP\Security\ICrypto;

class Version010301Date20241119004002 extends SimpleMigrationStep {

	public function __construct(
		private ICrypto $crypto,
		private IAppConfig $appConfig,
	) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
		foreach (['apikey'] as $key) {
			$value = $this->appConfig->getAppValueString($key);
			if ($value !== '') {
				$encryptedValue = $this->crypto->encrypt($value);
				$this->appConfig->setAppValueString($key, $encryptedValue);
			}
		}
	}
}
