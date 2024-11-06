<?php


namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

trait DynamicFilter
{


    private array $dateTypes = ["date", "datetime","timestamp"];
    private array $textTypes = ["text", 'varchar', 'longtext', 'mediumtext', "longtext", "string", "tinytext"];

    private array $numericTypes = ["int", "double", "float", "bigint", "decimal", "bigint"];

    private array $enumTypes = ["enum", "tinyint"];

    /**
     * Fetch dynamic data, headers, and filters for any model.
     *
     * @param string $modelName The model name (e.g., 'Users')
     * @param array $filters Optional filters that can be customized (e.g., for date ranges, enums)
     * @return \Illuminate\Http\JsonResponse
     */

    public function getDynamicData(Model $model, $filters = [])
    {
        // get all columns from the model's table
        $columns = Schema::getColumnListing($model->getTable());
        $header = [];

        // prepare column headers
        foreach ($columns as $column) {
            $header[] = [
                'value' => $column,
                'title' => Str::of($column)->replace('_', ' ')->title()
            ];
        }

        // Default fields for filtering
        $fields = $this->getTableFields($model);

        $data = $model->newQuery();
        if (count($filters) > 0) {
            //apply filters
            foreach ($filters as $filter) {
                switch ($filter['value']) {
                    case 'true':
                        $filter['value'] = true;
                        break;
                    case 'false':
                        $filter['value'] = false;
                        break;
                    default:
                        break;
                }
                $this->applyFilterCondition($data, $filter);
            }
        }
            $data = $data->get();
            return response()->json([
                'data' => $data,
                'headers' => $header,
                'fields' => $fields
            ]);
        }
    private function applyFilterCondition($query, $filter)
    {
        if ($filter['type'] === 'date') {
            // if date convert to date range and use whereBetween
            $dates = $this->dateRange($filter['value']);
            $query->whereBetween($filter['field'], $dates);
        } else {
            if ($filter['operator'] == 'contains') {
                $query->where($filter['field'], 'like', "%{$filter['value']}%");
            } elseif ($filter['operator'] == 'in') {
                $query->whereIn($filter['field'], $filter['value']);
            } else {
                $query->where($filter['field'], $filter['operator'], $filter['value']);
            }
        }
    }

        /**
         * Get the default fields based on the model columns
         *
         * @param Model $model
         * @return array
         */
        private function getTableFields(Model $model)
        {
            $columns = Schema::getColumnListing($model->getTable());

            $fields = [];
            foreach ($columns as $column) {
                // get column type
                $columnType = Schema::getColumnType($model->getTable(), $column);
                // treat all columns as text for now
                $field = [
                    'title' => Str::of($column)->replace('_', ' ')->title(),
                    'value' => $column,
                    'type' => 'text',  // Default or most common filter type
                ];

                //handle numeric fields
                if (in_array($columnType, $this->numericTypes)) {
                    $field = [
                        'title' => Str::of($column)->replace('_', ' ')->title(),
                        'value' => $column,
                        'type' => 'number',
                    ];
                    if ($columnType === 'tinyint') {
                        $field['options'] = ["true", "false"];
                    } else {
                        $field['options'] = $this->getEnumOptions($model, $column);
                    }
                }


                // special handling for specific column types, such as date or enum
                if (Str::contains($column, 'date') || $column === 'created_at' || $column === 'updated_at' || in_array($columnType, $this->dateTypes)) {
                    $field = [
                        'title' => Str::of($column)->replace('_', ' ')->title(),
                        'value' => $column,
                        'type' => 'date',
                    ];
                }
                if (in_array($columnType, $this->enumTypes)) {
                    $field = [
                        'title' => Str::of($column)->replace('_', ' ')->title(),
                        'value' => $column,
                        'type' => 'enum',
                    ];
                    if ($columnType === 'tinyint') {
                        $field['options'] = ["true", "false"];
                    } else {
                        $field['options'] = $this->getEnumOptions($model, $column);
                    }
                }
                $fields[] = $field;
            }

            return $fields;
        }

        private function getEnumOptions(Model $model, $column)
        {
            $table = $model->getTable();
            $columnDetails = DB::select("SHOW COLUMNS FROM `{$table}` WHERE `Field` = ?", [$column]);
            if (!empty($columnDetails)) {
                // Extract enum values from the column definition
                $enumType = $columnDetails[0]->Type;

                // The enum values are enclosed in single quotes and separated by commas
                preg_match_all("/'([^']+)'/", $enumType, $matches);

                return $matches[1];  // the enum options as an array
            }


            return [];  // an empty array if not an enum
        }

        private function dateRange($dates)
        {
            // get first item in the array as start date
            $startDate = Carbon::parse($dates[0]);

            // get last item in the array as end date
            $endDate = Carbon::parse(end($dates));
            return array($startDate, $endDate); // return [startDate,endDate]
        }
}

