<?php

/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\IntegrationDeepl\AppInfo;

use OCA\IntegrationDeepl\Providers\TranslationProvider;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IConfig;

class Application extends App implements IBootstrap {
	public const APP_ID = 'integration_deepl';
	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		require_once __DIR__ . '/../../vendor/autoload.php';

		$context->registerTranslationProvider(TranslationProvider::class);

		$config = $this->getContainer()->query(IConfig::class);
		if (version_compare($config->getSystemValueString('version', '0.0.0'), '30.0', '>=')) {
			$context->registerTaskProcessingProvider(\OCA\IntegrationDeepl\Providers\TaskProcessingProvider::class);
		}
	}

	public function boot(IBootContext $context): void {
	}
}
