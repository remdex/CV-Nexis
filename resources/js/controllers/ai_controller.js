/**
 * AI Controller - Custom Stimulus controller for AI processing of attachments
 * 
 * Usage in Blade template:
 * <div data-controller="ai" 
 *      data-ai-url-value="/api/ai/process"
 *      data-ai-model-type-value="candidate"
 *      data-ai-model-id-value="123">
 *     <button data-action="click->ai#process" 
 *             data-ai-attachment-id-param="456">
 *         AI Process
 *     </button>
 * </div>
 */

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: { type: String, default: '/admin/ai/process' },
        modelType: String,
        modelId: Number,
        fieldMapping: { type: Object, default: {} },
    };

    static targets = ['result', 'spinner'];

    /**
     * Get the dashboard prefix from meta tag
     * @param {string} path 
     * @returns {string}
     */
    prefix(path) {
        let prefix = document.head.querySelector('meta[name="dashboard-prefix"]');
        let pathname = `${prefix?.content || '/admin'}${path}`.replace(/\/\/+/g, '/');
        return `${location.protocol}//${location.hostname}${location.port ? `:${location.port}` : ''}${pathname}`;
    }

    /**
     * Display toast/alert notification
     * @param {string} title 
     * @param {string} message 
     * @param {string} type 
     */
    alert(title, message, type = 'warning') {
        let toastWrapper = document.querySelector('[data-controller="toast"]');
        if (toastWrapper) {
            let toastController = window.application.getControllerForElementAndIdentifier(toastWrapper, 'toast');
            toastController?.alert(title, message, type);
        }
    }

    /**
     * Display toast notification
     * @param {string} message 
     * @param {string} type 
     */
    toast(message, type = 'info') {
        let toastWrapper = document.querySelector('[data-controller="toast"]');
        if (toastWrapper) {
            let toastController = window.application.getControllerForElementAndIdentifier(toastWrapper, 'toast');
            toastController?.toast(message, type);
        }
    }

    /**
     * Process attachment with AI
     * Called via data-action="click->ai#process"
     * 
     * @param {Event} event - Click event with params
     */
    async process(event) {
        event.preventDefault();
        
        const attachmentId = event.params.attachmentId || event.currentTarget.dataset.attachmentId;
        
        if (!attachmentId) {
            this.alert('Error', 'No attachment ID provided', 'error');
            return;
        }

        const button = event.currentTarget;

        // Remove any previous success alerts immediately when starting processing
        this.clearSuccessAlerts();

        // Show loading state
        this.setLoading(button, true);

        try {
            const response = await this.sendRequest(attachmentId);
            this.handleSuccess(response, button);
        } catch (error) {
            this.handleError(error);
        } finally {
            this.setLoading(button, false);
        }
    }

    /**
     * Remove inline success alerts so old messages disappear when a new request starts
     */
    clearSuccessAlerts() {
        // Remove any alerts inside duplicate-candidates-alert containers
        document.querySelectorAll('.duplicate-candidates-alert').forEach(container => {
            container.querySelectorAll('.alert').forEach(el => el.remove());
        });

        // Also remove any standalone success or duplicate-warning alerts inserted elsewhere
        document.querySelectorAll('.alert.alert-success, .alert.alert-warning').forEach(el => {
             el.remove();
         });
    }

    /**
     * Send AJAX request to process attachment
     * @param {string|number} attachmentId 
     * @returns {Promise<Object>}
     */
    async sendRequest(attachmentId) {
        const url = this.hasUrlValue ? this.urlValue : this.prefix('/ai/process');
        const csrfToken = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') 
                       || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await window.axios.post(url, {
            attachment_id: attachmentId,
            model_type: this.hasModelTypeValue ? this.modelTypeValue : null,
            model_id: this.hasModelIdValue ? this.modelIdValue : null,
            _token: csrfToken,
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        return response.data;
    }

    /**
     * Handle successful response
     * @param {Object} response 
     * @param {HTMLElement} button 
     */
    handleSuccess(response, button) {
        if (response.success) {
            // Show inline alert near the upload field instead of a toast
            this.showAlert(response.message || 'AI processing completed', 'success');
            
            // Map AI extracted fields to form inputs
            const extractedData = response.data?.ai_result?.extracted_data;
            if (extractedData) {
                this.mapAiFields(extractedData);
            }
            
            // Check for duplicate candidates and show alert
            const duplicateCandidates = response.data?.duplicate_candidates;
            if (duplicateCandidates && duplicateCandidates.length > 0) {
                this.showDuplicateCandidatesAlert(duplicateCandidates);
            }
            
            // Dispatch custom event for parent components to handle
            this.dispatch('completed', { 
                detail: { 
                    data: response.data,
                    attachmentId: response.attachment_id 
                } 
            });

            // If there's a result target, update it
            if (this.hasResultTarget) {
                this.resultTarget.innerHTML = response.html || JSON.stringify(response.data, null, 2);
            }
        } else {
            this.alert('Warning', response.message || 'Processing completed with warnings', 'warning');
        }
    }

    /**
     * Show alert for duplicate candidates found
     * @param {Array} duplicates - Array of duplicate candidate objects
     */
    showDuplicateCandidatesAlert(duplicates) {
        // Find or create the duplicate alert container
        let alertContainer = document.querySelector('.duplicate-candidates-alert');
        
        if (!alertContainer) {
            // Create container if it doesn't exist
            alertContainer = document.createElement('div');
            alertContainer.className = 'duplicate-candidates-alert';
            
            // Try to insert it near the upload field
            const uploadContainer = this.element.closest('.dropzone-wrapper')?.parentElement 
                                 || this.element.closest('[data-controller*="upload"]')
                                 || this.element;
            uploadContainer.insertAdjacentElement('afterend', alertContainer);
        }

        // Find the template for duplicate alert
        const dropzoneWrapper = this.element.closest('[data-controller*="upload_preview"]');
        const uploadId = dropzoneWrapper?.dataset?.upload_previewId || dropzoneWrapper?.dataset?.uploadPreviewId;
        const template = uploadId ? document.querySelector(`#${uploadId}-duplicate-alert`) : null;
        
        if (!template) {
            console.error('Duplicate alert template not found', { uploadId, dropzoneWrapper });
            return;
        }

        // Clone the wrapper template
        const alertElement = template.content.cloneNode(true);

        // Find the per-item template (moved into Blade)
        const itemTemplate = uploadId ? document.querySelector(`#${uploadId}-duplicate-item`) : null;

        // Populate the list in the cloned template using the item template when available
        const listContainer = alertElement.querySelector('[data-duplicate-list]');
        if (listContainer) {
            if (itemTemplate && itemTemplate.content) {
                const fragment = document.createDocumentFragment();
                duplicates.forEach(candidate => {
                    const clone = itemTemplate.content.cloneNode(true);
                    const anchor = clone.querySelector('[data-candidate-edit-url]');
                    const nameHolder = clone.querySelector('[data-candidate-name]');
                    const meta = clone.querySelector('[data-candidate-meta]');

                    if (anchor) {
                        anchor.href = candidate.edit_url || '#';
                        anchor.target = '_blank';
                        anchor.textContent = `${candidate.name || ''} ${candidate.surname || ''}`.trim();
                    }

                    if (meta) {
                        const matchedFieldsText = (candidate.matched_fields || []).join(', ');
                        const emailPart = candidate.email ? ' - ' + candidate.email : '';
                        const phonePart = candidate.phone ? ' - ' + candidate.phone : '';
                        meta.innerHTML = `(${matchedFieldsText})${emailPart}${phonePart}`;
                    }

                    fragment.appendChild(clone);
                });

                listContainer.appendChild(fragment);
            } else {
                // Fallback to previous string-based approach if template missing
                const candidateLinks = duplicates.map(candidate => {
                    const matchedFieldsText = (candidate.matched_fields || []).join(', ');
                    return `<li class="mb-1">\n                <a href="${candidate.edit_url || '#'}" target="_blank" class="text-danger fw-bold">\n                    ${candidate.name || ''} ${candidate.surname || ''}\n                </a>\n                <span class="text-muted small">\n                    (${matchedFieldsText})\n                    ${candidate.email ? ' - ' + candidate.email : ''}\n                    ${candidate.phone ? ' - ' + candidate.phone : ''}\n                </span>\n            </li>`;
                }).join('');

                listContainer.innerHTML = candidateLinks;
            }
        }

        // Remove only existing duplicate alerts (preserve success/info alerts), then append the new one
        alertContainer.querySelectorAll('[data-duplicate-alert]').forEach(el => el.remove());

        // Mark the cloned alert so we can identify and replace it later
        const newAlertNode = alertElement.querySelector && alertElement.querySelector('.alert') ? alertElement.querySelector('.alert') : null;
        if (newAlertNode) {
            newAlertNode.setAttribute('data-duplicate-alert', '1');
        }

        alertContainer.appendChild(alertElement);

        // Also show a toast notification
        this.alert('Warning', 'Potential duplicate candidates found! Please review the candidates listed below.', 'warning');
    }

    /**
     * Show a bootstrap alert near the upload field (reuses duplicate alert container).
     * @param {string} message
     * @param {string} type - bootstrap alert type (success, warning, danger, info)
     * @param {string} title
     */
    showAlert(message, type = 'success', title = '') {
        // Find or create the container used for duplicate alerts so messages appear nearby
        let alertContainer = document.querySelector('.duplicate-candidates-alert');

        if (!alertContainer) {
            // Try to insert it near the upload field
            const uploadContainer = this.element.closest('.dropzone-wrapper')?.parentElement 
                                 || this.element.closest('[data-controller*="upload"]')
                                 || this.element;
            alertContainer = document.createElement('div');
            alertContainer.className = 'duplicate-candidates-alert';
            uploadContainer.insertAdjacentElement('afterend', alertContainer);
        }

        // Build alert element
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                ${title ? `<strong>${title}</strong> ` : ''}${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Append alert
        alertContainer.appendChild(wrapper.firstElementChild);
    }

    /**
     * Normalize a field name so it can be used safely inside data attributes/selectors.
     * @param {string} fieldName
     * @returns {string}
     */
    normalizeFieldKey(fieldName) {
        return String(fieldName || '').replace(/[^a-z0-9_\-]/gi, '_');
    }

    /**
     * Find a form element for a mapped field name (covers inputs and Orchid relation selects).
     * @param {string} fieldName
     * @returns {HTMLElement|null}
     */
    findFieldElement(fieldName) {
        if (!fieldName) {
            return null;
        }

        let element = document.querySelector(`[name="${fieldName}"]`);
        if (element) {
            return element;
        }

        // Array-style name (candidate.field[])
        const arrayFieldName = fieldName.replace(/\./g, '[') + '[]';
        const bracketName = arrayFieldName.replace(/^([^\[]+)/, '$1[').slice(0, -2) + '[]';
        element = document.querySelector(`select[name="${bracketName}"]`);
        if (element) {
            return element;
        }

        // Orchid relation id patterns
        const compactIdPrefix = 'field-' + fieldName.replace(/[\.\[\]]/g, '').toLowerCase();
        element = document.querySelector(`select[id^="${compactIdPrefix}-"][data-relation-target="select"]`)
               || document.querySelector(`select[id^="${compactIdPrefix}"][data-relation-target="select"]`);
        if (element) {
            return element;
        }

        const fieldParts = fieldName.split('.');
        const lastPart = fieldParts[fieldParts.length - 1];
        const relationContainer = document.querySelector(`[data-controller="relation"][data-relation-id*="${lastPart}"]`);
        if (relationContainer) {
            element = relationContainer.querySelector('select[data-relation-target="select"]');
            if (element) {
                return element;
            }
        }

        return null;
    }

    /**
     * Remove previously injected AI HTML blocks for a specific field.
     * @param {string} formFieldName
     */
    removeAiHtml(formFieldName) {
        const key = this.normalizeFieldKey(formFieldName);
        document.querySelectorAll(`[data-ai-html-for="${key}"]`).forEach(el => el.remove());
    }

    /**
     * Append custom HTML returned by AI right after the mapped form field, if available.
     * @param {string} formFieldName
     * @param {string} htmlContent
     * @param {HTMLElement|null} targetElement
     */
    appendHtmlAfterField(formFieldName, htmlContent, targetElement = null) {
        if (!formFieldName) {
            return;
        }

        if (!htmlContent) {
            this.removeAiHtml(formFieldName);
            return;
        }

        const key = this.normalizeFieldKey(formFieldName);
        const anchor = targetElement || this.findFieldElement(formFieldName);

        if (!anchor) {
            console.warn(`AI field mapping: Could not append HTML because field "${formFieldName}" was not found`);
            return;
        }

        // Clear any previous HTML for this field before inserting a new one
        this.removeAiHtml(formFieldName);

        const wrapper = document.createElement('div');
        wrapper.setAttribute('data-ai-html-for', key);
        wrapper.innerHTML = htmlContent;

        const insertAfter = anchor.closest('.form-group') || anchor.parentElement || anchor;
        insertAfter.insertAdjacentElement('afterend', wrapper);
    }

    /**
     * Map AI extracted fields to form inputs
     * Uses the fieldMapping value to determine which form fields to update
     * 
     * @param {Object} extractedData - Data extracted by AI (e.g., { email, phone, name, surname })
     */
    mapAiFields(extractedData) {
        if (!extractedData || typeof extractedData !== 'object') {
            return;
        }

        const mapping = this.hasFieldMappingValue ? this.fieldMappingValue : {};
        let fieldsUpdated = 0;

        for (const [aiField, formFieldName] of Object.entries(mapping)) {
            const value = extractedData[aiField];
            const htmlContent = extractedData[`${aiField}_url`];
            let targetElement = null;
            
            if (value !== undefined && value !== null) {
                // Handle array values (e.g., for multi-select fields like competences)
                if (Array.isArray(value)) {
                    // For array values, let updateMultiSelectField handle finding the element
                    if (this.updateMultiSelectField(null, value, formFieldName)) {
                        fieldsUpdated++;
                    }
                    targetElement = this.findFieldElement(formFieldName);
                } else {
                    // Handle scalar values - find the form input by name attribute
                    const input = document.querySelector(`[name="${formFieldName}"]`);
                    targetElement = input;
                    
                    if (input) {
                        const currentValue = input.value?.trim();
                        
                        if (!currentValue || currentValue === '') {
                            input.value = value;
                            fieldsUpdated++;
                            
                            // Trigger change event so other listeners can react
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        } else if (currentValue !== String(value)) {
                            // Field has a value, update it anyway (AI data takes priority)
                            input.value = value;
                            fieldsUpdated++;
                            
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    } else {
                        console.warn(`AI field mapping: Could not find input with name "${formFieldName}" for AI field "${aiField}"`);
                    }
                }
            }

            // Append or clear any supplemental HTML (e.g., *_url) after the mapped field
            this.appendHtmlAfterField(formFieldName, htmlContent, targetElement);
        }

        /*if (fieldsUpdated > 0) {
            this.toast(`Updated ${fieldsUpdated} field(s) from AI extraction`, 'info');
        }*/
    }

    /**
     * Update a multi-select field (e.g., Orchid Relation field) with array values
     * 
     * @param {HTMLElement|null} input - Optional input element hint (can be null)
     * @param {Array} values - Array of IDs to select
     * @param {string} fieldName - Field name for logging (e.g., 'candidate.competences')
     * @returns {boolean} - True if field was found and updated, false otherwise
     */
    updateMultiSelectField(input, values, fieldName) {
        // Try to find Orchid Relation field by the field name pattern
        // Convert 'candidate.competences' to various possible formats
        const fieldParts = fieldName.split('.');
        const lastPart = fieldParts[fieldParts.length - 1];
        let selectElement = null;
        


        // Strategy 1: Find by id pattern (field-candidatecompetences-*)
        // candidate[competences] => field-candidatecompetences-277a55326c7cfd00
        // The end is just random string and should be ignored
        const compactIdPrefix = 'field-' + fieldName.replace(/[\.\[\]]/g, '').toLowerCase();
        selectElement = document.querySelector(`select[id^="${compactIdPrefix}-"][data-relation-target="select"]`)
                     || document.querySelector(`select[id^="${compactIdPrefix}"][data-relation-target="select"]`);

    

        // Strategy 2: Find by exact name attribute: candidate[competences][]
        if (!selectElement) {
            const arrayFieldName = fieldName.replace(/\./g, '[') + '[]';
            const bracketName = arrayFieldName.replace(/^([^\[]+)/, '$1[').slice(0, -2) + '[]';
            selectElement = document.querySelector(`select[name="${bracketName}"]`);
        }
        
        // Strategy 3: Find by data-relation-id containing the field name
        if (!selectElement) {
            const relationContainer = document.querySelector(`[data-controller="relation"][data-relation-id*="${lastPart}"]`);
            if (relationContainer) {
                selectElement = relationContainer.querySelector('select[data-relation-target="select"]');
            }
        }
        
        // Strategy 4: Find near the provided input hint
        if (!selectElement && input) {
            selectElement = input.closest('.form-group')?.querySelector('select[multiple]') 
                         || input.parentElement?.querySelector('select[multiple]');
        }
        
        if (!selectElement) {
            console.warn(`Could not find multi-select element for field "${fieldName}"`);
            return false;
        }
        
        // Get the TomSelect instance from the Orchid relation controller
        // The relation controller stores TomSelect in this.choices
        const relationContainer = selectElement.closest('[data-controller="relation"]');
        let tomSelectInstance = null;
        
        if (relationContainer && window.application) {
            const relationController = window.application.getControllerForElementAndIdentifier(relationContainer, 'relation');
            tomSelectInstance = relationController?.choices;
        }
        
        // Fallback: try to get TomSelect instance directly from the element
        if (!tomSelectInstance && selectElement.tomselect) {
            tomSelectInstance = selectElement.tomselect;
        }
        
        if (tomSelectInstance) {
            // Use TomSelect API
            values.forEach(item => {
                // Handle both object format {id, name} and plain value format
                const value = typeof item === 'object' ? item.id : item;
                const label = typeof item === 'object' ? item.name : String(item);
                
                // Add option if it doesn't exist
                if (!tomSelectInstance.options[value]) {
                    tomSelectInstance.addOption({ value: String(value), label: label });
                }
                // Add item (select it)
                tomSelectInstance.addItem(String(value), true);
            });
        } else {
            // Vanilla select handling - add options and select them
            values.forEach(item => {
                // Handle both object format {id, name} and plain value format
                const value = typeof item === 'object' ? item.id : item;
                const label = typeof item === 'object' ? item.name : String(item);
                
                let option = selectElement.querySelector(`option[value="${value}"]`);
                if (!option) {
                    option = new Option(label, String(value), true, true);
                    selectElement.add(option);
                }
                option.selected = true;
            });
            
            selectElement.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        return true;
    }

    /**
     * Handle error response
     * @param {Error} error 
     */
    handleError(error) {
        console.error('AI processing error:', error);
        
        const message = error.response?.data?.message 
                     || error.message 
                     || 'An error occurred during AI processing';
        
        this.alert('Error', message, 'error');
        
        // Dispatch error event
        this.dispatch('error', { 
            detail: { 
                error: error.response?.data || error.message 
            } 
        });
    }

    /**
     * Toggle loading state on button
     * @param {HTMLElement} button 
     * @param {boolean} isLoading 
     */
    setLoading(button, isLoading) {
        if (isLoading) {
            button.dataset.originalContent = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            button.disabled = true;
            button.classList.add('disabled');
        } else {
            button.innerHTML = button.dataset.originalContent || button.innerHTML;
            button.disabled = false;
            button.classList.remove('disabled');
            delete button.dataset.originalContent;
        }

        // Also toggle spinner target if available
        if (this.hasSpinnerTarget) {
            this.spinnerTarget.classList.toggle('d-none', !isLoading);
        }
    }

    /**
     * Analyze attachment (alternative action)
     * Called via data-action="click->ai#analyze"
     * 
     * @param {Event} event
     */
    async analyze(event) {
        event.preventDefault();
        
        const attachmentId = event.params.attachmentId || event.currentTarget.dataset.attachmentId;
        const button = event.currentTarget;
        
        this.setLoading(button, true);

        try {
            const url = this.prefix('/ai/analyze');
            const csrfToken = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content');

            const response = await window.axios.post(url, {
                attachment_id: attachmentId,
                model_type: this.modelTypeValue,
                model_id: this.modelIdValue,
                _token: csrfToken,
            });

            this.handleSuccess(response.data, button);
        } catch (error) {
            this.handleError(error);
        } finally {
            this.setLoading(button, false);
        }
    }
}
