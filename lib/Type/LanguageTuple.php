<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */


namespace OCA\IntegrationDeepl\Type;

use JsonSerializable;

class LanguageTuple implements JsonSerializable {
	public function __construct(
		public string $from,
		public string $fromLabel,
		public string $to,
		public string $toLabel
	) {
	}

	public function jsonSerialize(): array {
		return [
			'from' => $this->from,
			'fromLabel' => $this->fromLabel,
			'to' => $this->to,
			'toLabel' => $this->toLabel,
		];
	}

	public static function fromArray(array $data): LanguageTuple {
		return new self(
			$data['from'],
			$data['fromLabel'],
			$data['to'],
			$data['toLabel'],
		);
	}
}
