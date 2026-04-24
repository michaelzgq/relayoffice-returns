<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Returns\StoreBrandRuleProfileRequest;
use App\Models\Brand;
use App\Models\BrandRuleProfile;
use App\Models\ReturnCase;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function App\CPU\translate;

class ReturnsRuleController extends Controller
{
    public function __construct(
        private readonly BrandRuleProfile $brandRuleProfile,
        private readonly Brand $brand
    ) {
    }

    public function index(Request $request): View
    {
        $editingProfile = null;
        if ($request->filled('edit') && Helpers::returns_user_can_edit_playbooks()) {
            $editingProfile = $this->brandRuleProfile->find($request->integer('edit'));
        }

        return view('admin-views.returns.rules.index', [
            'resources' => $this->brandRuleProfile->with('brand')->latest()->paginate(Helpers::pagination_limit()),
            'editingProfile' => $editingProfile,
            'brands' => $this->brand->where('status', 1)->orderBy('name')->get(),
            'conditionOptions' => ReturnCase::conditionOptions(),
            'dispositionOptions' => ReturnCase::dispositionOptions(),
            'refundStatusOptions' => ReturnCase::refundStatusOptions(),
            'photoTypeOptions' => ReturnCase::photoTypeOptions(),
        ]);
    }

    public function store(StoreBrandRuleProfileRequest $request): RedirectResponse
    {
        if (!Helpers::returns_user_can_edit_playbooks()) {
            Toastr::error(translate('Guest demo users can review playbooks but cannot edit them'));
            return redirect()->route('admin.returns.rules.index');
        }

        $this->brandRuleProfile->create($this->payload($request));

        Toastr::success(translate('Returns rule profile created successfully'));
        return redirect()->route('admin.returns.rules.index');
    }

    public function update(StoreBrandRuleProfileRequest $request, int $id): RedirectResponse
    {
        if (!Helpers::returns_user_can_edit_playbooks()) {
            Toastr::error(translate('Guest demo users can review playbooks but cannot edit them'));
            return redirect()->route('admin.returns.rules.index');
        }

        $profile = $this->brandRuleProfile->findOrFail($id);
        $payload = $this->payload($request);
        $payload['rule_version'] = (int) ($profile->rule_version ?? 1) + 1;
        $profile->update($payload);

        Toastr::success(translate('Returns rule profile updated successfully'));
        return redirect()->route('admin.returns.rules.index');
    }

    private function payload(StoreBrandRuleProfileRequest $request): array
    {
        return [
            'brand_id' => $request->integer('brand_id'),
            'profile_name' => (string) $request->input('profile_name'),
            'allowed_conditions' => array_values($request->input('allowed_conditions', [])),
            'allowed_dispositions' => array_values($request->input('allowed_dispositions', [])),
            'recommended_dispositions' => collect((array) $request->input('recommended_dispositions', []))
                ->filter(fn($value) => filled($value))
                ->map(fn($value) => (string) $value)
                ->all(),
            'product_rule_scope' => array_values(array_filter((array) $request->input('product_rule_scope', []), fn($value) => filled($value))),
            'auto_hold_triggers' => array_values(array_filter((array) $request->input('auto_hold_triggers', []), fn($value) => filled($value))),
            'escalation_rules' => array_values(array_filter((array) $request->input('escalation_rules', []), fn($value) => filled($value))),
            'reviewer_note_template' => $request->filled('reviewer_note_template') ? (string) $request->input('reviewer_note_template') : null,
            'required_photo_types' => array_values($request->input('required_photo_types', [])),
            'required_photo_count' => $request->integer('required_photo_count'),
            'notes_required' => $request->boolean('notes_required'),
            'sku_required' => $request->boolean('sku_required'),
            'serial_required' => $request->boolean('serial_required'),
            'default_refund_status' => (string) $request->input('default_refund_status'),
            'active' => $request->boolean('active', true),
        ];
    }
}
