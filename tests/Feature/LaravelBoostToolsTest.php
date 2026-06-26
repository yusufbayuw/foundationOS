<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Process;
use Laravel\Boost\Mcp\ToolRegistry;
use Laravel\Boost\Mcp\Tools\ApplicationInfo;
use Laravel\Boost\Mcp\Tools\GetAbsoluteUrl;
use Laravel\Mcp\Request;
use Tests\TestCase;

class LaravelBoostToolsTest extends TestCase
{
    public function test_bootstrap_script_exists_and_is_executable(): void
    {
        $script = base_path('scripts/bootstrap-boost.sh');

        $this->assertFileExists($script);
        $this->assertTrue(is_executable($script));
    }

    public function test_cloud_environment_configures_laravel_boost_terminal(): void
    {
        $environment = json_decode(
            (string) file_get_contents(base_path('.cursor/environment.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertStringContainsString('bootstrap-boost.sh', $environment['install']);
        $this->assertStringContainsString('bootstrap-boost.sh', $environment['start']);

        $terminalNames = array_column($environment['terminals'], 'name');
        $this->assertContains('laravel-boost-mcp', $terminalNames);
    }

    public function test_mcp_config_declares_laravel_boost_server(): void
    {
        $mcp = json_decode(
            (string) file_get_contents(base_path('.cursor/mcp.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertArrayHasKey('laravel-boost', $mcp['mcpServers']);
        $this->assertSame('local', $mcp['mcpServers']['laravel-boost']['env']['APP_ENV']);
        $this->assertSame('true', $mcp['mcpServers']['laravel-boost']['env']['APP_DEBUG']);
    }

    public function test_boost_tool_registry_includes_core_tools(): void
    {
        $tools = ToolRegistry::getAvailableTools();

        $this->assertContains(ApplicationInfo::class, $tools);
        $this->assertContains(GetAbsoluteUrl::class, $tools);
        $this->assertContains('Laravel\\Boost\\Mcp\\Tools\\SearchDocs', $tools);
        $this->assertContains('Laravel\\Boost\\Mcp\\Tools\\DatabaseQuery', $tools);
    }

    public function test_get_absolute_url_tool_returns_admin_url(): void
    {
        $tool = new GetAbsoluteUrl;
        $response = $tool->handle(new Request(['path' => '/admin']));

        $this->assertStringEndsWith('/admin', (string) $response->content());
    }

    public function test_application_info_tool_reports_laravel_version(): void
    {
        $tool = app(ApplicationInfo::class);
        $response = $tool->handle(new Request([]));
        $payload = json_decode((string) $response->content(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION, $payload['php_version']);
        $this->assertSame(app()->version(), $payload['laravel_version']);
        $this->assertSame(config('database.default'), $payload['database_engine']);
    }

    public function test_boost_execute_tool_command_runs_database_query(): void
    {
        $arguments = base64_encode(json_encode([
            'query' => 'SELECT 1 AS ok',
        ], JSON_THROW_ON_ERROR));

        $result = Process::path(base_path())
            ->env([
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true',
            ])
            ->run([
                'php',
                'artisan',
                'boost:execute-tool',
                'Laravel\\Boost\\Mcp\\Tools\\DatabaseQuery',
                $arguments,
            ]);

        $result->throw();

        $payload = json_decode($result->output(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertFalse($payload['isError']);
        $this->assertStringContainsString('"ok":1', $payload['content'][0]['text']);
    }
}
