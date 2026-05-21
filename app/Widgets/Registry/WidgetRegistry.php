<?php

declare(strict_types=1);

namespace App\Widgets\Registry;

use App\Widgets\Contracts\WidgetContract;
use InvalidArgumentException;

/**
 * Application-scoped singleton that holds all known widget classes keyed by
 * their stable `type` slug. Bound by WidgetServiceProvider.
 */
final class WidgetRegistry
{
    /** @var array<string, class-string<WidgetContract>> */
    private array $widgets = [];

    /**
     * @param  class-string<WidgetContract>  $widgetClass
     */
    public function register(string $widgetClass): void
    {
        if (! is_subclass_of($widgetClass, WidgetContract::class)) {
            throw new InvalidArgumentException("{$widgetClass} must implement WidgetContract.");
        }

        $this->widgets[$widgetClass::type()] = $widgetClass;
    }

    /**
     * @param  array<int, class-string<WidgetContract>>  $widgetClasses
     */
    public function registerMany(array $widgetClasses): void
    {
        foreach ($widgetClasses as $class) {
            $this->register($class);
        }
    }

    /** @return class-string<WidgetContract> */
    public function resolve(string $type): string
    {
        if (! isset($this->widgets[$type])) {
            throw new InvalidArgumentException("Unknown widget type: {$type}");
        }

        return $this->widgets[$type];
    }

    public function has(string $type): bool
    {
        return isset($this->widgets[$type]);
    }

    /** @return array<int, string> */
    public function types(): array
    {
        return array_keys($this->widgets);
    }

    /**
     * Metadata for every registered widget, ready to ship to the picker.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $result = [];
        foreach ($this->widgets as $class) {
            /** @var class-string<WidgetContract> $class */
            $result[] = $class::toArray();
        }

        return $result;
    }
}
