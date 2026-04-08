<?php

use App\CPU\Helpers;
use App\Models\BusinessSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use function App\CPU\translate;

if (!function_exists('displayImage')) {
    function displayImage($image, $isAvatar = false)
    {
        if (file_exists($image) && is_file($image)) {
            return asset($image);
        } elseif ($isAvatar) {
            return asset('assets/admin/img/160x160/img1.jpg');
        } else {
            return asset('assets/admin/img/160x160/img1.jpg');
        }
    }
}

if (!function_exists('onErrorImage')) {
    function onErrorImage($data, $src, $error_src ,$path)
    {
        if(isset($data) && strlen($data) >1 && Storage::disk('public')->exists($path. '/' .$data)){
            return $src;
        }
        return $error_src;
    }
}

if (!function_exists('getSorting'))
{
    function getSorting(string $sortingType)
    {
        $sortOptions = [
            'latest' => ['id', 'desc'],
            'oldest' => ['id', 'asc'],
            'ascending' => ['name', 'asc'],
            'descending' => ['name', 'desc'],
        ];

        return $sortOptions[$sortingType] ?? ['id', 'desc'];
    }
}

if (!function_exists('getExportColumnMap')) {
    function getExportColumnMap()
    {
        return [
            'sl' => fn($index) => [
                'SL' => $index,
            ],
            'name' => fn($resource) => [
                'Name' => $resource->name,
            ],
            'parent_name' => fn($resource) => [
                'Parent Category' => $resource->parent ? $resource->parent->name : 'No parent category',
            ],
            'description' => fn($resource) => [
                'Description' => $resource->description ?? '',
            ],
            'product_count' => fn($resource) => [
                'Total Product' => $resource->product_count ?? 0,
            ],
            'status' => fn($resource) => [
                'Status' => $resource->status ? 'Active' : 'Inactive',
            ],
            'supplier_info' => fn($resource) => [
                'Supplier' => $resource?->supplier ? $resource->supplier->name . ' (' . $resource->supplier->mobile . ')' : 'No supplier',
            ],
            'category' => fn($resource) => [
                'Category' => $resource->category ? $resource->category->name : 'No category',
            ],
            'quantity' => fn($resource) => [
                'Quantity' => $resource->quantity,
            ],
            'total_ordered' => fn($resource) => [
                'Total Ordered' => $resource->order_count ?? 0,
            ],
            'stock_status' => fn($resource) => [
                'Stock Status' => $resource->quantity == 0 ? 'Out of Stock' : 'Low Stock',
            ],
            'purchase_price' => fn($resource) => [
                'Purchase Price' => $resource->purchase_price ? Helpers::currency_symbol() . ' ' . number_format($resource->purchase_price, 2) : 'N/A',
            ],

            'selling_price' => fn($resource) => [
                'Selling Price' => $resource->selling_price ? Helpers::currency_symbol() . ' ' . number_format($resource->selling_price, 2) : 'N/A',
            ],
        ];
    }
}

if (!function_exists('shortNumberFormat')) {
    function shortNumberFormat($number, $precision = 1)
    {
        if (!is_numeric($number)) return $number;

        if ($number < 1000) {
            return (string) $number;
        }

        $units = [
            12 => 't',
            9  => 'b',
            6  => 'm',
            3  => 'k',
        ];

        foreach ($units as $exponent => $suffix) {
            if ($number >= pow(10, $exponent)) {
                return round($number / pow(10, $exponent), $precision) . $suffix;
            }
        }

        return (string) $number;
    }

}

if (!function_exists('convertToBytes')){
    function convertToBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower($value[strlen($value) - 1]);
        $num = (int) $value;
        $multipliers = ['g' => 1073741824, 'm' => 1048576, 'k' => 1024];

        return $num * ($multipliers[$unit] ?? 1);
    }
}

if (!function_exists('convertBytesToKiloBytes')){
    function convertBytesToKiloBytes(string $value): int
    {
        return (int) $value / 1024;
    }
}

if (!function_exists('convertToReadableSize')) {
    function convertToReadableSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . 'GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . 'MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . 'KB';
        }
        return $bytes . 'B';
    }
}

