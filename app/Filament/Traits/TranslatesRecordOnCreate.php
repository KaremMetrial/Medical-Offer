<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

trait TranslatesRecordOnCreate
{
    protected function handleRecordCreation(array $data): Model
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

        return \Illuminate\Support\Facades\DB::transaction(function () use ($recordData, $translations) {
            $record = new ($this->getModel())($recordData);

            if ($parentRecord = $this->getParentRecord()) {
                $record = $this->associateRecordWithParent($record, $parentRecord);
            }

            $record->save();

            if (method_exists($record, 'translations') && !empty($translations)) {
                $insertData = [];
                $now = now();
                $foreignKey = $record->translations()->getForeignKeyName();

                foreach ($translations as $locale => $fields) {
                    // Skip if name is provided but empty, or if all fields are empty
                    if ((isset($fields['name']) && empty($fields['name'])) || collect($fields)->every(fn($value) => is_null($value) || $value === '')) {
                        continue;
                    }

                    // Ensure array attributes (like JSON columns) are encoded for raw insertion
                    foreach ($fields as $key => $value) {
                        if (is_array($value)) {
                            $fields[$key] = json_encode($value);
                        }
                    }

                    $insertData[] = array_merge($fields, [
                        'local' => $locale,
                        $foreignKey => $record->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                if (!empty($insertData)) {
                    $record->translations()->insert($insertData);
                }
            }

            return $record;
        });
    }
}
