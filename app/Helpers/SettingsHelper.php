<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    /**
     * Get company name from settings
     *
     * @param string $default
     * @return string
     */
    public static function getCompanyName($default = 'Veasat')
    {
        return Setting::getValue('company_name', $default);
    }

    /**
     * Get company logo URL from settings
     *
     * @param string $default
     * @return string
     */
    public static function getCompanyLogo($default = 'images/default-logo.png')
    {
        $logo = Setting::getValue('company_logo');

        if ($logo) {
            // If logo starts with http/https, return as is (external URL)
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                return $logo;
            }

            // If logo doesn't start with images/, prepend it
            if (!str_starts_with($logo, 'images/')) {
                $logo = 'images/' . $logo;
            }

            return asset($logo);
        }

        return asset($default);
    }

    /**
     * Get company address from settings
     *
     * @param string $default
     * @return string
     */
    public static function getCompanyAddress($default = '')
    {
        return Setting::getValue('company_address', $default);
    }

    /**
     * Get company email from settings
     *
     * @param string $default
     * @return string
     */
    public static function getCompanyEmail($default = '')
    {
        return Setting::getValue('company_email', $default);
    }

    /**
     * Get support phone from settings
     *
     * @param string $default
     * @return string
     */
    public static function getSupportPhone($default = '')
    {
        return Setting::getValue('support_phone', $default);
    }

    /**
     * Get support email from settings
     *
     * @param string $default
     * @return string
     */
    public static function getSupportEmail($default = '')
    {
        return Setting::getValue('support_email', $default);
    }

    /**
     * Get all company settings at once using Setting model
     *
     * @return array
     */
    public static function getCompanySettings()
    {
        $slugs = [
            'company_name',
            'company_logo',
            'company_address',
            'company_email',
            'support_phone',
            'support_email'
        ];

        $settings = Setting::getMultiple($slugs);

        return [
            'name' => $settings->get('company_name')?->value ?? 'Veasat',
            'logo' => static::getCompanyLogo(),
            'address' => $settings->get('company_address')?->value ?? '',
            'email' => $settings->get('company_email')?->value ?? '',
            'support_phone' => $settings->get('support_phone')?->value ?? '',
            'support_email' => $settings->get('support_email')?->value ?? '',
        ];
    }

    /**
     * Get application meta settings for SEO using Setting model
     *
     * @return array
     */
    public static function getMetaSettings()
    {
        $slugs = [
            'app_description',
            'app_keywords',
            'app_author'
        ];

        $settings = Setting::getMultiple($slugs);

        return [
            'description' => $settings->get('app_description')?->value ?? 'Professional internet data subscription service',
            'keywords' => $settings->get('app_keywords')?->value ?? 'internet, data, subscription, nigeria',
            'author' => $settings->get('app_author')?->value ?? static::getCompanyName(),
        ];
    }

    /**
     * Check if Paystack is enabled using Setting model
     *
     * @return bool
     */
    public static function isPaystackEnabled()
    {
        return (bool) Setting::getValue('paystack_enabled', false);
    }

    /**
     * Get Paystack public key using Setting model
     *
     * @return string|null
     */
    public static function getPaystackPublicKey()
    {
        return Setting::getValue('paystack_public_key');
    }

    /**
     * Clear all settings cache using Setting model
     *
     * @return void
     */
    public static function clearCache()
    {
        Setting::clearCache();
    }

    /**
     * Get a setting value using Setting model
     *
     * @param string $slug
     * @param mixed $default
     * @return mixed
     */
    public static function get($slug, $default = null)
    {
        return Setting::getValue($slug, $default);
    }

    /**
     * Set a setting value using Setting model
     *
     * @param string $slug
     * @param mixed $value
     * @param string|null $name
     * @return \App\Models\Setting
     */
    public static function set($slug, $value, $name = null)
    {
        return Setting::setValue($slug, $value, $name);
    }
}
