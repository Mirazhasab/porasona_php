@extends('layouts.mcq')

@section('title', 'Settings - MCQ System')
@section('page-title', 'Settings')
@section('page-subtitle', 'System configuration')

@section('content')
<div class="mb-6">
  <h3 class="text-xl font-semibold text-gray-800">System Settings</h3>
  <p class="text-gray-600">Configure system preferences and settings</p>
</div>

<form action="{{ route('mcq.settings.update') }}" method="POST">
  @csrf
  @method('PUT')
  
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <h4 class="font-semibold text-gray-800 mb-4">General Settings</h4>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">System Name</label>
          <input type="text" name="system_name" value="{{ $settings['system_name'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Default Exam Duration</label>
          <select name="default_exam_duration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="30" {{ $settings['default_exam_duration'] == 30 ? 'selected' : '' }}>30 minutes</option>
            <option value="60" {{ $settings['default_exam_duration'] == 60 ? 'selected' : '' }}>60 minutes</option>
            <option value="90" {{ $settings['default_exam_duration'] == 90 ? 'selected' : '' }}>90 minutes</option>
            <option value="120" {{ $settings['default_exam_duration'] == 120 ? 'selected' : '' }}>120 minutes</option>
          </select>
        </div>
      </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <h4 class="font-semibold text-gray-800 mb-4">Notification Settings</h4>
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-700">Email Notifications</span>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="email_notifications" {{ $settings['email_notifications'] ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-700">SMS Notifications</span>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="sms_notifications" {{ $settings['sms_notifications'] ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>
      </div>
    </div>
  </div>
  
  <div class="mt-6 flex items-center justify-between">
    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-secondary transition-colors">
      <i class="fas fa-save mr-2"></i>Save Settings
    </button>
    <a href="{{ route('mcq.dashboard') }}" wire:navigate class="text-gray-600 hover:text-gray-800">
      <i class="fas fa-times mr-2"></i>Cancel
    </a>
  </div>
</form>
@endsection
