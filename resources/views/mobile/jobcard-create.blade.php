
@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.mobile')

@section('content')
<form method="POST" action="{{ route('mobile-jobcard.store') }}" enctype="multipart/form-data" style="max-width: 600px; margin: 0 auto;" id="jobcardCreateForm" onsubmit="return validateJobcardForm();">
    @csrf
    <!-- Job Info Card -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.2rem 1rem 1rem 1rem; margin-bottom: 1.2rem;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #2563eb; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-briefcase"></i> Jobcard #<span class="jobcard-number">Select Category First</span>
    <input type="hidden" name="jobcard_number" id="jobcard_number_input" value="{{ old('jobcard_number', '') }}">
        </div>
        <div style="font-weight: 500; color: #059669; margin-bottom: 0.7rem;">New Jobcard</div>
        <div style="display: flex; gap: 0.7rem; margin-bottom: 0.7rem;">
            <div style="flex:1;">
                <label style="font-weight: 500; color: #64748b;">Date</label>
                <input type="date" name="job_date" class="form-control" value="{{ old('job_date', date('Y-m-d')) }}" style="width: 100%;" id="job_date">
                <div class="invalid-feedback" id="job_date_error" style="color:red;display:none;"></div>
            </div>
            <div style="flex:1;">
                <label style="font-weight: 500; color: #64748b;">Category</label>
                <select name="category" class="form-control" onchange="generateJobcardNumber()" style="width: 100%;" id="category">
                <div class="invalid-feedback" id="category_error" style="color:red;display:none;"></div>
                    <option value="">Select Category</option>
                    <option value="General Maintenance" {{ old('category') == 'General Maintenance' ? 'selected' : '' }}>General Maintenance</option>
                    <option value="Emergency Repair" {{ old('category') == 'Emergency Repair' ? 'selected' : '' }}>Emergency Repair</option>
                    <option value="Installation" {{ old('category') == 'Installation' ? 'selected' : '' }}>Installation</option>
                    <option value="Call Out" {{ old('category') == 'Call Out' ? 'selected' : '' }}>Call Out</option>
                    <option value="Preventive Maintenance" {{ old('category') == 'Preventive Maintenance' ? 'selected' : '' }}>Preventive Maintenance</option>
                    <option value="Inspection" {{ old('category') == 'Inspection' ? 'selected' : '' }}>Inspection</option>
                    <option value="Quote" {{ old('category') == 'Quote' ? 'selected' : '' }}>Quote</option>
                </select>
            </div>
        </div>
        <div style="margin-bottom: 0.7rem;">
            <label style="font-weight: 500; color: #64748b;">Work Request</label>
            <input type="text" name="work_request" class="form-control" value="{{ old('work_request') }}" style="width: 100%;" id="work_request">
            <div class="invalid-feedback" id="work_request_error" style="color:red;display:none;"></div>
        </div>
        <div style="margin-bottom: 0.7rem;">
            <label style="font-weight: 500; color: #64748b;">Special Instructions</label>
            <input type="text" name="special_request" class="form-control" value="{{ old('special_request') }}" style="width: 100%;" id="special_request">
            <div class="invalid-feedback" id="special_request_error" style="color:red;display:none;"></div>
        </div>
    </div>
    <!-- Client Details Card -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.2rem 1rem 1rem 1rem; margin-bottom: 1.2rem;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #0e7490; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-user"></i> Client Details
        </div>
        <div style="margin-bottom: 0.7rem;">
            <label style="font-weight: 500; color: #64748b;">Client</label>
            <select id="client_select" name="client_id" class="form-control" onchange="toggleTempClientFields(this)" style="width: 100%;">
            <div class="invalid-feedback" id="client_id_error" style="color:red;display:none;"></div>
                <option value="">Select Client</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
                <option value="temp">Add Temporary Client</option>
            </select>
        </div>
        <div id="temp_client_fields" style="display:none; margin-top:0.5rem;">
            <input type="text" name="temp_client_name" class="form-control" placeholder="First Name" value="{{ old('temp_client_name') }}" style="margin-bottom: 0.5rem;" id="temp_client_name">
            <div class="invalid-feedback" id="temp_client_name_error" style="color:red;display:none;"></div>
            <input type="text" name="temp_client_surname" class="form-control" placeholder="Surname" value="{{ old('temp_client_surname') }}" style="margin-bottom: 0.5rem;" id="temp_client_surname">
            <div class="invalid-feedback" id="temp_client_surname_error" style="color:red;display:none;"></div>
            <input type="text" name="temp_client_telephone" class="form-control" placeholder="Telephone" value="{{ old('temp_client_telephone') }}" style="margin-bottom: 0.5rem;" id="temp_client_telephone">
            <div class="invalid-feedback" id="temp_client_telephone_error" style="color:red;display:none;"></div>
            <input type="text" name="temp_client_address" class="form-control" placeholder="Address" value="{{ old('temp_client_address') }}" style="margin-bottom: 0.5rem;" id="temp_client_address">
            <div class="invalid-feedback" id="temp_client_address_error" style="color:red;display:none;"></div>
            <input type="email" name="temp_client_email" class="form-control" placeholder="Email" value="{{ old('temp_client_email') }}" id="temp_client_email">
            <div class="invalid-feedback" id="temp_client_email_error" style="color:red;display:none;"></div>
        </div>
    </div>
    <!-- Assigned Employees Card -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.2rem 1rem 1rem 1rem; margin-bottom: 1.2rem;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #64748b; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-users"></i> Assign Employees
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
            <select id="employee_select" class="form-control" style="width: 100%;">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
            <input type="number" id="employee_hours" class="form-control" min="0" step="0.1" value="" placeholder="Hours">
            <select id="employee_hour_type" class="form-control">
                <option value="normal">Normal</option>
                <option value="overtime">Overtime</option>
                <option value="weekend">Weekend</option>
                <option value="public_holiday">Public Holiday</option>
                <option value="call_out">Call Out</option>
            </select>
            <input type="number" id="employee_travel_km" class="form-control" min="0" step="0.1" value="" placeholder="Kilometers (if Traveling)" style="display:none;">
            <button type="button" id="add_employee" class="btn btn-light inventory-add-btn" style="background:#e0e7ef; color:#2563eb; font-weight:600; border:1px solid #cbd5e1;">Add Employee</button>
        </div>
        <div class="inventory-list" id="employee_list" style="margin-top:0.5rem;">
            <div class="text-muted">No employees assigned yet</div>
        </div>
        <!-- Traveling Section -->
        <div style="background: #f0fdfa; border-radius: 8px; padding: 1rem; margin-top: 1.2rem;">
            <div style="font-size: 1rem; font-weight: bold; color: #14b8a6; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-car"></i> Traveling (Kilometers)
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
                <select id="travel_employee_select" class="form-control" style="width: 100%;">
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
                <input type="number" id="travel_km" class="form-control" min="0" step="0.1" value="" placeholder="Kilometers">
                <button type="button" id="add_traveling" class="btn btn-light inventory-add-btn" style="background:#e0e7ef; color:#14b8a6; font-weight:600; border:1px solid #99f6e4;">Add Traveling</button>
            </div>
            <div class="inventory-list" id="traveling_list" style="margin-top:0.5rem;">
                <div class="text-muted">No traveling entries yet</div>
            </div>
        </div>
    </div>
    <!-- Inventory Card -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.2rem 1rem 1rem 1rem; margin-bottom: 1.2rem;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #111827; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-box"></i> Materials Required
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
            <select id="inventory_select" class="form-control inventory-select" style="width: 100%;">
                <option value="">Select Inventory Item</option>
                @foreach($inventory as $item)
                    <option value="{{ $item->id }}">[{{ $item->short_code }}] {{ $item->name }} - {{ $item->description }} (Stock: {{ $item->stock_level }})</option>
                @endforeach
            </select>
            <input type="number" id="inventory_quantity" class="form-control inventory-qty" min="1" max="100" value="1" placeholder="Qty">
            <button type="button" id="add_inventory" class="btn btn-primary inventory-add-btn">Add</button>
        </div>
        <div class="inventory-list" id="inventory_list">
            <div class="text-muted">No inventory items added yet</div>
        </div>
        <script>
        // Patch renderInventory function to show description
        let inventoryItems = [];
        function renderInventory() {
            const list = document.getElementById('inventory_list');
            list.innerHTML = '';
            if (inventoryItems.length === 0) {
                list.innerHTML = '<div class="text-muted">No inventory items added yet</div>';
                return;
            }
            inventoryItems.forEach((item, i) => {
                list.innerHTML += `<div class='inventory-list-item'>
                    <span style='display:flex; flex-direction:column; align-items:flex-start; gap:0.1rem; font-size:1rem; color:#111; font-weight:400;'>
                        <span>${item.name} <span style='color:#64748b; font-size:0.95em;'>${item.description || ''}</span></span>
                        <span>Qty: ${item.quantity}</span>
                    </span>
                    <button type='button' class='inventory-remove-btn' onclick='removeInventory(${i})' style='background:#e5e7eb; color:#dc2626; border-radius:50%; width:28px; height:28px; font-size:1.1em; display:flex; align-items:center; justify-content:center; margin-left:0.5rem;'>&times;</button>
                </div>`;
            });
        }
        </script>
    </div>
    <!-- Work Progress Card -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.2rem 1rem 1rem 1rem; margin-bottom: 1.2rem;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #059669; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-tasks"></i> Work Progress
        </div>
        <label class="form-label" style="font-weight: 500; color: #64748b;">Work Completed:</label>
        <textarea class="form-control" name="work_done" placeholder="Describe the work completed..." style="width: 100%;"></textarea>
    </div>
    <!-- Photos Card -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.2rem 1rem 1rem 1rem; margin-bottom: 1.2rem;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #f59e42; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-camera"></i> Photos
        </div>
        <div class="photo-list" id="photo-list" style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.5rem;"></div>
    </div>
    <!-- Job Completion Card -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.2rem 1rem 1rem 1rem; margin-bottom: 1.2rem;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #059669; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-check-circle"></i> Job Status
        </div>
        <label class="form-label" style="font-weight: 500; color: #64748b;">Change Job Status:</label>
        <select class="form-control" name="status" style="width: 100%;">
            <option value="assigned">Assigned</option>
            <option value="in progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>
    
    <button type="submit" style="width: 100%; background: #059669; color: #fff; border: none; border-radius: 6px; padding: 0.9rem 0; font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem;">Create Jobcard</button>
