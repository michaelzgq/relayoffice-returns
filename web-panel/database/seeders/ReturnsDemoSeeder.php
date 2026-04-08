<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandRuleProfile;
use App\Models\RefundGateDecision;
use App\Models\ReturnCase;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReturnsDemoSeeder extends Seeder
{
    public function run()
    {
        Storage::disk('public')->makeDirectory('return-cases');

        $brands = collect([
            [
                'name' => 'Aster Apparel',
                'image' => 'demo-aster-apparel.png',
                'description' => 'Soft goods brand with resale-focused return rules.',
                'status' => 1,
                'profile' => [
                    'profile_name' => 'Apparel resale lane',
                    'allowed_conditions' => ['like_new', 'opened_resaleable', 'opened_damaged', 'missing_parts'],
                    'allowed_dispositions' => ['restock', 'refurb', 'destroy', 'return_to_brand'],
                    'recommended_dispositions' => [
                        'like_new' => 'restock',
                        'opened_resaleable' => 'restock',
                        'opened_damaged' => 'refurb',
                        'missing_parts' => 'return_to_brand',
                    ],
                    'required_photo_types' => ['front', 'back', 'label'],
                    'required_photo_count' => 3,
                    'notes_required' => true,
                    'sku_required' => true,
                    'serial_required' => false,
                    'default_refund_status' => 'ready_to_release',
                    'active' => true,
                ],
            ],
            [
                'name' => 'Peak Audio',
                'image' => 'demo-peak-audio.png',
                'description' => 'Serialized electronics with heavier evidence requirements.',
                'status' => 1,
                'profile' => [
                    'profile_name' => 'Electronics inspection lane',
                    'allowed_conditions' => ['unopened', 'opened_resaleable', 'opened_damaged', 'wrong_item', 'empty_box'],
                    'allowed_dispositions' => ['restock', 'hold', 'refurb', 'return_to_brand', 'quarantine'],
                    'recommended_dispositions' => [
                        'unopened' => 'restock',
                        'opened_resaleable' => 'restock',
                        'opened_damaged' => 'refurb',
                        'wrong_item' => 'return_to_brand',
                        'empty_box' => 'quarantine',
                    ],
                    'required_photo_types' => ['front', 'back', 'packaging', 'serial_number'],
                    'required_photo_count' => 4,
                    'notes_required' => true,
                    'sku_required' => true,
                    'serial_required' => true,
                    'default_refund_status' => 'hold',
                    'active' => true,
                ],
            ],
            [
                'name' => 'Luma Home',
                'image' => 'demo-luma-home.png',
                'description' => 'Home goods with fraud-sensitive packaging checks.',
                'status' => 1,
                'profile' => [
                    'profile_name' => 'Home goods damage lane',
                    'allowed_conditions' => ['like_new', 'opened_damaged', 'wrong_item', 'empty_box', 'missing_parts'],
                    'allowed_dispositions' => ['restock', 'hold', 'return_to_brand', 'destroy', 'quarantine'],
                    'recommended_dispositions' => [
                        'like_new' => 'restock',
                        'opened_damaged' => 'return_to_brand',
                        'wrong_item' => 'quarantine',
                        'empty_box' => 'quarantine',
                        'missing_parts' => 'hold',
                    ],
                    'required_photo_types' => ['front', 'back', 'packaging', 'label', 'damage_closeup'],
                    'required_photo_count' => 5,
                    'notes_required' => true,
                    'sku_required' => false,
                    'serial_required' => false,
                    'default_refund_status' => 'needs_review',
                    'active' => true,
                ],
            ],
        ])->mapWithKeys(function (array $definition) {
            $brand = Brand::updateOrCreate(
                ['name' => $definition['name']],
                [
                    'image' => $definition['image'],
                    'description' => $definition['description'],
                    'status' => $definition['status'],
                ]
            );

            $profile = BrandRuleProfile::updateOrCreate(
                ['brand_id' => $brand->id],
                $definition['profile']
            );

            return [$definition['name'] => compact('brand', 'profile')];
        });

        $cases = [
            [
                'return_id' => 'RMA-1001',
                'brand' => 'Aster Apparel',
                'product_sku' => 'AST-JKT-001',
                'serial_number' => null,
                'condition_code' => 'like_new',
                'disposition_code' => 'restock',
                'refund_status' => 'ready_to_release',
                'required_photo_count' => 3,
                'evidence_photo_count' => 3,
                'evidence_complete' => true,
                'sla_hours' => 24,
                'created_by' => 3,
                'notes' => 'Tags missing but garment is clean and resaleable.',
                'received_at' => Carbon::now()->subHours(8),
                'inspected_at' => Carbon::now()->subHours(7),
                'refund_decided_at' => Carbon::now()->subHours(6),
                'media_types' => ['front', 'back', 'label'],
                'events' => [
                    ['event_type' => 'case_created', 'title' => 'Case received', 'description' => 'Warehouse received the return and matched it to the RMA.'],
                    ['event_type' => 'inspection_submitted', 'title' => 'Inspection completed', 'description' => 'Evidence captured on mobile and condition marked like new.'],
                    ['event_type' => 'refund_decision_updated', 'title' => 'Refund moved to ready to release', 'description' => 'All evidence matched the brand profile.'],
                ],
                'decision' => [
                    'status' => 'ready_to_release',
                    'reason' => 'Evidence complete and item is resaleable.',
                ],
            ],
            [
                'return_id' => 'RMA-1002',
                'brand' => 'Peak Audio',
                'product_sku' => 'PEAK-EAR-201',
                'serial_number' => 'PA-88912',
                'condition_code' => 'opened_damaged',
                'disposition_code' => 'refurb',
                'refund_status' => 'hold',
                'required_photo_count' => 4,
                'evidence_photo_count' => 2,
                'evidence_complete' => false,
                'sla_hours' => 24,
                'created_by' => 3,
                'notes' => 'Charging case hinge cracked. Serial captured, packaging photo still missing.',
                'received_at' => Carbon::now()->subHours(31),
                'inspected_at' => Carbon::now()->subHours(29),
                'refund_decided_at' => null,
                'media_types' => ['front', 'serial_number'],
                'events' => [
                    ['event_type' => 'case_created', 'title' => 'Case received', 'description' => 'Case entered the Peak Audio queue.'],
                    ['event_type' => 'inspection_submitted', 'title' => 'Inspection partial', 'description' => 'Inspector uploaded damage proof but did not finish the required evidence set.'],
                ],
                'decision' => [
                    'status' => 'hold',
                    'reason' => 'Need packaging evidence before refund can move.',
                ],
            ],
            [
                'return_id' => 'RMA-1003',
                'brand' => 'Peak Audio',
                'product_sku' => 'PEAK-SPK-077',
                'serial_number' => 'SP-11402',
                'condition_code' => 'wrong_item',
                'disposition_code' => 'return_to_brand',
                'refund_status' => 'needs_review',
                'required_photo_count' => 4,
                'evidence_photo_count' => 4,
                'evidence_complete' => true,
                'sla_hours' => 24,
                'created_by' => 2,
                'notes' => 'Customer sent an older model that does not match order history.',
                'received_at' => Carbon::now()->subHours(53),
                'inspected_at' => Carbon::now()->subHours(50),
                'refund_decided_at' => Carbon::now()->subHours(46),
                'media_types' => ['front', 'back', 'packaging', 'serial_number'],
                'events' => [
                    ['event_type' => 'case_created', 'title' => 'Case received', 'description' => 'Return entered fraud review lane.'],
                    ['event_type' => 'inspection_submitted', 'title' => 'Wrong item confirmed', 'description' => 'Serial and packaging mismatch the outbound order.'],
                    ['event_type' => 'refund_decision_updated', 'title' => 'Escalated for review', 'description' => 'Brand ops requested manual approval before any release.'],
                ],
                'decision' => [
                    'status' => 'needs_review',
                    'reason' => 'Evidence points to wrong-item fraud; human review required.',
                ],
            ],
            [
                'return_id' => 'RMA-1004',
                'brand' => 'Luma Home',
                'product_sku' => 'LUMA-LAMP-410',
                'serial_number' => null,
                'condition_code' => 'empty_box',
                'disposition_code' => 'quarantine',
                'refund_status' => 'hold',
                'required_photo_count' => 5,
                'evidence_photo_count' => 1,
                'evidence_complete' => false,
                'sla_hours' => 24,
                'created_by' => 2,
                'notes' => 'Master carton arrived with filler only. Need all outer-box evidence and carrier claim packet.',
                'received_at' => Carbon::now()->subHours(61),
                'inspected_at' => Carbon::now()->subHours(60),
                'refund_decided_at' => null,
                'media_types' => ['packaging'],
                'events' => [
                    ['event_type' => 'case_created', 'title' => 'Case received', 'description' => 'Empty-box exception opened at inbound dock.'],
                    ['event_type' => 'inspection_submitted', 'title' => 'Quarantined', 'description' => 'Inspector moved the case to quarantine pending claim evidence.'],
                ],
                'decision' => [
                    'status' => 'hold',
                    'reason' => 'Carrier claim packet incomplete.',
                ],
            ],
            [
                'return_id' => 'RMA-1005',
                'brand' => 'Aster Apparel',
                'product_sku' => 'AST-TEE-204',
                'serial_number' => null,
                'condition_code' => 'opened_resaleable',
                'disposition_code' => 'restock',
                'refund_status' => 'released',
                'required_photo_count' => 3,
                'evidence_photo_count' => 3,
                'evidence_complete' => true,
                'sla_hours' => 24,
                'created_by' => 3,
                'notes' => 'Lightly tried on, all evidence complete, refund already released.',
                'received_at' => Carbon::now()->subHours(18),
                'inspected_at' => Carbon::now()->subHours(17),
                'refund_decided_at' => Carbon::now()->subHours(15),
                'media_types' => ['front', 'back', 'label'],
                'events' => [
                    ['event_type' => 'case_created', 'title' => 'Case received', 'description' => 'Apparel return routed to resale lane.'],
                    ['event_type' => 'inspection_submitted', 'title' => 'Inspection completed', 'description' => 'No damage found and evidence complete.'],
                    ['event_type' => 'refund_decision_updated', 'title' => 'Refund released', 'description' => 'Auto-release threshold met after inspection.'],
                ],
                'decision' => [
                    'status' => 'released',
                    'reason' => 'Met auto-release criteria for apparel returns.',
                ],
            ],
        ];

        foreach ($cases as $definition) {
            $bundle = $brands->get($definition['brand']);
            $brand = $bundle['brand'];
            $profile = $bundle['profile'];

            $case = ReturnCase::updateOrCreate(
                ['return_id' => $definition['return_id']],
                [
                    'brand_id' => $brand->id,
                    'brand_rule_profile_id' => $profile->id,
                    'product_sku' => $definition['product_sku'],
                    'serial_number' => $definition['serial_number'],
                    'condition_code' => $definition['condition_code'],
                    'disposition_code' => $definition['disposition_code'],
                    'inspection_status' => 'completed',
                    'refund_status' => $definition['refund_status'],
                    'required_photo_count' => $definition['required_photo_count'],
                    'evidence_photo_count' => $definition['evidence_photo_count'],
                    'evidence_complete' => $definition['evidence_complete'],
                    'sla_hours' => $definition['sla_hours'],
                    'notes' => $definition['notes'],
                    'received_at' => $definition['received_at'],
                    'inspected_at' => $definition['inspected_at'],
                    'refund_decided_at' => $definition['refund_decided_at'],
                    'created_by' => $definition['created_by'] ?? 1,
                    'assigned_to' => 1,
                ]
            );

            DB::table('return_case_media')->where('return_case_id', $case->id)->delete();
            foreach ($definition['media_types'] as $index => $captureType) {
                $fileName = strtolower($definition['return_id']) . '-' . ($index + 1) . '.png';
                $this->writeDemoEvidenceImage(
                    $fileName,
                    $definition,
                    $captureType,
                    $index + 1
                );

                DB::table('return_case_media')->insert([
                    'return_case_id' => $case->id,
                    'file_path' => $fileName,
                    'media_type' => 'image',
                    'capture_type' => $captureType,
                    'sort_order' => $index + 1,
                    'uploaded_by' => $definition['created_by'] ?? 1,
                    'created_at' => $definition['inspected_at'],
                    'updated_at' => $definition['inspected_at'],
                ]);
            }

            DB::table('return_case_events')->where('return_case_id', $case->id)->delete();
            foreach ($definition['events'] as $index => $event) {
                DB::table('return_case_events')->insert([
                    'return_case_id' => $case->id,
                    'event_type' => $event['event_type'],
                    'title' => $event['title'],
                    'description' => $event['description'],
                    'meta' => json_encode(['seeded' => true]),
                    'created_by' => $definition['created_by'] ?? 1,
                    'created_at' => $definition['received_at']->copy()->addHours($index),
                    'updated_at' => $definition['received_at']->copy()->addHours($index),
                ]);
            }

            RefundGateDecision::updateOrCreate(
                ['return_case_id' => $case->id],
                [
                    'status' => $definition['decision']['status'],
                    'reason' => $definition['decision']['reason'],
                    'meta' => ['seeded' => true],
                    'decided_by' => 1,
                    'decided_at' => $definition['refund_decided_at'] ?? $definition['inspected_at'],
                ]
            );
        }
    }

    private function writeDemoEvidenceImage(string $fileName, array $definition, string $captureType, int $slot): void
    {
        $width = 1280;
        $height = 920;
        $image = imagecreatetruecolor($width, $height);

        $palette = $this->paletteForBrand($image, (string) $definition['brand']);
        [$background, $panel, $accent, $ink, $muted] = $palette;

        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        imagefilledrectangle($image, 0, 0, $width, 120, $accent);
        imagefilledrectangle($image, 60, 170, $width - 60, $height - 70, $panel);

        $fontRegular = base_path('storage/fonts/ttf/DejaVuSans.ttf');
        $fontBold = base_path('storage/fonts/ttf/DejaVuSans-Bold.ttf');

        if (!file_exists($fontRegular) || !file_exists($fontBold)) {
            imagedestroy($image);
            throw new \RuntimeException('Demo evidence fonts are missing from storage/fonts/ttf.');
        }

        $this->drawText($image, $fontBold, 34, 80, 78, $background, strtoupper((string) $definition['brand']));
        $this->drawText($image, $fontRegular, 18, 80, 108, $background, 'Demo evidence image for Brand Defense Pack');

        $this->drawText($image, $fontBold, 30, 100, 240, $ink, ucfirst(str_replace('_', ' ', $captureType)));
        $this->drawText($image, $fontRegular, 18, 100, 280, $muted, 'Return ID: ' . $definition['return_id']);
        $this->drawText($image, $fontRegular, 18, 100, 315, $muted, 'Condition: ' . ucfirst(str_replace('_', ' ', (string) $definition['condition_code'])));
        $this->drawText($image, $fontRegular, 18, 100, 350, $muted, 'Warehouse action: ' . ucfirst(str_replace('_', ' ', (string) $definition['disposition_code'])));
        $this->drawText($image, $fontRegular, 18, 100, 385, $muted, 'Evidence slot: ' . $slot);

        imagefilledrectangle($image, 100, 430, $width - 100, 620, $background);
        $this->drawText($image, $fontBold, 20, 125, 470, $ink, 'Observed detail');

        $wrappedNotes = wordwrap((string) $definition['notes'], 66, "\n");
        $y = 510;
        foreach (explode("\n", $wrappedNotes) as $line) {
            $this->drawText($image, $fontRegular, 18, 125, $y, $ink, $line);
            $y += 32;
        }

        imagefilledrectangle($image, 100, 680, $width - 100, 760, $accent);
        $this->drawText($image, $fontBold, 22, 125, 730, $background, 'Brand rule coverage and refund control can be reviewed in the generated pack.');

        $png = $this->encodePng($image);

        if ($png === '') {
            imagedestroy($image);
            throw new \RuntimeException("Failed to encode demo evidence image for {$fileName}.");
        }

        if (!Storage::disk('public')->put('return-cases/' . $fileName, $png)) {
            imagedestroy($image);
            throw new \RuntimeException("Failed to write demo evidence image to public disk for {$fileName}.");
        }

        imagedestroy($image);
    }

    private function paletteForBrand($image, string $brand): array
    {
        return match ($brand) {
            'Peak Audio' => [
                imagecolorallocate($image, 244, 247, 252),
                imagecolorallocate($image, 255, 255, 255),
                imagecolorallocate($image, 20, 60, 116),
                imagecolorallocate($image, 16, 35, 59),
                imagecolorallocate($image, 95, 111, 130),
            ],
            'Luma Home' => [
                imagecolorallocate($image, 249, 246, 240),
                imagecolorallocate($image, 255, 252, 247),
                imagecolorallocate($image, 111, 78, 55),
                imagecolorallocate($image, 61, 43, 30),
                imagecolorallocate($image, 122, 103, 87),
            ],
            default => [
                imagecolorallocate($image, 246, 247, 251),
                imagecolorallocate($image, 255, 255, 255),
                imagecolorallocate($image, 133, 47, 93),
                imagecolorallocate($image, 48, 28, 45),
                imagecolorallocate($image, 112, 93, 107),
            ],
        };
    }

    private function drawText($image, string $font, int $size, int $x, int $y, $color, string $text): void
    {
        imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
    }

    private function encodePng($image): string
    {
        ob_start();
        imagepng($image);
        return (string) ob_get_clean();
    }
}
