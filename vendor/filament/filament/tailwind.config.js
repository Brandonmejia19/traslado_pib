import preset from './tailwind.config.preset'

export default {
    presets: [preset],
    content: ['./packages/**/*.blade.php', './vendor/cmsmaxinc/filament-error-pages/resources/**/*.blade.php'],
}
