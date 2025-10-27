<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UpdateCourseImagesSeeder extends Seeder
{
    /**
     * Map từng slug khóa học sang file ảnh tương ứng trong public/Assets/Images.
     */
    private const IMAGE_MAP = [
        'luyen-thi-toeic-speaking-405-600'  => 'toeic-speaking-405-600.png',
        'luyen-thi-toeic-writing-405-600'   => 'toeic-writing-405-600.png',
        'luyen-thi-toeic-listening-405-600' => 'toeic-listening-405-600.png',
        'luyen-thi-toeic-reading-405-600'   => 'toeic-reading-405-600.png',

        'luyen-thi-toeic-speaking-605-780'  => 'toeic-speaking-605-780.png',
        'luyen-thi-toeic-writing-605-780'   => 'toeic-writing-605-780.png',
        'luyen-thi-toeic-listening-605-780' => 'toeic-listening-605-780.png',
        'luyen-thi-toeic-reading-605-780'   => 'toeic-reading-605-780.png',

        'luyen-thi-toeic-speaking-785-990'  => 'toeic-speaking-785-990.png',
        'luyen-thi-toeic-writing-785-990'   => 'toeic-writing-785-990.png',
        'luyen-thi-toeic-listening-785-990' => 'toeic-listening-785-990.png',
        'luyen-thi-toeic-reading-785-990'   => 'toeic-reading-785-990.png',
    ];

    public function run(): void
    {
        $this->warnMissingAssets();

        DB::transaction(function () {
            foreach (self::IMAGE_MAP as $slug => $fileName) {
                Course::where('slug', $slug)->update(['hinhanh' => $fileName]);
            }
        });
    }

    private function warnMissingAssets(): void
    {
        if (!$this->command) {
            return;
        }

        foreach (self::IMAGE_MAP as $fileName) {
            $relativePath = 'Assets/Images/' . $fileName;
            if (!File::exists(public_path($relativePath))) {
                $this->command->warn("Không tìm thấy file ảnh: {$relativePath}");
            }
        }
    }
}
