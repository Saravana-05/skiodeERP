<?php
/**
 * PATCH FILE — designUploadModal_patch.php
 * ═══════════════════════════════════════════════════════════════
 * Replace ONLY the following two sections in your dashboard.php:
 *
 *  1.  The <style> block inside the existing <style> tag — add the NEW styles below
 *  2.  MODAL 1 (designUploadModal) — replace entire modal div + its <script> block
 *
 * Everything else in dashboard.php stays unchanged.
 * ═══════════════════════════════════════════════════════════════
 */
?>

<!-- ══════════════════════════════════════════════════════════════
     STEP 1 — ADD these CSS rules inside your existing <style> tag
     (replace the old #designUploadModal styles)
     ══════════════════════════════════════════════════════════════ -->
<style>
/* Design Upload Modal */
#designUploadModal .upload-drop-zone {
    border: 2px dashed #6c757d;
    border-radius: 8px;
    padding: 24px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.25s, background 0.25s;
    background: #f8f9fa;
}
#designUploadModal .upload-drop-zone.dragover {
    border-color: #0d6efd;
    background: #e8f0fe;
}
#designUploadModal .upload-drop-zone .upload-icon {
    font-size: 2rem; color: #6c757d; display: block; margin-bottom: 6px;
}
#designUploadModal .reason-section {
    margin-top: 14px; padding: 12px;
    background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px;
}
#du_progress_wrap { display: none; margin-top: 10px; }

/* Multi-file preview grid */
#du_files_preview_list {
    display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;
}
.du-file-chip {
    display: flex; align-items: center; gap: 6px;
    background: #e9ecef; border-radius: 20px;
    padding: 4px 10px; font-size: 0.78rem; max-width: 260px;
}
.du-file-chip .chip-name {
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 160px;
}
.du-file-chip .chip-remove {
    cursor: pointer; color: #dc3545; font-weight: bold;
    background: none; border: none; padding: 0; font-size: 0.9rem; line-height: 1;
}

