@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Bulk Email Sender</h5>
                    <p class="text-muted">Send personalized emails to multiple recipients</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('bulk-email.index') }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Emails
                        </a>
                        <a href="{{ route('bulk-email.data') }}" class="btn btn-outline-primary">
                            <i class="fas fa-database me-2"></i>View Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>
                        Send Bulk Emails
                    </h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Validation Error:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bulk-email.send') }}" enctype="multipart/form-data" id="bulkEmailForm">
                        @csrf

                        <div class="row">
                            <!-- Names Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="names" class="form-label fw-bold">
                                        <i class="fas fa-user me-2"></i>Recipient Names
                                        <small class="text-muted">(one per line or comma-separated)</small>
                                    </label>
                                    <textarea class="form-control @error('names') is-invalid @enderror" 
                                        id="names" name="names" rows="4" required placeholder="John Doe&#10;Jane Smith&#10;Bob Wilson">{{ old('names') }}</textarea>
                                    @error('names')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Universities Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="universities" class="form-label fw-bold">
                                        <i class="fas fa-university me-2"></i>Universities
                                        <small class="text-muted">(one per line or comma-separated)</small>
                                    </label>
                                    <textarea class="form-control @error('universities') is-invalid @enderror" 
                                        id="universities" name="universities" rows="4" required placeholder="Harvard University&#10;MIT&#10;Stanford University">{{ old('universities') }}</textarea>
                                    @error('universities')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="emails" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2"></i>Email Addresses
                                        <small class="text-muted">(one per line or comma-separated)</small>
                                    </label>
                                    <textarea class="form-control @error('emails') is-invalid @enderror" 
                                        id="emails" name="emails" rows="4" required placeholder="email1@example.com&#10;email2@example.com&#10;email3@example.com">{{ old('emails') }}</textarea>
                                    @error('emails')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Research Fields Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="research_fields" class="form-label fw-bold">
                                        <i class="fas fa-microscope me-2"></i>Research Fields
                                        <small class="text-muted">(one per line or comma-separated)</small>
                                    </label>
                                    <textarea class="form-control @error('research_fields') is-invalid @enderror" 
                                        id="research_fields" name="research_fields" rows="4" required placeholder="Computer Science&#10;Biology&#10;Physics">{{ old('research_fields') }}</textarea>
                                    @error('research_fields')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                            <!-- CV Upload -->
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="cv" class="form-label fw-bold">
                                        <i class="fas fa-file-pdf me-2"></i>CV File Upload
                                        <small class="text-danger">*</small>
                                        <small class="text-muted">(PDF only, max 10MB - Required for email sending)</small>
                                    </label>
                                    <div class="upload-area" id="uploadArea">
                                        <input type="file" class="form-control @error('cv') is-invalid @enderror" 
                                            id="cv" name="cv" accept=".pdf" required>
                                        <div class="upload-preview text-center p-4 border border-2 border-dashed rounded" id="uploadPreview">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="mb-2">Drag & drop your PDF file here or click to browse (required)</p>
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('cv').click()">
                                                <i class="fas fa-folder-open me-2"></i>Choose File
                                            </button>
                                        </div>
                                        <div class="file-info mt-3 p-3 bg-success bg-opacity-10 border border-success rounded" id="fileInfo" style="display: none;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-pdf text-danger fa-2x me-3"></i>
                                                    <div>
                                                        <strong id="fileName"></strong>
                                                        <br>
                                                        <small class="text-muted" id="fileSize"></small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeFile()">
                                                    <i class="fas fa-times"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @error('cv')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Send Emails
                                </button>
                                <button type="reset" class="btn btn-secondary btn-lg px-5 ms-2">
                                    <i class="fas fa-redo me-2"></i>Reset Form
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.upload-area {
    position: relative;
}
.upload-placeholder {
    cursor: pointer;
    transition: all 0.3s ease;
}
.upload-placeholder:hover {
    border-color: #007bff !important;
    background-color: #f8f9fa;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File upload handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('cv');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadPreview = document.getElementById('uploadPreview');

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-primary', 'bg-light');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-primary', 'bg-light');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-primary', 'bg-light');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            if (file.type === 'application/pdf') {
                // Show file information
                fileName.textContent = file.name;
                fileSize.textContent = 'Size: ' + (file.size / 1024 / 1024).toFixed(2) + ' MB';
                
                // Show file info and hide upload preview
                fileInfo.style.display = 'block';
                uploadPreview.style.display = 'none';
                
                // Add success styling to upload area
                uploadArea.classList.add('border-success', 'bg-success', 'bg-opacity-10');
                uploadArea.classList.remove('border-dashed');
                
                console.log('File selected:', file.name);
                console.log('File size:', file.size);
            } else {
                alert('Please select a PDF file only.');
                removeFile();
            }
        }

        window.removeFile = function() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            uploadPreview.style.display = 'block';
            
            // Remove success styling
            uploadArea.classList.remove('border-success', 'bg-success', 'bg-opacity-10');
            uploadArea.classList.add('border-dashed');
            
            console.log('File removed');
        }
    });
</script>
@endpush
@endsection
