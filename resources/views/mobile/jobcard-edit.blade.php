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
    .photo-list { display: flex; flex-wrap:wrap; gap: 0.5rem; margin-bottom: 0.5rem; }
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
        <input type="hidden" id="deleted_employees" name="deleted_employees" value="">
        <input type="hidden" id="deleted_traveling" name="deleted_traveling" value="">
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
    <!-- Assigned Employees Card -->
    <div class="mobile-card">
        <div class="section-header header-gray">
            <i class="fas fa-users"></i> Assign Employees
        </div>
        <div class="inventory-add-row" style="gap: 0.5rem; margin-bottom: 1rem;">
            <select id="employee_select" class="form-control" style="margin-bottom: 0.3rem;">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
            <input type="number" id="employee_hours" class="form-control" min="0" step="0.1" value="" placeholder="Hours" style="margin-bottom: 0.3rem;">
            <select id="employee_hour_type" class="form-control" style="margin-bottom: 0.3rem;">
                <option value="normal">Normal</option>
                <option value="overtime">Overtime</option>
                <option value="weekend">Weekend</option>
                <option value="public_holiday">Public Holiday</option>
                <option value="call_out">Call Out</option>
            </select>
            <input type="number" id="employee_travel_km" class="form-control" min="0" step="0.1" value="" placeholder="Kilometers (if Traveling)" style="margin-bottom: 0.3rem; display:none;">
            <button type="button" id="add_employee" class="btn btn-light inventory-add-btn" style="background:#e0e7ef; color:#2563eb; font-weight:600; border:1px solid #cbd5e1;">Add Employee</button>
        </div>
        <div class="inventory-list" id="employee_list" style="margin-top:0.5rem;">
            @if(isset($jobcard->employees) && $jobcard->employees->count())
                @foreach($jobcard->employees as $employee)
                    @if(($employee->pivot->hour_type ?? 'normal') !== 'traveling')
                    <div class="inventory-list-item" data-id="{{ $employee->id }}" style="background:#f3f4f6; border-radius:10px; margin-bottom:0.4rem; display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0.8rem;">
                        <span style="display:flex; flex-direction:column; align-items:flex-start; gap:0.1rem; font-size:1rem; color:#111; font-weight:400;">
                            <span>{{ $employee->name }}</span>
                            <span>Hours: {{ $employee->pivot->hours_worked ?? 0 }}</span>
                            <span>Type: {{ $employee->pivot->hour_type ?? 'normal' }}</span>
                        </span>
                        <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                        <input type="hidden" name="employee_hours[{{ $employee->id }}]" value="{{ $employee->pivot->hours_worked ?? 0 }}">
                        <input type="hidden" name="employee_hour_types[{{ $employee->id }}]" value="{{ $employee->pivot->hour_type ?? 'normal' }}">
                        <button type="button" class="inventory-remove-btn" onclick="removeEmployee(this)" style="background:#e5e7eb; color:#dc2626; border-radius:50%; width:28px; height:28px; font-size:1.1em; display:flex; align-items:center; justify-content:center; margin-left:0.5rem;">&times;</button>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>
        <!-- Traveling Section -->
        <div class="mobile-card">
            <div class="section-header header-teal">
                <i class="fas fa-car"></i> Traveling (Kilometers)
            </div>
            <div class="inventory-add-row" style="gap: 0.5rem; margin-bottom: 1rem;">
                <select id="travel_employee_select" class="form-control" style="margin-bottom: 0.3rem;">
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
                <input type="number" id="travel_km" class="form-control" min="0" step="0.1" value="" placeholder="Kilometers">
                <button type="button" id="add_traveling" class="btn btn-light inventory-add-btn" style="background:#e0e7ef; color:#14b8a6; font-weight:600; border:1px solid #99f6e4;">Add Traveling</button>
            </div>
            <div class="inventory-list" id="traveling_list" style="margin-top:0.5rem;">
                @if(isset($jobcard->employees) && $jobcard->employees->count())
                    @foreach($jobcard->employees as $employee)
                        @if(($employee->pivot->hour_type ?? '') === 'traveling')
                        <div class="inventory-list-item" data-id="{{ $employee->id }}" style="background:#e0f2f1; border-radius:10px; margin-bottom:0.4rem; display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0.8rem;">
                            <span style="display:flex; flex-direction:column; align-items:flex-start; gap:0.1rem; font-size:1rem; color:#111; font-weight:400;">
                                <span>{{ $employee->name }}</span>
                                <span>Kilometers: {{ $employee->pivot->travel_km ?? 0 }}</span>
                            </span>
                            <input type="hidden" name="traveling_employees[]" value="{{ $employee->id }}">
                            <input type="hidden" name="traveling_km[{{ $employee->id }}]" value="{{ $employee->pivot->travel_km ?? 0 }}">
                            <button type="button" class="inventory-remove-btn" onclick="removeTraveling(this)" style="background:#e0e7eb; color:#14b8a6; border-radius:50%; width:28px; height:28px; font-size:1.1em; display:flex; align-items:center; justify-content:center; margin-left:0.5rem;">&times;</button>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
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
                <div class="inventory-list-item" data-id="{{ $item['id'] }}" style="background:#f3f4f6; border-radius:10px; margin-bottom:0.4rem; display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0.8rem;">
                    <span style="display:flex; flex-direction:column; align-items:flex-start; gap:0.1rem; font-size:1rem; color:#111; font-weight:400;">
                        <span>{{ $item['name'] ?? $item['description'] ?? 'No description' }}</span>
                        <span>Qty: {{ $item['quantity'] }}</span>
                    </span>
                    <input type="hidden" name="inventory_items[]" value="{{ $item['id'] }}">
                    <input type="hidden" name="inventory_qty[]" value="{{ $item['quantity'] }}">
                    <button type="button" class="inventory-remove-btn" onclick="removeInventory(this)" style="background:#e5e7eb; color:#dc2626; border-radius:50%; width:28px; height:28px; font-size:1.1em; display:flex; align-items:center; justify-content:center; margin-left:0.5rem;">&times;</button>
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
        <textarea class="form-control" name="work_done" placeholder="Describe the work completed...">{{ old('work_done', $jobcard->work_done) }}</textarea>
    </div>
    <!-- Photos Card -->
    <div class="mobile-card">
        <div class="section-header header-yellow">
            <i class="fas fa-camera"></i> Photos
        </div>
        <div class="photo-list" id="photo-list" style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.5rem;">
            @foreach($jobcard->mobilePhotos ?? [] as $photo)
                <div style="position:relative; display:inline-block;">
                    <img src="{{ Storage::url($photo->file_path) }}" class="photo-thumb" style="width:80px; height:80px; object-fit:cover; border-radius:8px; margin:0.2rem; border:1px solid #e5e7eb;">
                    <button class="photo-delete-btn" onclick="deletePhoto({{ $photo->id }})" type="button">&times;</button>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Job Completion Card -->
    <div class="mobile-card">
        <div class="section-header header-green">
            <i class="fas fa-check-circle"></i> Job Status
        </div>
        <label class="form-label">Change Job Status:</label>
        <select class="form-control" name="status">
            <option value="assigned" {{ $jobcard->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
            <option value="in progress" {{ $jobcard->status == 'in progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ $jobcard->status == 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
    </div>
        <button type="submit" class="save-btn">Save Progress</button>
        <div id="save-status" class="save-status" style="display:none;"></div>
    </form>
    <!-- Move photo upload form OUTSIDE the main form -->
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-top:1rem;">
            <ul style="margin-bottom:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="photo-upload-form" action="{{ route('mobile-jobcard-photos.store') }}" method="POST" enctype="multipart/form-data" style="margin-top:1rem;">
        @csrf
        <input type="hidden" name="jobcard_id" value="{{ $jobcard->id }}">
        <label class="photo-upload-label">
            <i class="fas fa-camera"></i> Select Photo from Gallery
            <input type="file" name="photo" accept="image/*">
        </label>
        <small style="color:#888; display:block; margin-bottom:0.5rem;">Max file size: 15MB. Large camera photos may fail to upload.</small>
        <input type="text" name="caption" class="form-control" placeholder="Add a caption (optional)">
        <button type="submit" class="btn btn-primary">Upload Photo</button>
        <div id="photo-uploading" class="photo-uploading" style="display:none;">Uploading...</div>
    </form>
</div>
<script>
console.log('Script loaded');
const jobcardForm = document.getElementById('jobcard-edit-form');
console.log('jobcardForm:', jobcardForm);
const saveStatus = document.getElementById('save-status');
if (jobcardForm) {
    console.log('Attaching submit handler');
    jobcardForm.addEventListener('submit', function(e) {
        console.log('Jobcard form submit handler triggered');
        e.preventDefault();
        saveStatus.textContent = 'Saving...';
        saveStatus.className = 'save-status loading';
        saveStatus.style.display = 'block';
        const data = new FormData(jobcardForm);
        fetch("{{ route('jobcard.update', $jobcard->id) }}", {
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
} else {
    console.log('jobcardForm not found');
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
            div.innerHTML = `<span style='display:flex; flex-direction:column; align-items:flex-start; gap:0.1rem; font-size:1rem; color:#111; font-weight:400;'><span>${name.split(' (Stock:')[0]}</span><span>Qty: ${qty}</span></span>
                <input type='hidden' name='inventory_items[]' value='${id}'>
                <input type='hidden' name='inventory_qty[]' value='${qty}'>
                <button type='button' class='inventory-remove-btn' onclick='removeInventory(this)' style='background:#e5e7eb; color:#dc2626; border-radius:50%; width:28px; height:28px; font-size:1.1em; display:flex; align-items:center; justify-content:center; margin-left:0.5rem;'>&times;</button>`;
            document.getElementById('inventory_list').appendChild(div);
            // Reset form
            select.value = '';
            document.getElementById('inventory_quantity').value = 1;
        });
    }

function removeInventory(btn) {
    btn.closest('.inventory-list-item').remove();
}

function removeEmployee(btn) {
    const item = btn.closest('.inventory-list-item');
    const id = item.getAttribute('data-id');
    // Add to deleted_employees hidden input
    let deleted = document.getElementById('deleted_employees').value;
    let arr = deleted ? deleted.split(',') : [];
    if (!arr.includes(id)) arr.push(id);
    document.getElementById('deleted_employees').value = arr.join(',');
    item.remove();
}
const hourTypeInput = document.getElementById('employee_hour_type');
const travelKmInput = document.getElementById('employee_travel_km');
if (hourTypeInput) {
    hourTypeInput.addEventListener('change', function() {
        if (hourTypeInput.value === 'traveling') {
            travelKmInput.style.display = '';
        } else {
            travelKmInput.style.display = 'none';
            travelKmInput.value = '';
        }
    });
}
const addEmployeeBtn = document.getElementById('add_employee');
if (addEmployeeBtn) {
    addEmployeeBtn.addEventListener('click', function() {
        let select = document.getElementById('employee_select');
        let hours = document.getElementById('employee_hours').value;
        let hourType = document.getElementById('employee_hour_type').value;
        let travelKm = document.getElementById('employee_travel_km').value;
        let id = select.value;
        let name = select.options[select.selectedIndex].text;
        if (!id || !hours || hours < 0) {
            alert('Please select an employee and enter valid hours');
            return;
        }
        if (hourType === 'traveling' && (!travelKm || travelKm < 0)) {
            alert('Please enter kilometers for traveling');
            return;
        }
        // Prevent duplicate
        if (document.querySelector('#employee_list .inventory-list-item[data-id="'+id+'"]')) {
            alert('This employee is already assigned');
            return;
        }
        let extraTravel = hourType === 'traveling' ? `<span>Kilometers: ${travelKm}</span>` : '';
        let travelInput = hourType === 'traveling' ? `<input type='hidden' name='employee_travel_km[${id}]' value='${travelKm}'>` : '';
        let div = document.createElement('div');
        div.className = 'inventory-list-item';
        div.setAttribute('data-id', id);
        div.innerHTML = `<span style='display:flex; flex-direction:column; align-items:flex-start; gap:0.1rem; font-size:1rem; color:#111; font-weight:400;'><span>${name}</span><span>Hours: ${hours}</span><span>Type: ${hourType}</span>${extraTravel}</span>
            <input type='hidden' name='employees[]' value='${id}'>
            <input type='hidden' name='employee_hours[${id}]' value='${hours}'>
            <input type='hidden' name='employee_hour_types[${id}]' value='${hourType}'>
            ${travelInput}
            <button type='button' class='inventory-remove-btn' onclick='removeEmployee(this)' style='background:#e5e7eb; color:#dc2626; border-radius:50%; width:28px; height:28px; font-size:1.1em; display:flex; align-items:center; justify-content:center; margin-left:0.5rem;'>&times;</button>`;
        document.getElementById('employee_list').appendChild(div);
        // Reset form
        select.value = '';
        document.getElementById('employee_hours').value = '';
        document.getElementById('employee_hour_type').value = 'normal';
        travelKmInput.value = '';
        travelKmInput.style.display = 'none';
    });
}

function removeTraveling(btn) {
    const item = btn.closest('.inventory-list-item');
    const id = item.getAttribute('data-id');
    // Add to deleted_traveling hidden input
    let deleted = document.getElementById('deleted_traveling').value;
    let arr = deleted ? deleted.split(',') : [];
    if (!arr.includes(id)) arr.push(id);
    document.getElementById('deleted_traveling').value = arr.join(',');
    item.remove();
}
const addTravelingBtn = document.getElementById('add_traveling');
if (addTravelingBtn) {
    addTravelingBtn.addEventListener('click', function() {
        let select = document.getElementById('travel_employee_select');
        let km = document.getElementById('travel_km').value;
        let id = select.value;
        let name = select.options[select.selectedIndex].text;
        if (!id || !km || km < 0) {
            alert('Please select an employee and enter valid kilometers');
            return;
        }
        // Prevent duplicate traveling for same employee
        if (document.querySelector('#traveling_list .inventory-list-item[data-id="'+id+'"]')) {
            alert('This employee already has a traveling entry');
            return;
        }
        let div = document.createElement('div');
        div.className = 'inventory-list-item';
        div.setAttribute('data-id', id);
        div.innerHTML = `<span style='display:flex; flex-direction:column; align-items:flex-start; gap:0.1rem; font-size:1rem; color:#111; font-weight:400;'><span>${name}</span><span>Kilometers: ${km}</span></span>
            <input type='hidden' name='traveling_employees[]' value='${id}'>
            <input type='hidden' name='traveling_km[${id}]' value='${km}'>
            <button type='button' class='inventory-remove-btn' onclick='removeTraveling(this)' style='background:#e0e7eb; color:#14b8a6; border-radius:50%; width:28px; height:28px; font-size:1.1em; display:flex; align-items:center; justify-content:center; margin-left:0.5rem;'>&times;</button>`;
        document.getElementById('traveling_list').appendChild(div);
        // Reset form
        select.value = '';
        document.getElementById('travel_km').value = '';
    });
}

function deletePhoto(photoId) {
    if (!confirm('Delete this photo?')) return;
    fetch('/mobile-jobcard-photos/' + photoId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove the photo thumbnail from the DOM
            document.querySelector('button[onclick="deletePhoto(' + photoId + ')"]').closest('div').remove();
        }
    });
}
</script>
@endsection
