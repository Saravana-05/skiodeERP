<?php
session_start();
include_once '../connect_db.php';
$result = "";

$customer_mobile_no_txt = isset($_POST['customer_mobile_no_txt']) ? trim($_POST['customer_mobile_no_txt']) : '';
$customer_name_txt      = isset($_POST['customer_name_txt'])      ? trim($_POST['customer_name_txt'])      : '';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$user_type = strtoupper(isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'OPERATOR');
$today     = date('Y-m-d');
$filter_user      = isset($_POST['filter_user'])      ? trim($_POST['filter_user'])      : '';
$filter_from_date = isset($_POST['filter_from_date']) ? trim($_POST['filter_from_date']) : '';
$filter_to_date   = isset($_POST['filter_to_date'])   ? trim($_POST['filter_to_date'])   : '';

// New check — any filter is valid:
if ($customer_mobile_no_txt !== '' || $customer_name_txt !== '' || $filter_user !== '' || $filter_from_date !== '' || $filter_to_date !== '') {

    $conditions = [];

    if ($customer_mobile_no_txt !== '')
        $conditions[] = "customer_mobile_no = '" . mysqli_real_escape_string($connection, $customer_mobile_no_txt) . "'";

    if ($customer_name_txt !== '')
        $conditions[] = "customer_name LIKE '%" . mysqli_real_escape_string($connection, $customer_name_txt) . "%'";

    // Build WHERE clause
    $where_parts = [];

    if (!empty($conditions))
        $where_parts[] = '(' . implode(' OR ', $conditions) . ')';

    // User filter — AND condition (not OR)
    if ($filter_user !== '')
        $where_parts[] = "created_by = '" . mysqli_real_escape_string($connection, $filter_user) . "'";

    // Date range filters — AND conditions
    if ($filter_from_date !== '')
        $where_parts[] = "DATE(created_dt_tm) >= '" . mysqli_real_escape_string($connection, $filter_from_date) . "'";

    if ($filter_to_date !== '')
        $where_parts[] = "DATE(created_dt_tm) <= '" . mysqli_real_escape_string($connection, $filter_to_date) . "'";

    $where = !empty($where_parts) ? implode(' AND ', $where_parts) : '1=1';

    $sql = "SELECT * FROM jobcard_master
            WHERE $where
            ORDER BY jobcard_id DESC";  

    if ($query = mysqli_query($connection, $sql)) {

        // ── Daily download usage ───────────────────────────────
        $esc_user   = mysqli_real_escape_string($connection, $user_name);
        $used_res   = mysqli_query($connection, "
            SELECT COUNT(*) AS cnt FROM jc_download_logs
            WHERE downloaded_by='$esc_user' AND DATE(downloaded_at)='$today'
        ");
        $used_today = ($used_res) ? (int)mysqli_fetch_assoc($used_res)['cnt'] : 0;
        $is_admin   = ($user_type === 'ADMIN' || $user_type === 'SUPERADMIN');
        $remaining  = $is_admin ? 9999 : max(0, 15 - $used_today);

        // ── Collect rows ───────────────────────────────────────
        $rows = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $rows[] = $row;
        }

        // ── Batch-fetch uploads ────────────────────────────────
        $uploads_map = [];
        if (!empty($rows)) {
            $jc_numbers = [];
            foreach ($rows as $r) {
                $jc_numbers[] = (int)$r['jobcard_no'];
            }
            $jc_in  = implode(',', $jc_numbers);
            $up_res = mysqli_query($connection, "
                SELECT id, jobcard_no, files_json,
                       file_name, file_path, file_type, file_size_kb
                FROM jc_design_uploads
                WHERE jobcard_no IN ($jc_in)
            ");
            if ($up_res) {
                while ($up = mysqli_fetch_assoc($up_res)) {
                    $jcn   = (int)$up['jobcard_no'];
                    $files = [];

                    if (!empty($up['files_json'])) {
                        $parsed = json_decode($up['files_json'], true);
                        if (is_array($parsed)) $files = $parsed;
                    }
                    if (empty($files) && !empty($up['file_name'])) {
                        $files[] = array(
                            'file_name'     => $up['file_name'],
                            'file_path'     => $up['file_path'],
                            'original_name' => $up['file_name'],
                            'file_type'     => $up['file_type'],
                        );
                    }
                    foreach ($files as $f) {
                        $ext = isset($f['file_type']) ? strtolower($f['file_type']) : strtolower(pathinfo($f['file_name'], PATHINFO_EXTENSION));
                        $uploads_map[$jcn][] = array(
                            'upload_id' => (int)$up['id'],
                            'file_name' => $f['file_name'],
                            'file_path' => isset($f['file_path']) ? $f['file_path'] : '',
                            'file_type' => $ext,
                        );
                    }
                }
            }
        }

        $image_exts = array('jpg','jpeg','png','gif','webp','bmp','svg');

        // ── Base URL for correct image paths ───────────────────
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $base_url  = $protocol . '://' . $_SERVER['HTTP_HOST'];

        // ── CSS ────────────────────────────────────────────────
        $result .= '<style>
#resultDetailTable { border-collapse: collapse; width: 100%; }
#resultDetailTable th,
#resultDetailTable td {
    padding: 5px 8px !important;
    vertical-align: middle !important;
    white-space: nowrap;
    font-size: 12px;
    line-height: 1.3;
}
#resultDetailTable thead th {
    background: #3a3a3a;
    color: #fff;
    font-weight: 600;
    font-size: 12px;
    text-align: center;
}
#resultDetailTable tbody tr:nth-child(even) { background: #f9f9f9; }
#resultDetailTable tbody tr:hover { background: #eef3ff; }
#resultDetailTable .badge-general {
    background: #d1e7dd; color: #0a3622;
    padding: 2px 8px; border-radius: 4px;
    font-size: 11px; font-weight: 600;
    display: inline-block; line-height: 1.6;
}
#resultDetailTable .badge-credit {
    background: #fff3cd; color: #856404;
    padding: 2px 8px; border-radius: 4px;
    font-size: 11px; font-weight: 600;
    display: inline-block; line-height: 1.6;
}
#resultDetailTable .btn-print {
    padding: 2px 10px !important;
    font-size: 11px !important;
    line-height: 1.5 !important;
    height: 24px;
    display: inline-flex;
    align-items: center;
    margin: 0 !important;
}
#resultDetailTable .btn-files {
    padding: 2px 8px !important;
    font-size: 11px !important;
    line-height: 1.5 !important;
    height: 24px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
