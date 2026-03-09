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
                    $insertData[] = array_merge($fields, [
                        'local' => $locale,
                        $foreignKey => $record->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $record->translations()->insert($insertData);
            }

            return $record;
        });
    }
}