/* Existing files table */
.du-existing-files-table { width: 100%; font-size: 0.78rem; border-collapse: collapse; margin-top: 8px; }
.du-existing-files-table th { background: #343a40; color: #fff; padding: 6px 8px; text-align: left; }
.du-existing-files-table td { padding: 5px 8px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
.du-existing-files-table tr:last-child td { border-bottom: none; }
.du-file-thumb { width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
.du-file-icon-box {
    width: 36px; height: 36px; border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; border: 1px solid #dee2e6; background: #f8f9fa;
}
</style>

<!-- ══════════════════════════════════════════════════════════════
     STEP 2 — Replace MODAL 1 entirely with this block
     ══════════════════════════════════════════════════════════════ -->

<div class="modal fade modal-lg" id="designUploadModal" tabindex="-1"
     aria-labelledby="designUploadModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-warning text-dark" style="padding:0.5rem 1rem;">
        <h5 class="modal-title" id="designUploadModalLabel">
          <span class="material-icons-round me-2" style="font-size:20px;vertical-align:-4px;">cloud_upload</span>
          Design Files — JC #<span id="du_jc_no_display"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">

        <!-- Customer info strip -->
        <div class="d-flex gap-3 mb-3 p-2 bg-light rounded border" style="font-size:0.85rem;">
          <span><strong>Customer:</strong> <span id="du_customer_name_display"></span></span>
          <span><strong>Mobile:</strong> <span id="du_customer_mobile_display"></span></span>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" id="du_jc_no"           value="" />
        <input type="hidden" id="du_customer_name"   value="" />
        <input type="hidden" id="du_customer_mobile" value="" />

        <!-- ── EXISTING FILES (shown when files already uploaded) ── -->
        <div id="du_existing_files_section" style="display:none;" class="mb-3">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <strong style="font-size:0.85rem;">
              <i class="fas fa-folder-open text-success me-1"></i>
              Already Uploaded Files
            </strong>
            <span class="badge bg-success" id="du_existing_count">0</span>
          </div>
          <table class="du-existing-files-table" id="du_existing_files_table">
            <thead>
              <tr>
                <th style="width:44px;"></th>
                <th>File Name</th>
                <th>Type</th>
                <th>Size</th>
                <th>Uploaded</th>
                <th>By</th>
                <th style="width:70px;">Action</th>
              </tr>
            </thead>
            <tbody id="du_existing_files_tbody"></tbody>
          </table>
          <hr class="mt-3"/>
        </div>

        <!-- ── NEW FILE UPLOAD ZONE ── -->
        <div class="upload-drop-zone" id="du_drop_zone"
             onclick="document.getElementById('du_file_input').click()">
          <span class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></span>
          <div><strong>Click to browse</strong> or drag &amp; drop files here</div>
          <div class="text-muted mt-1" style="font-size:0.75rem;">
            <strong>Multiple files allowed</strong> &nbsp;•&nbsp;
            PDF, PSD, JPEG, JPG, PNG, ZIP, RAR, CDR, AI, EPS &nbsp;•&nbsp; Max 20 MB each
          </div>
        </div>

        <!-- Hidden multiple file input -->
        <input type="file" id="du_file_input"
               accept=".pdf,.psd,.jpeg,.jpg,.png,.zip,.rar,.7z,.cdr,.cdt,.ai,.eps"
               multiple style="display:none;"
               onchange="du_onFilesSelected(this)" />

        <!-- New-files preview chips -->
        <div id="du_files_preview_list"></div>

        <!-- Upload progress -->
        <div id="du_progress_wrap">
          <div class="progress mt-2" style="height:16px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                 id="du_progress_bar" role="progressbar" style="width:0%">0%</div>
          </div>
          <div class="text-muted mt-1" style="font-size:0.75rem;" id="du_progress_label">Uploading…</div>
        </div>

        <!-- OR divider -->
        <div class="d-flex align-items-center my-3">
          <hr class="flex-grow-1"><span class="mx-2 text-muted fw-bold" style="font-size:0.8rem;">OR — No file?</span><hr class="flex-grow-1">
        </div>

        <!-- Reason section -->
        <div class="reason-section">
          <label for="du_no_file_reason" class="form-label fw-bold mb-1" style="font-size:0.82rem;">
            <i class="fas fa-comment-alt me-1 text-warning"></i>
            Reason for not uploading a design file
          </label>
          <textarea id="du_no_file_reason" class="form-control" rows="2"
                    placeholder="e.g. Customer will send file later / No design needed / Hard copy provided…"
                    style="font-size:0.82rem;"></textarea>
          <div class="form-text text-muted" style="font-size:0.72rem;">
            Fill this ONLY when no file is uploaded above.
          </div>
        </div>

      </div><!-- /modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="du_proceed_btn"
                onclick="du_submitAndProceed()">
          <span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details
        </button>
      </div>

    </div>
  </div>
</div>


<!-- ══════════════════════════════════════════════════════════════
     STEP 3 — Replace the entire DESIGN UPLOAD MODAL JavaScript
     block in your <script> section with this
     ══════════════════════════════════════════════════════════════ -->
<script>
/* ═══════════════════════════════════════════════════════════════
   DESIGN UPLOAD MODAL — v2 (multi-file, edit/delete, bug fixed)
   ═══════════════════════════════════════════════════════════════ */

var du_pendingFiles = []; // File objects staged by user (not yet uploaded)

/* ── Entry point ──────────────────────────────────────────────
   Called from the "In Progress" button in get_jobcard_list.php */
function fnDesignUploadPopUp(pJobCardNo, pCustomerName, pCustomerMobile) {
    du_pendingFiles = [];
    $('#du_jc_no').val(pJobCardNo);
    $('#du_customer_name').val(pCustomerName);
    $('#du_customer_mobile').val(pCustomerMobile);
    $('#du_jc_no_display').text(pJobCardNo);
    $('#du_customer_name_display').text(pCustomerName || '—');
    $('#du_customer_mobile_display').text(pCustomerMobile || '—');
    $('#du_no_file_reason').val('');
    $('#du_file_input').val('');
    $('#du_files_preview_list').html('');
    $('#du_progress_wrap').hide();
    du_resetProgressBar();
    du_setDropZoneStyle(false);
    $('#du_existing_files_section').hide();
    $('#du_existing_files_tbody').html('');

    // Check for existing uploads
    $.ajax({
        type: 'GET',
        url: 'api/get_jc_design_upload.php',
        data: { jobcard_no: pJobCardNo },
        async: false,
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.status === 'found') {
                    var files = [];
                    if (r.data.files_json) {
                        try { files = JSON.parse(r.data.files_json); } catch(e) {}
                    }
                    // Fallback: legacy single file
                    if (!files.length && r.data.file_name) {
                        files = [{
                            file_name: r.data.file_name,
                            file_path: r.data.file_path,
                            file_type: r.data.file_type,
                            file_size_kb: r.data.file_size_kb,
                            original_name: r.data.file_name,
                            uploaded_at: r.data.uploaded_at,
                            uploaded_by: r.data.uploaded_by
                        }];
                    }
                    // Also show reason if present
                    if (r.data.upload_reason) {
                        $('#du_no_file_reason').val(r.data.upload_reason);
                    }
                    if (files.length > 0) {
                        du_renderExistingFiles(files, pJobCardNo);
                    }
                }
            } catch(e) {}
        }
    });

    $('#designUploadModal').modal('show');
}

