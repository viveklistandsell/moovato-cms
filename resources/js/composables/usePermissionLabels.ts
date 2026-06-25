import { useT } from '@/composables/useT';

/**
 * Helpers that resolve human-readable labels for permission/role/module
 * records that come from the database into the active admin locale.
 *
 * Strategy: try a stable lang key first, fall back to whatever the DB
 * sent (the seeded English `display_name`). That way custom roles and
 * permissions added through the seeder still render — they just stay in
 * English until a translator adds a key for them.
 */
export function usePermissionLabels(): {
    moduleLabel: (group: string) => string;
    permissionLabel: (name: string, displayName: string) => string;
    roleLabel: (name: string, displayName: string) => string;
} {
    const t = useT();

    function moduleLabel(group: string): string {
        const key = `permissions.modules.${group.toLowerCase()}`;
        const translated = t(key);
        return translated === key ? group : translated;
    }

    function permissionLabel(name: string, displayName: string): string {
        // `name` is shaped like `<resource>.<verb>` — e.g. `blog.view`.
        // The lang key mirrors that with a dot so the lookup is direct.
        const key = `permissions.items.${name}`;
        const translated = t(key);
        return translated === key ? displayName : translated;
    }

    function roleLabel(name: string, displayName: string): string {
        const slug = slugify(name);
        const key = `roles.display.${slug}`;
        const translated = t(key);
        return translated === key ? displayName : translated;
    }

    return { moduleLabel, permissionLabel, roleLabel };
}

/**
 * Normalize a role name (e.g. `Super Admin`, `Developer Mode`) to the
 * kebab-case slug used as the translation key.
 */
function slugify(name: string): string {
    return name
        .trim()
        .toLowerCase()
        .replace(/[\s_]+/g, '-')
        .replace(/[^a-z0-9-]/g, '');
}
