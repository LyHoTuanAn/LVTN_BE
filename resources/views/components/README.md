# Components Directory

This directory contains reusable Blade components for the application.

## Component Categories

### UI Components (Reusable UI elements)
- `stats-card.blade.php` - Statistic card component with title, value, and optional change indicator
- `card-container.blade.php` - Card wrapper component with optional title and description
- `chart-container.blade.php` - Chart canvas container component
- `tabs.blade.php` - Tab navigation component using Alpine.js
- `tab-panel.blade.php` - Individual tab panel component (used with tabs)

### Form Components (Form-related components)
- `profile-settings.blade.php` - Profile settings form
- `security-settings.blade.php` - Security settings form
- `notification-settings.blade.php` - Notification preferences form

### API Components (API management related)
- `api-keys.blade.php` - API keys management table
- `api-usage.blade.php` - API usage chart component

## Usage Examples

### Stats Card
```blade
<x-stats-card 
    title="{{ __('Total Users') }}" 
    value="10,482" 
    change="+20.1% from last month"
/>
```

### Card Container
```blade
<x-card-container 
    title="{{ __('Title') }}" 
    description="{{ __('Description') }}"
>
    <!-- Content here -->
</x-card-container>
```

### Tabs
```blade
<x-tabs :defaultTab="'keys'" :tabs="['keys' => __('Keys'), 'usage' => __('Usage')]">
    <x-tab-panel tabKey="keys">
        <!-- Content for keys tab -->
    </x-tab-panel>
    <x-tab-panel tabKey="usage">
        <!-- Content for usage tab -->
    </x-tab-panel>
</x-tabs>
```

### Chart Container
```blade
<x-chart-container id="myChart" height="300" />
```

## Notes

- All components use Laravel's component syntax (`<x-component-name />`)
- Components support i18n using `__()` helper
- Components are designed to be reusable across different views