/* ── Render existing uploaded files table ─────────────────────*/
function du_renderExistingFiles(files, jcNo) {
    $('#du_existing_count').text(files.length);
    var rows = '';
    files.forEach(function(f) {
        var ext = (f.file_type || '').toLowerCase();
        var isImage = ['jpeg','jpg','png'].includes(ext);
        var thumb = isImage
            ? '<img src="' + f.file_path + '" class="du-file-thumb" onerror="this.style.display=\'none\'">'
            : '<div class="du-file-icon-box">' + du_fileIcon(ext) + '</div>';

        var dispName = f.original_name || f.file_name || '—';
        var sizeStr  = f.file_size_kb > 1024
                     ? (f.file_size_kb/1024).toFixed(2)+' MB'
                     : f.file_size_kb+' KB';
        var uploadedAt = (f.uploaded_at || '').substring(0,10);
        var uploadedBy = f.uploaded_by || '—';

        rows += '<tr id="du_row_' + du_safeId(f.file_name) + '">'
             + '<td>' + thumb + '</td>'
             + '<td title="' + du_esc(f.file_name) + '">' + du_esc(dispName) + '</td>'
             + '<td><span class="badge bg-secondary">' + ext.toUpperCase() + '</span></td>'
             + '<td>' + sizeStr + '</td>'
             + '<td>' + uploadedAt + '</td>'
             + '<td>' + du_esc(uploadedBy) + '</td>'
             + '<td>'
             + '<button class="btn btn-danger btn-sm py-0 px-1" style="font-size:0.72rem;"'
             + ' onclick="du_deleteFile(' + jcNo + ',\'' + du_esc(f.file_name) + '\')">'
             + '<span class="material-icons-round" style="font-size:14px;">delete</span></button>'
             + '</td>'
             + '</tr>';
    });
    $('#du_existing_files_tbody').html(rows);
    $('#du_existing_files_section').show();
}

/* ── Delete an existing file ──────────────────────────────────*/
function du_deleteFile(jcNo, fileName) {
    if (!confirm('Delete this file from JC #' + jcNo + '? This cannot be undone.')) return;
    $.ajax({
        type: 'POST',
        url:  'api/delete_jc_design_file.php',
        data: { jobcard_no: jcNo, file_name: fileName },
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.status === 'success') {
                    toastr.success('File deleted.');
                    if (r.files && r.files.length > 0) {
                        du_renderExistingFiles(r.files, jcNo);
                    } else {
                        $('#du_existing_files_section').hide();
                    }
                } else {
                    toastr.error(r.message || 'Delete failed.');
                }
            } catch(e) { toastr.error('Unexpected response.'); }
        },
        error: function() { toastr.error('Network error. Please try again.'); }
    });
}

/* ── File selection (multiple) ────────────────────────────────*/
function du_onFilesSelected(input) {
    if (!input.files || input.files.length === 0) return;
    var allowed = ['pdf','psd','jpeg','jpg','png','zip','rar','7z','cdr','cdt','ai','eps'];
    for (var i = 0; i < input.files.length; i++) {
        var file = input.files[i];
        var ext  = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
            toastr.error('"' + file.name + '" — invalid type. Allowed: PDF, PSD, JPEG, JPG, PNG, ZIP, RAR, CDR, AI, EPS.');
            continue;
        }
        if (file.size > 20 * 1024 * 1024) {
            toastr.error('"' + file.name + '" exceeds 20 MB limit.');
            continue;
        }
        // Avoid duplicates by name
        var already = du_pendingFiles.some(function(f){ return f.name === file.name; });
        if (!already) du_pendingFiles.push(file);
    }
    du_renderPendingChips();
    // Clear the input so same file can be re-added if needed
    input.value = '';
}

function du_renderPendingChips() {
    var html = '';
    du_pendingFiles.forEach(function(f, idx) {
        var sizeStr = f.size > 1024*1024
                    ? (f.size/1024/1024).toFixed(1)+' MB'
                    : (f.size/1024).toFixed(0)+' KB';
        html += '<div class="du-file-chip">'
             + du_fileIcon(f.name.split('.').pop().toLowerCase())
             + '<span class="chip-name" title="'+du_esc(f.name)+'">'+du_esc(f.name)+'</span>'
             + '<span style="color:#6c757d;font-size:0.7rem;">('+sizeStr+')</span>'
             + '<button class="chip-remove" onclick="du_removePending('+idx+')" title="Remove">&#x2715;</button>'
             + '</div>';
    });
    $('#du_files_preview_list').html(html);
}

