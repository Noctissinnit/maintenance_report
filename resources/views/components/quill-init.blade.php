<!-- Quill Editor Initialization Script -->
<script>
    // Global function for image upload in Quill
    function uploadImageQuill(quillEditor) {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = function() {
            const file = input.files[0];
            if (file) {
                const data = new FormData();
                data.append('image', file);
                data.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                fetch('{{ route("commands.upload-image") }}', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    const range = quillEditor.getSelection();
                    quillEditor.insertEmbed(range.index, 'image', data.url);
                })
                .catch(err => {
                    alert('Gagal upload gambar: ' + (err.message || 'Terjadi kesalahan'));
                });
            }
        };
    }

    // Initialize Quill editors
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing Quill editors...');
        
        // Find all elements with quill-editor class
        document.querySelectorAll('.quill-editor').forEach(function(element, index) {
            console.log('Setting up Quill editor ' + (index + 1));
            
            // Get the hidden textarea
            const textarea = element.querySelector('textarea.editor-content');
            const initialContent = textarea ? textarea.value : '';
            
            // Initialize Quill
            const quill = new Quill(element.querySelector('.editor'), {
                theme: 'snow',
                placeholder: 'Masukkan catatan di sini...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            // Set initial content if any
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }

            // Handle image button click
            const toolbar = quill.getModule('toolbar');
            toolbar.addHandler('image', function() {
                uploadImageQuill(quill);
            });

            // On form submit, copy content to textarea
            const form = element.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    if (textarea) {
                        textarea.value = quill.root.innerHTML;
                    }
                });
            }

            console.log('✓ Quill editor ' + (index + 1) + ' initialized');
        });
    });
</script>
