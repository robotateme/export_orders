<?php

declare(strict_types=1);

namespace ExportOrders\Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class ArchitectureBoundaryTest extends TestCase
{
    public function testDomainDoesNotDependOnApplicationOrInfrastructure(): void
    {
        $this->assertFilesDoNotContain(
            __DIR__ . '/../../src/Domain',
            [
                'ExportOrders\\Application\\',
                'ExportOrders\\Infrastructure\\',
                'PDO',
            ]
        );
    }

    public function testApplicationDoesNotDependOnInfrastructure(): void
    {
        $this->assertFilesDoNotContain(
            __DIR__ . '/../../src/Application',
            [
                'ExportOrders\\Infrastructure\\',
            ]
        );
    }

    /**
     * @param list<string> $forbiddenFragments
     */
    private function assertFilesDoNotContain(string $directory, array $forbiddenFragments): void
    {
        foreach ($this->phpFiles($directory) as $file) {
            $contents = file_get_contents($file);

            self::assertIsString($contents);

            foreach ($forbiddenFragments as $fragment) {
                self::assertStringNotContainsString(
                    $fragment,
                    $contents,
                    sprintf('%s must not contain %s', $file, $fragment)
                );
            }
        }
    }

    /**
     * @return list<string>
     */
    private function phpFiles(string $directory): array
    {
        $files = glob($directory . '/**/*.php', GLOB_BRACE);

        self::assertIsArray($files);

        return $files;
    }
}
