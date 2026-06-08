<x-guest-layout>
<style>
/* ── Wizard shell ── */
.wizard-wrap { width:100%; max-width:480px; }

.step-header { text-align:center; margin-bottom:24px; }
.step-header h2 { font-size:22px; font-weight:800; color:#0f172a; margin:0 0 4px; }
.step-header p  { font-size:13px; color:#64748b; margin:0; }

/* Progress bar */
.progress-steps { display:flex; align-items:center; margin-bottom:28px; }
.step-node {
    display:flex; flex-direction:column; align-items:center; flex:1;
    position:relative;
}
.step-node:not(:last-child)::after {
    content:''; position:absolute; top:18px; left:50%; right:-50%;
    height:2px; background:#e2e8f0; z-index:0;
    transition:.3s;
}
.step-node.completed:not(:last-child)::after { background:#667eea; }
.step-circle {
    width:36px; height:36px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:14px; z-index:1; transition:.3s;
    border:2px solid #e2e8f0; background:#fff; color:#94a3b8;
}
.step-node.active   .step-circle { border-color:#667eea; background:#667eea; color:#fff; }
.step-node.completed .step-circle { border-color:#48bb78; background:#48bb78; color:#fff; }
.step-label { font-size:11px; margin-top:5px; color:#94a3b8; font-weight:600; text-align:center; }
.step-node.active    .step-label { color:#667eea; }
.step-node.completed .step-label { color:#48bb78; }

/* Form fields */
.field { margin-bottom:16px; }
.field label { display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:5px; }
.field input, .field select, .field textarea {
    width:100%; height:44px; border:1.5px solid #e2e8f0; border-radius:10px;
    padding:0 14px; font-size:14px; color:#0f172a; outline:none; background:#fff;
    transition:.15s;
}
.field input:focus, .field select:focus {
    border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.12);
}
.field .err { color:#ef4444; font-size:12px; margin-top:4px; }
.row2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.row3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; }

/* ID type pills */
.id-pills { display:flex; flex-wrap:wrap; gap:8px; }
.id-pill {
    padding:6px 14px; border:1.5px solid #e2e8f0; border-radius:20px;
    font-size:12px; font-weight:600; cursor:pointer; color:#475569;
    transition:.15s; user-select:none;
}
.id-pill.sel { border-color:#667eea; background:#ede9fe; color:#5b21b6; }

/* Upload zone */
.upload-zone {
    border:2px dashed #cbd5e1; border-radius:10px; padding:20px;
    text-align:center; cursor:pointer; background:#f8fafc; transition:.2s;
}
.upload-zone:hover { border-color:#667eea; background:#f0f4ff; }
.file-list { margin-top:10px; }
.file-item {
    display:flex; align-items:center; gap:8px;
    background:#f1f5f9; border-radius:8px; padding:6px 10px;
    font-size:12px; margin-bottom:4px;
}

/* Navigation */
.nav-row { display:flex; justify-content:space-between; align-items:center; margin-top:24px; gap:12px; }
.btn-next {
    flex:1; background:#1e3a8a; color:#fff; border:none;
    padding:13px; border-radius:12px; font-size:15px; font-weight:800;
    cursor:pointer; transition:.2s;
}
.btn-next:hover { background:#172554; }
.btn-back {
    flex:1; background:#f1f5f9; color:#334155; border:none;
    padding:13px; border-radius:12px; font-size:15px; font-weight:700;
    cursor:pointer; transition:.2s;
}
.btn-back:hover { background:#e2e8f0; }
.login-link { text-align:center; margin-top:16px; font-size:13px; color:#64748b; }
.login-link a { color:#2563eb; font-weight:600; text-decoration:none; }
.login-link a:hover { text-decoration:underline; }

/* Error alert */
.err-alert { background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:12px 14px; margin-bottom:16px; font-size:13px; color:#b91c1c; }
.err-alert ul { margin:6px 0 0 16px; padding:0; }
</style>

{{-- Show server-side errors at top of active step --}}
@php $hasErrors = $errors->any(); @endphp

<div class="wizard-wrap">

    {{-- Step progress --}}
    <div class="progress-steps" id="progressBar">
        @php
        $steps = [
            ['Account',      '🔐'],
            ['Personal Info','👤'],
            ['Upload ID',    '🪪'],
        ];
        @endphp
        @foreach($steps as $i => $s)
        <div class="step-node {{ $i === 0 ? 'active' : '' }}" id="node{{ $i }}">
            <div class="step-circle" id="circle{{ $i }}" style="font-size:16px;">
                {{ $s[1] }}
            </div>
            <div class="step-label">{{ $s[0] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Server validation errors (only shown on submit attempt) --}}
    @if($hasErrors)
    <div class="err-alert">
        <strong>Please fix the following:</strong>
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="regForm">
        @csrf

        {{-- ══════════════ STEP 1 — Account Setup ══════════════ --}}
        <div class="step-panel" id="step1">
            <div class="step-header">
                <h2>Create Your Account</h2>
                <p>Step 1 of 3 — Set up your login credentials</p>
            </div>

            <div class="field">
                <label>Display Name <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="e.g. Juan Santos Dela Cruz" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Email Address <span style="color:#ef4444">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="yourname@email.com" required autocomplete="username">
                @error('email')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="row2">
                <div class="field">
                    <label>Password <span style="color:#ef4444">*</span></label>
                    <input type="password" name="password" placeholder="Min. 8 characters"
                           required autocomplete="new-password" id="pw1">
                    @error('password')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Confirm Password <span style="color:#ef4444">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password"
                           required autocomplete="new-password" id="pw2">
                </div>
            </div>

            <div class="nav-row">
                <button type="button" class="btn-next" onclick="goStep(2)">
                    Continue <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ══════════════ STEP 2 — Personal Information ══════════════ --}}
        <div class="step-panel" id="step2" style="display:none;">
            <div class="step-header">
                <h2>Personal Information</h2>
                <p>Step 2 of 3 — Tell us about yourself</p>
            </div>

            <div class="row3">
                <div class="field">
                    <label>First Name <span style="color:#ef4444">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                           placeholder="Juan" required>
                    @error('first_name')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                           placeholder="Santos">
                </div>
                <div class="field">
                    <label>Last Name <span style="color:#ef4444">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           placeholder="Dela Cruz" required>
                    @error('last_name')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row2">
                <div class="field">
                    <label>Date of Birth <span style="color:#ef4444">*</span></label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                           required max="{{ now()->subYears(15)->toDateString() }}">
                    @error('birth_date')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Gender <span style="color:#ef4444">*</span></label>
                    <select name="gender" required>
                        <option value="">Select</option>
                        <option value="male"   @selected(old('gender')==='male')>Male</option>
                        <option value="female" @selected(old('gender')==='female')>Female</option>
                    </select>
                    @error('gender')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="field">
                <label>Contact Number <span style="color:#ef4444">*</span></label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                       placeholder="09XXXXXXXXX" required minlength="7" maxlength="15">
                @error('contact_number')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label>Home Address <span style="color:#ef4444">*</span></label>
                <input type="text" name="address" value="{{ old('address') }}"
                       placeholder="House No., Street, Purok, Barangay" required>
                @error('address')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="nav-row">
                <button type="button" class="btn-back" onclick="goStep(1)">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <button type="button" class="btn-next" onclick="goStep(3)">
                    Continue <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ══════════════ STEP 3 — Government ID Upload ══════════════ --}}
        <div class="step-panel" id="step3" style="display:none;">
            <div class="step-header">
                <h2>Upload Government ID</h2>
                <p>Step 3 of 3 — Barangay verification requires a valid ID</p>
            </div>

            <div class="field">
                <label>ID Type <span style="color:#ef4444">*</span></label>
                <div class="id-pills">
                    @foreach($documentTypes as $value => $label)
                    <label class="id-pill {{ old('id_type') === $value ? 'sel' : '' }}">
                        <input type="radio" name="id_type" value="{{ $value }}"
                               {{ old('id_type') === $value ? 'checked' : '' }}
                               style="display:none" required>
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
                @error('id_type')<div class="err" style="margin-top:6px;">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label>
                    ID File(s) <span style="color:#ef4444">*</span>
                    <span style="color:#94a3b8;font-weight:400"> — 1 to 3 files, JPEG / PNG / PDF, max 5 MB each</span>
                </label>
                <div class="upload-zone" onclick="document.getElementById('id_files_input').click()">
                    <i class="bi bi-cloud-arrow-up" style="font-size:2rem;color:#667eea;display:block;margin-bottom:6px;"></i>
                    <div style="font-size:13px;font-weight:600;color:#475569;">Click to browse or drag files here</div>
                    <div style="font-size:12px;color:#94a3b8;margin-top:3px;">JPEG · PNG · PDF — Max 5 MB each</div>
                </div>
                <input type="file" id="id_files_input" name="id_files[]"
                       accept=".jpg,.jpeg,.png,.pdf" multiple style="display:none"
                       onchange="showFiles(this)">
                <div class="file-list" id="fileList"></div>
                @error('id_files')  <div class="err">{{ $message }}</div>@enderror
                @error('id_files.*')<div class="err">{{ $message }}</div>@enderror
                {{-- Catch individual file slot errors like id_files.0 --}}
                @foreach(range(0, 2) as $i)
                    @error('id_files.'.$i)<div class="err">One or more files failed to upload — please re-select your ID file(s) and try again.</div>@enderror
                @endforeach
            </div>

            <div style="background:#f0f6ff;border-radius:10px;padding:12px 14px;font-size:12px;color:#334155;margin-bottom:4px;">
                <i class="bi bi-shield-check" style="color:#667eea;"></i>
                Your account will be reviewed by a barangay admin before you can access the system.
                You will receive an email notification once your account is approved.
            </div>

            <div class="nav-row">
                <button type="button" class="btn-back" onclick="goStep(2)">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <button type="submit" class="btn-next" onclick="return validateStep3()">
                    <i class="bi bi-send-check"></i> Submit Registration
                </button>
            </div>
        </div>

    </form>

    <div class="login-link">
        Already have an account? <a href="{{ route('login') }}">Log in here</a>
    </div>
</div>

@push('scripts')
<script>
// Which step to start on — if server returned errors, go to step with the error
const serverErrors = {{ $errors->any() ? 'true' : 'false' }};
const errorFields  = {{ json_encode($errors->keys()) }};

function getStepForField(field) {
    const step1 = ['name','email','password','password_confirmation'];
    const step2 = ['first_name','middle_name','last_name','birth_date','gender','contact_number','address'];
    if (step1.includes(field)) return 1;
    if (step2.includes(field)) return 2;
    return 3; // id_files, id_files.0, id_files.1, id_type, etc.
}

let currentStep = 1;
// Track whether we are doing the initial auto-navigation to a server error step
let initialServerNav = false;

if (serverErrors && errorFields.length > 0) {
    currentStep = getStepForField(errorFields[0]);
    initialServerNav = true;
}

function goStep(n) {
    // Skip client-side validation when auto-jumping to a server error step on
    // page load — passwords and files are blank after reload so it would block.
    if (!initialServerNav && n > currentStep) {
        if (currentStep === 1 && !validateStep1()) return;
        if (currentStep === 2 && !validateStep2()) return;
    }
    initialServerNav = false; // only skip once

    document.querySelectorAll('.step-panel').forEach(p => p.style.display = 'none');
    document.getElementById('step' + n).style.display = 'block';
    currentStep = n;
    updateProgress(n);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateProgress(n) {
    // Unicode symbols — no icon font dependency in JS
    const symbols = ['🔐', '👤', '🪪'];
    const checks  = '✓';

    for (let i = 0; i < 3; i++) {
        const node   = document.getElementById('node' + i);
        const circle = document.getElementById('circle' + i);
        node.classList.remove('active', 'completed');
        if (i + 1 < n) {
            node.classList.add('completed');
            circle.innerHTML = checks;
            circle.style.fontSize = '16px';
        } else if (i + 1 === n) {
            node.classList.add('active');
            circle.innerHTML = symbols[i];
            circle.style.fontSize = '16px';
        } else {
            circle.innerHTML = symbols[i];
            circle.style.fontSize = '16px';
        }
    }
}

function validateStep1() {
    const name = document.querySelector('[name="name"]').value.trim();
    const email = document.querySelector('[name="email"]').value.trim();
    const pw1 = document.getElementById('pw1').value;
    const pw2 = document.getElementById('pw2').value;
    if (!name) { alert('Please enter your display name.'); return false; }
    if (!email || !email.includes('@')) { alert('Please enter a valid email address.'); return false; }
    if (pw1.length < 8) { alert('Password must be at least 8 characters.'); return false; }
    if (pw1 !== pw2) { alert('Passwords do not match.'); return false; }
    return true;
}

function validateStep2() {
    const fn = document.querySelector('[name="first_name"]').value.trim();
    const ln = document.querySelector('[name="last_name"]').value.trim();
    const bd = document.querySelector('[name="birth_date"]').value;
    const gn = document.querySelector('[name="gender"]').value;
    const cn = document.querySelector('[name="contact_number"]').value.trim();
    const ad = document.querySelector('[name="address"]').value.trim();
    if (!fn || !ln) { alert('First and last name are required.'); return false; }
    if (!bd) { alert('Date of birth is required.'); return false; }
    if (!gn) { alert('Please select your gender.'); return false; }
    if (!cn || cn.length < 7) { alert('Please enter a valid contact number.'); return false; }
    if (!ad) { alert('Home address is required.'); return false; }
    return true;
}

// ID type pill selection
document.querySelectorAll('.id-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('.id-pill').forEach(p => p.classList.remove('sel'));
        pill.classList.add('sel');
    });
});

function validateStep3() {
    const idTypeSelected = document.querySelector('[name="id_type"]:checked');
    if (!idTypeSelected) {
        alert('Please select the type of government ID you are uploading.');
        return false;
    }
    const fileInput = document.getElementById('id_files_input');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please select at least one ID file before submitting.');
        fileInput.closest('.field').scrollIntoView({ behavior: 'smooth' });
        return false;
    }
    return true;
}

// File preview
function showFiles(input) {
    const list = document.getElementById('fileList');
    list.innerHTML = '';
    Array.from(input.files).forEach(f => {
        const isPdf = f.type === 'application/pdf';
        const icon  = isPdf ? 'bi-file-pdf-fill' : 'bi-image-fill';
        const color = isPdf ? '#ef4444' : '#667eea';
        const size  = f.size > 1048576
            ? (f.size/1048576).toFixed(1)+' MB'
            : Math.round(f.size/1024)+' KB';
        list.innerHTML += `<div class="file-item">
            <i class="bi ${icon}" style="color:${color};font-size:15px;flex-shrink:0;"></i>
            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">${f.name}</span>
            <span style="color:#94a3b8;white-space:nowrap;">${size}</span>
        </div>`;
    });
}

// Init to correct step
goStep(currentStep);
</script>
@endpush
</x-guest-layout>
