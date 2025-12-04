# Installation Source Verification

## ✅ Files Correctly Using Your Repository

### Installation Scripts
- **`scripts/install.sh`**: ✅ Uses `https://raw.githubusercontent.com/oudayfatteh0/coolify-white-label/refs/heads/v4.x/`
  - Downloads `versions.json` from your repo
  - Downloads `docker-compose.yml` from your repo
  - Downloads `docker-compose.prod.yml` from your repo
  - Downloads `.env.production` from your repo
  - Downloads `scripts/upgrade.sh` from your repo

- **`scripts/upgrade.sh`**: ✅ Uses `https://raw.githubusercontent.com/oudayfatteh0/coolify-white-label/refs/heads/v4.x/`
  - Downloads `docker-compose.yml` from your repo
  - Downloads `docker-compose.prod.yml` from your repo
  - Downloads `.env.production` from your repo

## ⚠️ Files That Still Reference Coolify (Non-Critical)

### Docker Images (OK to Keep)
- **`scripts/upgrade.sh`** (lines 69, 71): Uses `coollabsio/coolify-helper` Docker image
  - **Status**: ✅ OK - These are Docker images from GitHub Container Registry, not code
  - These images are built by Coolify and hosted on GHCR
  - Your fork uses the same Docker images, which is correct

### Configuration Files (Defaults Only)
- **`config/constants.php`** (lines 15-17): Default CDN URLs
  - **Status**: ⚠️ Non-critical - These are defaults that can be overridden via environment variables
  - Used for version checking within the application
  - Can be overridden with `CDN_URL`, `VERSIONS_URL`, `UPGRADE_SCRIPT_URL` env vars

### Default Branding Values (User Can Override)
- **`app/Support/BrandingOptions.php`** (line 41): Default social image URL
- **`app/Models/BrandingPreset.php`** (line 38): Default social image URL
  - **Status**: ✅ OK - These are just default values
  - Users can override these in the branding settings UI
  - These don't affect installation

### Documentation/UI References
- Various view files reference `coolify.io` documentation URLs
- **Status**: ✅ OK - These are just links to documentation
- Users can customize these via branding settings (docs_url, marketing_site_url)

## 📋 Required Files in Your Repository

Make sure these files exist in your `v4.x` branch:

1. ✅ `versions.json` - Version information
2. ✅ `docker-compose.yml` - Docker Compose configuration
3. ✅ `docker-compose.prod.yml` - Production Docker Compose configuration
4. ✅ `.env.production` - Production environment template
5. ✅ `scripts/upgrade.sh` - Upgrade script
6. ✅ `scripts/install.sh` - Install script (already in repo)

## 🔍 Verification Checklist

- [x] Install script uses your GitHub repo
- [x] Upgrade script uses your GitHub repo
- [x] All file downloads point to your repo
- [x] Docker images use official Coolify images (correct behavior)
- [x] Default values can be overridden by users

## ✅ Conclusion

**All critical installation files are correctly configured to use your repository.**

The only references to Coolify that remain are:
1. Docker images (which should use the official Coolify images)
2. Default configuration values (which users can override)
3. Documentation links (which users can customize via branding settings)

These are all non-critical and don't affect the installation process.

