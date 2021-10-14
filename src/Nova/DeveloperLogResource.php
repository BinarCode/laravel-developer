<?php

namespace Binarcode\LaravelDeveloper\Nova;

use Binarcode\LaravelDeveloper\Models\DeveloperLog;
use Binarcode\LaravelDeveloper\Nova\Fields\Badge;
use Binarcode\LaravelDeveloper\Nova\Fields\Line;
use Binarcode\LaravelDeveloper\Nova\Filters\CreatedAtFilter;
use Binarcode\LaravelDeveloper\Nova\Filters\TagFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class DeveloperLogResource extends Resource
{
    public static $model = DeveloperLog::class;

    public static $globallySearchable = false;

    public static $title = 'name';

    public static $search = [
        'uuid',
        'name',
        'tags',
        'file',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make(),

            Badge::make('Tags', 'tags'),

            Text::make('File')->displayUsing(function ($file) {
                $file = Str::after(
                    Str::after($file, base_path()),
                    '/'
                );

                return $file ?
                    "$file:$this->line"
                    : '';
            }),

            Text::make('Name')->displayUsing(fn ($name) => Str::substr($name, 0, 50)),

            Text::make('File')->hideFromIndex(),

            Text::make('Line')->hideFromIndex(),

            Code::make('Payload')->json(),

            Code::make('Exception')->json(),

            Stack::make('Created At (and et format)', 'created_at', [
                DateTime::make('Created At')->sortable(),

                Line::make('Created ET', 'created_at')->asDate(),
            ])->sortable(),
        ];
    }

    public static function icon()
    {
        return '<path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21"/>';
    }

    public function filters(Request $request)
    {
        return [
            CreatedAtFilter::make(),

            TagFilter::make(),
        ];
    }
}
