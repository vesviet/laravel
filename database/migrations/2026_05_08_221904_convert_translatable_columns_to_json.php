<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $tablesAndColumns = [
        'products' => ['name', 'slug', 'short_description', 'description', 'meta_title', 'meta_description'],
        'categories' => ['name', 'slug', 'description', 'meta_title', 'meta_description'],
        'pages' => ['title', 'slug', 'content', 'meta_title', 'meta_description'],
        'articles' => ['title', 'slug', 'excerpt', 'content', 'meta_title', 'meta_description'],
    ];

    public function up(): void
    {
        DB::transaction(function () {
            foreach ($this->tablesAndColumns as $table => $columns) {
                if (!Schema::hasTable($table)) continue;

                // 1. Fetch old data
                $records = DB::table($table)->get();

                // 2. Rename old columns and create new json columns
                Schema::table($table, function (Blueprint $tableBlueprint) use ($columns) {
                    foreach ($columns as $column) {
                        $tableBlueprint->renameColumn($column, $column . '_old_backup');
                    }
                });

                Schema::table($table, function (Blueprint $tableBlueprint) use ($columns) {
                    foreach ($columns as $column) {
                        $tableBlueprint->json($column)->nullable();
                    }
                });

                // 3. Re-insert data as JSON format {"vi": "old_string"}
                foreach ($records as $record) {
                    $updateData = [];
                    foreach ($columns as $column) {
                        $oldCol = $column . '_old_backup';
                        $oldValue = $record->{$oldCol} ?? '';
                        
                        if ($oldValue === null || $oldValue === '') {
                            $updateData[$column] = json_encode(['vi' => '']);
                        } elseif (is_string($oldValue) && !str_starts_with(trim($oldValue), '{')) {
                            $updateData[$column] = json_encode(['vi' => $oldValue], JSON_UNESCAPED_UNICODE);
                        } else {
                             $updateData[$column] = $oldValue; // fallback if already json
                        }
                    }

                    if (!empty($updateData)) {
                        DB::table($table)->where('id', $record->id)->update($updateData);
                    }
                }

                // 4. Drop old backup columns
                Schema::table($table, function (Blueprint $tableBlueprint) use ($columns) {
                    foreach ($columns as $column) {
                        $tableBlueprint->dropColumn($column . '_old_backup');
                    }
                });
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            foreach ($this->tablesAndColumns as $table => $columns) {
                if (!Schema::hasTable($table)) continue;

                // 1. Fetch JSON data
                $records = DB::table($table)->get();

                // 2. Extract 'vi' value and change column types back to Text/String
                foreach ($records as $record) {
                    $updateData = [];
                    foreach ($columns as $column) {
                        $currentValue = $record->{$column};
                        if ($currentValue && str_starts_with(trim($currentValue), '{')) {
                            $decoded = json_decode($currentValue, true);
                            $updateData[$column] = $decoded['vi'] ?? '';
                        }
                    }

                    if (!empty($updateData)) {
                        DB::table($table)->where('id', $record->id)->update($updateData);
                    }
                }

                Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $columns) {
                    foreach ($columns as $column) {
                        if (in_array($column, ['description', 'short_description', 'content', 'excerpt'])) {
                            $tableBlueprint->text($column)->nullable()->change();
                        } else {
                            $tableBlueprint->string($column)->nullable()->change();
                        }
                    }
                });
            }
        });
    }
};