</form>

<script>
function showError(id, message) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = message;
        el.style.display = 'block';
    }
}
function hideError(id) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = '';
        el.style.display = 'none';
    }
}
function validateEmail(email) {
    return /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test(email);
}
function validateTelephone(tel) {
    return /^\+?[0-9\-\s]{7,15}$/.test(tel);
}
function validateJobcardForm() {
    let valid = true;
    // Job Info
    const jobDate = document.getElementById('job_date').value;
    if (!jobDate) { showError('job_date_error', 'Date is required.'); valid = false; } else { hideError('job_date_error'); }
    const category = document.getElementById('category').value;
    if (!category) { showError('category_error', 'Category is required.'); valid = false; } else { hideError('category_error'); }
    const workRequest = document.getElementById('work_request').value;
    if (!workRequest) { showError('work_request_error', 'Work request is required.'); valid = false; } else { hideError('work_request_error'); }
    const specialRequest = document.getElementById('special_request').value;
    if (!specialRequest) { showError('special_request_error', 'Special instructions are required.'); valid = false; } else { hideError('special_request_error'); }
    // Client
    const clientId = document.getElementById('client_select').value;
    if (!clientId) { showError('client_id_error', 'Client is required.'); valid = false; } else { hideError('client_id_error'); }
    if (clientId === 'temp') {
        const tempName = document.getElementById('temp_client_name').value;
        if (!tempName) { showError('temp_client_name_error', 'First name is required.'); valid = false; } else { hideError('temp_client_name_error'); }
        const tempSurname = document.getElementById('temp_client_surname').value;
        if (!tempSurname) { showError('temp_client_surname_error', 'Surname is required.'); valid = false; } else { hideError('temp_client_surname_error'); }
        const tempTel = document.getElementById('temp_client_telephone').value;
        if (!tempTel) { showError('temp_client_telephone_error', 'Telephone is required.'); valid = false; }
        else if (!validateTelephone(tempTel)) { showError('temp_client_telephone_error', 'Invalid telephone number.'); valid = false; } else { hideError('temp_client_telephone_error'); }
        const tempAddress = document.getElementById('temp_client_address').value;
        if (!tempAddress) { showError('temp_client_address_error', 'Address is required.'); valid = false; } else { hideError('temp_client_address_error'); }
        const tempEmail = document.getElementById('temp_client_email').value;
        if (!tempEmail) { showError('temp_client_email_error', 'Email is required.'); valid = false; }
        else if (!validateEmail(tempEmail)) { showError('temp_client_email_error', 'Invalid email address.'); valid = false; } else { hideError('temp_client_email_error'); }
    }
    return valid;
}
function generateJobcardNumber() {
    const categorySelect = document.querySelector('select[name="category"]');
    const category = categorySelect.value;
    
    if (!category) {
        document.querySelector('.jobcard-number').textContent = 'Select Category First';
        document.getElementById('jobcard_number_input').value = '';
        return;
    }
    
    // Generate timestamp: year/month/day/hour/minute
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hour = String(now.getHours()).padStart(2, '0');
    const minute = String(now.getMinutes()).padStart(2, '0');
    
    const timestamp = `${year}${month}${day}${hour}${minute}`;
    
    // Get 2-letter abbreviation based on category
    let abbreviation = '';
    switch(category) {
        case 'General Maintenance':
            abbreviation = 'gm';
            break;
        case 'Emergency Repair':
            abbreviation = 'er';
            break;
        case 'Installation':
            abbreviation = 'in';
            break;
        case 'Call Out':
            abbreviation = 'ca';
            break;
        case 'Preventive Maintenance':
            abbreviation = 'pm';
            break;
        case 'Inspection':
            abbreviation = 'is';
            break;
        case 'Quote':
            abbreviation = 'qt';
            break;
        default:
            abbreviation = 'xx';
    }
    
    // Create jobcard number: abbreviation-timestamp
    const jobcardNumber = `${abbreviation}-${timestamp}`;
    
    // Update display and hidden input
    document.querySelector('.jobcard-number').textContent = jobcardNumber;
    document.getElementById('jobcard_number_input').value = jobcardNumber;
}

