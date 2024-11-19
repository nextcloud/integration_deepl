<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2022 Julius Härtl <jus@bitgrid.net>
 *
 * @author Julius Härtl <jus@bitgrid.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


namespace OCA\IntegrationDeepl\Service;

use DeepL\DeepLException;
use DeepL\Translator;
use OCA\IntegrationDeepl\AppInfo\Application;
use OCA\IntegrationDeepl\Type\LanguageTuple;
use OCA\IntegrationDeepl\Service\UtilsService;
use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\L10N\IFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;

class DeeplService {
	private Translator $translator;
	public const IDENTIFIER_FORMAL = 'more';
	public const IDENTIFIER_INFORMAL = 'less';

	private array $localCache = [];

	public function __construct(
		private IConfig $config,
		private ICacheFactory $cacheFactory,
		private LoggerInterface $logger,
		private IFactory $l10nFactory,
		private UtilsService $utilsService,
	) {
		try {
			$this->translator = new Translator(
				$this->utilsService->getEncryptedAppValue('apikey'),
				[]
			);
		} catch (DeepLException $e) {
			throw new RuntimeException('Failed to initialize deepl translator class, ensure you have entered the required api key in the admin settings', 0, $e);
		}
	}

	public function getName(): string {
		return 'Deepl.com';
	}

	/**
	 * @return array<LanguageTuple>
	 */
	public function getAvailableLanguages(): array {
		$cache = $this->cacheFactory->createDistributed('integration_deepl');
		if ($cached = $cache->get('languages')) {
			return array_map(function ($entry) {
				return $entry instanceof LanguageTuple ? $entry : LanguageTuple::fromArray($entry);
			}, $cached);
		}

		try {
			$sourceLanguages = $this->translator->getSourceLanguages();
			$targetLanguages = $this->translator->getTargetLanguages();
		} catch (DeepLException $e) {
			$this->logger->error('Failed to fetch supported languages', ['exception' => $e]);
			return [];
		}

		$coreL = $this->l10nFactory->getLanguages();
		$coreLanguages = array_reduce(array_merge($coreL['commonLanguages'], $coreL['otherLanguages']), function ($carry, $val) {
			$carry[$val['code']] = $val['name'];
			return $carry;
		});
		$l10n = $this->l10nFactory->get(Application::APP_ID);

		$availableLanguages = [];
		foreach ($sourceLanguages as $sourceLanguage) {
			foreach ($targetLanguages as $targetLanguage) {
				if ($targetLanguage->code === $sourceLanguage->code) {
					continue;
				}

				$sourceName = $coreLanguages[strtolower($sourceLanguage->code)]
					?? $sourceLanguage->name;
				$targetName = $coreLanguages[strtolower($targetLanguage->code)]
					?? $targetLanguage->name;

				if ($targetLanguage->supportsFormality) {
					$targetNameFormal = $targetName . ' - ' . $l10n->t('formal');
					$targetNameInformal = $targetName . ' - ' . $l10n->t('informal');
					if ($targetLanguage->code === 'DE') {
						$targetNameFormal = $coreLanguages['de_DE'];
						$targetNameInformal = $coreLanguages['de'];
					}

					$availableLanguages[] = new LanguageTuple(
						$sourceLanguage->code, $sourceName,
						$targetLanguage->code . '_' . self::IDENTIFIER_FORMAL,
						$targetNameFormal
					);
					$availableLanguages[] = new LanguageTuple(
						$sourceLanguage->code, $sourceName,
						$targetLanguage->code . '_' . self::IDENTIFIER_INFORMAL,
						$targetNameInformal
					);
				} else {
					$availableLanguages[] = new LanguageTuple(
						$sourceLanguage->code, $sourceName,
						$targetLanguage->code, $targetName
					);
				}
			}
		}

		$cache->set('languages', $availableLanguages, 3600);
		return $availableLanguages;
	}

	public function detectLanguage(string $text): ?string {
		try {
			$cacheKey = md5($text);
			$result = $this->localCache[$cacheKey] ?? $this->translator->translateText($text, null, 'en-US');
			$this->localCache[$cacheKey] = $result;
			return $result->detectedSourceLang;
		} catch (DeepLException $e) {
			$this->logger->error('Failed to detect language', ['exception' => $e]);
			return null;
		}
	}

	public function translate(?string $fromLanguage, string $toLanguage, string $text): string {
		try {
			$cacheKey = ($fromLanguage ?? '') . '/' . $toLanguage . '/' . md5($text);

			// If formality is passed in the language code, use it but otherwise prefer formal
			$splitLanguage = explode('_', $toLanguage, 2);
			$toLanguage = $splitLanguage[0];
			$formal = $splitLanguage[1] ?? 'prefer_more';
			$formality = $formal === 'less' ? 'prefer_less' : 'prefer_more';

			$result = $this->localCache[$cacheKey] ?? $this->translator->translateText($text, $fromLanguage, $toLanguage, [
				'formality' => $formality,
			]);
			$this->localCache[$cacheKey] = $result;
			return $result->text;
		} catch (DeepLException $e) {
			throw new RuntimeException("Failed translate from {$fromLanguage} to {$toLanguage}", 0, $e);
		}
	}
}
