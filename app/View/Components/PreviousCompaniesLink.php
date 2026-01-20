<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Candidate;

class PreviousCompaniesLink extends Component
{
    public ?string $previous_companies_url;
    public ?Candidate $candidate;

    /**
     * Create a new component instance.
     */
    public function __construct($candidate = null)
    {
        $this->candidate = $candidate;
        $this->previous_companies_url = $this->buildUrl();
    }

    protected function buildUrl(): ?string
    {
        if (! $this->candidate) {
            return null;
        }

        if ($this->candidate->relationLoaded('workedCompanies')) {
            $codes = collect($this->candidate->workedCompanies)->pluck('company_code')->filter()->unique()->values()->all();
        } else {
            $codes = $this->candidate->workedCompanies()->pluck('companies.company_code')->filter()->unique()->values()->all();
        }

        if (empty($codes)) {
            return null;
        }

        $codesParam = implode(',', $codes);
        $url = route('platform.hrm.company.list', ['company_code' => $codesParam]);

        return '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">🔗 ' . __('companies.fields.view_details') . '</a>';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.previous-companies-link');
    }
}
