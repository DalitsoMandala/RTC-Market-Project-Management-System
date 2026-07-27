<?php
namespace App\Services;

use App\Models\Indicator;
use App\Models\IndicatorClass;
use App\Models\IndicatorClasses;
use Illuminate\Support\Facades\File;

/**
 * Resolves (or creates) the IndicatorClasses row that stores an indicator's
 * file location, and makes sure the corresponding directory/file exist on disk.
 *
 * This is the same logic as IndicatorTable::ensureRtcMarketPathExists(),
 * pulled out so both the table and the create/edit form can share it.
 *
 * ASSUMPTION: IndicatorClasses is keyed one-row-per-indicator via `indicator_id`
 * (matches the original join in IndicatorTable::datasource()). If your intent
 * is actually "one location per indicator *type*" shared across several
 * indicators, this needs to key off a type/category column instead — flag it
 * and I'll adjust.
 */
class IndicatorDirectoryService
{
    /**
     * Ensure an IndicatorClasses row + on-disk directory/file exist for this indicator.
     * Returns the IndicatorClasses model (with ->class holding the location path).
     */
    public function ensureLocation(Indicator $indicator): IndicatorClass
    {
        $class = IndicatorClass::firstOrNew(['indicator_id' => $indicator->id]);

        if (! $class->exists || empty($class->class)) {
            $class->indicator_id = $indicator->id;
            $class->class        = $this->buildLocationPath($indicator);
            $class->save();
        }

        $this->ensurePathExists($class->class, $this->fileName($indicator));

        return $class;
    }

    protected function buildLocationPath(Indicator $indicator): string
    {
        $folder = 'indicator_' . str_replace('.', '_', $indicator->indicator_no);

        return "Helpers/rtc_market/indicators/{$folder}";
    }

    protected function fileName(Indicator $indicator): string
    {
        return 'indicator_' . str_replace('.', '_', $indicator->indicator_no) . '.php';
    }

    /**
     * Same behaviour as the original ensureRtcMarketPathExists() on IndicatorTable.
     */
    public function ensurePathExists(string $location, ?string $fileName = null, string $defaultContent = ''): string
    {
        $cleanPath    = str_replace('\\', '/', $location);
        $relativePath = preg_replace('#^app/#i', '', $cleanPath);
        $segments     = explode('/', trim($relativePath, '/'));

        $directoryPath = app_path(...$segments);

        if (! File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        if (! $fileName) {
            return $directoryPath;
        }

        $filePath = app_path(...array_merge($segments, [$fileName]));

        if (! File::exists($filePath)) {
            File::put($filePath, $defaultContent);
        }

        return $filePath;
    }
}