function toggleTempClientFields(select) {
    document.getElementById('temp_client_fields').style.display = select.value === 'temp' ? 'block' : 'none';
}

function validateForm() {
    const category = document.querySelector('select[name="category"]').value;
    const jobcardNumber = document.getElementById('jobcard_number_input').value;
    
    if (!category) {
        alert('Please select a category to generate a jobcard number.');
        return false;
    }
    
    if (!jobcardNumber) {
        alert('Jobcard number was not generated. Please select a category.');
        return false;
    }
    
    return true;
}

// --- EMPLOYEES ---
let employees = [];
document.getElementById('add_employee').onclick = function() {
    const select = document.getElementById('employee_select');
    const hours = document.getElementById('employee_hours').value;
    const hourType = document.getElementById('employee_hour_type').value;
    const empId = select.value;
    const empName = select.options[select.selectedIndex].text;
    if (!empId || !hours) return;
    employees.push({id: empId, name: empName, hours, hourType});
    renderEmployees();
};
function renderEmployees() {
    const list = document.getElementById('employee_list');
    list.innerHTML = '';
    employees.forEach((e, i) => {
        list.innerHTML += `<div>${e.name} - ${e.hours}h (${e.hourType}) <button type='button' onclick='removeEmployee(${i})' style='color:red;'>Remove</button></div>`;
    });
    if (employees.length === 0) list.innerHTML = '<div class="text-muted">No employees assigned yet</div>';
    // Remove old hidden inputs
    document.querySelectorAll('.employee-hidden').forEach(e => e.remove());
    // Add hidden inputs
    employees.forEach((e, i) => {
        const f = document.querySelector('form');
        f.insertAdjacentHTML('beforeend', `<input type='hidden' class='employee-hidden' name='employees[${i}][id]' value='${e.id}'><input type='hidden' class='employee-hidden' name='employees[${i}][hours]' value='${e.hours}'><input type='hidden' class='employee-hidden' name='employees[${i}][hour_type]' value='${e.hourType}'>`);
    });
}
window.removeEmployee = function(i) { employees.splice(i,1); renderEmployees(); };

