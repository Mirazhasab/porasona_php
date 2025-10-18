<?php

namespace App\Http\Controllers;

use App\Models\McqSet;
use App\Services\SubscriptionAccessManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class McqSetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin') || $user->role === 'admin') {
            // Admin sees all sets with their status
            $mcqSets = McqSet::with('user', 'questions')
                ->latest()
                ->paginate(10);
        } else {
            // Users see only approved sets + their own sets
            $mcqSets = McqSet::with('user', 'questions')
                ->where(function($query) use ($user) {
                    $query->where('status', 'approved')
                          ->orWhere('user_id', $user->id);
                })
                ->latest()
                ->paginate(10);
        }

        return view('mcq_management.sets.index', compact('mcqSets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mcq_management.sets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'exam_name' => 'nullable|string|max:255',
            'exam_date' => 'nullable|date',
            'exam_time' => 'nullable|date_format:H:i',
            'total_marks' => 'nullable|integer|min:0',
            'duration' => 'nullable|integer|min:1',
            '_nonce' => 'required|string|size:32',
        ]);

        $nonceKey = 'mcq_set_nonce_' . $validated['_nonce'];
        if (session()->has($nonceKey)) {
            return redirect()->route('mcq_sets.index')->with('error', 'Duplicate submission detected.');
        }
        session()->put($nonceKey, true);

        $user = Auth::user();
        // If user is admin, set status to approved, otherwise pending
        $validated['user_id'] = $user->id;
        $validated['status'] = $user->hasRole('admin') ? 'approved' : 'pending';

        $mcqSet = McqSet::create($validated);

        // Remove nonce after use
        session()->forget($nonceKey);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'MCQ Set created successfully!',
                'redirect' => route('mcq_sets.show', $mcqSet)
            ]);
        }
        
        return redirect()
            ->route('mcq_sets.show', $mcqSet)
            ->with('success', 'MCQ Set created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Check if user can view this set
        // Allow if: user is admin OR user owns the set OR set is approved
        if (!($user->hasRole('admin') || $user->role === 'admin') && 
            $mcqSet->user_id !== $user->id && 
            $mcqSet->status !== 'approved') {
            abort(403, 'Unauthorized access to this MCQ set.');
        }

        $hasActiveSubscription = request()->attributes->get('has_active_subscription', false);
        $totalQuestions = $mcqSet->questions()->count();

        $accessManager = SubscriptionAccessManager::for($user);
        $freeLimit = $accessManager->questionLimit('mcq_preview') ?? Setting::getValue(Setting::FREE_MCQ_LIMIT_KEY, 10);

        if ($accessManager->isLimited()) {
            $mcqSet->setRelation('questions', $accessManager->questionsForSet($mcqSet, 'mcq_preview'));
            $mcqSet->loadMissing('user');
            $limitedView = true;
        } else {
            $mcqSet->load(['questions', 'user']);
            $limitedView = false;
        }

        return view('mcq_management.sets.show', [
            'mcqSet' => $mcqSet,
            'limitedView' => $limitedView,
            'freeLimit' => $freeLimit,
            'totalQuestions' => $totalQuestions,
            'hasActiveSubscription' => $hasActiveSubscription,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Check if user can edit this set
        if (!$user->hasRole('admin') && $user->role !== 'admin' && ($mcqSet->user_id !== $user->id || $mcqSet->status === 'approved')) {
            abort(403, 'You cannot edit this MCQ set.');
        }

        return view('mcq_management.sets.edit', compact('mcqSet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Check if user can update this set
        if (!$user->hasRole('admin') && $user->role !== 'admin' && ($mcqSet->user_id !== $user->id || $mcqSet->status === 'approved')) {
            abort(403, 'You cannot update this MCQ set.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_name' => 'nullable|string|max:255',
            'exam_date' => 'nullable|date',
            'exam_time' => 'nullable|date_format:H:i',
            'total_marks' => 'nullable|integer|min:0',
            'duration' => 'nullable|integer|min:1',
        ]);

        $mcqSet->update($validated);

        return redirect()
            ->route('mcq_sets.show', $mcqSet)
            ->with('success', 'MCQ Set updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(McqSet $mcqSet)
    {
        $user = Auth::user();
        
        // Check if user can delete this set
        if (!$user->hasRole('admin') && $user->role !== 'admin' && ($mcqSet->user_id !== $user->id || $mcqSet->status === 'approved')) {
            abort(403, 'You cannot delete this MCQ set.');
        }

        $mcqSet->delete();

        return redirect()
            ->route('mcq_sets.index')
            ->with('success', 'MCQ Set deleted successfully!');
    }

    /**
     * Approve the MCQ set (Admin only).
     */
    public function approve($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && $user->role !== 'admin') {
            abort(403, 'Only admins can approve MCQ sets.');
        }

        $mcqSet = McqSet::findOrFail($id);
        $mcqSet->update(['status' => 'approved']);

        return redirect()
            ->back()
            ->with('success', 'MCQ Set approved successfully!');
    }

    /**
     * Reject the MCQ set (Admin only).
     */
    public function reject($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && $user->role !== 'admin') {
            abort(403, 'Only admins can reject MCQ sets.');
        }

        $mcqSet = McqSet::findOrFail($id);
        $mcqSet->update(['status' => 'rejected']);

        return redirect()
            ->back()
            ->with('success', 'MCQ Set rejected.');
    }

    /**
     * Show the import form (Admin only).
     */
    public function showImport(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && $user->role !== 'admin') {
            abort(403, 'Only admins can import MCQ data.');
        }

        // Get existing MCQ sets for import into existing set option
        $existingSets = McqSet::where('status', 'approved')
            ->orderBy('title')
            ->get();

        // Check if importing into specific set
        $targetSet = null;
        if ($request->has('set_id')) {
            $targetSet = McqSet::findOrFail($request->get('set_id'));
        }

        return view('mcq_management.sets.import', compact('existingSets', 'targetSet'));
    }

    /**
     * Import MCQ data from file (Admin only).
     */
    public function import(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Admin access required'], 403);
        }

        $request->validate([
            'import_file' => 'required|file|max:10240',
            'existing_set_id' => 'required|exists:mcq_sets,id'
        ]);

        try {
            $file = $request->file('import_file');
            $mcqSet = McqSet::findOrFail($request->existing_set_id);
            
            // Process CSV file
            $questions = $this->parseCSV($file->getPathname());
            
            if (empty($questions)) {
                return response()->json(['success' => false, 'error' => 'No valid questions found']);
            }

            // Import questions
            $imported = 0;
            foreach ($questions as $q) {
                if ($this->isValidQuestion($q)) {
                    \App\Models\McqQuestion::create([
                        'mcq_set_id' => $mcqSet->id,
                        'question' => $q['question'],
                        'ans_1' => $q['ans_1'],
                        'ans_2' => $q['ans_2'],
                        'ans_3' => $q['ans_3'],
                        'ans_4' => $q['ans_4'],
                        'correct_ans' => $this->getCorrectAnswer($q),
                        'marks' => 1
                    ]);
                    $imported++;
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully imported {$imported} questions",
                    'imported_count' => $imported,
                    'mcq_set_title' => $mcqSet->title
                ]);
            }
            return redirect()->route('mcq_sets.show', $mcqSet)
                ->with('success', "Successfully imported {$imported} questions into '{$mcqSet->title}'");

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function parseCSV($filePath)
    {
        $questions = [];
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        $header = array_map('strtolower', array_map('trim', $header));
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 6) {
                $questions[] = array_combine($header, $row);
            }
        }
        fclose($handle);
        return $questions;
    }

    private function isValidQuestion($q)
    {
        return !empty($q['question']) && !empty($q['ans_1']) && 
               !empty($q['ans_2']) && !empty($q['ans_3']) && 
               !empty($q['ans_4']) && !empty($q['correct']);
    }

    private function getCorrectAnswer($q)
    {
        $correct = trim($q['correct']);
        
        // Check if it's already a number
        if (in_array($correct, ['1', '2', '3', '4'])) {
            return $correct;
        }
        
        // Match with answer options
        $answers = [
            '1' => trim($q['ans_1']),
            '2' => trim($q['ans_2']),
            '3' => trim($q['ans_3']),
            '4' => trim($q['ans_4'])
        ];
        
        foreach ($answers as $num => $answer) {
            if ($answer === $correct) {
                return $num;
            }
        }
        
        return '1'; // Default fallback
    }



    /**
     * Process SQLite file and extract questions.
     */
    private function processSQLiteFile($filePath, $tableName)
    {
        try {
            // Ensure the file is readable
            if (!is_readable($filePath)) {
                throw new \Exception('SQLite file is not readable.');
            }

            $pdo = new \PDO('sqlite:' . $filePath);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Check if table exists
            $tableExists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='" . $pdo->quote($tableName) . "'")->fetch();
            
            if (!$tableExists) {
                // List available tables for debugging
                $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(\PDO::FETCH_COLUMN);
                throw new \Exception('Table "' . $tableName . '" not found in the SQLite database. Available tables: ' . implode(', ', $tables));
            }

            // Get table structure to verify columns
            $columns = $pdo->query("PRAGMA table_info([" . $tableName . "])")->fetchAll(\PDO::FETCH_ASSOC);
            $columnNames = array_map('strtolower', array_column($columns, 'name'));
            
            $requiredColumns = ['question', 'ans_1', 'ans_2', 'ans_3', 'ans_4', 'correct'];
            $missingColumns = array_diff($requiredColumns, $columnNames);
            
            if (!empty($missingColumns)) {
                throw new \Exception('Missing required columns: ' . implode(', ', $missingColumns) . '. Found columns: ' . implode(', ', $columnNames));
            }

            // Fetch data from SQLite with proper escaping
            $stmt = $pdo->prepare("SELECT * FROM [" . $tableName . "]");
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            \Log::info('SQLite processing complete', ['table' => $tableName, 'rows' => count($results)]);
            
            return $results;
        } catch (\PDOException $e) {
            throw new \Exception('SQLite database error: ' . $e->getMessage());
        }
    }

    /**
     * Process CSV file and extract questions.
     */
    private function processCSVFile($filePath)
    {
        $questions = [];
        
        // Try different encodings
        $content = file_get_contents($filePath);
        if (!$content) {
            throw new \Exception('Unable to read CSV file.');
        }

        // Convert to UTF-8 if needed
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
            file_put_contents($filePath, $content);
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Unable to open CSV file.');
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header || empty(array_filter($header))) {
            fclose($handle);
            throw new \Exception('CSV file is empty or has no valid header row.');
        }

        // Normalize header names (case-insensitive, remove BOM)
        $header = array_map(function($col) {
            return strtolower(trim($col, " \t\n\r\0\x0B\xEF\xBB\xBF"));
        }, $header);
        
        // Headers found and validated
        
        // Check required columns
        $requiredColumns = ['question', 'ans_1', 'ans_2', 'ans_3', 'ans_4', 'correct'];
        $missingColumns = array_diff($requiredColumns, $header);
        
        if (!empty($missingColumns)) {
            fclose($handle);
            throw new \Exception('Missing required columns in CSV: ' . implode(', ', $missingColumns) . '. Found columns: ' . implode(', ', $header));
        }

        // Create column index mapping
        $columnIndexes = array_flip($header);
        
        \Log::info('CSV column mapping', ['columns' => $header, 'indexes' => $columnIndexes]);

        // Read data rows
        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $questionData = [];
            $hasRequiredData = true;

            foreach ($requiredColumns as $column) {
                $value = isset($columnIndexes[$column]) && isset($row[$columnIndexes[$column]]) 
                    ? trim($row[$columnIndexes[$column]]) : '';
                
                $questionData[$column] = $value;
                
                // Check if essential data is missing
                if (empty($value) && ($column === 'question' || $column === 'correct')) {
                    $hasRequiredData = false;
                }
            }

            // Add optional columns if they exist
            $optionalColumns = ['subject', 'id'];
            foreach ($optionalColumns as $column) {
                if (isset($columnIndexes[$column]) && isset($row[$columnIndexes[$column]])) {
                    $questionData[$column] = trim($row[$columnIndexes[$column]]);
                }
            }
            
            // Handle correct answer format - support both text and number formats
            if (!empty($questionData['correct'])) {
                $correctAnswer = $this->normalizeCorrectAnswer($questionData['correct'], $questionData);
                if ($correctAnswer) {
                    $questionData['correct'] = $correctAnswer;
                } else {
                    \Log::warning("Could not normalize correct answer in row {$rowNumber}", [
                        'correct' => $questionData['correct'],
                        'ans_1' => $questionData['ans_1'] ?? '',
                        'ans_2' => $questionData['ans_2'] ?? '',
                        'ans_3' => $questionData['ans_3'] ?? '',
                        'ans_4' => $questionData['ans_4'] ?? ''
                    ]);
                    // Don't skip the question, just use '1' as default
                    $questionData['correct'] = '1';
                }
            }

            if ($hasRequiredData) {
                $questions[] = $questionData;
            }
        }

        fclose($handle);
        
        \Log::info("CSV processing complete", ['total_rows' => $rowNumber, 'extracted_questions' => count($questions)]);
        
        return $questions;
    }

    /**
     * Process Excel file and extract questions.
     */
    private function processExcelFile($filePath)
    {
        try {
            // Try to read as Excel using PhpSpreadsheet if available
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                return $this->processExcelWithPhpSpreadsheet($filePath);
            } else {
                // Fallback: Try to convert Excel to CSV using simple method
                return $this->processExcelFallback($filePath);
            }
        } catch (\Exception $e) {
            throw new \Exception('Error processing Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Process Excel file using PhpSpreadsheet library.
     */
    private function processExcelWithPhpSpreadsheet($filePath)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $questions = [];
        $header = [];
        $headerRow = 1;
        
        // Read header row
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $headerRow)->getCalculatedValue();
            $header[] = strtolower(trim($cellValue));
        }

        // Check required columns
        $requiredColumns = ['question', 'ans_1', 'ans_2', 'ans_3', 'ans_4', 'correct'];
        $missingColumns = array_diff($requiredColumns, $header);
        
        if (!empty($missingColumns)) {
            throw new \Exception('Missing required columns in Excel: ' . implode(', ', $missingColumns));
        }

        // Create column index mapping
        $columnIndexes = array_flip($header);

        // Read data rows
        $highestRow = $worksheet->getHighestRow();
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $questionData = [];
            $hasData = false;

            foreach ($requiredColumns as $column) {
                if (isset($columnIndexes[$column])) {
                    $colIndex = $columnIndexes[$column] + 1; // PhpSpreadsheet uses 1-based indexing
                    $cellValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getCalculatedValue();
                    $questionData[$column] = trim($cellValue);
                    if (!empty($cellValue)) {
                        $hasData = true;
                    }
                } else {
                    $questionData[$column] = '';
                }
            }

            // Add optional subject column if exists
            if (isset($columnIndexes['subject'])) {
                $colIndex = $columnIndexes['subject'] + 1;
                $cellValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getCalculatedValue();
                $questionData['subject'] = trim($cellValue);
            }

            if ($hasData) {
                $questions[] = $questionData;
            }
        }

        return $questions;
    }

    /**
     * Map correct answer from various formats to our format (1, 2, 3, 4).
     */
    private function mapCorrectAnswer($correct, $questionData = null)
    {
        $correct = trim($correct);
        $correctLower = strtolower($correct);
        
        // Handle standard formats first
        switch ($correctLower) {
            case '1':
            case 'a':
            case 'ans_1':
            case 'option_1':
                return '1';
            case '2':
            case 'b':
            case 'ans_2':
            case 'option_2':
                return '2';
            case '3':
            case 'c':
            case 'ans_3':
            case 'option_3':
                return '3';
            case '4':
            case 'd':
            case 'ans_4':
            case 'option_4':
                return '4';
        }
        
        // If questionData is provided, try to match the correct answer text with answer options
        if ($questionData) {
            $answers = [
                '1' => trim($questionData['ans_1'] ?? ''),
                '2' => trim($questionData['ans_2'] ?? ''),
                '3' => trim($questionData['ans_3'] ?? ''),
                '4' => trim($questionData['ans_4'] ?? '')
            ];
            
            // Exact match
            foreach ($answers as $num => $answer) {
                if ($answer === $correct) {
                    return $num;
                }
            }
            
            // Case-insensitive match
            foreach ($answers as $num => $answer) {
                if (strtolower($answer) === $correctLower) {
                    return $num;
                }
            }
            
            // Partial match (if correct answer is contained in one of the options)
            foreach ($answers as $num => $answer) {
                if (!empty($answer) && !empty($correct) && 
                    (strpos(strtolower($answer), $correctLower) !== false || 
                     strpos($correctLower, strtolower($answer)) !== false)) {
                    return $num;
                }
            }
        }
        
        return null; // Invalid format
    }
    
    /**
     * Normalize correct answer to standard format (1, 2, 3, 4).
     */
    private function normalizeCorrectAnswer($correct, $questionData = null)
    {
        $correct = trim($correct);
        
        // If it's already in the right format
        if (in_array($correct, ['1', '2', '3', '4'])) {
            return $correct;
        }
        
        // Handle A, B, C, D format
        $letterMap = ['a' => '1', 'b' => '2', 'c' => '3', 'd' => '4'];
        $correctLower = strtolower($correct);
        if (isset($letterMap[$correctLower])) {
            return $letterMap[$correctLower];
        }
        
        // If questionData is provided, try to match the correct answer text with answer options
        if ($questionData) {
            $answers = [
                '1' => trim($questionData['ans_1'] ?? ''),
                '2' => trim($questionData['ans_2'] ?? ''),
                '3' => trim($questionData['ans_3'] ?? ''),
                '4' => trim($questionData['ans_4'] ?? '')
            ];
            
            // Exact match (case sensitive)
            foreach ($answers as $num => $answer) {
                if (!empty($answer) && $answer === $correct) {
                    return $num;
                }
            }
            
            // Case-insensitive match
            foreach ($answers as $num => $answer) {
                if (!empty($answer) && strtolower($answer) === $correctLower) {
                    return $num;
                }
            }
            
            // Partial match (if correct answer contains or is contained in option)
            foreach ($answers as $num => $answer) {
                if (!empty($answer) && !empty($correct)) {
                    if (mb_strpos($answer, $correct) !== false || mb_strpos($correct, $answer) !== false) {
                        return $num;
                    }
                }
            }
        }
        
        // Default fallback - return '1' instead of null
        return '1';
    }

    /**
     * Fallback Excel processing without PhpSpreadsheet.
     */
    private function processExcelFallback($filePath)
    {
        // For now, suggest CSV conversion
        throw new \Exception('Excel processing requires PhpSpreadsheet library or GD extension. Please convert your Excel file to CSV format and try again. You can open the Excel file and save it as CSV, then upload the CSV file instead.');
    }

    /**
     * Enhanced file format detection.
     */
    private function detectFileFormat($filePath, $originalName)
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Check file signature/magic bytes for better detection
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            throw new \Exception('Cannot read uploaded file.');
        }
        
        $header = fread($handle, 16);
        fclose($handle);
        
        // SQLite signature
        if (substr($header, 0, 6) === 'SQLite') {
            return 'sqlite';
        }
        
        // Excel signatures
        if (substr($header, 0, 2) === 'PK' || substr($header, 0, 8) === "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
            return 'excel';
        }
        
        // Default to extension-based detection
        if (in_array($extension, ['sqlite', 'sqlite3', 'db'])) {
            return 'sqlite';
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            return 'excel';
        } elseif (in_array($extension, ['csv', 'txt'])) {
            return 'csv';
        }
        
        return 'csv'; // Default fallback
    }
}
