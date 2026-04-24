<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ReturnCase;
use App\Models\ReturnExpectedInbound;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ReturnExpectedInboundController extends Controller
{
    public function __construct(
        private readonly ReturnExpectedInbound $returnExpectedInbound,
        private readonly Brand $brand
    ) {
    }

    public function index(Request $request): View
    {
        $resources = $this->returnExpectedInbound
            ->newQuery()
            ->with(['brand:id,name', 'matchedReturnCase:id,return_id,refund_status,inspection_status'])
            ->when($request->filled('brand_id'), fn($query) => $query->where('brand_id', $request->integer('brand_id')))
            ->when($request->filled('status'), fn($query) => $query->where('status', (string) $request->input('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->input('search');
                $query->where(function ($nested) use ($search) {
                    $nested->where('return_id', 'like', "%{$search}%")
                        ->orWhere('product_sku', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(Helpers::pagination_limit())
            ->appends($request->query());

        return view('admin-views.returns.inbound.index', [
            'resources' => $resources,
            'brands' => $this->brand->where('status', 1)->orderBy('name')->get(),
            'statusLabels' => ReturnExpectedInbound::statusLabels(),
            'conditionOptions' => ReturnCase::conditionOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helpers::returns_user_is_guest_demo()) {
            Toastr::error('Guest demo users can review inbound records but cannot import data.');
            return redirect()->route('admin.returns.inbound.index');
        }

        $request->validate([
            'inbound_csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        [$imported, $skipped, $errors] = $this->importCsv($request->file('inbound_csv'));

        if ($imported > 0) {
            Toastr::success($imported . ' expected inbound row(s) imported.');
        }

        if ($skipped > 0 || $errors) {
            Toastr::warning($skipped . ' row(s) skipped. ' . implode(' ', array_slice($errors, 0, 3)));
        }

        return redirect()->route('admin.returns.inbound.index');
    }

    private function importCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            return [0, 0, ['Unable to read uploaded CSV file.']];
        }

        $headers = null;
        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = $this->normalizeHeaders($row);
                continue;
            }

            if ($this->isBlankRow($row)) {
                continue;
            }

            $payload = $this->payloadFromCsvRow($headers, $row);
            $returnId = trim((string) Arr::get($payload, 'return_id'));
            $brand = $this->resolveBrand($payload);

            if ($returnId === '' || !$brand) {
                $skipped++;
                $errors[] = 'Missing return_id or known brand for row ' . ($imported + $skipped + 1) . '.';
                continue;
            }

            ReturnExpectedInbound::query()->updateOrCreate(
                [
                    'brand_id' => $brand->id,
                    'return_id' => $returnId,
                ],
                [
                    'product_sku' => $this->nullableString(Arr::get($payload, 'product_sku')),
                    'serial_number' => $this->nullableString(Arr::get($payload, 'serial_number')),
                    'tracking_number' => $this->nullableString(Arr::get($payload, 'tracking_number')),
                    'return_reason' => $this->nullableString(Arr::get($payload, 'return_reason')),
                    'expected_condition' => $this->normalizeCondition(Arr::get($payload, 'expected_condition')),
                    'source' => 'csv',
                    'status' => 'pending',
                    'imported_by' => auth('admin')->id(),
                    'imported_at' => now(),
                    'raw_payload' => $payload,
                ]
            );

            $imported++;
        }

        fclose($handle);

        return [$imported, $skipped, $errors];
    }

    private function normalizeHeaders(array $row): array
    {
        return array_map(function ($header) {
            $normalized = strtolower(trim((string) $header));
            $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized) ?: '';
            return trim($normalized, '_');
        }, $row);
    }

    private function payloadFromCsvRow(array $headers, array $row): array
    {
        $payload = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $payload[$this->canonicalHeader($header)] = trim((string) ($row[$index] ?? ''));
        }

        return $payload;
    }

    private function canonicalHeader(string $header): string
    {
        return match ($header) {
            'brand', 'client', 'merchant', 'brand_name', 'client_name' => 'brand_name',
            'brand_id', 'client_id', 'merchant_id' => 'brand_id',
            'sku', 'productsku', 'item_sku', 'barcode' => 'product_sku',
            'serial', 'serial_no', 'serialnumber', 'sn' => 'serial_number',
            'tracking', 'tracking_no', 'tracking_number' => 'tracking_number',
            'reason', 'return_reason' => 'return_reason',
            'condition', 'expected_condition' => 'expected_condition',
            'rma', 'rma_id', 'return_reference' => 'return_id',
            default => $header,
        };
    }

    private function resolveBrand(array $payload): ?Brand
    {
        if (filled(Arr::get($payload, 'brand_id'))) {
            return Brand::query()->find((int) Arr::get($payload, 'brand_id'));
        }

        $brandName = trim((string) Arr::get($payload, 'brand_name'));
        if ($brandName === '') {
            return null;
        }

        return Brand::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($brandName)])
            ->first();
    }

    private function normalizeCondition(mixed $condition): ?string
    {
        $value = strtolower(trim((string) $condition));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value) ?: '';
        $value = trim($value, '_');

        return in_array($value, ReturnCase::conditionOptions(), true) ? $value : null;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function isBlankRow(array $row): bool
    {
        return collect($row)->every(fn($value) => trim((string) $value) === '');
    }
}