// --- TRAVELING ---
let traveling = [];
document.getElementById('add_traveling').onclick = function() {
    const select = document.getElementById('travel_employee_select');
    const empId = select.value;
    const empName = select.options[select.selectedIndex].text;
    const km = document.getElementById('travel_km').value;
    if (!empId || !km) return;
    traveling.push({id: empId, name: empName, km});
    renderTraveling();
};
function renderTraveling() {
    const list = document.getElementById('traveling_list');
    list.innerHTML = '';
    traveling.forEach((t, i) => {
        list.innerHTML += `<div>${t.name} - ${t.km} km <button type='button' onclick='removeTraveling(${i})' style='color:red;'>Remove</button></div>`;
    });
    if (traveling.length === 0) list.innerHTML = '<div class="text-muted">No traveling entries yet</div>';
    document.querySelectorAll('.traveling-hidden').forEach(e => e.remove());
    traveling.forEach((t, i) => {
        const f = document.querySelector('form');
        f.insertAdjacentHTML('beforeend', `<input type='hidden' class='traveling-hidden' name='traveling[${i}][id]' value='${t.id}'><input type='hidden' class='traveling-hidden' name='traveling[${i}][km]' value='${t.km}'>`);
    });
}
window.removeTraveling = function(i) { traveling.splice(i,1); renderTraveling(); };

