import {
    Book,
    Bookmark,
    Briefcase,
    Cake,
    Calendar,
    Camera,
    Car,
    Coffee,
    Flame,
    Folder,
    Globe,
    Heart,
    Home,
    Image,
    Laptop,
    Leaf,
    Mail,
    MapPin,
    MessageCircle,
    Music,
    Newspaper,
    Pencil,
    Plane,
    ShoppingCart,
    Star,
    Tag,
    TrendingUp,
    Trophy,
    Users,
    Video,
} from 'lucide-vue-next';
import type { LucideIcon } from 'lucide-vue-next';

export const iconMap: Record<string, LucideIcon> = {
    'ti ti-home': Home,
    'ti ti-folder': Folder,
    'ti ti-bookmark': Bookmark,
    'ti ti-news': Newspaper,
    'ti ti-tag': Tag,
    'ti ti-star': Star,
    'ti ti-heart': Heart,
    'ti ti-globe': Globe,
    'ti ti-book': Book,
    'ti ti-pencil': Pencil,
    'ti ti-photo': Image,
    'ti ti-music': Music,
    'ti ti-video': Video,
    'ti ti-shopping-cart': ShoppingCart,
    'ti ti-coffee': Coffee,
    'ti ti-plane': Plane,
    'ti ti-car': Car,
    'ti ti-cake': Cake,
    'ti ti-trophy': Trophy,
    'ti ti-flame': Flame,
    'ti ti-leaf': Leaf,
    'ti ti-briefcase': Briefcase,
    'ti ti-camera': Camera,
    'ti ti-trending-up': TrendingUp,
    'ti ti-message-circle': MessageCircle,
    'ti ti-mail': Mail,
    'ti ti-calendar': Calendar,
    'ti ti-map-pin': MapPin,
    'ti ti-users': Users,
    'ti ti-device-laptop': Laptop,
};

export function getIcon(className: string | null | undefined): LucideIcon | null {
    if (!className) {
        return null;
    }
    return iconMap[className] ?? null;
}

export const iconOptions: { value: string; label: string; icon: LucideIcon }[] =
    Object.entries(iconMap).map(([cls, icon]) => ({
        value: cls,
        label: cls,
        icon,
    }));
