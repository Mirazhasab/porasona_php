<div>
    @section('title', 'Taking Exam - ' . $mcqSet->title)
    @section('page-title', 'Exam in Progress')
    @section('page-subtitle', $mcqSet->title)

    @if(!$examStarted)
    <!-- Exam Start Screen -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 text-white text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clipboard-list" class="w-10 h-10"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">{{ $mcqSet->title }}</h1>
                <p class="text-blue-100">{{ $mcqSet->exam_name }}</p>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <i data-lucide="help-circle" class="w-8 h-8 text-blue-500 mx-auto mb-2"></i>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $questions->count() }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Questions
                            @if($limitedAttempt)
                                <span class="block text-xs text-blue-500 dark:text-blue-300 mt-1">Previewing {{ $questions->count() }} of {{ $totalQuestionCount }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                        <i data-lucide="clock" class="w-8 h-8 text-purple-500 mx-auto mb-2"></i>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $mcqSet->duration ? $mcqSet->duration . ' min' : 'No Limit' }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Duration</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
                        <i data-lucide="star" class="w-8 h-8 text-green-500 mx-auto mb-2"></i>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $mcqSet->total_marks ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Marks</div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4 mb-6">
                    <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        Instructions
                    </h3>
                    @if($limitedAttempt)
                        <div class="bg-white/80 dark:bg-gray-900/30 border border-yellow-200/70 dark:border-yellow-700/70 rounded-lg px-3 py-2 text-sm text-yellow-800 dark:text-yellow-100 mb-3">
                            Your plan currently allows {{ $attemptLimit }} question{{ $attemptLimit == 1 ? '' : 's' }} per exam attempt. Upgrade to unlock the complete set.
                        </div>
                    @endif
                    <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                        <li>• Read each question carefully before selecting your answer</li>
                        <li>• You can navigate between questions using the navigation panel</li>
                        <li>• Your progress is automatically saved</li>
                        @if($mcqSet->duration)
                        <li>• The exam will auto-submit when time expires</li>
                        @endif
                        <li>• Once submitted, you cannot change your answers</li>
                    </ul>
                </div>
                
                <div class="text-center">
                    <button wire:click="startExam" 
                            class="px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-semibold text-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 flex items-center gap-3 mx-auto shadow-lg hover:shadow-xl">
                        <i data-lucide="play" class="w-6 h-6"></i>
                        Start Exam
                    </button>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Exam Interface -->
    <div class="max-w-7xl mx-auto">
        <!-- Exam Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold mb-1">{{ $mcqSet->title }}</h1>
                        <p class="text-blue-100">
                            Question {{ $currentQuestion }} of {{ $questions->count() }}
                            @if($limitedAttempt)
                                <span class="block text-xs text-blue-100/80 mt-1">Showing {{ $questions->count() }} of {{ $totalQuestionCount }} questions allowed for your plan.</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-6">
                        @if($mcqSet->duration)
                        <div class="text-center" x-data="timer({{ $timeRemaining }})" x-init="startTimer()">
                            <div class="text-3xl font-mono font-bold" x-text="formatTime(timeLeft)"></div>
                            <p class="text-sm text-blue-100">Time Remaining</p>
                        </div>
                        @endif
                        <button wire:click="submitExam" 
                                wire:confirm="Submit exam with {{ $this->answeredCount }} of {{ $questions->count() }} questions answered?"
                                class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-colors flex items-center gap-2">
                            <i data-lucide="send" class="w-5 h-5"></i>
                            Submit Exam
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50">
                @if($limitedAttempt)
                    <div class="mb-3 bg-blue-100/70 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 text-sm text-blue-800 dark:text-blue-200 rounded-lg px-3 py-2">
                        Your current plan limits exams to {{ $attemptLimit }} question{{ $attemptLimit == 1 ? '' : 's' }} per attempt. Upgrade to unlock the full exam.
                    </div>
                @endif
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $this->answeredCount }} / {{ $questions->count() }} answered
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $this->progressPercentage }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Question Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="list" class="w-5 h-5"></i>
                            Questions
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-5 lg:grid-cols-4 gap-2">
                            @foreach($questions as $index => $question)
                            <button wire:click="goToQuestion({{ $index + 1 }})"
                                    class="w-10 h-10 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105
                                           @if($currentQuestion === $index + 1) 
                                               bg-blue-500 text-white border-2 border-blue-500
                                           @elseif($answers[$question->id] !== null)
                                               bg-green-500 text-white border-2 border-green-500
                                           @else
                                               border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-300
                                           @endif">
                                {{ $index + 1 }}
                            </button>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 text-xs">
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 rounded bg-blue-500"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Current</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 rounded bg-green-500"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Answered</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Question -->
            <div class="lg:col-span-3">
                @if($this->currentQuestionData)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $currentQuestion }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Question {{ $currentQuestion }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $currentQuestion }} of {{ $questions->count() }}</p>
                                </div>
                            </div>
                            @if($this->currentQuestionData->marks)
                            <div class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $this->currentQuestionData->marks }} {{ $this->currentQuestionData->marks == 1 ? 'mark' : 'marks' }}
                            </div>
                            @endif
                        </div>
                        
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-lg text-gray-800 dark:text-gray-200 leading-relaxed">{{ $this->currentQuestionData->question }}</p>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            @php 
                            $options = [
                                1 => ['letter' => 'A', 'text' => $this->currentQuestionData->ans_1],
                                2 => ['letter' => 'B', 'text' => $this->currentQuestionData->ans_2],
                                3 => ['letter' => 'C', 'text' => $this->currentQuestionData->ans_3],
                                4 => ['letter' => 'D', 'text' => $this->currentQuestionData->ans_4]
                            ];
                            @endphp
                            
                            @foreach($options as $value => $option)
                            <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 group
                                          @if($answers[$this->currentQuestionData->id] == $value)
                                              border-blue-500 bg-blue-50 dark:bg-blue-900/20
                                          @else
                                              border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20
                                          @endif">
                                <input type="radio" 
                                       wire:model.live="answers.{{ $this->currentQuestionData->id }}" 
                                       value="{{ $value }}" 
                                       class="mt-1 w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $option['letter'] }}
                                        </div>
                                        <span class="text-gray-800 dark:text-gray-200 leading-relaxed">{{ $option['text'] }}</span>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-b-2xl">
                        <div class="flex justify-between items-center">
                            <button wire:click="previousQuestion"
                                    class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors flex items-center gap-2 {{ $currentQuestion === 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $currentQuestion === 1 ? 'disabled' : '' }}>
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                Previous
                            </button>
                            
                            @if($currentQuestion === $questions->count())
                            <button wire:click="submitExam"
                                    wire:confirm="Submit exam with {{ $this->answeredCount }} of {{ $questions->count() }} questions answered?"
                                    class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Submit Exam
                            </button>
                            @else
                            <button wire:click="nextQuestion"
                                    class="px-6 py-3 bg-blue-500 text-white rounded-xl font-medium hover:bg-blue-600 transition-colors flex items-center gap-2">
                                Next
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <script>
    function timer(initialTime) {
        return {
            timeLeft: initialTime,
            startTimer() {
                if (this.timeLeft) {
                    setInterval(() => {
                        if (this.timeLeft > 0) {
                            this.timeLeft--;
                            if (this.timeLeft <= 0) {
                                alert('Time is up! Submitting exam automatically.');
                                @this.call('submitExam');
                            }
                        }
                    }, 1000);
                }
            },
            formatTime(seconds) {
                if (!seconds) return 'No Limit';
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        }
    }

    document.addEventListener('livewire:init', () => {
        lucide.createIcons();
    });

    document.addEventListener('livewire:navigated', () => {
        lucide.createIcons();
    });
    </script>
</div>