#resultDetailTable .no-files {
    color: #bbb; font-size: 13px;
}
#resultDetailTable td { text-align: left; }
#resultDetailTable td:nth-child(1),
#resultDetailTable td:nth-child(6),
#resultDetailTable td:nth-child(7),
#resultDetailTable td:nth-child(10),
#resultDetailTable td:nth-child(11) { text-align: right; }
#resultDetailTable td:nth-child(3),
#resultDetailTable td:nth-child(8),
#resultDetailTable td:nth-child(9) { text-align: center; }
</style>';

        // ── Table ──────────────────────────────────────────────
        $result .= '<table class="table table-bordered" id="resultDetailTable">';
        $result .= '<thead><tr>
                        <th>JC No</th>
                        <th>Date &amp; Time</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>App.Amt</th>
                        <th>Adv.Amt</th>
                        <th>User</th>
                        <th>Files</th>
                        <th>Print</th>
                        <th>SQ.No</th>
                        <th>Discount</th>
                    </tr></thead><tbody>';

        foreach ($rows as $row) {
            $jcn       = (int)$row['jobcard_no'];
            $cust_type = isset($row['customer_type']) ? $row['customer_type'] : '';

            // ── Customer type badge ────────────────────────────
            if ($cust_type === 'Credit') {
                $type_badge = '<span class="badge-credit">Credit</span>';
            } else {
                $type_badge = '<span class="badge-general">General</span>';
            }

            // ── Sales quotation ────────────────────────────────
            $sq_no  = '';
            $sq_dis = '';
            $sq_sql = "SELECT * FROM sales_quotation_master
                       WHERE jobcard_nos LIKE '%" . mysqli_real_escape_string($connection, $row['jobcard_no']) . "%'";
            if ($sq_qry = mysqli_query($connection, $sq_sql)) {
                if ($sq_row = mysqli_fetch_array($sq_qry)) {
                    $sq_no  = $sq_row['quotation_no'];
                    $sq_dis = $sq_row['discount'];
                }
            }

            // ── Files button ───────────────────────────────────
            $files_cell = '<span class="no-files">&#8212;</span>';
            if (!empty($uploads_map[$jcn])) {
                $file_list     = $uploads_map[$jcn];
                $count         = count($file_list);
                $files_json_js = htmlspecialchars(json_encode($file_list), ENT_QUOTES);
                $rem_js        = (int)$remaining;
                $admin_js      = $is_admin ? 'true' : 'false';
                $label         = $count . ' file' . ($count > 1 ? 's' : '');

                $files_cell = '<button class="btn btn-sm btn-outline-primary btn-files"
                                   onclick="openJcFileViewer(' . $jcn . ', ' . $files_json_js . ', ' . $rem_js . ', ' . $admin_js . ')">
                                   &#128206; ' . $label . '
                               </button>';
            }

            $result .= '<tr>';
            $result .= '<td>' . $jcn . '</td>';
            $result .= '<td>' . date("d-m-Y H:i:s", strtotime($row['created_dt_tm'])) . '</td>';
            $result .= '<td>' . $type_badge . '</td>';
            $result .= '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
            $result .= '<td>' . htmlspecialchars($row['customer_mobile_no']) . '</td>';
            $result .= '<td>' . htmlspecialchars($row['approximate_amount']) . '</td>';
            $result .= '<td>' . htmlspecialchars($row['advance_amount']) . '</td>';
            $result .= '<td>' . htmlspecialchars($row['created_by'] ?? '') . '</td>';
            $result .= '<td>' . $files_cell . '</td>';
            $result .= '<td><a class="btn btn-sm btn-primary btn-print" target="_blank"
                            href="jc_print_out.php?jc=' . $jcn . '">Print</a></td>';
            $result .= '<td>' . htmlspecialchars($sq_no) . '</td>';
            $result .= '<td>' . htmlspecialchars($sq_dis) . '</td>';
            $result .= '</tr>';
        }

        $result .= '</tbody></table>';

        // ── Modal + Lightbox + JS ──────────────────────────────
        $image_exts_js = json_encode($image_exts);

        $result .= '