// --- INVENTORY ---
let inventory = [];
document.getElementById('add_inventory').onclick = function() {
    const select = document.getElementById('inventory_select');
    const itemId = select.value;
    const itemName = select.options[select.selectedIndex].text;
    const qty = document.getElementById('inventory_quantity').value;
    if (!itemId || !qty) return;
    inventory.push({id: itemId, name: itemName, qty});
    renderInventory();
};
function renderInventory() {
    const list = document.getElementById('inventory_list');
    list.innerHTML = '';
    inventory.forEach((inv, i) => {
        list.innerHTML += `<div>${inv.name} - ${inv.qty} <button type='button' onclick='removeInventory(${i})' style='color:red;'>Remove</button></div>`;
    });
    if (inventory.length === 0) list.innerHTML = '<div class="text-muted">No inventory items added yet</div>';
    document.querySelectorAll('.inventory-hidden').forEach(e => e.remove());
    inventory.forEach((inv, i) => {
        const f = document.querySelector('form');
        f.insertAdjacentHTML('beforeend', `<input type='hidden' class='inventory-hidden' name='inventory[${i}][id]' value='${inv.id}'><input type='hidden' class='inventory-hidden' name='inventory[${i}][qty]' value='${inv.qty}'>`);
    });
}
window.removeInventory = function(i) { inventory.splice(i,1); renderInventory(); };

// Generate jobcard number on page load if category is already selected
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.querySelector('select[name="category"]');
    if (categorySelect.value) {
        generateJobcardNumber();
    }
});
</script>
@endsection