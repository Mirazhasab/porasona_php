<!DOCTYPE html>
<html>
<head>
    <title>Database Debug - MCQ PRO</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>MCQ PRO - Database Debug Information</h1>
    
    <div class="debug-section">
        <h2>1. Database Connection Test</h2>
        @try
            @php
                $connection = \DB::connection();
                $databaseName = $connection->getDatabaseName();
                echo "<p class='success'>✅ Database Connected: {$databaseName}</p>";
            @endphp
        @catch(Exception $e)
            <p class='error'>❌ Database Connection Failed: {{ $e->getMessage() }}</p>
        @endtry
    </div>

    <div class="debug-section">
        <h2>2. Available Tables</h2>
        @try
            @php
                $tables = \DB::select('SHOW TABLES');
                echo "<p class='info'>Found " . count($tables) . " tables:</p>";
                echo "<ul>";
                foreach($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    echo "<li>{$tableName}</li>";
                }
                echo "</ul>";
            @endphp
        @catch(Exception $e)
            <p class='error'>❌ Could not fetch tables: {{ $e->getMessage() }}</p>
        @endtry
    </div>

    <div class="debug-section">
        <h2>3. User Model Test</h2>
        @try
            @php
                $userCount = \App\Models\User::count();
                echo "<p class='success'>✅ Users count: {$userCount}</p>";
                
                $users = \App\Models\User::limit(3)->get(['id', 'name', 'email', 'created_at']);
                if($users->count() > 0) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th></tr>";
                    foreach($users as $user) {
                        echo "<tr><td>{$user->id}</td><td>{$user->name}</td><td>{$user->email}</td><td>{$user->created_at}</td></tr>";
                    }
                    echo "</table>";
                }
            @endphp
        @catch(Exception $e)
            <p class='error'>❌ User Model Error: {{ $e->getMessage() }}</p>
        @endtry
    </div>

    <div class="debug-section">
        <h2>4. MCQ Questions Test</h2>
        @try
            @php
                echo "<h3>Testing McqQuestion Model:</h3>";
                try {
                    $questionCount = \App\Models\McqQuestion::count();
                    echo "<p class='success'>✅ McqQuestion Model: {$questionCount} questions</p>";
                    
                    if($questionCount > 0) {
                        $questions = \App\Models\McqQuestion::limit(3)->get(['id', 'question', 'mcq_set_id', 'created_at']);
                        echo "<table>";
                        echo "<tr><th>ID</th><th>Question</th><th>Set ID</th><th>Created</th></tr>";
                        foreach($questions as $q) {
                            echo "<tr><td>{$q->id}</td><td>" . substr($q->question, 0, 50) . "...</td><td>{$q->mcq_set_id}</td><td>{$q->created_at}</td></tr>";
                        }
                        echo "</table>";
                    }
                } catch(Exception $e1) {
                    echo "<p class='error'>❌ McqQuestion Model Error: {$e1->getMessage()}</p>";
                    
                    echo "<h3>Testing Direct DB Queries:</h3>";
                    $possibleTables = ['mcq_questions', 'questions', 'mcq_question', 'question'];
                    foreach($possibleTables as $tableName) {
                        try {
                            $count = \DB::table($tableName)->count();
                            echo "<p class='success'>✅ Table '{$tableName}': {$count} records</p>";
                            
                            if($count > 0) {
                                $sample = \DB::table($tableName)->limit(3)->get();
                                echo "<p class='info'>Sample data from {$tableName}:</p>";
                                echo "<pre>" . json_encode($sample, JSON_PRETTY_PRINT) . "</pre>";
                            }
                            break;
                        } catch(Exception $e2) {
                            echo "<p class='error'>❌ Table '{$tableName}' not found</p>";
                        }
                    }
                }
            @endphp
        @catch(Exception $e)
            <p class='error'>❌ Questions Test Error: {{ $e->getMessage() }}</p>
        @endtry
    </div>

    <div class="debug-section">
        <h2>5. MCQ Sets Test</h2>
        @try
            @php
                echo "<h3>Testing McqSet Model:</h3>";
                try {
                    $setCount = \App\Models\McqSet::count();
                    echo "<p class='success'>✅ McqSet Model: {$setCount} sets</p>";
                    
                    if($setCount > 0) {
                        $sets = \App\Models\McqSet::limit(3)->get(['id', 'title', 'exam_name', 'status', 'created_at']);
                        echo "<table>";
                        echo "<tr><th>ID</th><th>Title</th><th>Exam Name</th><th>Status</th><th>Created</th></tr>";
                        foreach($sets as $set) {
                            echo "<tr><td>{$set->id}</td><td>{$set->title}</td><td>{$set->exam_name}</td><td>{$set->status}</td><td>{$set->created_at}</td></tr>";
                        }
                        echo "</table>";
                    }
                } catch(Exception $e1) {
                    echo "<p class='error'>❌ McqSet Model Error: {$e1->getMessage()}</p>";
                    
                    echo "<h3>Testing Direct DB Queries:</h3>";
                    $possibleTables = ['mcq_sets', 'exams', 'exam_sets', 'mcq_set', 'exam'];
                    foreach($possibleTables as $tableName) {
                        try {
                            $count = \DB::table($tableName)->count();
                            echo "<p class='success'>✅ Table '{$tableName}': {$count} records</p>";
                            
                            if($count > 0) {
                                $sample = \DB::table($tableName)->limit(3)->get();
                                echo "<p class='info'>Sample data from {$tableName}:</p>";
                                echo "<pre>" . json_encode($sample, JSON_PRETTY_PRINT) . "</pre>";
                            }
                            break;
                        } catch(Exception $e2) {
                            echo "<p class='error'>❌ Table '{$tableName}' not found</p>";
                        }
                    }
                }
            @endphp
        @catch(Exception $e)
            <p class='error'>❌ Sets Test Error: {{ $e->getMessage() }}</p>
        @endtry
    </div>

    <div class="debug-section">
        <h2>6. All Tables Content Summary</h2>
        @try
            @php
                $tables = \DB::select('SHOW TABLES');
                echo "<table>";
                echo "<tr><th>Table Name</th><th>Record Count</th><th>Sample Columns</th></tr>";
                
                foreach($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    try {
                        $count = \DB::table($tableName)->count();
                        $columns = \DB::select("DESCRIBE {$tableName}");
                        $columnNames = array_slice(array_column($columns, 'Field'), 0, 5);
                        
                        echo "<tr>";
                        echo "<td>{$tableName}</td>";
                        echo "<td>{$count}</td>";
                        echo "<td>" . implode(', ', $columnNames) . "</td>";
                        echo "</tr>";
                    } catch(Exception $e) {
                        echo "<tr><td>{$tableName}</td><td colspan='2'>Error: {$e->getMessage()}</td></tr>";
                    }
                }
                echo "</table>";
            @endphp
        @catch(Exception $e)
            <p class='error'>❌ Table Summary Error: {{ $e->getMessage() }}</p>
        @endtry
    </div>

    <div class="debug-section">
        <h2>7. Laravel Configuration</h2>
        <p><strong>App Environment:</strong> {{ config('app.env') }}</p>
        <p><strong>Database Connection:</strong> {{ config('database.default') }}</p>
        <p><strong>Database Name:</strong> {{ config('database.connections.mysql.database') }}</p>
        <p><strong>Database Host:</strong> {{ config('database.connections.mysql.host') }}</p>
        <p><strong>Database Port:</strong> {{ config('database.connections.mysql.port') }}</p>
    </div>

    <p><a href="{{ url('/') }}" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">← Back to Homepage</a></p>
</body>
</html>
