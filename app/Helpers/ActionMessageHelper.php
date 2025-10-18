<?php

namespace App\Helpers;

class ActionMessageHelper
{
    /**
     * Get contextual message based on action and context
     */
    public static function getMessage(string $action, array $context = []): array
    {
        $messages = [
            // Authentication Messages
            'login_success' => ['type' => 'success', 'message' => 'Welcome back!'],
            'login_failed' => ['type' => 'error', 'message' => 'Incorrect email or password.'],
            'logout' => ['type' => 'info', 'message' => 'You have been logged out successfully.'],
            'register_success' => ['type' => 'success', 'message' => 'Account created successfully!'],
            'register_failed' => ['type' => 'error', 'message' => 'Registration failed. Please try again.'],
            'session_expired' => ['type' => 'info', 'message' => 'Session expired. Please log in again.'],

            // Exam Messages
            'exam_submit_success' => ['type' => 'success', 'message' => 'Exam submitted successfully!'],
            'exam_submit_failed' => ['type' => 'error', 'message' => 'Failed to submit exam. Please try again.'],
            'exam_start' => ['type' => 'info', 'message' => 'Exam started. Good luck!'],
            'exam_not_found' => ['type' => 'error', 'message' => 'Exam not found or no longer available.'],
            'exam_already_taken' => ['type' => 'warning', 'message' => 'You have already taken this exam.'],

            // MCQ Management Messages
            'mcq_create_success' => ['type' => 'success', 'message' => 'MCQ set created successfully!'],
            'mcq_update_success' => ['type' => 'success', 'message' => 'MCQ set updated successfully!'],
            'mcq_delete_success' => ['type' => 'success', 'message' => 'MCQ set deleted successfully!'],
            'mcq_import_success' => ['type' => 'success', 'message' => 'MCQ questions imported successfully!'],
            'mcq_approve_success' => ['type' => 'success', 'message' => 'MCQ set approved successfully!'],
            'mcq_reject_success' => ['type' => 'success', 'message' => 'MCQ set rejected successfully!'],

            // User Management Messages
            'user_access_updated' => ['type' => 'success', 'message' => 'User permissions updated successfully!'],
            'user_access_granted' => ['type' => 'success', 'message' => 'All permissions granted to user.'],
            'user_access_reset' => ['type' => 'success', 'message' => 'User permissions reset successfully.'],
            'user_not_found' => ['type' => 'error', 'message' => 'User not found.'],

            // Access Control Messages
            'access_denied' => ['type' => 'error', 'message' => 'Access denied. Contact your administrator.'],
            'permission_required' => ['type' => 'warning', 'message' => 'You need permission to access this feature.'],
            'admin_required' => ['type' => 'error', 'message' => 'Administrator access required.'],

            // Form Messages
            'form_validation_failed' => ['type' => 'warning', 'message' => 'Please check the form and try again.'],
            'form_save_success' => ['type' => 'success', 'message' => 'Changes saved successfully!'],
            'form_save_failed' => ['type' => 'error', 'message' => 'Failed to save changes. Please try again.'],

            // File Upload Messages
            'file_upload_success' => ['type' => 'success', 'message' => 'File uploaded successfully!'],
            'file_upload_failed' => ['type' => 'error', 'message' => 'File upload failed. Please try again.'],
            'file_too_large' => ['type' => 'warning', 'message' => 'File size exceeds the maximum limit.'],
            'invalid_file_type' => ['type' => 'warning', 'message' => 'Invalid file type. Please select a valid file.'],

            // General Messages
            'operation_success' => ['type' => 'success', 'message' => 'Operation completed successfully!'],
            'operation_failed' => ['type' => 'error', 'message' => 'Operation failed. Please try again.'],
            'data_not_found' => ['type' => 'error', 'message' => 'Requested data not found.'],
            'server_error' => ['type' => 'error', 'message' => 'Server error occurred. Please try again later.'],
            'maintenance_mode' => ['type' => 'info', 'message' => 'System is under maintenance. Please try again later.'],
        ];

        $baseMessage = $messages[$action] ?? ['type' => 'info', 'message' => 'Action completed.'];

        // Apply context-specific modifications
        if (!empty($context)) {
            $baseMessage['message'] = self::applyContext($baseMessage['message'], $context);
        }

        return $baseMessage;
    }

    /**
     * Apply context to message template
     */
    private static function applyContext(string $message, array $context): string
    {
        foreach ($context as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        return $message;
    }

    /**
     * Flash message to session
     */
    public static function flash(string $action, array $context = []): void
    {
        $messageData = self::getMessage($action, $context);
        session()->flash($messageData['type'], $messageData['message']);
    }

    /**
     * Return JSON response with message
     */
    public static function jsonResponse(string $action, array $context = [], array $data = [], int $status = 200): \Illuminate\Http\JsonResponse
    {
        $messageData = self::getMessage($action, $context);
        
        return response()->json(array_merge([
            'type' => $messageData['type'],
            'message' => $messageData['message']
        ], $data), $status);
    }

    /**
     * Get message for specific permission denial
     */
    public static function getPermissionMessage(string $permission): array
    {
        $permissionNames = [
            'practice' => 'Practice Questions',
            'exams' => 'Examinations',
            'results' => 'Results & Analytics',
            'classmate' => 'Classmates',
            'leaderboard' => 'Leaderboard',
            'post' => 'Posts',
            'read_access' => 'Reading Materials',
            'mcq_management' => 'MCQ Management'
        ];

        $featureName = $permissionNames[$permission] ?? ucfirst($permission);
        
        return [
            'type' => 'error',
            'message' => "Access denied. You need permission to access {$featureName}. Contact your administrator."
        ];
    }

    /**
     * Get exam-specific messages with context
     */
    public static function getExamMessage(string $action, array $examData = []): array
    {
        $examTitle = $examData['title'] ?? 'the exam';
        $timeRemaining = $examData['time_remaining'] ?? null;
        $score = $examData['score'] ?? null;

        switch ($action) {
            case 'exam_started':
                return ['type' => 'info', 'message' => "Exam '{$examTitle}' started. Good luck!"];
            
            case 'exam_completed':
                $scoreText = $score !== null ? " Your score: {$score}%" : '';
                return ['type' => 'success', 'message' => "Exam '{$examTitle}' completed successfully!{$scoreText}"];
            
            case 'exam_time_warning':
                $timeText = $timeRemaining ? " {$timeRemaining} minutes remaining." : '';
                return ['type' => 'warning', 'message' => "Time running out!{$timeText}"];
            
            case 'exam_auto_submit':
                return ['type' => 'info', 'message' => "Time expired for '{$examTitle}'. Exam submitted automatically."];
            
            default:
                return self::getMessage($action);
        }
    }
}