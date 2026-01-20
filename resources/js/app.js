import './bootstrap';
import { Application } from '@hotwired/stimulus';

// Import custom controllers
import AiController from './controllers/ai_controller';
import PreviewUploadController from './controllers/preview_upload_controller';


// Register custom controllers with the Orchid application
// Wait for Orchid to initialize if not already available
document.addEventListener('DOMContentLoaded', () => {
    // Use existing Orchid application instance if available, otherwise create new one
    const application = window.application || Application.start();
    
    // Register custom controllers
    application.register('ai', AiController);
    application.register('upload_preview', PreviewUploadController);
    
    // Store reference globally if not already set
    if (!window.application) {
        window.application = application;
    }
});