@extends('layouts.mcq')

@section('title', 'Import MCQ Data')
@section('page-title', 'Import MCQ Data')
@section('page-subtitle', 'Upload CSV files with MCQ questions')

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Import MCQ Data</h1>
        <a href="{{ route('mcq_sets.index') }}" wire:navigate class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
            <i class="data-lucide="arrow-left" class="w-4 h-4""></i>
            <span>Back to MCQ Sets</span>
        </a>
    </div>

    <!-- Instructions -->
    <div class="bg-white border-2 border-gray-300 rounded-xl p-6 mb-6">
        <div class="flex items-start gap-3">
            <i class="data-lucide="info" class="w-5 h-5" text-gray-600 text-xl mt-1"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Import Instructions</h3>
                <ul class="text-gray-700 space-y-2">
                    <li class="flex items-start gap-2">
                        <i class="data-lucide="check" class="w-4 h-4" text-gray-600 mt-1 text-sm"></i>
                        <span>Upload a <strong>SQLite3</strong> (.sqlite, .sqlite3, .db), <strong>CSV</strong> (.csv), or <strong>Excel</strong> (.xlsx, .xls) file</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-gray-600 mt-1"></i>
                        <span><strong>Note:</strong> If Excel processing fails, convert your Excel file to CSV format for guaranteed compatibility</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="data-lucide="check" class="w-4 h-4" text-gray-600 mt-1 text-sm"></i>
                        <span>Required columns: <code class="bg-gray-100 px-2 py-1 rounded text-sm">question</code>, <code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_1</code>, <code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_2</code>, <code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_3</code>, <code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_4</code>, <code class="bg-gray-100 px-2 py-1 rounded text-sm">correct</code></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-gray-600 mt-1"></i>
                        <span><strong>Column order doesn't matter</strong> - the system will automatically detect column positions</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="data-lucide="check" class="w-4 h-4" text-gray-600 mt-1 text-sm"></i>
                        <span>The <code class="bg-gray-100 px-2 py-1 rounded text-sm">correct</code> column should contain: 1, 2, 3, 4 or A, B, C, D or the actual answer text</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="data-lucide="check" class="w-4 h-4" text-gray-600 mt-1 text-sm"></i>
                        <span>Maximum file size: 10MB</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Import Form -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="data-lucide="upload" class="w-5 h-5" text-primary"></i>
                Multi-Format Import Form
            </h2>
        </div>
        
        <form action="{{ route('mcq_sets.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            
            <!-- Target MCQ Set -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Select MCQ Set</h3>
                <div class="max-w-md">
                    <label for="existing_set_id" class="block text-sm font-medium text-gray-700 mb-2">MCQ Set <span class="text-red-500">*</span></label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" id="existing_set_id" name="existing_set_id" required>
                        <option value="">Choose MCQ set...</option>
                        @foreach($existingSets as $set)
                            <option value="{{ $set->id }}" {{ old('existing_set_id', $targetSet?->id) == $set->id ? 'selected' : '' }}>
                                {{ $set->title }} ({{ $set->questions->count() }} questions)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>


                        




            <!-- File Upload -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Upload CSV File</h3>
                
                <div class="max-w-md">
                    <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">CSV File <span class="text-red-500">*</span></label>
                    <input type="file" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" 
                           id="import_file" 
                           name="import_file" 
                           accept=".csv" 
                           required>
                    <p class="text-sm text-gray-600 mt-1">Only CSV files (.csv) - Max: 10MB</p>
                </div>
            </div>

            <!-- File Preview -->
            <div class="mb-6" id="filePreview" style="display: none;">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-green-800 font-semibold mb-2">
                        <i class="data-lucide="file-check" class="w-5 h-5""></i>
                        File Selected
                    </div>
                    <div id="fileInfo" class="text-green-700"></div>
                </div>
            </div>

            <!-- Sample Templates -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sample Templates</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-gray-700 mb-3"><strong>Download sample templates to get started:</strong></p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ asset('samples/sample_mcq_template.csv') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center gap-2" download>
                            <i class="data-lucide="download" class="w-4 h-4""></i>CSV Template
                        </a>
                        <a href="{{ asset('samples/bcs_10.csv') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center gap-2" download>
                            <i class="data-lucide="download" class="w-4 h-4""></i>BCS 10 Sample
                        </a>
                        <button type="button" onclick="createSampleFiles()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center gap-2">
                            <i class="data-lucide="wand-2" class="w-4 h-4""></i>Create Sample Files
                        </button>
                    </div>
                </div>
            </div>

            <!-- Expected Table Structure -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Supported File Formats</h3>
                
                <!-- Format Examples -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-semibold text-green-800 mb-2">✅ Your Format (Supported)</h4>
                        <code class="text-xs text-green-700">ans_1,ans_2,ans_3,ans_4,correct,id,question,subject</code>
                        <p class="text-sm text-green-600 mt-2">Column order doesn't matter - system auto-detects</p>
                    </div>
                    <div class="bg-white border-2 border-gray-300 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">✅ Standard Format</h4>
                        <code class="text-xs text-gray-700">question,ans_1,ans_2,ans_3,ans_4,correct</code>
                        <p class="text-sm text-gray-600 mt-2">Traditional format also supported</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Column</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Required</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Description</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Example</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">question</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Yes</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">The question text</td>
                                <td class="px-4 py-3 text-sm text-gray-500">"What is the capital?"</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_1</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Yes</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">First option</td>
                                <td class="px-4 py-3 text-sm text-gray-500">"Dhaka"</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_2</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Yes</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">Second option</td>
                                <td class="px-4 py-3 text-sm text-gray-500">"Chittagong"</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_3</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Yes</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">Third option</td>
                                <td class="px-4 py-3 text-sm text-gray-500">"Sylhet"</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">ans_4</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Yes</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">Fourth option</td>
                                <td class="px-4 py-3 text-sm text-gray-500">"Rajshahi"</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">correct</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Yes</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">Correct answer</td>
                                <td class="px-4 py-3 text-sm text-gray-500">"1" or "A" or "Dhaka"</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">subject</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">No</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">Subject/category</td>
                                <td class="px-4 py-3 text-sm text-gray-500">"bangla", "english"</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-sm">id</code></td>
                                <td class="px-4 py-3 text-sm"><span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">No</span></td>
                                <td class="px-4 py-3 text-sm text-gray-600">Question ID (ignored)</td>
                                <td class="px-4 py-3 text-sm text-gray-500">1, 2, 3...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Troubleshooting Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Troubleshooting Tips</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-2">
                            <i class="data-lucide="lightbulb" class="w-4 h-4" text-yellow-600 mt-1"></i>
                            <div>
                                <strong>File not uploading?</strong> Check file size (max 10MB) and ensure file isn't corrupted.
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="data-lucide="lightbulb" class="w-4 h-4" text-yellow-600 mt-1"></i>
                            <div>
                                <strong>Excel issues?</strong> Convert to CSV format: Open Excel → Save As → CSV (Comma delimited).
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="data-lucide="lightbulb" class="w-4 h-4" text-yellow-600 mt-1"></i>
                            <div>
                                <strong>SQLite table not found?</strong> Check table name spelling or use a database viewer to confirm table names.
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="data-lucide="lightbulb" class="w-4 h-4" text-yellow-600 mt-1"></i>
                            <div>
                                <strong>Missing columns?</strong> Ensure your file has: question, ans_1, ans_2, ans_3, ans_4, correct columns.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="bg-gray-600 border-2 border-gray-600 hover:bg-gray-700 hover:border-gray-700 text-white px-8 py-3 rounded-lg transition-colors text-lg font-semibold flex items-center gap-3 mx-auto" id="submitBtn">
                    <i class="data-lucide="upload" class="w-5 h-5""></i>
                    Import MCQ Data
                </button>
                <div class="mt-3">
                    <p class="text-sm text-gray-600">This process may take a few moments for large files.</p>
                </div>
                <div class="mt-4 space-x-2">
                    <button type="button" onclick="testImportFunction()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        Test Import Function
                    </button>
                    <button type="button" onclick="validateFile()" class="bg-gray-600 border-2 border-gray-600 hover:bg-gray-700 hover:border-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        Validate File
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Progress Modal -->
    <div id="progressModal" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="bg-gray-600 text-white px-6 py-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="data-lucide="settings" class="w-5 h-5 animate-spin""></i>
                    Processing Import
                </h3>
            </div>
            <div class="p-6 text-center">
                <div class="mb-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
                </div>
                
                <h4 id="progressStatus" class="text-lg font-semibold text-gray-800 mb-2">Uploading file...</h4>
                <p class="text-gray-600 mb-4" id="progressDetails">Please wait while we process your import file.</p>
                
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-6 mb-4">
                    <div class="bg-gray-600 h-6 rounded-full transition-all duration-300 flex items-center justify-center" 
                         id="importProgress" 
                         style="width: 0%">
                        <span class="text-white text-sm font-medium progress-text">0%</span>
                    </div>
                </div>
                
                <div class="flex justify-center space-x-8 text-sm">
                    <div class="step flex flex-col items-center" id="step1">
                        <i class="data-lucide="upload" class="w-5 h-5" text-gray-400 mb-1"></i>
                        <span class="text-gray-600">Upload File</span>
                    </div>
                    <div class="step flex flex-col items-center" id="step2">
                        <i class="data-lucide="check" class="w-4 h-4"-circle text-gray-400 mb-1"></i>
                        <span class="text-gray-600">Validate Data</span>
                    </div>
                    <div class="step flex flex-col items-center" id="step3">
                        <i data-lucide="database" class="w-5 h-5 text-gray-400 mb-1"></i>
                        <span class="text-gray-600">Import Questions</span>
                    </div>
                    <div class="step flex flex-col items-center" id="step4">
                        <i class="data-lucide="check" class="w-4 h-4" text-gray-400 mb-1"></i>
                        <span class="text-gray-600">Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="bg-green-600 text-white px-6 py-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="data-lucide="check" class="w-4 h-4"-circle"></i>
                    Import Successful!
                </h3>
            </div>
            <div class="p-6 text-center">
                <div class="mb-4">
                    <i class="data-lucide="check" class="w-4 h-4"-circle text-green-600 text-6xl"></i>
                </div>
                <h4 class="text-xl font-semibold text-green-600 mb-4">Import Completed Successfully!</h4>
                <div id="successDetails" class="space-y-2 text-gray-700">
                    <p><strong>Questions Imported:</strong> <span id="importedCount">0</span></p>
                    <p><strong>MCQ Set:</strong> <span id="setTitle">-</span></p>
                    <p><strong>Total Time:</strong> <span id="processingTime">-</span></p>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" class="bg-gray-600 border-2 border-gray-600 hover:bg-gray-700 hover:border-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2" id="viewSetBtn">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        View MCQ Set
                    </button>
                    <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2" id="importMoreBtn">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Import More
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="bg-red-600 text-white px-6 py-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    Import Failed
                </h3>
            </div>
            <div class="p-6 text-center">
                <div class="mb-4">
                    <i data-lucide="x-circle" class="w-16 h-16 text-red-600"></i>
                </div>
                <h4 class="text-xl font-semibold text-red-600 mb-4">Import Failed</h4>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-left" id="errorDetails">
                    <p class="text-red-800"><strong>Error:</strong> <span id="errorMessage">Unknown error occurred</span></p>
                </div>
                <div class="text-left">
                    <h5 class="font-semibold text-gray-800 mb-2">Common Solutions:</h5>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Check file format matches selected type</li>
                        <li>• Ensure all required columns are present</li>
                        <li>• Verify file is not corrupted</li>
                        <li>• Check file size is under 10MB</li>
                    </ul>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2" onclick="document.getElementById('errorModal').classList.add('hidden')">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Close
                    </button>
                    <button type="button" class="bg-gray-600 border-2 border-gray-600 hover:bg-gray-700 hover:border-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2" id="tryAgainBtn">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Try Again
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    padding: 0.5rem;
}

