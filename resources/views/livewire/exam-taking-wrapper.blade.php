@extends('layouts.mcq')

@section('title', 'Taking Exam - ' . $mcqSet->title)
@section('page-title', 'Exam in Progress')
@section('page-subtitle', $mcqSet->title)

@section('content')
<livewire:exam-taking :mcqSet="$mcqSet" />
@endsection