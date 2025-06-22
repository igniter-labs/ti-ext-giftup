<?php

declare(strict_types=1);

namespace IgniterLabs\GiftUp\Models;

use Igniter\Flame\Database\Model;
use Igniter\System\Actions\SettingsModel;
use IgniterLabs\GiftUp\Classes\Manager;

/**
 * Settings Model for GiftUp integration.
 *
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed set(string|array $key, mixed $value = null)
 * @mixin SettingsModel
 */
class Settings extends Model
{
    public array $implement = [SettingsModel::class];

    // A unique code
    public string $settingsCode = 'igniterlabs_giftup_settings';

    // Reference to field configuration
    public string $settingsFieldsConfig = 'settings';

    public static function isConnected(): bool
    {
        return self::getApiKey() && self::getCompanyId();
    }

    public static function getApiKey(): string
    {
        return (string)self::get('api_key');
    }

    public static function isStaging(): bool
    {
        return (string)self::get('is_live') === 'staging';
    }

    public static function getMinimumValue(): int
    {
        return (int)self::get('minimum_value', 0); // @phpstan-ignore-line arguments.count
    }

    public static function getCompanyId(): string
    {
        return (string)array_get(self::get('company_info', []), 'id'); // @phpstan-ignore-line arguments.count
    }

    protected function afterSave()
    {
        rescue(function(): void {
            $oldCompanyId = array_get($this->data, 'company_info.id');
            $company = resolve(Manager::class)->fetchCompany();
            $companyId = array_get($company, 'id');

            if ($companyId && $oldCompanyId !== $companyId) {
                static::set('company_info', $company);
            }
        });
    }
}
