<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

trait TranslatesRecordOnEdit
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        if (method_exists($record, 'translations')) {
            $translations = $record->translations;
            if ($translations->isNotEmpty()) {
                $foreignKey = $record->translations()->getForeignKeyName();
                $excludeKeys = array_flip(['id', $foreignKey, 'local', 'created_at', 'updated_at']);

                foreach ($translations as $translation) {
                    $locale = $translation->local;
                    foreach ($translation->getAttributes() as $key => $value) {
                        if (!isset($excludeKeys[$key])) {
                            $data["{$key}:{$locale}"] = $value;
                        }
                    }
                }
            }
        }
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $translations = [];
        $recordData = [];

        foreach ($data as $key => $value) {
            if (str_contains($key, ':')) {
                [$field, $locale] = explode(':', $key, 2);
                $translations[$locale][$field] = $value;
            } else {
                $recordData[$key] = $value;
            }
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($record, $recordData, $translations) {
            $record->update($recordData);

            if (method_exists($record, 'translations') && !empty($translations)) {
                foreach ($translations as $locale => $fields) {
                    // Skip if name is provided but empty, or if all fields are empty
                    if ((isset($fields['name']) && empty($fields['name'])) || collect($fields)->every(fn($value) => is_null($value) || $value === '')) {
                        continue;
                    }

                    $record->translations()->updateOrCreate(
                        ['local' => $locale],
                        $fields
                    );
                }
            }

            return $record;
        });
    }
}
