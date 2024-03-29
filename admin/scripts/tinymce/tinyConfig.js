var templateDir = 'admin/scripts/tinymce/templates/';
var customCss = 'css/style.min.css';

function startTiny() {
    tinymce.init({
        selector:'textarea:not(.noTiny):not(.tinySlider)',
        plugins: 'paste image imagetools table code save link moxiemanager media fullscreen lists template textcolor colorpicker',
        menubar: 'file edit format insert table',
        toolbar: 'undo redo | styleselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | fontsizeselect | link insert | code fullscreen | template',
        templates: [
            {title: 'Two Column', description: 'Two responsive columns.', url: templateDir + 'two-column.html'},
            {title: 'Three Column', description: 'Three responsive columns.', url: templateDir + 'three-column.html'},
            {title: 'Four Column', description: 'Four responsive columns.', url: templateDir + 'four-column.html'},
        ],
        min_height: 350,
        height: 800,
        max_height: 1500,
        relative_urls: true,
        remove_script_host: true,
        image_title: true,
        content_css: customCss,
		document_base_url: http_host + server_name + root_dir,
		extended_valid_elements: 'span[*],iframe[*]',
        end_container_on_empty_block: true,
    });
}

startTiny();