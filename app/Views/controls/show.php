<?php
$page_title = $control['code'] . ' - ' . $control['title'];
$current_page = 'controls';

ob_start();
?>

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="<?= $url('controls') ?>">Controls</a> /
            <a href="<?= $url('controls/' . strtolower($control['framework'])) ?>"><?= $e($control['framework']) ?></a> /
            <?= $e($control['code']) ?>
        </div>
        <h1><?= $e($control['code']) ?></h1>
        <p class="page-subtitle"><?= $e($control['title']) ?></p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Control Details</h3>
        <?php if (isset($control['ml_level'])): ?>
            <span class="badge badge-info">CMMC ML<?= $control['ml_level'] ?></span>
        <?php endif; ?>
        <?php if (isset($control['stig_severity'])): ?>
            <span class="badge badge-<?= $control['stig_severity'] === 'high' ? 'danger' : ($control['stig_severity'] === 'medium' ? 'warning' : 'secondary') ?>">
                <?= ucfirst($control['stig_severity']) ?>
            </span>
        <?php endif; ?>
    </div>

    <table class="info-table">
        <tr>
            <th>Framework</th>
            <td><?= $e($control['framework']) ?></td>
        </tr>
        <tr>
            <th>Control Code</th>
            <td><code><?= $e($control['code']) ?></code></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?= $e($control['title']) ?></td>
        </tr>
        <?php if (!empty($control['description'])): ?>
        <tr>
            <th>Description</th>
            <td><?= $e($control['description']) ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($control['category'])): ?>
        <tr>
            <th>Category</th>
            <td>
                <?php
                $categoryAbbreviations = [
                    'Access Control' => 'AC',
                    'Awareness and Training' => 'AT',
                    'Audit and Accountability' => 'AU',
                    'Configuration Management' => 'CM',
                    'Identification and Authentication' => 'IA',
                    'Incident Response' => 'IR',
                    'Maintenance' => 'MA',
                    'Media Protection' => 'MP',
                    'Personnel Security' => 'PS',
                    'Physical Protection' => 'PE',
                    'Risk Assessment' => 'RA',
                    'Security Assessment' => 'CA',
                    'System and Communications Protection' => 'SC',
                    'System and Information Integrity' => 'SI',
                ];
                $abbr = $categoryAbbreviations[$control['category']] ?? '';
                ?>
                <span class="badge badge-info"><?= $e($abbr) ?></span> <?= $e($control['category']) ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (isset($control['sprs_score']) && in_array($control['framework'], ['NIST800171', 'CMMC'])): ?>
        <tr>
            <th>SPRS Points</th>
            <td>
                <strong style="font-size: 1.1em;"><?= $control['sprs_score'] ?></strong> points
                <?php if ($control['sprs_score'] == 5): ?>
                    <span class="badge badge-danger">High-Risk</span>
                <?php elseif ($control['sprs_score'] == 1): ?>
                    <span class="badge badge-success">Low-Risk</span>
                <?php else: ?>
                    <span class="badge badge-warning">Medium-Risk</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (isset($control['partial_credit']) && in_array($control['framework'], ['NIST800171', 'CMMC'])): ?>
        <tr>
            <th>Partial Credit</th>
            <td>
                <?php if ($control['partial_credit'] == 1): ?>
                    <span class="badge badge-success">✓ Yes</span> - Partial credit may be awarded for this control
                <?php else: ?>
                    <span class="badge badge-secondary">No</span> - Full implementation required
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (isset($control['stig_version'])): ?>
        <tr>
            <th>STIG Version</th>
            <td><?= $e($control['stig_version']) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<?php if (!empty($control['description'])): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>📋 Implementation Guidance</h3>
    </div>
    <div style="padding: 20px;">
        <h4 style="margin-top: 0; color: #4a5568;">What Does This Control Require?</h4>
        <div style="background: #f7fafc; padding: 15px; border-left: 4px solid #667eea; margin-bottom: 20px;">
            <?= nl2br($e($control['description'])) ?>
        </div>

        <?php if (!empty($control['implementation_guidance'])): ?>
        <h4 style="color: #4a5568;">How to Implement:</h4>
        <div style="line-height: 1.8; background: #f0fdf4; padding: 15px; border-left: 4px solid #10b981; margin-bottom: 20px;">
            <?= nl2br($e($control['implementation_guidance'])) ?>
        </div>
        <?php else: ?>
        <h4 style="color: #4a5568;">How to Implement:</h4>
        <div style="line-height: 1.8;">
            <?php
            // Generate basic implementation steps based on control category
            $category = $control['category'] ?? '';
            $code = $control['code'];

            echo "<p><strong>General Steps:</strong></p>";
            echo "<ol style='padding-left: 25px;'>";

            // Category-specific guidance
            if (strpos($category, 'Access Control') !== false) {
                echo "<li>Document your access control policy and procedures</li>";
                echo "<li>Implement technical controls (user permissions, role-based access, etc.)</li>";
                echo "<li>Train users on proper access procedures</li>";
                echo "<li>Review and update access lists regularly</li>";
                echo "<li>Audit and log access attempts</li>";
            } elseif (strpos($category, 'Awareness and Training') !== false) {
                echo "<li>Develop security awareness training materials</li>";
                echo "<li>Schedule regular training sessions for all personnel</li>";
                echo "<li>Document training attendance and completion</li>";
                echo "<li>Update training content annually or when threats change</li>";
                echo "<li>Test user knowledge through quizzes or simulations</li>";
            } elseif (strpos($category, 'Audit') !== false) {
                echo "<li>Configure logging on all relevant systems</li>";
                echo "<li>Determine what events need to be logged</li>";
                echo "<li>Implement centralized log collection if possible</li>";
                echo "<li>Set up log retention policies</li>";
                echo "<li>Review logs regularly for security events</li>";
            } elseif (strpos($category, 'Configuration Management') !== false) {
                echo "<li>Document baseline configurations for all systems</li>";
                echo "<li>Implement configuration management tools</li>";
                echo "<li>Create change control procedures</li>";
                echo "<li>Test all configuration changes before deployment</li>";
                echo "<li>Maintain inventory of all systems and software</li>";
            } elseif (strpos($category, 'Identification and Authentication') !== false) {
                echo "<li>Implement unique user accounts (no shared accounts)</li>";
                echo "<li>Configure strong password requirements</li>";
                echo "<li>Enable multi-factor authentication where required</li>";
                echo "<li>Disable or remove inactive accounts</li>";
                echo "<li>Monitor and log authentication attempts</li>";
            } elseif (strpos($category, 'Incident Response') !== false) {
                echo "<li>Create an incident response plan</li>";
                echo "<li>Define roles and responsibilities for incident handling</li>";
                echo "<li>Establish incident detection and monitoring capabilities</li>";
                echo "<li>Set up incident reporting procedures</li>";
                echo "<li>Practice incident response through tabletop exercises</li>";
            } elseif (strpos($category, 'Maintenance') !== false) {
                echo "<li>Document maintenance procedures and schedules</li>";
                echo "<li>Approve all maintenance activities in advance</li>";
                echo "<li>Control and sanitize maintenance tools</li>";
                echo "<li>Log all maintenance activities</li>";
                echo "<li>Review maintenance logs periodically</li>";
            } elseif (strpos($category, 'Media Protection') !== false) {
                echo "<li>Identify and mark all media containing sensitive information</li>";
                echo "<li>Implement physical controls for media storage</li>";
                echo "<li>Create procedures for media transport</li>";
                echo "<li>Sanitize media before disposal or reuse</li>";
                echo "<li>Maintain logs of media handling and disposal</li>";
            } elseif (strpos($category, 'Personnel Security') !== false) {
                echo "<li>Conduct background checks appropriate to position risk</li>";
                echo "<li>Require signed confidentiality agreements</li>";
                echo "<li>Implement termination procedures (access removal, exit interview)</li>";
                echo "<li>Define acceptable use policies</li>";
                echo "<li>Review and update personnel security policies annually</li>";
            } elseif (strpos($category, 'Physical Protection') !== false) {
                echo "<li>Identify physical security boundaries</li>";
                echo "<li>Implement access controls (badges, locks, etc.)</li>";
                echo "<li>Install monitoring equipment (cameras, alarms)</li>";
                echo "<li>Control visitor access and escort procedures</li>";
                echo "<li>Maintain logs of physical access</li>";
            } elseif (strpos($category, 'Risk Assessment') !== false) {
                echo "<li>Identify and document threats and vulnerabilities</li>";
                echo "<li>Assess likelihood and impact of potential risks</li>";
                echo "<li>Prioritize risks based on assessment results</li>";
                echo "<li>Develop risk mitigation strategies</li>";
                echo "<li>Review and update risk assessments regularly</li>";
            } elseif (strpos($category, 'Security Assessment') !== false) {
                echo "<li>Develop a security assessment plan</li>";
                echo "<li>Conduct regular vulnerability scans</li>";
                echo "<li>Perform penetration testing periodically</li>";
                echo "<li>Document all findings and create remediation plans</li>";
                echo "<li>Track remediation progress and verify fixes</li>";
            } elseif (strpos($category, 'System and Communications Protection') !== false) {
                echo "<li>Implement boundary protection (firewalls, etc.)</li>";
                echo "<li>Configure encryption for data in transit</li>";
                echo "<li>Separate internal networks with VLANs or segmentation</li>";
                echo "<li>Monitor network traffic for anomalies</li>";
                echo "<li>Update and patch security controls regularly</li>";
            } elseif (strpos($category, 'System and Information Integrity') !== false) {
                echo "<li>Deploy and configure antimalware software</li>";
                echo "<li>Implement patch management procedures</li>";
                echo "<li>Set up security alert and advisory monitoring</li>";
                echo "<li>Conduct regular system integrity checks</li>";
                echo "<li>Update malware definitions and security tools regularly</li>";
            } else {
                echo "<li>Review the control requirements carefully</li>";
                echo "<li>Document your implementation approach</li>";
                echo "<li>Implement necessary technical and administrative controls</li>";
                echo "<li>Test the implementation</li>";
                echo "<li>Maintain documentation and evidence of compliance</li>";
            }

            echo "</ol>";
            ?>
        </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            <?php if (!empty($control['who_implements'])): ?>
            <div style="padding: 15px; background: #eff6ff; border-left: 4px solid #3b82f6;">
                <strong>👤 Who Implements:</strong>
                <p style="margin: 5px 0 0 0;"><?= nl2br($e($control['who_implements'])) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($control['estimated_effort'])): ?>
            <div style="padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b;">
                <strong>⏱️ Estimated Effort:</strong>
                <p style="margin: 5px 0 0 0;">
                    <span class="badge badge-<?= $control['estimated_effort'] === 'Low' ? 'success' : ($control['estimated_effort'] === 'High' || $control['estimated_effort'] === 'Very High' ? 'danger' : 'warning') ?>">
                        <?= $e($control['estimated_effort']) ?>
                    </span>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($control['tools_needed'])): ?>
        <div style="margin-top: 20px; padding: 15px; background: #f3e8ff; border-left: 4px solid #a855f7;">
            <strong>🔧 Tools & Technologies Needed:</strong>
            <div style="margin-top: 10px;">
                <?= nl2br($e($control['tools_needed'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($control['common_solutions'])): ?>
        <div style="margin-top: 20px; padding: 15px; background: #ecfdf5; border-left: 4px solid #10b981;">
            <strong>✅ Common Solutions:</strong>
            <div style="margin-top: 10px;">
                <?= nl2br($e($control['common_solutions'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($control['helpful_resources'])): ?>
        <div style="margin-top: 20px; padding: 15px; background: #fce7f3; border-left: 4px solid #ec4899;">
            <strong>📚 Helpful Resources:</strong>
            <div style="margin-top: 10px;">
                <?= nl2br($e($control['helpful_resources'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <div style="margin-top: 20px; padding: 15px; background: #fffbeb; border-left: 4px solid #f59e0b;">
            <strong>💡 Tip:</strong> Document everything! Keep records of policies, procedures, implementation details, training records, and any changes made to systems. This documentation is essential for demonstrating compliance during assessments.
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($mappings['maps_to']) || !empty($mappings['mapped_from'])): ?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>Related Controls</h3>
    </div>

    <?php if (!empty($mappings['maps_to'])): ?>
    <h4 style="padding: 15px; margin: 0; background: #f7fafc;">Maps To</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>Framework</th>
                <th>Control Code</th>
                <th>Title</th>
                <th>Relationship</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mappings['maps_to'] as $mapping): ?>
            <tr>
                <td><?= $e($mapping['target_framework']) ?></td>
                <td><code><?= $e($mapping['target_code']) ?></code></td>
                <td><?= $e($mapping['target_title']) ?></td>
                <td><span class="badge badge-secondary"><?= ucwords(str_replace('_', ' ', $mapping['relation_type'])) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (!empty($mappings['mapped_from'])): ?>
    <h4 style="padding: 15px; margin: 0; background: #f7fafc;">Mapped From</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>Framework</th>
                <th>Control Code</th>
                <th>Title</th>
                <th>Relationship</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mappings['mapped_from'] as $mapping): ?>
            <tr>
                <td><?= $e($mapping['source_framework']) ?></td>
                <td><code><?= $e($mapping['source_code']) ?></code></td>
                <td><?= $e($mapping['source_title']) ?></td>
                <td><span class="badge badge-secondary"><?= ucwords(str_replace('_', ' ', $mapping['relation_type'])) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="form-actions" style="margin-top: 20px;">
    <a href="<?= $url('controls/' . strtolower($control['framework'])) ?>" class="btn btn-secondary">Back to Controls</a>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
