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

<div id="upload-form" class="card" style="display: none; margin-bottom: 20px;">
    <div class="card-header">
        <h3>Upload Document</h3>
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
                    <td><strong><?= $e($doc['title']) ?></strong></td>
                    <td>
                        <?php if ($doc['control_code']): ?>
                            <code><?= $e($doc['control_code']) ?></code>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $e(strtoupper($doc['file_type'])) ?></td>
                    <td><?= number_format($doc['file_size'] / 1024, 1) ?> KB</td>
                    <td><?= $e($doc['uploaded_by_name']) ?></td>
                    <td><?= date('M d, Y', strtotime($doc['uploaded_at'])) ?></td>
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