<!-- File list modal -->
<div id="jcFileModal" style="display:none;position:fixed;inset:0;z-index:9999;
     background:rgba(0,0,0,0.78);overflow:auto;">
  <div style="background:#fff;max-width:920px;margin:40px auto;border-radius:10px;
              overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.35);">
    <div style="display:flex;align-items:center;justify-content:space-between;
                padding:13px 20px;border-bottom:1px solid #e0e0e0;background:#f8f9fa;">
      <span id="jcModalTitle" style="font-weight:600;font-size:15px;"></span>
      <div style="display:flex;align-items:center;gap:14px;">
        <span id="jcDlBadge" style="font-size:12px;color:#555;"></span>
        <button onclick="closeJcModal()"
                style="border:none;background:none;font-size:26px;cursor:pointer;
                       line-height:1;color:#444;padding:0;">&times;</button>
      </div>
    </div>
    <div id="jcFileGrid"
         style="display:flex;flex-wrap:wrap;gap:14px;padding:20px;min-height:110px;"></div>
  </div>
</div>

<!-- Lightbox for full image preview -->
<div id="jcLightbox" style="display:none;position:fixed;inset:0;z-index:10999;
     background:rgba(0,0,0,0.92);align-items:center;justify-content:center;flex-direction:column;">
  <div style="position:relative;max-width:90vw;max-height:85vh;display:flex;
              align-items:center;justify-content:center;">
    <img id="jcLightboxImg" src="" alt="preview"
         style="max-width:90vw;max-height:85vh;object-fit:contain;border-radius:6px;
                box-shadow:0 4px 32px rgba(0,0,0,0.6);display:none;" />
    <div id="jcLightboxLoader"
         style="color:#fff;font-size:14px;padding:40px;">Loading...</div>
  </div>
  <div style="margin-top:14px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;
              justify-content:center;">
    <span id="jcLightboxName"
          style="color:#ddd;font-size:13px;max-width:60vw;
                 overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
    <a id="jcLightboxDl" href="#" target="_blank"
       style="background:#0d6efd;color:#fff;padding:6px 16px;border-radius:5px;
              text-decoration:none;font-size:12px;font-weight:600;">&#11015; Download</a>
    <button onclick="closeLightbox()"
            style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);
                   color:#fff;padding:6px 16px;border-radius:5px;cursor:pointer;
                   font-size:12px;">&#x2715; Close</button>
  </div>
