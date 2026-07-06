<?php session_start(); include_once "connect_db.php"; include_once "page_guard.php";
?>
<style>
#egc_table th, #egc_table td { font-size: 13px; vertical-align: middle; }
#egc_searchBox { width: 280px; }
</style>

<div class="container-fluid mt-3">
  <h5 class="mb-3" style="color:#3c2201;font-weight:bold;">
    <i class="fas fa-users me-2"></i>General Customer Master
  </h5>

  <!-- Search -->
  <div class="row mb-3">
    <div class="col-auto">
      <input type="text" id="egc_searchBox" class="form-control" placeholder="Search by Name or Phone..." oninput="egc_search()" />
    </div>
    <div class="col-auto">
      <button class="btn btn-primary" onclick="egc_load()">Show All</button>
    </div>
  </div>

  <!-- Table -->
  <div id="egc_tableDiv">
    <table class="table table-bordered table-hover" id="egc_table">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Address</th>
          <th>City</th>
          <th>GST No</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="egc_tbody">
        <tr><td colspan="7" class="text-center text-muted">Loading customers...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="egcEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="background:#3c2201;">
        <h5 class="modal-title">Edit General Customer</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="egc_mobile_key" />
        <div class="row mb-2">
          <label class="col-md-3 col-form-label fw-bold">Name</label>
          <div class="col-md-9">
            <input type="text" id="egc_name" class="form-control" maxlength="75" />
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-3 col-form-label fw-bold">Phone</label>
          <div class="col-md-9">
            <input type="text" id="egc_phone" class="form-control" maxlength="15" />
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-3 col-form-label fw-bold">Address Line 1</label>
          <div class="col-md-9">
            <input type="text" id="egc_addr1" class="form-control" maxlength="100" />
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-3 col-form-label fw-bold">Address Line 2</label>
          <div class="col-md-9">
            <input type="text" id="egc_addr2" class="form-control" maxlength="100" />
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-3 col-form-label fw-bold">City</label>
          <div class="col-md-9">
            <input type="text" id="egc_city" class="form-control" maxlength="50" />
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-3 col-form-label fw-bold">GST No</label>
          <div class="col-md-9">
            <input type="text" id="egc_gst" class="form-control" maxlength="15" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="egc_save()" id="egc_saveBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="egcDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white" style="background:#b71c1c;">
        <h5 class="modal-title"><span class="material-icons-round me-2" style="font-size:20px;vertical-align:-4px;">delete</span>Delete General Customer</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Are you sure you want to delete this customer?</p>
        <p class="fw-bold mb-3" id="egc_deleteNameLabel" style="color:#b71c1c;"></p>
        <div class="alert alert-warning py-2 mb-0" style="font-size:13px;">
          <span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">info</span>
          This will <strong>erase the customer's name, phone, and address</strong> from all their job cards.
          The job card records themselves will remain safe.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="egc_doDelete()" id="egc_deleteBtn">
          <span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">delete</span>Yes, Delete
        </button>
      </div>
    </div>
  </div>
</div>

<script>
var egc_allRows = [];
var egc_deleteMobileKey = "";

$(document).ready(function() { egc_load(); });

function egc_load() {
  $.ajax({
    type: "POST",
    url: "api/get_general_customers.php",
    data: { search: "" },
    success: function(res) {
      egc_allRows = res;
      egc_render(res);
    },
    error: function() { toastr.error("Failed to load customers."); }
  });
}

function egc_search() {
  var q = $("#egc_searchBox").val().toLowerCase().trim();
  if (q.length === 0) { egc_render(egc_allRows); return; }
  var filtered = egc_allRows.filter(function(r) {
    return (r.customer_name || "").toLowerCase().includes(q)
        || (r.customer_mobile_no || "").toLowerCase().includes(q);
  });
  egc_render(filtered);
}

