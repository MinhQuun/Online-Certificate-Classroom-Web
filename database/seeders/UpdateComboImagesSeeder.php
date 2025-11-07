<?php

namespace Database\Seeders;

use App\Models\Combo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UpdateComboImagesSeeder extends Seeder
{
    /**
     * Map từng slug combo sang file ảnh đại diện tương ứng trong public/Assets/Combos.
     */
    private const IMAGE_MAP = [
        'toeic-combo' => 'combo_khoahoc.png',
        'toeic-foundation-full-pack-405-600' => 'combo_toeic_foundation_405-600.jpg',
        'toeic-intermediate-full-pack-605-780' => 'combo_toeic_intermediate_605-780.jpg',
        'toeic-advanced-full-pack-785-990' => 'combo_toeic_advanced_785-990.jpg',
        'toeic-foundation-405-600' => 'combo_toeic_foundation_405-600.jpg',
        'toeic-intermediate-605-780' => 'combo_toeic_intermediate_605-780.jpg',
        'toeic-advanced-785-990' => 'combo_toeic_advanced_785-990.jpg',
    ];

    public function run(): void
    {
        $this->warnMissingAssets();

        DB::transaction(function () {
            foreach (self::IMAGE_MAP as $slug => $fileName) {
                Combo::where('slug', $slug)->update(['hinhanh' => $fileName]);
            }
        });
    }

    private function warnMissingAssets(): void
    {
        if (!$this->command) {
            return;
        }

        $files = array_unique(array_values(self::IMAGE_MAP));

        foreach ($files as $fileName) {
            $relativePath = 'Assets/Combos/' . $fileName;
            if (!File::exists(public_path($relativePath))) {
                $this->command->warn("Không tìm thấy file ảnh: {$relativePath}");
            }
        }
    }
}