.step i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.step span {
    font-size: 0.8rem;
    text-align: center;
}

.step.active i {
    color: #007bff !important;
    animation: pulse 1s infinite;
}

.step.completed i {
    color: #28a745 !important;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.progress-text {
    font-weight: bold;
    font-size: 0.9rem;
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    border-radius: 15px 15px 0 0;
}

.spinner-border {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Import script starting');
    
    const fileInput = document.getElementById('import_file');
    const filePreview = document.getElementById('filePreview');
    const fileInfo = document.getElementById('fileInfo');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('importForm');
    
    console.log('Elements found:', {
        fileInput: !!fileInput,
        filePreview: !!filePreview,
        fileInfo: !!fileInfo,
        submitBtn: !!submitBtn,
        form: !!form
    });
    

    


    // File selection handler is now moved to the end of the script


    

    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('import_file');
        const existingSetId = document.getElementById('existing_set_id');
        
        // Validation
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('⚠️ Please select a CSV file');
            return;
        }
        
        if (!existingSetId.value) {
            e.preventDefault();
            alert('⚠️ Please select an MCQ set');
            return;
        }
        
        // Show confirmation
        const fileName = fileInput.files[0].name;
        const selectedOption = existingSetId.options[existingSetId.selectedIndex];
        
        let confirmMsg = '📁 IMPORT CONFIRMATION\n\n';
        confirmMsg += `File: ${fileName}\n`;
        confirmMsg += `Target Set: ${selectedOption.text}\n`;
        confirmMsg += '\nProceed with import?';
        
        if (!confirm(confirmMsg)) {
            e.preventDefault();
            return;
        }
        
        // Prevent default and use AJAX
        e.preventDefault();
        
        submitBtn.innerHTML = 'Importing...';
        submitBtn.disabled = true;
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`✅ SUCCESS!\n\nImported: ${data.imported_count} questions\nMCQ Set: ${data.mcq_set_title}`);
                form.reset();
            } else {
                alert(`❌ ERROR!\n\n${data.error}`);
            }
        })
        .catch(error => {
            alert(`❌ ERROR!\n\n${error.message}`);
        })
        .finally(() => {
            submitBtn.innerHTML = '<i class="data-lucide="upload" class="w-5 h-5""></i> Import MCQ Data';
            submitBtn.disabled = false;
        });
    });
    

    

    
    // Function to validate file before import
    window.validateFile = function() {
        const fileInput = document.getElementById('import_file');
        const fileFormat = document.querySelector('input[name="file_format"]:checked');
        
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Please select a file first.');
            return;
        }
        
        if (!fileFormat) {
            alert('Please select a file format.');
            return;
        }
        
        const file = fileInput.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileExtension = fileName.split('.').pop().toLowerCase();
        
        let validationResults = [];
        
        // Check file size
        if (file.size > 10 * 1024 * 1024) {
            validationResults.push('❌ File size (' + fileSize + ' MB) exceeds 10 MB limit');
        } else {
            validationResults.push('✅ File size (' + fileSize + ' MB) is acceptable');
        }
        
        // Check file extension
        const expectedExtensions = {
            'sqlite': ['sqlite', 'sqlite3', 'db'],
            'csv': ['csv', 'txt'],
            'excel': ['xlsx', 'xls']
        };
        
        if (expectedExtensions[fileFormat.value].includes(fileExtension)) {
            validationResults.push('✅ File extension (.' + fileExtension + ') matches selected format');
        } else {
            validationResults.push('⚠️ File extension (.' + fileExtension + ') may not match selected format (' + fileFormat.value + ')');
        }
        
        // Check table name for SQLite
        if (fileFormat.value === 'sqlite') {
            const tableName = document.getElementById('table_name').value;
            if (tableName.trim()) {
                validationResults.push('✅ Table name specified: ' + tableName);
            } else {
                validationResults.push('❌ Table name is required for SQLite files');
            }
        }
        
        // Show results
        const resultMessage = 'File Validation Results:\n\n' + validationResults.join('\n') + '\n\nFile: ' + fileName;
        alert(resultMessage);
    };
    
    // Function to create sample files
    window.createSampleFiles = function() {
        console.log('Creating sample files...');
        
        // Create sample CSV content
        const csvContent = `question,ans_1,ans_2,ans_3,ans_4,correct
"What is the capital of Bangladesh?","Dhaka","Chittagong","Sylhet","Rajshahi","1"
"Which is the largest river in Bangladesh?","Padma","Jamuna","Meghna","Karnaphuli","1"
"When did Bangladesh gain independence?","1970","1971","1972","1973","2"`;
        
        // Create and download CSV file
        const csvBlob = new Blob([csvContent], { type: 'text/csv' });
        const csvUrl = window.URL.createObjectURL(csvBlob);
        const csvLink = document.createElement('a');
        csvLink.href = csvUrl;
        csvLink.download = 'sample_mcq_questions.csv';
        csvLink.click();
        window.URL.revokeObjectURL(csvUrl);
        
        alert('Sample CSV file downloaded! You can use this as a template for your imports.');
    };
    
    // Test function to debug import issues
    window.testImportFunction = function() {
        console.log('=== TESTING IMPORT FUNCTION ===');
        console.log('Form element:', form);
        console.log('Submit button:', submitBtn);
        console.log('Progress modal:', progressModal);
        console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        console.log('Form action:', form?.action);
        
        // Test form elements
        const fileFormat = document.querySelector('input[name="file_format"]:checked');
        const importType = document.querySelector('input[name="import_type"]:checked');
        const fileInput = document.getElementById('import_file');
        
        console.log('File format selected:', fileFormat?.value);
        console.log('Import type selected:', importType?.value);
        console.log('File input:', fileInput);
        console.log('Files selected:', fileInput?.files?.length);
        
        if (fileInput?.files?.length > 0) {
            console.log('Selected file:', fileInput.files[0]);
        }
        
        // Test modal functionality
        if (progressModal) {
            console.log('Testing modal show/hide...');
            progressModal.show();
            setTimeout(() => {
                progressModal.hide();
                console.log('Modal test completed');
            }, 2000);
        } else {
            console.log('ERROR: Progress modal not initialized');
        }
        
        console.log('=== TEST COMPLETED ===');
    };
    

});
</script>

<style>
.table code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.875em;
}

.alert-heading {
    margin-bottom: 0.5rem;
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}
</style>
@endsection