function egc_render(rows) {
  var html = "";
  if (!rows || rows.length === 0) {
    html = "<tr><td colspan='7' class='text-center text-muted'>No customers found</td></tr>";
  } else {
    $.each(rows, function(i, r) {
      html += "<tr>";
      html += "<td>" + (i + 1) + "</td>";
      html += "<td>" + (r.customer_name || "-") + "</td>";
      html += "<td>" + (r.customer_mobile_no || "-") + "</td>";
      html += "<td>" + (r.customer_addr1 || "") + (r.customer_addr2 ? ", " + r.customer_addr2 : "") + "</td>";
      html += "<td>" + (r.customer_city || "-") + "</td>";
      html += "<td>" + (r.customer_gst_no || "-") + "</td>";
      html += "<td class='d-flex gap-1'>"
            + "<button class='btn btn-sm btn-warning' onclick='egc_openEdit("
            + JSON.stringify(r).replace(/'/g, "\\'") + ")'>Edit</button>"
            + "<button class='btn btn-sm btn-danger' onclick='egc_confirmDelete("
            + JSON.stringify(r.customer_mobile_no).replace(/'/g, "\\'") + ","
            + JSON.stringify(r.customer_name).replace(/'/g, "\\'") + ")'>Delete</button>"
            + "</td>";
      html += "</tr>";
    });
  }
  $("#egc_tbody").html(html);
}

function egc_openEdit(r) {
  $("#egc_mobile_key").val(r.customer_mobile_no);
  $("#egc_name").val(r.customer_name);
  $("#egc_phone").val(r.customer_mobile_no);
  $("#egc_addr1").val(r.customer_addr1);
  $("#egc_addr2").val(r.customer_addr2);
  $("#egc_city").val(r.customer_city);
  $("#egc_gst").val(r.customer_gst_no);
  $("#egcEditModal").modal("show");
}

function egc_save() {
  var mobileKey = $("#egc_mobile_key").val().trim();
  var name      = $("#egc_name").val().trim();
  var phone     = $("#egc_phone").val().trim();

  if (name === "") { toastr.warning("Name is required."); return; }
  if (phone === "") { toastr.warning("Phone is required."); return; }

  $("#egc_saveBtn").prop("disabled", true).text("Saving...");

  $.ajax({
    type: "POST",
    url: "api/update_general_customer.php",
    data: {
      mobile_key      : mobileKey,
      customer_name   : name,
      customer_mobile : phone,
      customer_addr1  : $("#egc_addr1").val(),
      customer_addr2  : $("#egc_addr2").val(),
      customer_city   : $("#egc_city").val(),
      customer_gst_no : $("#egc_gst").val()
    },
    success: function(res) {
      $("#egc_saveBtn").prop("disabled", false).text("Save Changes");
      if (res.status === "success") {
        toastr.success("Customer updated successfully! (" + res.updated + " job card(s) updated)");
        $("#egcEditModal").modal("hide");
        egc_load();
      } else {
        toastr.error(res.message || "Update failed.");
      }
    },
    error: function() {
      $("#egc_saveBtn").prop("disabled", false).text("Save Changes");
      toastr.error("Server error. Please try again.");
    }
  });
}

function egc_confirmDelete(mobileNo, customerName) {
  egc_deleteMobileKey = mobileNo;
  $("#egc_deleteNameLabel").text(customerName + " (" + mobileNo + ")");
  $("#egc_deleteBtn").prop("disabled", false).html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">delete</span>Yes, Delete');
  $("#egcDeleteModal").modal("show");
}

function egc_doDelete() {
  if (!egc_deleteMobileKey) return;
  $("#egc_deleteBtn").prop("disabled", true).html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;animation:spin 1s linear infinite;">sync</span>Deleting...');

  $.ajax({
    type: "POST",
    url: "api/delete_general_customer.php",
    data: { mobile_key: egc_deleteMobileKey },
    dataType: "json",
    success: function(res) {
      $("#egc_deleteBtn").prop("disabled", false).html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">delete</span>Yes, Delete');
      if (res.status === "success") {
        toastr.success("Customer deleted. (" + res.affected + " job card(s) cleared)");
        $("#egcDeleteModal").modal("hide");
        egc_deleteMobileKey = "";
        egc_load();
      } else {
        toastr.error(res.message || "Delete failed.");
      }
    },
    error: function() {
      $("#egc_deleteBtn").prop("disabled", false).html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">delete</span>Yes, Delete');
      toastr.error("Server error. Please try again.");
    }
  });
}
</script>
