@extends('layouts.app')

@section('title', 'Global Leaderboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-globe me-2"></i>
                            Global Leaderboard
                        </h4>
                        <div>
                            <a href="{{ route('results.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to My Results
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary mb-2">Top Performers Across All Exams</h3>
                        <p class="text-muted mb-0">Ranking based on total marks and performance across multiple exams</p>
                    </div>
                </div>
            </div>

            @if($globalLeaderboard->count() > 0)
                <!-- Top 3 Global Performers -->
                @if($globalLeaderboard->count() >= 3)
                    <div class="row mb-4">
                        <!-- 2nd Place -->
                        <div class="col-md-4 order-md-1">
                            @php $second = $globalLeaderboard->get(1); @endphp
                            <div class="card shadow-sm global-podium-card second-place">
                                <div class="card-body text-center">
                                    <div class="podium-rank">
                                        <i class="fas fa-medal text-secondary fa-3x mb-3"></i>
                                        <h4 class="text-secondary">#2</h4>
                                    </div>
                                    <div class="user-avatar mb-3">
                                        <div class="avatar-circle bg-secondary text-white">
                                            {{ substr($second->user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <h5 class="mb-1">{{ $second->user->name }}</h5>
                                    <p class="text-muted small mb-2">{{ $second->user->email }}</p>
                                    <div class="score-display">
                                        <h3 class="text-secondary mb-1">{{ $second->total_marks }}</h3>
                                        <small class="text-muted">Total Marks</small>
                                    </div>
                                    <div class="stats mt-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted">Exams</small>
                                                <div class="fw-bold">{{ $second->exams_taken }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Accuracy</small>
                                                <div class="fw-bold">{{ $second->accuracy }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 1st Place -->
                        <div class="col-md-4 order-md-2">
                            @php $first = $globalLeaderboard->get(0); @endphp
                            <div class="card shadow-lg global-podium-card first-place">
                                <div class="card-body text-center">
                                    <div class="podium-rank">
                                        <i class="fas fa-crown text-warning fa-4x mb-3"></i>
                                        <h3 class="text-warning">#1</h3>
                                    </div>
                                    <div class="user-avatar mb-3">
                                        <div class="avatar-circle bg-warning text-dark">
                                            {{ substr($first->user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <h4 class="mb-1">{{ $first->user->name }}</h4>
                                    <p class="text-muted small mb-2">{{ $first->user->email }}</p>
                                    <div class="score-display">
                                        <h2 class="text-warning mb-1">{{ $first->total_marks }}</h2>
                                        <small class="text-muted">Total Marks</small>
                                    </div>
                                    <div class="stats mt-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted">Exams</small>
                                                <div class="fw-bold">{{ $first->exams_taken }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Accuracy</small>
                                                <div class="fw-bold">{{ $first->accuracy }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3rd Place -->
                        <div class="col-md-4 order-md-3">
                            @php $third = $globalLeaderboard->get(2); @endphp
                            <div class="card shadow-sm global-podium-card third-place">
                                <div class="card-body text-center">
                                    <div class="podium-rank">
                                        <i class="fas fa-medal text-warning fa-3x mb-3"></i>
                                        <h4 class="text-warning">#3</h4>
                                    </div>
                                    <div class="user-avatar mb-3">
                                        <div class="avatar-circle bg-warning text-dark">
                                            {{ substr($third->user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <h5 class="mb-1">{{ $third->user->name }}</h5>
                                    <p class="text-muted small mb-2">{{ $third->user->email }}</p>
                                    <div class="score-display">
                                        <h3 class="text-warning mb-1">{{ $third->total_marks }}</h3>
                                        <small class="text-muted">Total Marks</small>
                                    </div>
                                    <div class="stats mt-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted">Exams</small>
                                                <div class="fw-bold">{{ $third->exams_taken }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Accuracy</small>
                                                <div class="fw-bold">{{ $third->accuracy }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Complete Global Leaderboard -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list-ol me-2"></i>
                                Complete Global Rankings
                            </h5>
                            <div class="text-muted">
                                <small>{{ $globalLeaderboard->count() }} active participants</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="8%">Rank</th>
                                        <th width="25%">Participant</th>
                                        <th width="12%">Total Marks</th>
                                        <th width="12%">Exams Taken</th>
                                        <th width="12%">Avg. Score</th>
                                        <th width="12%">Accuracy</th>
                                        <th width="12%">Total Questions</th>
                                        <th width="7%">Badge</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($globalLeaderboard as $participant)
                                        <tr class="{{ Auth::id() == $participant->user_id ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($participant->rank <= 3)
                                                        @if($participant->rank == 1)
                                                            <i class="fas fa-crown text-warning me-2"></i>
                                                        @elseif($participant->rank == 2)
                                                            <i class="fas fa-medal text-secondary me-2"></i>
                                                        @else
                                                            <i class="fas fa-medal text-warning me-2"></i>
                                                        @endif
                                                    @endif
                                                    <span class="fw-bold">#{{ $participant->rank }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-small me-3">
                                                        {{ substr($participant->user->name, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $participant->user->name }}
                                                            @if(Auth::id() == $participant->user_id)
                                                                <span class="badge bg-primary ms-1">You</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">{{ $participant->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">{{ $participant->total_marks }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $participant->exams_taken }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $participant->avg_marks_per_question }}</span>
                                                <small class="text-muted">/question</small>
                                            </td>
                                            <td>
                                                @php
                                                    $accuracyClass = $participant->accuracy >= 80 ? 'text-success' : 
                                                                    ($participant->accuracy >= 60 ? 'text-warning' : 'text-danger');
                                                @endphp
                                                <span class="fw-bold {{ $accuracyClass }}">{{ $participant->accuracy }}%</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $participant->total_questions }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $badge = '';
                                                    $badgeClass = '';
                                                    if ($participant->rank == 1) {
                                                        $badge = 'Champion';
                                                        $badgeClass = 'bg-warning';
                                                    } elseif ($participant->rank <= 3) {
                                                        $badge = 'Elite';
                                                        $badgeClass = 'bg-secondary';
                                                    } elseif ($participant->rank <= 10) {
                                                        $badge = 'Expert';
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($participant->accuracy >= 80) {
                                                        $badge = 'Ace';
                                                        $badgeClass = 'bg-info';
                                                    } elseif ($participant->exams_taken >= 5) {
                                                        $badge = 'Active';
                                                        $badgeClass = 'bg-primary';
                                                    }
                                                @endphp
                                                @if($badge)
                                                    <span class="badge {{ $badgeClass }}">{{ $badge }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Global Statistics -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Performance Distribution
                                </h6>
                            </div>
                            <div class="card-body">
                                <canvas id="performanceChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-trophy me-2"></i>
                                    Achievement Levels
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="achievement-stats">
                                    @php
                                        $champions = $globalLeaderboard->where('rank', 1)->count();
                                        $elite = $globalLeaderboard->where('rank', '<=', 3)->count();
                                        $experts = $globalLeaderboard->where('rank', '<=', 10)->count();
                                        $aces = $globalLeaderboard->where('accuracy', '>=', 80)->count();
                                        $active = $globalLeaderboard->where('exams_taken', '>=', 5)->count();
                                    @endphp
                                    
                                    <div class="achievement-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-crown text-warning me-2"></i>
                                                <span>Champions</span>
                                            </div>
                                            <span class="badge bg-warning">{{ $champions }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="achievement-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-medal text-secondary me-2"></i>
                                                <span>Elite (Top 3)</span>
                                            </div>
                                            <span class="badge bg-secondary">{{ $elite }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="achievement-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-star text-success me-2"></i>
                                                <span>Experts (Top 10)</span>
                                            </div>
                                            <span class="badge bg-success">{{ $experts }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="achievement-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-bullseye text-info me-2"></i>
                                                <span>Aces (80%+ Accuracy)</span>
                                            </div>
                                            <span class="badge bg-info">{{ $aces }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="achievement-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-fire text-primary me-2"></i>
                                                <span>Active (5+ Exams)</span>
                                            </div>
                                            <span class="badge bg-primary">{{ $active }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Participants -->
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-globe fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No Global Rankings Yet</h4>
                        <p class="text-muted mb-4">Be among the first to take exams and appear on the global leaderboard!</p>
                        <a href="{{ route('exams.index') }}" class="btn btn-primary">
                            <i class="fas fa-play me-2"></i>
                            Take Your First Exam
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($globalLeaderboard->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Distribution Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const accuracyData = @json($globalLeaderboard->pluck('accuracy'));
    const examCounts = @json($globalLeaderboard->pluck('exams_taken'));
    const names = @json($globalLeaderboard->pluck('user.name'));
    
    new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Participants',
                data: accuracyData.map((accuracy, index) => ({
                    x: examCounts[index],
                    y: accuracy,
                    label: names[index]
                })),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Number of Exams Taken'
                    },
                    beginAtZero: true
                },
                y: {
                    title: {
                        display: true,
                        text: 'Accuracy (%)'
                    },
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.raw.label}: ${context.parsed.y}% accuracy, ${context.parsed.x} exams`;
                        }
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endif

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.global-podium-card {
    transition: transform 0.3s ease;
    margin-bottom: 2rem;
}

.first-place {
    transform: scale(1.05);
    border: 3px solid #ffc107;
}

.second-place, .third-place {
    margin-top: 1rem;
}

.global-podium-card:hover {
    transform: scale(1.1);
}

.first-place:hover {
    transform: scale(1.15);
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    margin: 0 auto;
}

.avatar-small {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
}

.achievement-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.achievement-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.table th {
    border-top: none;
    font-weight: 600;
}

@media (max-width: 768px) {
    .first-place {
        transform: none;
        margin-top: 0;
    }
    
    .second-place, .third-place {
        margin-top: 0;
    }
    
    .global-podium-card:hover, .first-place:hover {
        transform: none;
    }
}
</style>
@endsection
