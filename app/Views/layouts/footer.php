</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    tinymce.init({
        selector: '.wysiwyg-editor',
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
        height: 300,
        menubar: false,
        branding: false,
        promotion: false,
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        paste_as_text: true,
        paste_enable_default_filters: true,
        paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,h5,h6,p,ul,ol,li",
        paste_retain_style_properties: "none"
    });
    </script>
</body>
</html>