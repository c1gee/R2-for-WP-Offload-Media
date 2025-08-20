# Cloudflare R2 Setup for WP Offload Media

This setup allows you to use Cloudflare R2 (S3-compatible storage) with the WP Offload Media plugin.

## Installation

Add `cloudflare-r2-support.php` to the mu-plugins folder.

## Files Created

1. **`cloudflare-r2-support.php`** - Adds R2 endpoint support to the AWS provider by injecting the custom endpoint URL
2. **`wp-offload-media-r2-config.php`** - Automatically configures the plugin settings for R2 when the plugin initializes

## Configuration

Add the `r2_endpoint` value provided in the Cloudflare R2 API admin screen to the AS3CF config.

**API Tokens Location:** https://dash.cloudflare.com/ACCOUNTID/r2/api-tokens

```php
define( 'AS3CF_SETTINGS', serialize( array(
    'provider' => 'aws',
    'access-key-id' => '********************',
    'secret-access-key' => '**************************************',
    'r2_endpoint' => '********************'
) ) );
```

## How It Works

1. **Endpoint Injection**: The `cloudflare-r2-support.php` file hooks into the AWS provider's client creation process and injects the R2 endpoint URL. This allows the AWS SDK to communicate with R2 instead of AWS S3.

2. **S3 Compatibility**: Cloudflare R2 is S3-compatible so that we can use the existing AWS provider with custom endpoint configuration.

## Required R2 Settings

- **Access Key ID**: Your R2 access key
- **Secret Access Key**: Your R2 secret key  
- **Endpoint**: Your R2 endpoint URL (format: `https://account-id.r2.cloudflarestorage.com`)

## Troubleshooting

**Cannot read buckets** - Your API token credentials need to be set to **Admin Read & Write** to be able to fetch and read buckets.

## Notes

- This solution works by extending the existing AWS provider
- R2 is S3-compatible, so no major modifications to the plugin are needed
- The custom endpoint allows R2 to work seamlessly with the plugin
