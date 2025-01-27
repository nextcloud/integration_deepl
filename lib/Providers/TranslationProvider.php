<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */


namespace OCA\IntegrationDeepl\Providers;

use OCA\IntegrationDeepl\Service\DeeplService;
use OCP\Translation\IDetectLanguageProvider;
use OCP\Translation\ITranslationProvider;

class TranslationProvider extends DeeplService implements ITranslationProvider, IDetectLanguageProvider {
}
