<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */


namespace OCA\IntegrationDeepl\Providers;

use OCA\IntegrationDeepl\AppInfo\Application;
use OCA\IntegrationDeepl\Service\DeeplService;
use OCA\IntegrationDeepl\Service\UtilsService;
use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\IL10N;
use OCP\L10N\IFactory;
use OCP\TaskProcessing\ISynchronousProvider;
use OCP\TaskProcessing\ShapeEnumValue;
use OCP\TaskProcessing\TaskTypes\TextToTextTranslate;
use Psr\Log\LoggerInterface;

class TaskProcessingProvider extends DeeplService implements ISynchronousProvider {

	private const DETECT_LANGUAGE = 'detect_language';

	public function __construct(
		private IConfig $config,
		private ICacheFactory $cacheFactory,
		private LoggerInterface $logger,
		private IFactory $l10nFactory,
		private IL10N $l,
		private UtilsService $utilsService,
	) {
		parent::__construct($config, $cacheFactory, $logger, $l10nFactory, $utilsService);
	}

	public function getId(): string {
		return Application::APP_ID;
	}

	public function getTaskTypeId(): string {
		return TextToTextTranslate::ID;
	}

	public function getExpectedRuntime(): int {
		return 30;
	}

	public function getOptionalInputShape(): array {
		return [];
	}

	public function getOptionalOutputShape(): array {
		return [];
	}

	public function getInputShapeEnumValues(): array {
		$availableLanguages = $this->getAvailableLanguages();
		$originLanguages = [];
		$targetLanguages = [];

		foreach ($availableLanguages as $language) {
			$originLanguages[] = new ShapeEnumValue($language->fromLabel, $language->from);
			$targetLanguages[] = new ShapeEnumValue($language->toLabel, $language->to);
		}

		$detectLanguageEnumValue = new ShapeEnumValue($this->l->t('Detect language'), self::DETECT_LANGUAGE);
		return [
			'origin_language' => array_merge([$detectLanguageEnumValue], $originLanguages),
			'target_language' => $targetLanguages,
		];
	}

	public function getInputShapeDefaults(): array {
		return [ 'origin_language' => self::DETECT_LANGUAGE ];
	}

	public function getOptionalInputShapeEnumValues(): array {
		return [];
	}

	public function getOptionalInputShapeDefaults(): array {
		return [];
	}

	public function getOutputShapeEnumValues(): array {
		return [];
	}

	public function getOptionalOutputShapeEnumValues(): array {
		return [];
	}

	public function process(?string $userId, array $input, callable $reportProgress): array {
		/** @var string */
		$text = $input['input'];
		/** @var string|null */
		$originLanguage = $input['origin_language'];
		/** @var string */
		$targetLanguage = $input['target_language'];

		if ($originLanguage === self::DETECT_LANGUAGE) {
			$originLanguage = null;
		}

		$translation = $this->translate($originLanguage, $targetLanguage, $text);

		return [
			'output' => $translation,
		];
	}
}
