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


namespace OCA\IntegrationDeepl;

use DeepL\DeepLException;
use DeepL\Translator;
use OCA\IntegrationDeepl\AppInfo\Application;
use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\Translation\ITranslationProvider;

class DeeplProvider implements ITranslationProvider {
	private Translator $translator;
	private IConfig $config;
	private ICacheFactory $cacheFactory;

	private $localCache = [];

	public function __construct(IConfig $config, ICacheFactory $cacheFactory) {
		$this->config = $config;
		$this->cacheFactory = $cacheFactory;
		$this->translator = new Translator(
			$this->config->getAppValue(Application::APP_ID, 'apikey', ''),
			[]
		);
	}

	public function getName(): string {
		return 'Deepl.com';
	}

	public function getAvailableLanguages(): array {
		$cache = $this->cacheFactory->createDistributed('integration_deepl');
		if ($cached = $cache->get('languages')) {
			return $cached;
		}

		$sourceLanguages = $this->translator->getSourceLanguages();
		$targetLanguages = $this->translator->getTargetLanguages();
		$availableLanguages = [];
		foreach ($sourceLanguages as $sourceLanguage) {
			foreach ($targetLanguages as $targetLanguage) {
				$availableLanguages[] = [
					'from' => [
						'code' => $sourceLanguage->code,
						'name' => $sourceLanguage->name,
					],
					'to' => [
						'code' => $targetLanguage->code,
						'name' => $targetLanguage->name,
					],
				];
			}
		}
		$cache->set('languages', $availableLanguages, 3600);
		return $availableLanguages;
	}

	public function detectLanguage(string $text): ?string {
		try {
			$cacheKey = md5($text);
			$result = $this->localCache[$cacheKey] ?? $this->translator->translateText($text, null, 'en');
			$this->localCache[$cacheKey] = $result;
			return $result->detectedSourceLang;
		} catch (DeepLException $e) {
			return null;
		}
	}

	public function translate(?string $fromLanguage, string $toLanguage, string $text): string {
		try {
			$cacheKey = ($fromLanguage ?? '') . md5($text);
			$result = $this->localCache[$cacheKey] ?? $this->translator->translateText($text, $fromLanguage, $toLanguage);
			$this->localCache[$cacheKey] = $result;
			return $result->text;
		} catch (DeepLException $e) {
			throw new \Exception('Unable to translate');
		}
	}
}
