<?php

declare(strict_types=1);

namespace App\Orchid\Fields;

use Orchid\Screen\Fields\Upload;

/**
 * Custom Upload field with preview action and inline file display.
 * 
 * Usage:
 * PreviewUpload::make('candidate.attachments')
 *     ->title('Files')
 *     ->previewModal('viewAttachmentModal')  // Modal name for preview
 *     ->previewRoute('platform.hrm.candidate.view-attachment')  // Route for download/view
 *     ->modelIdField('candidate')  // Field name to get model ID for async parameters
 */
class PreviewUpload extends Upload
{
    /**
     * @var string
     */
    protected $view = 'fields.preview-upload';

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        // Add custom attributes for preview functionality
        $this->set('previewModal', null);
        $this->set('downloadRoute', null);
        $this->set('modelId', null);
        $this->set('modelIdField', null);
        $this->set('showInline', true);
        $this->set('aiRoute', null);
    }

    /**
     * Set the modal name for preview action.
     *
     * @param string $modalName
     * @return static
     */
    public function previewModal(string $modalName): static
    {
        return $this->set('previewModal', $modalName);
    }

    /**
     * Set the route name for downloading/viewing attachments.
     *
     * @param string $routeName
     * @return static
     */
    public function downloadRoute(string $routeName): static
    {
        return $this->set('downloadRoute', $routeName);
    }

    /**
     * Set the model ID directly for async parameters.
     *
     * @param int|string|null $id
     * @return static
     */
    public function modelId(int|string|null $id): static
    {
        return $this->set('modelId', $id);
    }

    /**
     * Set the field name to use for getting the model ID in async parameters.
     *
     * @param string $fieldName
     * @return static
     */
    public function modelIdField(string $fieldName): static
    {
        return $this->set('modelIdField', $fieldName);
    }

    /**
     * Set whether to show files inline (in a row).
     *
     * @param bool $showInline
     * @return static
     */
    public function showInline(bool $showInline = true): static
    {
        return $this->set('showInline', $showInline);
    }

    /**
     * Set the route name for AI processing.
     *
     * @param string $routeName
     * @return static
     */
    public function aiRoute(string $routeName): static
    {
        return $this->set('aiRoute', $routeName);
    }

    /**
     * Set the field mapping for AI extracted data.
     * Maps AI field names to form input names.
     *
     * Example:
     * ->mapAiFields([
     *     'name' => 'candidate.name',
     *     'email' => 'candidate.email',
     * ])
     *
     * @param array $mapping AI field name => form field name (dot notation)
     * @return static
     */
    public function mapAiFields(array $mapping): static
    {
        return $this->set('fieldMapping', $mapping);
    }
}
