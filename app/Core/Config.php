<?php

namespace App\Core;

/**
 * Configuration Manager
 */
class Config
{
    private array $config = [];
    private bool $isInstalled;

    public function __construct(bool $isInstalled)
    {
        $this->isInstalled = $isInstalled;

        if ($isInstalled) {
            $envFile = CONFIG_PATH . '/.env.php';
            if (file_exists($envFile)) {
                $this->config = require $envFile;
            }
        }

        // Set defaults
        $this->setDefaults();
    }

    private function setDefaults(): void
    {
        $defaults = [
            'app' => [
                'name' => 'CMMC Compliance Suite',
                'version' => '1.0.0',
                'timezone' => 'UTC',
                'debug' => false,
            ],
            'session' => [
                'lifetime' => 7200, // 2 hours
                'idle_timeout' => 1800, // 30 minutes
            ],
            'security' => [
                'csrf_enabled' => true,
                'rate_limit_enabled' => true,
                'force_https' => false,
            ],
            'upload' => [
                'max_size' => 10485760, // 10MB
                'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'png', 'jpg', 'jpeg'],
            ],
        ];

        foreach ($defaults as $key => $values) {
            if (!isset($this->config[$key])) {
                $this->config[$key] = $values;
            } else {
                $this->config[$key] = array_merge($values, $this->config[$key]);
            }
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    public function all(): array
    {
        return $this->config;
    }

    public function save(): bool
    {
        $envFile = CONFIG_PATH . '/.env.php';
        $content = "<?php\n\n// Auto-generated configuration file\n// Do not edit manually\n\nreturn " . var_export($this->config, true) . ";\n";

        return file_put_contents($envFile, $content) !== false;
    }
}
