@extends('layouts.mobile')

@section('content')
<style>
    body, .mobile-content {
        background: #f3f4f6 !important;
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 1.08rem;
    }
    .mobile-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 1.2rem 1rem 1rem 1rem;
        margin-bottom: 1.2rem;
    }
    .section-header {
        display: flex;
        align-items: center;
        font-size: 1.1rem;
        font-weight: 700;
        padding: 0.5rem 0.7rem;
        border-radius: 8px 8px 0 0;
        margin: -1.2rem -1rem 1rem -1rem;
        color: #fff;
    }
    .header-blue { background: #2563eb; }
    .header-green { background: #059669; }
    .header-black { background: #111827; }
    .header-cyan { background: #0891b2; }
    .header-yellow { background: #f59e42; }
    .header-gray { background: #64748b; }
    .header-orange { background: #ea580c; }
    .header-red { background: #dc2626; }
    .header-teal { background: #14b8a6; }
    .section-header i { margin-right: 0.7rem; font-size: 1.2em; }
    .badge-status { background: #f59e42; color: #fff; border-radius: 8px; padding: 0.2em 0.8em; font-size: 0.95em; font-weight: 600; margin-left: 0.5em; }
    .badge-qty { background: #facc15; color: #111; border-radius: 8px; padding: 0.2em 0.8em; font-size: 0.95em; font-weight: 600; margin-left: 0.5em; }
    .btn-primary, .btn-success {
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.05rem;
        padding: 0.7rem 0;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    .btn-link {
        color: #2563eb;
        text-align: center;
        display: block;
        margin-top: 0.5rem;
        font-size: 1rem;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.2rem;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 1rem;
        margin-bottom: 0.7rem;
        padding: 0.6rem 0.9rem;
    }
    textarea.form-control {
        min-height: 2.2rem;
    }
    .list-group-item {
        border: none;
        border-radius: 8px;
        background: #f9fafb;
        margin-bottom: 0.4rem;
        font-size: 0.98rem;
    }
    .icon-btn {
        background: #059669; color: #fff; border: none; border-radius: 8px; padding: 0.5em 0.8em; font-size: 1.1em; margin-left: 0.5em;
    }
    .save-btn {
        background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 1.1rem; padding: 0.8rem 0; width: 100%; margin-top: 1.2rem;
    }
    .photo-thumb { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin: 0.2rem; border: 1px solid #e5e7eb; }
    .photo-list { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem; }
    .photo-delete-btn { background: #dc2626; color: #fff; border: none; border-radius: 6px; padding: 0.2em 0.7em; font-size: 0.9em; margin-left: 0.2em; }
    .photo-upload-label { display: block; background: #f3f4f6; border: 1px dashed #2563eb; border-radius: 8px; padding: 0.7rem; text-align: center; color: #2563eb; font-weight: 600; cursor: pointer; margin-bottom: 0.7rem; }
    .photo-upload-label input { display: none; }
    .photo-uploading { color: #2563eb; font-size: 1em; margin-bottom: 0.5rem; }
    .inventory-add-row { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.7rem; }
    .inventory-select, .inventory-qty { width: 100%; }
    .inventory-add-btn { width: 100%; }
    .inventory-list { margin-top: 0.5rem; }
    .inventory-list-item { display: flex; align-items: center; justify-content: space-between; background: #f9fafb; border-radius: 8px; padding: 0.5rem 0.8rem; margin-bottom: 0.4rem; }
    .inventory-remove-btn { background: #dc2626; color: #fff; border: none; border-radius: 6px; padding: 0.2em 0.7em; font-size: 0.9em; }
    .save-status { text-align: center; font-size: 1.05rem; margin-top: 0.7rem; }
    .save-status.success { color: #059669; }
    .save-status.error { color: #dc2626; }
    .save-status.loading { color: #2563eb; }
</style>
<div style="padding: 0.5rem;">
    <form id="jobcard-edit-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
    <!-- Job Info Card -->
    <div class="mobile-card">
        <div class="section-header header-blue">
            <i class="fas fa-briefcase"></i> Jobcard #{{ $jobcard->jobcard_number }}
            <span class="badge-status">In Progress</span>
        </div>
        <div style="font-weight: 500; color: #2563eb; margin-bottom: 0.2rem;">{{ $jobcard->client->name ?? '' }}</div>
        <div class="row mb-2">
            <div class="col-6"><span style="font-weight: 500;">Date:</span> {{ $jobcard->job_date }}</div>
            <div class="col-6"><span style="font-weight: 500;">Category:</span> {{ $jobcard->category }}</div>
        </div>
        <div style="margin-bottom: 0.5rem;"><span style="font-weight: 500;">Work Request:</span> {{ $jobcard->work_request }}</div>
        <div style="margin-bottom: 0.5rem;"><span style="font-weight: 500;">Special Instructions:</span> <span style="color: #dc2626;">{{ $jobcard->special_request }}</span></div>
    </div>
    <!-- Client Details Card -->
    <div class="mobile-card">
        <div class="section-header header-cyan">
            <i class="fas fa-user"></i> Client Details
        </div>
        <div style="font-weight: 600;">{{ $jobcard->client->name ?? '' }}</div>
        <div style="color: #64748b;">{{ $jobcard->client->address ?? '' }}</div>
        <div style="color: #64748b;">{{ $jobcard->client->email ?? '' }}</div>
        <button class="icon-btn" type="button"><i class="fas fa-phone"></i></button>
    </div>
    <!-- Assigned Team Card -->
    <div class="mobile-card">
        <div class="section-header header-gray">
            <i class="fas fa-users"></i> Assigned Team
        </div>
        <div>Mike <span class="badge badge-status" style="background:#2563eb;">You</span></div>
    </div>
    <!-- Inventory Card -->
    <div class="mobile-card">
        <div class="section-header header-black">
            <i class="fas fa-box"></i> Materials Required
        </div>
        <div class="inventory-add-row">
            <select id="inventory_select" class="form-control inventory-select">
                <option value="">Select Inventory Item</option>
                @foreach($inventory as $item)
                    <option value="{{ $item->id }}">[{{ $item->short_code }}] {{ $item->name }} (Stock: {{ $item->stock_level }})</option>
                @endforeach
            </select>
            <input type="number" id="inventory_quantity" class="form-control inventory-qty" min="1" max="100" value="1" placeholder="Qty">
            <button type="button" id="add_inventory" class="btn btn-primary inventory-add-btn">Add</button>
        </div>
        <div class="inventory-list" id="inventory_list">
            @foreach($assignedInventory as $item)
                <div class="inventory-list-item" data-id="{{ $item['id'] }}">
                    <span><strong>{{ $item['name'] ?? $item['description'] ?? 'No description' }}</strong> <span class="badge-qty">Qty: {{ $item['quantity'] }}</span></span>
                    <input type="hidden" name="inventory_items[]" value="{{ $item['id'] }}">
                    <input type="hidden" name="inventory_qty[]" value="{{ $item['quantity'] }}">
                    <button type="button" class="inventory-remove-btn" onclick="removeInventory(this)">&times;</button>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Work Progress Card -->
    <div class="mobile-card">
        <div class="section-header header-green">
            <i class="fas fa-tasks"></i> Work Progress
        </div>
        <label class="form-label">Work Completed:</label>
        <textarea class="form-control" placeholder="Describe the work completed..."></textarea>
        <label class="form-label">Additional Notes:</label>
        <textarea class="form-control" placeholder="Any additional notes or observations..."></textarea>
        <label class="form-label">Time Spent (hours):</label>
        <input type="number" class="form-control" value="0.0">
    </div>
    <!-- Photos Card -->
    <div class="mobile-card">
        <div class="section-header header-yellow">
            <i class="fas fa-camera"></i> Photos
        </div>
        <form id="photo-upload-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jobcard_id" value="{{ $jobcard->id }}">
            <label class="photo-upload-label">
                <i class="fas fa-camera"></i> Take Photo / Gallery
                <input type="file" name="photo" accept="image/*" capture="environment">
            </label>
            <input type="text" name="caption" class="form-control" placeholder="Add a caption (optional)">
            <button type="submit" class="btn btn-primary">Upload Photo</button>
            <div id="photo-uploading" class="photo-uploading" style="display:none;">Uploading...</div>
        </form>
        <div class="photo-list" id="photo-list">
            @foreach($jobcard->mobilePhotos ?? [] as $photo)
                <div style="position:relative; display:inline-block;">
                    <img src="{{ Storage::url($photo->file_path) }}" class="photo-thumb">
                    <button class="photo-delete-btn" onclick="deletePhoto({{ $photo->id }})" type="button">&times;</button>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Job Completion Card -->
    <div class="mobile-card">
        <div class="section-header header-green">
            <i class="fas fa-check-circle"></i> Job Completion
        </div>
        <div>Mark this job as completed</div>
    </div>
        <button type="submit" class="save-btn">Save Progress</button>
        <div id="save-status" class="save-status" style="display:none;"></div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX save for jobcard
    const jobcardForm = document.getElementById('jobcard-edit-form');
    const saveStatus = document.getElementById('save-status');
    if (jobcardForm) {
        jobcardForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveStatus.textContent = 'Saving...';
            saveStatus.className = 'save-status loading';
            saveStatus.style.display = 'block';
            const data = new FormData(jobcardForm);
            fetch("{{ route('mobile.jobcards.edit', $jobcard->id) }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'PUT' },
                body: data
            })
            .then(res => {
                if (!res.ok) return res.text().then(text => { throw new Error(text); });
                return res.text();
            })
            .then(() => {
                saveStatus.textContent = 'Jobcard updated successfully!';
                saveStatus.className = 'save-status success';
                setTimeout(() => { saveStatus.style.display = 'none'; }, 2000);
            })
            .catch(err => {
                saveStatus.textContent = 'Error saving jobcard.';
                saveStatus.className = 'save-status error';
                console.error('Save error:', err);
            });
        });
    }

    // Add inventory item
    const addInventoryBtn = document.getElementById('add_inventory');
    if (addInventoryBtn) {
        addInventoryBtn.addEventListener('click', function() {
            let select = document.getElementById('inventory_select');
            let qty = document.getElementById('inventory_quantity').value;
            let id = select.value;
            let name = select.options[select.selectedIndex].text;
            if (!id || !qty || qty < 1) {
                alert('Please select an item and enter valid quantity');
                return;
            }
            // Prevent duplicate
            if (document.querySelector('#inventory_list .inventory-list-item[data-id="'+id+'"]')) {
                alert('This item is already added to the jobcard');
                return;
            }
            let div = document.createElement('div');
            div.className = 'inventory-list-item';
            div.setAttribute('data-id', id);
            div.innerHTML = `<span><strong>${name.split(' (Stock:')[0]}</strong> <span class='badge-qty'>Qty: ${qty}</span></span>
                <input type='hidden' name='inventory_items[]' value='${id}'>
                <input type='hidden' name='inventory_qty[]' value='${qty}'>
                <button type='button' class='inventory-remove-btn' onclick='removeInventory(this)'>&times;</button>`;
            document.getElementById('inventory_list').appendChild(div);
            // Reset form
            select.value = '';
            document.getElementById('inventory_quantity').value = 1;
        });
    }
});
function removeInventory(btn) {
    btn.closest('.inventory-list-item').remove();
}
</script>
@endsection