if (!function_exists('maxUploadSize'))
{
    function maxUploadSize(string $fileType): int
    {
        $phpLimit = convertToBytes(ini_get('upload_max_filesize'));

        if (env('APP_MODE') === 'demo') {
            $appLimit = convertToBytes('1M');
        }else{
            $appLimit = convertToBytes($fileType === 'image' ? '20M' : '50M');
        }

        return min($phpLimit, $appLimit);
    }
}

if (!function_exists('readableUploadMaxFileSize'))
{
    function readableUploadMaxFileSize(string $fileType): string
    {
        return  convertToReadableSize(maxUploadSize($fileType));
    }
}

if (!function_exists('showValidationMessageForUploadMaxSize')) {
    function showValidationMessageForUploadMaxSize(array $files, bool $isAjax, bool $doesExpectJson, int|string $responseCode = 200)
    {
        $maximumSize = readableUploadMaxFileSize('image');

        foreach (flattenFiles($files) as $key => $file)
        {
            if ($file->getError() == 0) continue;
            $fileExtension = $file->getClientOriginalExtension();
            if(in_array($fileExtension, ['txt', 'rtf', 'doc', 'docx', 'pdf', 'odt', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'log']))
            {
                $maximumSize = readableUploadMaxFileSize('file');
            }
            $message = translate(key: '{imageName} must be less than {maxSize}', replace: ['imageName' => $file->getClientOriginalName(), 'maxSize' => $maximumSize]);
            if ($isAjax || $doesExpectJson) {
                throw new HttpResponseException(response()->json([
                    'errors' => [['code' => (is_numeric($key)) ? 'images' : $key, 'message' => $message]]
                ], $responseCode));
            }

            Toastr::error($message);
            throw new HttpResponseException(Redirect::back()->withInput());
        }
    }
}

if (!function_exists('flattenFiles')) {
    function flattenFiles(array $input): array
    {
        $files = [];
        array_walk_recursive($input, function ($value, $key) use (&$files) {
            if ($value instanceof UploadedFile) $files[$key] = $value;
        });

        return $files;
    }
}

if (!function_exists('getFileFormatSizeTranslatedText')){
    function getFileFormatSizeTranslatedText(string $fileFormat): string
    {
        $formats = array_map('trim', explode(',', $fileFormat));
        $imageExtensions = array_map('trim', explode(',', IMAGE_ACCEPTED_EXTENSIONS));
        $isImage = count(array_diff($formats, $imageExtensions)) == 0;
        $fileSize = $isImage ? readableUploadMaxFileSize('image') : readableUploadMaxFileSize('file');

        return translate(key: 'File format - {fileFormat}, File size - Maximum {fileSize}.', replace: ['fileFormat' => $fileFormat, 'fileSize' => $fileSize]);
    }
}

if (!function_exists('formatNumberWithSymbol')) {
    function formatNumberWithSymbol($number, $useDecimal = false, $type = 'amount'): string
    {
        $isDecimal = str_contains((string)$number, '.');

        if ($type == 'percent') {
            return $number . '%';
        }

        $symbolPosition = BusinessSetting::where('key', 'currency_symbol_position')->first()?->value ?? 'right';
        $decimalPoints = $useDecimal ? BusinessSetting::where('key', 'digit_after_decimal')->first()?->value ?? 2 : 0;
        $formattedNumber = $isDecimal ? number_format($number, $decimalPoints) : $number;
        $currencySymbol = Helpers::currency_symbol();
        if ($symbolPosition == 'left') {
            return $currencySymbol . $formattedNumber;
        }

        return $formattedNumber . $currencySymbol;
    }
}

if (!function_exists('getTimeZones'))
{
    function getTimeZones()
    {
        $timezones = [];

        foreach (DateTimeZone::listIdentifiers() as $tz) {
            $dt = new \DateTime('now', new DateTimeZone($tz));
            $offset = $dt->getOffset();
            $hours = floor($offset / 3600);
            $minutes = abs(($offset % 3600) / 60);
            $sign = ($hours >= 0) ? '+' : '-';
            $formattedOffset = sprintf('(UTC%s%02d:%02d)', $sign, abs($hours), $minutes);

            $timezones[] = [
                'id' => $tz,
                'text' => $formattedOffset . ' ' . str_replace('_', ' ', $tz)
            ];
        }

        return Cache::rememberForever('timezones', function () use ($timezones) {
            return $timezones;
        });
    }
}

