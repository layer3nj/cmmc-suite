<?php
$page_title = 'Documents & Evidence';
$current_page = 'documents';

ob_start();
?>

<div class="page-header">
    <h1>Documents & Evidence</h1>
    <p class="page-subtitle">Upload and manage compliance evidence</p>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="document.getElementById('upload-form').style.display='block'">Upload Document</button>
    </div>
</div>

<!-- Security Warning -->
<div class="alert" style="background-color: #fef2f2; border-left: 4px solid #dc2626; color: #991b1b; margin-bottom: 20px; padding: 16px 20px;">
    <div style="display: flex; align-items: start; gap: 12px;">
        <svg style="width: 24px; height: 24px; flex-shrink: 0; color: #dc2626; margin-top: 2px;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            <h4 style="margin: 0 0 8px 0; font-weight: 600; font-size: 16px; color: #991b1b;">SECURITY NOTICE - DO NOT UPLOAD CONTROLLED INFORMATION</h4>
            <p style="margin: 0; line-height: 1.6;">
                <strong>Do NOT upload any documents containing:</strong>
            </p>
            <ul style="margin: 8px 0 0 20px; line-height: 1.8;">
                <li><strong>CUI (Controlled Unclassified Information)</strong></li>
                <li><strong>ITAR (International Traffic in Arms Regulations) controlled data</strong></li>
                <li><strong>Classified information</strong> of any level</li>
                <li><strong>Export-controlled technical data</strong></li>
                <li><strong>Personally Identifiable Information (PII)</strong> or sensitive customer data</li>
                <li><strong>Proprietary or confidential business information</strong></li>
            </ul>
            <p style="margin: 12px 0 0 0; font-weight: 500;">
                Only upload <strong>unclassified, non-sensitive compliance documentation</strong> such as policies, procedures, sanitized evidence, and public-facing materials.
            </p>
        </div>
    </div>
</div>

<div id="upload-form" class="card" style="display: none; margin-bottom: 20px;">
    <div class="card-header">
        <h3>Upload Document</h3>
    </div>
    <div style="background-color: #fffbeb; border: 1px solid #fbbf24; color: #92400e; padding: 12px 16px; margin: 16px 16px 0 16px; border-radius: 4px; font-size: 14px;">
        <strong>⚠️ Reminder:</strong> Do not upload CUI, ITAR, classified, or any other controlled/sensitive information.
    </div>
    <form action="<?= $url('documents/upload') ?>" method="POST" enctype="multipart/form-data">
        <?= $csrf() ?>
        <div class="form-group">
            <label>Document Title</label>
            <input type="text" name="title" required class="form-control">
        </div>
        <div class="form-group">
            <label>Control Reference (optional)</label>
            <input type="text" name="control_code" class="form-control" placeholder="e.g., 3.1.1">
        </div>
        <div class="form-group">
            <label>File</label>
            <input type="file" name="file" required class="form-control">
            <small>Allowed types: PDF, DOC, DOCX, XLS, XLSX, TXT, PNG, JPG, JPEG (Max: 10MB)</small>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('upload-form').style.display='none'">Cancel</button>
            <button type="submit" class="btn btn-primary">Upload</button>
        </div>
    </form>
</div>

<?php if (!$current_customer): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        Please <a href="<?= $url('customers') ?>">select a customer</a> to view documents.
    </div>
<?php elseif (empty($documents)): ?>
    <div class="alert alert-info">
        <span class="alert-icon">ℹ️</span>
        No documents uploaded yet. Upload your first compliance evidence document.
    </div>
<?php else: ?>
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Control Reference</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Uploaded By</th>
                    <th>Upload Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><strong><?= $e($doc['title'] ?? $doc['file_name']) ?></strong></td>
                    <td>
                        <?php if (!empty($doc['linked_code'])): ?>
                            <code><?= $e($doc['linked_code']) ?></code>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $doc['mime_type'] ? $e(strtoupper(pathinfo($doc['file_name'], PATHINFO_EXTENSION))) : 'Unknown' ?></td>
                    <td><?= number_format($doc['file_size'] / 1024, 1) ?> KB</td>
                    <td><?= $e($doc['uploaded_by_name'] ?? 'Unknown') ?></td>
                    <td><?= date('M d, Y', strtotime($doc['created_at'])) ?></td>
                    <td>
                        <a href="<?= $url('documents/' . $doc['id'] . '/download') ?>" class="btn btn-sm btn-primary">Download</a>
                        <form action="<?= $url('documents/' . $doc['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this document?');">
                            <?= $csrf() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layout/app.php';
