<?php

namespace App\Services;

/**
 * Template Processor - Handles variable substitution in document templates
 * Supports variables like {{client.name}}, {{current_date}}, etc.
 */
class TemplateProcessor
{
    private array $variables = [];

    /**
     * Set variables for substitution
     */
    public function setVariables(array $variables): self
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    /**
     * Set client data
     */
    public function setClient(array $client): self
    {
        $this->variables['client.name'] = $client['name'] ?? '';
        $this->variables['client.contact_name'] = $client['contact_name'] ?? '';
        $this->variables['client.contact_email'] = $client['contact_email'] ?? '';
        $this->variables['client.contact_phone'] = $client['contact_phone'] ?? '';
        $this->variables['client.address'] = $client['address'] ?? '';
        $this->variables['client.id'] = $client['id'] ?? '';

        // Add autotask/itglue IDs if available
        if (!empty($client['autotask_company_id'])) {
            $this->variables['client.autotask_id'] = $client['autotask_company_id'];
        }
        if (!empty($client['itglue_organization_id'])) {
            $this->variables['client.itglue_id'] = $client['itglue_organization_id'];
        }

        return $this;
    }

    /**
     * Set assessment data
     */
    public function setAssessment(?array $assessment): self
    {
        if ($assessment) {
            $this->variables['assessment.framework'] = $assessment['framework'] ?? '';
            $this->variables['assessment.level'] = $assessment['target_level'] ?? '';
            $this->variables['assessment.type'] = $assessment['assessment_type'] ?? '';
            $this->variables['assessment.date'] = $assessment['assessed_at'] ?? '';
            $this->variables['assessment.status'] = $assessment['status'] ?? '';
        }

        return $this;
    }

    /**
     * Set company/MSP data
     */
    public function setCompany(array $settings): self
    {
        $this->variables['company.name'] = $settings['site_name'] ?? 'CMMC Compliance Suite';
        $this->variables['company.email'] = $settings['company_email'] ?? '';
        $this->variables['company.phone'] = $settings['company_phone'] ?? '';
        $this->variables['company.address'] = $settings['company_address'] ?? '';
        $this->variables['company.website'] = $settings['company_website'] ?? '';

        return $this;
    }

    /**
     * Set date/time variables
     */
    public function setDateVariables(): self
    {
        $this->variables['current_date'] = date('F d, Y');
        $this->variables['current_date_short'] = date('m/d/Y');
        $this->variables['current_year'] = date('Y');
        $this->variables['current_month'] = date('F');
        $this->variables['current_day'] = date('d');

        return $this;
    }

    /**
     * Process template content and replace all variables
     */
    public function process(string $template): string
    {
        // Always set date variables
        $this->setDateVariables();

        $processed = $template;

        // Replace all {{variable.name}} with actual values
        foreach ($this->variables as $key => $value) {
            $pattern = '/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/';
            $processed = preg_replace($pattern, $value, $processed);
        }

        // Handle conditional blocks: {{#if variable}}content{{/if}}
        $processed = $this->processConditionals($processed);

        return $processed;
    }

    /**
     * Process conditional blocks
     */
    private function processConditionals(string $content): string
    {
        // Pattern: {{#if variable}}content{{/if}}
        $pattern = '/\{\{#if\s+([^}]+)\}\}(.*?)\{\{\/if\}\}/s';

        $content = preg_replace_callback($pattern, function($matches) {
            $variable = trim($matches[1]);
            $innerContent = $matches[2];

            // Check if variable exists and is not empty
            if (isset($this->variables[$variable]) && !empty($this->variables[$variable])) {
                return $innerContent;
            }

            return ''; // Remove the block if variable is not set
        }, $content);

        return $content;
    }

    /**
     * Get list of all available variables
     */
    public function getAvailableVariables(): array
    {
        return [
            'Client Variables' => [
                '{{client.name}}' => 'Client organization name',
                '{{client.contact_name}}' => 'Primary contact name',
                '{{client.contact_email}}' => 'Contact email',
                '{{client.contact_phone}}' => 'Contact phone',
                '{{client.address}}' => 'Client address',
            ],
            'Assessment Variables' => [
                '{{assessment.framework}}' => 'Framework (CMMC, NIST, etc.)',
                '{{assessment.level}}' => 'Maturity level',
                '{{assessment.type}}' => 'Assessment type',
                '{{assessment.date}}' => 'Assessment date',
            ],
            'Company/MSP Variables' => [
                '{{company.name}}' => 'Your company name',
                '{{company.email}}' => 'Your company email',
                '{{company.phone}}' => 'Your company phone',
                '{{company.address}}' => 'Your company address',
                '{{company.website}}' => 'Your company website',
            ],
            'Date/Time Variables' => [
                '{{current_date}}' => 'Current date (long format)',
                '{{current_date_short}}' => 'Current date (MM/DD/YYYY)',
                '{{current_year}}' => 'Current year',
                '{{current_month}}' => 'Current month name',
            ],
            'Conditional Blocks' => [
                '{{#if variable}}...{{/if}}' => 'Show content only if variable exists',
            ],
        ];
    }

    /**
     * Validate template syntax
     */
    public function validateTemplate(string $template): array
    {
        $errors = [];

        // Check for unclosed {{
        $openCount = substr_count($template, '{{');
        $closeCount = substr_count($template, '}}');

        if ($openCount !== $closeCount) {
            $errors[] = 'Mismatched curly braces: ' . $openCount . ' opening vs ' . $closeCount . ' closing';
        }

        // Check for unclosed conditionals
        $ifCount = preg_match_all('/\{\{#if\s+[^}]+\}\}/', $template);
        $endifCount = preg_match_all('/\{\{\/if\}\}/', $template);

        if ($ifCount !== $endifCount) {
            $errors[] = 'Mismatched #if blocks: ' . $ifCount . ' opening vs ' . $endifCount . ' closing';
        }

        return $errors;
    }
}
