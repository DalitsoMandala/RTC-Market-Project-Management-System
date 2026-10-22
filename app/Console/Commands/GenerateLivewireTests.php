<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateLivewireTests extends Command
{
    protected $signature   = 'livewire:generate-tests {--pest : Generate Pest PHP tests instead of PHPUnit}';
    protected $description = 'Scan all Livewire 3 components and create missing test files';

    public function handle()
    {
        $componentPath = app_path('Livewire');

        if (! File::exists($componentPath)) {
            $this->error("The directory [app/Livewire] does not exist.");
            return;
        }

        $files   = File::allFiles($componentPath);
        $usePest = $this->option('pest');
        $count   = 0;

        foreach ($files as $file) {
            // Only look for PHP component files
            if ($file->getExtension() !== 'php') {
                continue;
            }

            // Get relative path from app/Livewire to support nested components
            $relativePath = $file->getRelativePathname();
            $className    = pathinfo($relativePath, PATHINFO_FILENAME);

            // Subdirectory support (e.g., Admin/Dashboard.php -> Admin)
            $subDir = dirname($relativePath);
            $subDir = $subDir === '.' ? '' : str_replace('/', '\\', $subDir);

            // Fully qualified class name
            $fullNamespace = 'App\\Livewire' . ($subDir ? '\\' . $subDir : '');
            $fullClassName = $fullNamespace . '\\' . $className;

            // Determine test path
            $testSubPath  = $subDir ? str_replace('\\', '/', $subDir) . '/' : '';
            $testFileName = $className . 'Test.php';
            $testPath     = base_path("tests/Feature/Livewire/{$testSubPath}{$testFileName}");

            if (File::exists($testPath)) {
                $this->info("Skipped (Exists): {$testFileName}");
                continue;
            }

            // Ensure test directory exists
            File::ensureDirectoryExists(dirname($testPath));

            // Generate content based on test framework (Pest vs PHPUnit)
            if ($usePest) {
                $content = $this->getPestStub($fullClassName, $className);
            } else {
                $content = $this->getPhpUnitStub($fullNamespace, $fullClassName, $className);
            }

            File::put($testPath, $content);
            $this->line("<info>Created Test:</info> tests/Feature/Livewire/{$testSubPath}{$testFileName}");
            $count++;
        }

        $this->info("\nSuccessfully generated {$count} missing Livewire test file(s)!");
    }

    protected function getPhpUnitStub(string $namespace, string $fullClassName, string $className): string
    {
        return "<?php

namespace Tests\\Feature\\Livewire;

use App\\Livewire\\" . class_basename($fullClassName) . ";
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Livewire\\Livewire;
use Tests\\TestCase;

class {$className}Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        \$this->withoutVite();

        Livewire::test({$className}::class)
            ->assertStatus(200);
    }
}
";
    }

    protected function getPestStub(string $fullClassName, string $className): string
    {
        $shortName = class_basename($fullClassName);
        return "<?php

use App\\Livewire\\{$shortName};
use Livewire\\Livewire;

it('can render component', function () {
    Livewire::test({$shortName}::class)
        ->assertStatus(200);
});
";
    }
}