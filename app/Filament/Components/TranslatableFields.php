<?php

namespace App\Filament\Components;

use App\Models\Language;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\MarkdownEditor;

class TranslatableFields
{
    /**
     * Generate translatable tabs
     *
     * @param array $fields Array of fields to make
     *   Format: [
     *       'name' => ['type' => 'text', 'label' => 'Name', 'overrides' => function($component) {}],
     *       'description' => ['type' => 'textarea', 'label' => 'Description']
     *   ]
     *
     * @return Tabs
     */
    public static function make(array $fields): Tabs
    {
        $languages = collect(config('languages.supported'))->map(function ($name, $code) {
            return (object)[
                'code' => $code,
                'name' => $name,
            ];
        });

        $tabs = $languages->map(function ($lang) use ($fields) {
            $fieldComponents = [];
            foreach ($fields as $attribute => $config) {
                $type = $config['type'] ?? 'text';
                $label = $config['label'] ?? ucfirst($attribute);

                // Build field component
                switch ($type) {
                    case 'textarea':
                        $component = Textarea::make("{$attribute}:{$lang->code}")
                            ->label("{$label} ({$lang->name})")
                            ->default(fn($record) => $record?->translations->where('local', $lang->code)->first()?->$attribute)
                            ->required(fn() => $lang->code === app()->getLocale())
                            ->afterStateHydrated(function ($component, $state, $record) use ($attribute, $lang) {
                                if ($record) {
                                    $component->state(
                                        $record->translations->where('local', $lang->code)->first()?->$attribute
                                    );
                                }
                            });

                        break;
                    case 'markdown_editor':
                        $component = MarkdownEditor::make("{$attribute}:{$lang->code}")
                            ->label("{$label} ({$lang->name})")
                            ->default(fn($record) => $record?->translations->where('local', $lang->code)->first()?->$attribute)
                            ->required(fn() => $lang->code === app()->getLocale())
                            ->afterStateHydrated(function ($component, $state, $record) use ($attribute, $lang) {
                                if ($record) {
                                    $component->state(
                                        $record->translations->where('local', $lang->code)->first()?->$attribute
                                    );
                                }
                            });
                        break;
                    case 'tags':
                        $component = TagsInput::make("{$attribute}:{$lang->code}")
                            ->label("{$label} ({$lang->name})")
                            ->default(fn($record) => $record?->translations->where('local', $lang->code)->first()?->$attribute)
                            ->afterStateHydrated(function ($component, $state, $record) use ($attribute, $lang) {
                                if ($record) {
                                    $component->state(
                                        $record->translations->where('local', $lang->code)->first()?->$attribute
                                    );
                                }
                            });
                        break;

                    case 'text':
                    default:
                        $component = TextInput::make("{$attribute}:{$lang->code}")
                            ->label("{$label} ({$lang->name})")
                            ->required(fn() => $lang->code === app()->getLocale())
                            ->default(fn($record) => $record?->translations->where('local', $lang->code)->first()?->$attribute)
                            ->maxLength(255)
                            ->afterStateHydrated(function ($component, $state, $record) use ($attribute, $lang) {
                                if ($record) {
                                    $component->state(
                                        $record->translations->where('local', $lang->code)->first()?->$attribute
                                    );
                                }
                            });

                        break;
                }

                // Apply per-field overrides if provided
                if (isset($config['overrides']) && is_callable($config['overrides'])) {
                    $component = $config['overrides']($component, $lang);
                }

                $fieldComponents[] = $component;
            }

            return Tab::make($lang->name)->schema($fieldComponents);
        })->toArray();

        return Tabs::make('Translations')->tabs($tabs);
    }
}
