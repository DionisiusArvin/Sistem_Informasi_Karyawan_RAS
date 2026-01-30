<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class ProjectTasks extends Component
{
    public $projectId;
    public $project;

    protected $listeners = [
        'refreshTasks' => '$refresh',
    ];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->project = Project::with(['tasks.divisions', 'tasks.dailyTasks'])
            ->findOrFail($projectId);
    }

    public function render()
    {
        $this->project = Project::with([
            'tasks' => function($q) {
                $q->orderBy('order');
            },
            'tasks.divisions',
            'tasks.dailyTasks'
        ])->findOrFail($this->projectId);

        return view('livewire.project-tasks', [
            'project' => $this->project
        ]);
    }
}
