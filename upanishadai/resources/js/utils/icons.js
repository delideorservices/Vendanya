import * as LucideIcons from 'lucide-vue-next';

export function registerLucideIcons(app) {
    for (const [name, component] of Object.entries(LucideIcons)) {
        app.component(name, component);
    }
}