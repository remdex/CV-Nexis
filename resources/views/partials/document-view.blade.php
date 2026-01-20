@isset($url)
    <div style="height:80vh; min-height:400px;">
        <iframe src="{{ $url }}" style="width:100%;height:100%;border:0" frameborder="0"></iframe>
    </div>
    <script>
        (function(){
            // After the view is rendered inside Orchid modal, find the modal ancestor
            // and enlarge the dialog to take most of viewport width.
            setTimeout(function(){
                try {
                    var root = document.currentScript && document.currentScript.parentElement;
                    if (!root) root = document.body;
                    var modal = root.closest && root.closest('.modal');
                    if (!modal) {
                        // fallback: search upward from document for open modal
                        modal = document.querySelector('.modal.show');
                    }
                    if (modal) {
                        modal.classList.add('document-view-modal');
                        var dialog = modal.querySelector('.modal-dialog');
                        if (dialog) {
                            dialog.style.maxWidth = '90vw';
                            dialog.style.width = '90vw';
                        }
                        var content = modal.querySelector('.modal-content');
                        if (content) {
                            content.style.height = '80vh';
                        }
                    }
                } catch(e) { /* ignore */ }
            }, 10);
        })();
    </script>
@else
    <div class="p-3">No file available to view.</div>
@endisset
