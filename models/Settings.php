<?php

namespace IgniterLabs\GiftUp\Models;

use Exception;
use Igniter\Flame\Database\Model;
use IgniterLabs\GiftUp\Classes\Manager;

class Settings extends Model
{
    public $implement = [\System\Actions\SettingsModel::class];

    // A unique code
    public $settingsCode = 'igniterlabs_giftup_settings';

    // Reference to field configuration
    public $settingsFieldsConfig = 'settings';

    public static function isConnected()
    {
        return self::isConfigured() && strlen(self::getCompanyId());
    }

    public static function getApiKey()
    {
        return self::get('api_key');
    }

    public static function isStaging()
    {
        return self::get('is_live') === 'staging';
    }

    public static function getMinimumValue()
    {
        return (int)self::get('minimum_value', 0);
    }

    public static function getCompanyId()
    {
        return array_get(self::get('company_info', []), 'id');
    }

    public function afterSave()
    {
        try {
            $oldCompanyId = array_get($this->data, 'company_info.id');
            $company = Manager::instance()->fetchCompany();
            $companyId = array_get($company, 'id');

            if (!$companyId || $oldCompanyId === $companyId)
                return;

            $this->set('company_info', $company);
        }
        catch (Exception $ex) {
        }
    }
}