function du_removePending(idx) {
    du_pendingFiles.splice(idx, 1);
    du_renderPendingChips();
}

/* ── Drag and drop ────────────────────────────────────────────*/
$(document).on('dragover', '#du_drop_zone', function(e) {
    e.preventDefault(); du_setDropZoneStyle(true);
});
$(document).on('dragleave', '#du_drop_zone', function(e) {
    du_setDropZoneStyle(false);
});
$(document).on('drop', '#du_drop_zone', function(e) {
    e.preventDefault(); du_setDropZoneStyle(false);
    var files = e.originalEvent.dataTransfer.files;
    if (!files || !files.length) return;
    var dt = new DataTransfer();
    for (var i = 0; i < files.length; i++) dt.items.add(files[i]);
    var inp = document.getElementById('du_file_input');
    inp.files = dt.files;
    du_onFilesSelected(inp);
});

function du_setDropZoneStyle(active) {
    active ? $('#du_drop_zone').addClass('dragover') : $('#du_drop_zone').removeClass('dragover');
}
function du_resetProgressBar() {
    $('#du_progress_bar').css('width','0%').text('0%').removeClass('bg-success').addClass('bg-warning');
    $('#du_progress_label').text('Uploading…');
}

/* ── Submit ───────────────────────────────────────────────────*/
function du_submitAndProceed() {
    var jcNo           = $('#du_jc_no').val();
    var customerName   = $('#du_customer_name').val();
    var customerMobile = $('#du_customer_mobile').val();
    var reason         = $('#du_no_file_reason').val().trim();
    var hasFiles       = du_pendingFiles.length > 0;

    // Must have either new files OR a reason
    if (!hasFiles && reason === '') {
        toastr.warning('Please add at least one design file, or provide a reason for not uploading.');
        $('#du_no_file_reason').focus();
        return;
    }

    var fd = new FormData();
    fd.append('jobcard_no',      jcNo);
    fd.append('customer_name',   customerName);
    fd.append('customer_mobile', customerMobile);
    fd.append('upload_reason',   reason);
    du_pendingFiles.forEach(function(f) {
        fd.append('design_files[]', f);   // note: design_files[] matches PHP $_FILES['design_files']
    });

    $('#du_proceed_btn').prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    $('#du_progress_wrap').show();
    du_resetProgressBar();

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'api/save_jc_design_upload.php', true);

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            var pct = Math.round((e.loaded / e.total) * 100);
            $('#du_progress_bar').css('width', pct+'%').text(pct+'%');
            $('#du_progress_label').text('Uploading… ' + pct + '%');
        }
    };

    xhr.onload = function() {
        $('#du_proceed_btn').prop('disabled', false)
            .html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details');
        try {
            var resp = JSON.parse(xhr.responseText);
            if (resp.status === 'success') {
                $('#du_progress_bar').css('width','100%').text('100%')
                    .removeClass('bg-warning').addClass('bg-success');
                $('#du_progress_label').text('Saved! Opening Job Card Details…');
                toastr.success(resp.message);
                setTimeout(function() {
                    $('#designUploadModal').modal('hide');
                    fnCompletePopUp(parseInt(jcNo));
                }, 600);
            } else {
                $('#du_progress_wrap').hide();
                toastr.error(resp.message || 'Save failed. Please try again.');
            }
        } catch(e) {
            $('#du_progress_wrap').hide();
            toastr.error('Unexpected server response. Please try again.');
        }
    };

    xhr.onerror = function() {
        $('#du_proceed_btn').prop('disabled', false)
            .html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details');
        $('#du_progress_wrap').hide();
        toastr.error('Network error during upload. Please try again.');
    };

    xhr.send(fd);
}

/* ── Utility ──────────────────────────────────────────────────*/
function du_fileIcon(ext) {
    var icons = {
        pdf:'📄', psd:'🎨', jpg:'🖼️', jpeg:'🖼️', png:'🖼️',
        zip:'🗜️', rar:'🗜️', '7z':'🗜️',
        cdr:'🎨', cdt:'🎨', ai:'🎨', eps:'🎨'
    };
    return icons[ext] || '📁';
}
function du_esc(str) {
    return String(str||'')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function du_safeId(str) {
    return String(str||'').replace(/[^a-zA-Z0-9]/g,'_');
}
</script>