</div>

<script>
(function () {
    var IMAGE_EXTS = ' . $image_exts_js . ';
    var BASE_URL   = ' . json_encode($base_url) . ';
    var _remaining = 0;
    var _isAdmin   = false;

    function buildImgUrl(filePath) {
        var clean = filePath.replace(/^\/+/, "");
        return BASE_URL + "/" + clean;
    }

    // ── Open file list modal ───────────────────────────────────
    window.openJcFileViewer = function (jcn, files, remaining, isAdmin) {
        _remaining = remaining;
        _isAdmin   = isAdmin;
        document.getElementById("jcModalTitle").textContent = "Files - Job Card #" + jcn;
        refreshBadge();

        var grid = document.getElementById("jcFileGrid");
        grid.innerHTML = "";

        for (var i = 0; i < files.length; i++) {
            (function(f) {
                var ext   = (f.file_type || "").toLowerCase().replace(".", "");
                var isImg = IMAGE_EXTS.indexOf(ext) >= 0;
                var dlUrl = "api/download_jc_file.php?id=" + f.upload_id
                          + "&file=" + encodeURIComponent(f.file_name);

                var card = document.createElement("div");
                card.style.cssText = "width:170px;border:1px solid #ddd;border-radius:8px;"
                                   + "overflow:hidden;display:flex;flex-direction:column;"
                                   + "background:#fafafa;";

                // Thumbnail area
                var thumb = document.createElement("div");
                thumb.style.cssText = "height:130px;display:flex;align-items:center;"
                                    + "justify-content:center;background:#f0f0f0;"
                                    + "overflow:hidden;position:relative;";

                if (isImg) {
                    var imgUrl = buildImgUrl(f.file_path);

                    var loader = document.createElement("div");
                    loader.textContent = "Loading...";
                    loader.style.cssText = "font-size:11px;color:#999;position:absolute;";
                    thumb.appendChild(loader);

                    var img = document.createElement("img");
                    img.style.cssText = "max-width:100%;max-height:130px;object-fit:contain;"
                                      + "position:relative;z-index:1;display:none;"
                                      + "cursor:pointer;";
                    img.alt   = f.file_name;
                    img.title = "Click to view full size";

                    img.onload = function () {
                        loader.style.display = "none";
                        this.style.display   = "block";
                    };
                    img.onerror = function () {
                        thumb.innerHTML = "<div style=\"text-align:center;padding:8px;\">"
                            + "<div style=\"font-size:30px;\">&#128444;</div>"
                            + "<div style=\"font-size:10px;color:#e74c3c;margin-top:4px;\">Cannot preview</div>"
                            + "<div style=\"font-size:9px;color:#aaa;margin-top:2px;"
                            + "word-break:break-all;padding:0 4px;\">" + f.file_path + "</div>"
                            + "</div>";
                    };

                    // Set src AFTER handlers
                    img.src = imgUrl;

                    // Click to open lightbox
                    (function(url, name, dl) {
                        img.onclick   = function () { openLightbox(url, name, dl); };
                        thumb.onclick = function (e) {
                            if (e.target !== img) openLightbox(url, name, dl);
                        };
                    })(imgUrl, f.file_name, dlUrl);

                    thumb.style.cursor = "pointer";
                    thumb.appendChild(img);

                } else {
                    var icons = {
                        pdf: "&#128196;", psd: "&#127912;",
                        ai:  "&#127912;", zip: "&#128376;",
                        eps: "&#127912;", cdr: "&#127912;"
                    };
                    thumb.innerHTML = "<span style=\"font-size:44px;\">"
                                    + (icons[ext] || "&#128196;") + "</span>";
                }
                card.appendChild(thumb);

                // File name label
                var fname     = f.file_name || f.original_name || "File";
                var shortName = fname.length > 26 ? fname.substring(0, 24) + "..." : fname;
                var lbl       = document.createElement("div");
                lbl.title     = fname;
                lbl.style.cssText = "padding:6px 8px 4px;font-size:11px;color:#555;"
                                  + "word-break:break-all;flex:1;";
                lbl.textContent = shortName;
                card.appendChild(lbl);

                // Download button
                addDlBtn(card, dlUrl);

                grid.appendChild(card);

            })(files[i]);
        }

        document.getElementById("jcFileModal").style.display = "block";
    };

    // ── Lightbox ───────────────────────────────────────────────
    window.openLightbox = function (imgUrl, fname, dlUrl) {
        var lb     = document.getElementById("jcLightbox");
        var lbImg  = document.getElementById("jcLightboxImg");
        var lbName = document.getElementById("jcLightboxName");
        var lbDl   = document.getElementById("jcLightboxDl");
        var lbLoad = document.getElementById("jcLightboxLoader");

        lbImg.style.display  = "none";
        lbLoad.style.display = "block";
        lbLoad.textContent   = "Loading...";
        lbLoad.style.color   = "#fff";
        lbImg.src            = "";
        lbName.textContent   = fname;
        lbDl.href            = dlUrl;

        lb.style.display = "flex";

        lbImg.onload = function () {
            lbLoad.style.display = "none";
            lbImg.style.display  = "block";
        };
        lbImg.onerror = function () {
            lbLoad.textContent = "Could not load image. Path: " + imgUrl;
            lbLoad.style.color = "#e74c3c";
        };
        lbImg.src = imgUrl;
    };

    window.closeLightbox = function () {
        document.getElementById("jcLightbox").style.display = "none";
        document.getElementById("jcLightboxImg").src = "";
    };

    document.getElementById("jcLightbox").addEventListener("click", function (e) {
        if (e.target === this) closeLightbox();
    });

    // ── Download button ────────────────────────────────────────
    function addDlBtn(card, dlUrl) {
        var canDl = _isAdmin || _remaining > 0;
        if (canDl) {
            var a = document.createElement("a");
            a.href          = dlUrl;
            a.target        = "_blank";
            a.textContent   = "Download";
            a.style.cssText = "display:block;text-align:center;padding:7px;"
                            + "background:#0d6efd;color:#fff;text-decoration:none;"
                            + "font-size:12px;font-weight:500;";
            a.addEventListener("click", function (e) {
                e.stopPropagation();
                if (!_isAdmin && _remaining > 0) {
                    _remaining--;
                    refreshBadge();
                    if (_remaining === 0) disableAllDlBtns();
                }
            });
            card.appendChild(a);
        } else {
            var div = document.createElement("div");
            div.textContent   = "Limit reached";
            div.style.cssText = "text-align:center;padding:7px;background:#e9ecef;"
                              + "color:#6c757d;font-size:12px;";
            card.appendChild(div);
        }
    }

    function refreshBadge() {
        var txt = _isAdmin
            ? "Unlimited downloads"
            : (_remaining + " download" + (_remaining === 1 ? "" : "s") + " left today");
        document.getElementById("jcDlBadge").textContent = txt;
    }

    function disableAllDlBtns() {
        var links = document.querySelectorAll("#jcFileGrid a");
        for (var i = 0; i < links.length; i++) {
            var div = document.createElement("div");
            div.textContent   = "Limit reached";
            div.style.cssText = "text-align:center;padding:7px;background:#e9ecef;"
                              + "color:#6c757d;font-size:12px;";
            links[i].parentNode.replaceChild(div, links[i]);
        }
    }

    window.closeJcModal = function () {
        document.getElementById("jcFileModal").style.display = "none";
    };

    document.getElementById("jcFileModal").addEventListener("click", function (e) {
        if (e.target === this) closeJcModal();
    });

}());
</script>';

    } // end if($query)
} // end if(search params)

echo $result;
?>