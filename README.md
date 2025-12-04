<div align="center">

# Coolify White Label Edition
**Codename: Canvas** 🎨

An open-source & self-hostable Heroku / Netlify / Vercel alternative with comprehensive white-label branding capabilities.

![Latest Release Version](https://img.shields.io/badge/dynamic/json?labelColor=grey&color=6366f1&label=Latest%20released%20version&url=https%3A%2F%2Fcdn.coollabs.io%2Fcoolify%2Fversions.json&query=coolify.v4.version&style=for-the-badge
)

</div>

## About the Project

**Coolify White Label Edition (Canvas)** is a fork of [Coolify](https://github.com/coollabsio/coolify) with extensive white-label branding features, allowing you to completely customize the appearance and branding of your deployment platform.

It helps you manage your servers, applications, and databases on your own hardware; you only need an SSH connection. You can manage VPS, Bare Metal, Raspberry PIs, and anything else.

Imagine having the ease of a cloud but with your own servers, fully branded as your own platform. That is **Coolify White Label Edition**.

> **Note**: This is a fork of Coolify by CoolLabsIO. All core functionality remains the same, with added white-label branding features.

## 🎨 White Label Features (Canvas Edition)

This fork includes comprehensive white-label branding capabilities:

### Branding Customization
- **Product Name & Tagline**: Customize product name, short name, and tagline throughout the application
- **Logos**: Upload custom dark/light logos and favicon
- **Colors**: Set primary and accent colors that apply throughout the UI
- **Logo vs Text**: Choose to display logo or product name in navigation (login page and sidebar)
- **Dynamic Sidebar**: Sidebar width automatically adjusts based on product name length or logo size

### Visual Theming
- **Background Colors**: 10 predefined theme-adaptive colors that work in both light and dark modes
  - Colors automatically adapt: very light in light mode, almost black (but still colored) in dark mode
  - Includes: Slate, Gray, Zinc, Neutral, Stone, Red, Orange, Amber, Green, Blue
- **Typography**: Customize fonts with Google Fonts integration
  - Pre-configured fonts: Roboto, Poppins, Nunito, Open Sans, Lato, Montserrat, Raleway, Source Sans Pro, Ubuntu, Playfair Display
  - Support for custom Google Font URLs
  - Instant font preview in branding settings

### Branding Presets
- **System Presets**: 5 pre-configured branding themes (Coolify Default, Corporate Blue, Modern Green, Minimal Dark, Warm Orange)
- **User Presets**: Save your custom branding configurations as presets
- **Import/Export**: Export presets as JSON and import them for easy sharing or backup
- **One-Click Apply**: Switch between branding presets instantly

### Email Customization
- **Email Templates**: Customize transactional email templates (test, invitation, password reset, email verification, deployment success/failed, backup success/failed)
- **Email Header & Footer**: Set global header and footer for all transactional emails
- **Placeholder Support**: Use placeholders like `{product_name}`, `{support_url}`, `{marketing_url}`, etc.
- **Test Email**: Send test emails to verify your branding and templates

### Meta & SEO
- **Meta Description**: Customize meta description for SEO
- **Social Media Image**: Set custom Open Graph image for social sharing
- **Page Titles**: Dynamic page titles with your product name
- **Favicon**: Custom favicon support (PNG, SVG, ICO formats)

### Additional Features
- **Auto-updates Disabled**: Auto-updates are disabled by default to prevent overwriting customizations in forked versions
- **Settings Pages**: Dedicated settings pages for Branding, Email Templates, and Branding Presets
- **Cache Management**: Automatic cache clearing when branding changes

## Installation

```bash
curl -fsSL https://raw.githubusercontent.com/oudayfatteh0/coolify-white-label/refs/heads/v4.x/scripts/install.sh | bash
```

You can find the installation script source [here](./scripts/install.sh).

> [!NOTE]
> This installation script downloads files from the GitHub repository. Make sure you have internet access to `raw.githubusercontent.com`.

## What's Different from Original Coolify?

### Added Features
- ✅ Complete white-label branding system
- ✅ Branding presets/themes
- ✅ Email template customization
- ✅ Theme-adaptive background colors
- ✅ Google Fonts integration
- ✅ Dynamic sidebar width
- ✅ Logo/text toggle in navigation
- ✅ Auto-updates disabled by default

### Removed/Disabled
- ❌ Auto-update functionality (disabled to preserve customizations)
- ❌ Update menu/button (removed from settings)

### Core Functionality
- ✅ All original Coolify features remain intact
- ✅ Same deployment capabilities
- ✅ Same server management features
- ✅ Same database management
- ✅ Same application deployment workflows

## Configuration

After installation, navigate to **Settings → Branding** to customize:

1. **Product Information**: Set your product name, short name, and tagline
2. **Colors**: Choose primary and accent colors
3. **Logos**: Upload dark/light logos and favicon
4. **Typography**: Select fonts and preview them
5. **Background**: Choose theme-adaptive background colors
6. **Email Templates**: Customize transactional email templates
7. **Presets**: Save and apply branding presets

## Technical Details

### Branding Storage
- Branding settings are stored in the `instance_settings` table as JSON
- Cached for performance (24-hour cache)
- Automatically cleared when branding changes

### Database Changes
- Added `branding` JSON column to `instance_settings` table
- Added `email_templates` JSON column to `instance_settings` table
- Added `branding_presets` table for preset management
- Modified `is_auto_update_enabled` default to `false`

### Files Modified
- `app/Models/InstanceSettings.php`: Added branding support
- `app/Support/BrandingOptions.php`: New value object for branding
- `app/Livewire/Settings/Branding.php`: Branding settings UI
- `app/Livewire/Settings/EmailTemplates.php`: Email template customization
- `app/Livewire/Settings/BrandingPresets.php`: Preset management
- `resources/views/layouts/base.blade.php`: Dynamic meta tags and CSS variables
- `resources/views/layouts/app.blade.php`: Dynamic sidebar and logo display
- `resources/views/auth/login.blade.php`: Branded login page
- `resources/views/components/navbar.blade.php`: Branded sidebar navigation
- `bootstrap/helpers/shared.php`: Added `branding()` helper function

## Contributing

This is a fork focused on white-label branding. If you'd like to contribute:

1. Fork this repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## Credits

- **Original Project**: [Coolify](https://github.com/coollabsio/coolify) by [CoolLabsIO](https://coolify.io)
- **White Label Edition**: Maintained by [oudayfatteh0](https://github.com/oudayfatteh0)
- **Codename**: Canvas 🎨

## License

This project is licensed under the Apache-2.0 License - see the [LICENSE](LICENSE) file for details.

## Support

For issues related to:
- **Core Coolify functionality**: Please refer to [Coolify Documentation](https://coolify.io/docs)
- **White-label features**: Open an issue in this repository

## Acknowledgments

Special thanks to the Coolify team for creating an amazing open-source platform. This fork extends their work with comprehensive white-label branding capabilities.

---

<div align="center">

**Made with ❤️ - White Label Edition (Canvas)**

[Original Coolify](https://coolify.io) | [Documentation](https://coolify.io/docs) | [GitHub](https://github.com/coollabsio/coolify)

</div>
